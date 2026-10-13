<?php

namespace App\Http\Controllers\VacationManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSchedule;
use App\Models\Collaborator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use App\Services\SettingService;

class WorkSchedulesController extends Controller
{
    public function index($date = null)
    {

        // Se a URL vier com "10-27", extrai mês 10 e ano 2027
        if ($date && preg_match('/^(\d{2})-(\d{2})$/', $date, $matches)) {
            $month = $matches[1];
            $year = '20' . $matches[2];
        } else {
            $month = date('m');
            $year = date('Y');
        }

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Pega o domingo da primeira semana e o sábado da última semana para fechar a grade completa
        $startOfCalendar = $startOfMonth->copy()->subDays($startOfMonth->dayOfWeek); // Domingo
        $endOfCalendar = $endOfMonth->copy()->addDays(6 - $endOfMonth->dayOfWeek); // Sábado

        // Busca colaboradores ativos, com escalas 6x1 ou 5x2, ordenados por nome
        $colaboradores = Collaborator::query()
            ->where('status', 'active')
            ->whereIn('work_scale', ['6x1', '5x2'])
            ->orderBy('name', 'asc')
            ->with(['workSchedules' => function ($query) use ($startOfCalendar, $endOfCalendar) {
                $query->whereBetween('date', [$startOfCalendar, $endOfCalendar]);
            }])
            ->get();

        // Cria o período contínuo de dias para preencher as colunas
        $period = CarbonPeriod::create($startOfCalendar, $endOfCalendar);
        $daysList = [];
        foreach ($period as $dt) {
            $daysList[] = $dt->copy();
        }

        // Organiza as jornadas em um array indexado por [colaborador_id][YYYY-MM-DD]
        $schedulesMap = [];
        foreach ($colaboradores as $colaborador) {
            foreach ($colaborador->workSchedules as $schedule) {
                $dateKey = $schedule->date->format('Y-m-d');
                $schedulesMap[$colaborador->id][$dateKey] = $schedule;
            }
        }

        return view('vacation_manager.work_schedules.index', compact(
            'colaboradores',
            'daysList',
            'schedulesMap',
            'month',
            'year',
            'startOfMonth'
        ));
    }

    // Adicione este método para tratar o clique na célula
    public function toggleDayOff(Request $request)
    {
        $request->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'date' => 'required|date',
            'type' => 'required|in:work,day_off',
        ]);

        $collaboratorId = $request->collaborator_id;
        $date = $request->date;
        $newType = $request->type;

        $schedule = WorkSchedule::where('collaborator_id', $collaboratorId)
            ->where('date', $date)
            ->first();

        // TRAVA DE SEGURANÇA: Se já existir e for um tipo protegido, bloqueia a alteração
        if ($schedule && in_array($schedule->type, ['medical_leave', 'maternity_leave', 'lack', 'vacation', 'holiday'])) {
            return response()->json([
                'success' => false,
                'message' => 'Este tipo de registro é gerenciado em outra tela.'
            ], 422);
        }

        $oldData = $schedule ? $schedule->toArray() : null;
        
        if ($schedule) {
            $schedule->type = $newType;
            $schedule->save();
        } else {
            WorkSchedule::create([
                'collaborator_id' => $collaboratorId,
                'date' => $date,
                'type' => $newType
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($schedule)
            ->withProperties([
                'collaborator_id' => $collaboratorId,
                'date' => $date,
                'old' => $oldData,
                'new' => $schedule->toArray(),
            ])
            ->log('Escala Alterada Manualmente');

        $labels = [
            'work' => 'T',
            'day_off' => 'F',
            'medical_leave' => 'A',
            'vacation' => 'V',
            'holiday' => 'H'
        ];

        return response()->json([
            'success' => true,
            'type' => $newType,
            'label' => $labels[$newType] ?? 'T'
        ]);
    }


    // Método para o botão Atualizar jornada
    public function recalculate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'open_sunday' => 'required|in:0,1',
            'generate_all_days' => 'required|in:0,1',
            'apply_vacations' => 'required|in:0,1',
            'apply_holidays' => 'required|in:0,1',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        $openSunday = $request->input('open_sunday') == '1';
        $generateAllDays = $request->input('generate_all_days') == '1';
        $applyVacations = $request->input('apply_vacations') == '1';
        $applyHolidays = $request->input('apply_holidays') == '1'; // Guardado para o futuro

        // Define o período do mês selecionado
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        // Busca colaboradores ativos com escala 6x1 ou 5x2
        $collaborators = Collaborator::query()
            ->where('status', 'active')
            ->whereIn('work_scale', ['6x1', '5x2'])
            ->get();

        DB::transaction(function () use ($collaborators, $startOfMonth, $endOfMonth, $openSunday, $applyVacations, $applyHolidays, $generateAllDays, $month, $year) {

            foreach ($collaborators as $collaborator) {



                // 1. REGRA DE FERIADOS (Se habilitado)
                if ($applyHolidays) {
                    // Busca todos os feriados cadastrados
                    $holidays = DB::table('holidays')->get();

                    foreach ($holidays as $holiday) {
                        $holidayDate = Carbon::parse($holiday->date);
                        $targetDate = null;

                        if ($holiday->type === 'appellant') {
                            // Feriado recorrente: Pega o dia e mês do feriado e aplica no ano/mês atual da tela
                            $targetDate = Carbon::create($year, $holidayDate->month, $holidayDate->day);
                        } elseif ($holiday->type === 'unique') {
                            // Feriado único: Valida o ano exato
                            if ($holidayDate->year == $year) {
                                $targetDate = $holidayDate;
                            }
                        }

                        // Se a data calculada cair exatamente dentro do mês que está sendo processado
                        if ($targetDate && $targetDate->between($startOfMonth, $endOfMonth)) {
                            WorkSchedule::updateOrCreate(
                                [
                                    'collaborator_id' => $collaborator->id,
                                    'date' => $targetDate->format('Y-m-d'),
                                ],
                                [
                                    'type' => 'holiday'
                                ]
                            );
                        }
                    }
                }

                // 2. REGRA DE DOMINGOS (Se a empresa não abre aos domingos)
                if ($openSunday) {
                    $current = $startOfMonth->copy();
                    while ($current <= $endOfMonth) {
                        if ($current->isSunday()) {
                            WorkSchedule::updateOrCreate(
                                [
                                    'collaborator_id' => $collaborator->id,
                                    'date' => $current->format('Y-m-d'),
                                ],
                                [
                                    'type' => 'day_off'
                                ]
                            );
                        }
                        $current->addDay();
                    }
                }




                // 3. REGRA DE FÉRIAS (Se habilitado)
                if ($applyVacations) {
                    // Busca férias do colaborador que interagem com o mês selecionado
                    $vacations = DB::table('vacations')
                        ->where('collaborator_id', $collaborator->id)
                        ->whereNull('deleted_at')
                        ->where('start_date', '<=', $endOfMonth->format('Y-m-d'))
                        ->where('end_date', '>=', $startOfMonth->format('Y-m-d'))
                        ->get();

                    foreach ($vacations as $vacation) {
                        $vacationStart = Carbon::parse($vacation->start_date);
                        $vacationEnd = Carbon::parse($vacation->end_date);

                        $periodStart = $vacationStart->max($startOfMonth);
                        $periodEnd = $vacationEnd->min($endOfMonth);

                        $current = $periodStart->copy();
                        while ($current <= $periodEnd) {
                            WorkSchedule::updateOrCreate(
                                [
                                    'collaborator_id' => $collaborator->id,
                                    'date' => $current->format('Y-m-d'),
                                ],
                                [
                                    'type' => 'vacation'
                                ]
                            );
                            $current->addDay();
                        }
                    }
                }

                // 4. GERAR TODOS OS DIAS (Regra final: preenche o restante do mês com 'work')
                if ($generateAllDays) {
                    $current = $startOfMonth->copy();
                    while ($current <= $endOfMonth) {
                        $dateStr = $current->format('Y-m-d');

                        // Verifica se já existe algum registro para este dia (ex: day_off de domingo ou vacation)
                        $exists = WorkSchedule::where('collaborator_id', $collaborator->id)
                            ->where('date', $dateStr)
                            ->exists();

                        // Se não existir nada lançado, preenche como 'work' (T)
                        if (!$exists) {
                            WorkSchedule::create([
                                'collaborator_id' => $collaborator->id,
                                'date' => $dateStr,
                                'type' => 'work'
                            ]);
                        }

                        $current->addDay();
                    }
                }

                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($collaborator)
                    ->withProperties([
                        'month' => $month,
                        'year' => $year,
                        'open_sunday' => $openSunday,
                        'generate_all_days' => $generateAllDays,
                        'apply_vacations' => $applyVacations,
                        'apply_holidays' => $applyHolidays,
                    ])
                    ->log('Jornada Recalculada');
            }
        });

        $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
        $yearShort = substr($year, -2);


        return redirect()->route('vacation_manager.work_schedules.index', [
            'date' => "{$monthPadded}-{$yearShort}"
        ])->with('success', 'Jornada recalculada com sucesso!');
    }



    public function myscale($collaboratorId, $date)
    {
        $collaborator = Collaborator::findOrFail($collaboratorId);

        // Se o usuário NÃO tiver permissão de editar/gerenciar escalas E o user_id do colaborador for diferente do usuário logado
        if (!auth()->user()->can('collaborators.workschedule.edit') && (int) $collaborator->user_id !== (int) auth()->id()) {
            abort(403, 'Acesso restrito. Você só pode visualizar a sua própria escala.');
        }

        try {
            $carbonDate = Carbon::createFromFormat('m-y', $date)->startOfMonth();
        } catch (\Exception $e) {
            try {
                $carbonDate = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
            } catch (\Exception $ex) {
                $carbonDate = now()->startOfMonth();
            }
        }

        $startOfMonth = $carbonDate->copy()->startOfMonth();
        $endOfMonth = $carbonDate->copy()->endOfMonth();

        // Intervalo expandido da grade (cobre os dias das pontas que aparecem no calendário)
        $startDateGrid = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDateGrid = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        // Busca as escalas do colaborador abrangendo todo o período visível da grade
        $schedules = WorkSchedule::where('collaborator_id', $collaborator->id)
            ->whereBetween('date', [$startDateGrid->format('Y-m-d'), $endDateGrid->format('Y-m-d')])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $weeks = [];
        $week = [];
        $current = $startDateGrid->copy();

        while ($current <= $endDateGrid) {
            $week[] = $current->copy();
            if (count($week) == 7) {
                $weeks[] = $week;
                $week = [];
            }
            $current->addDay();
        }

        return view('vacation_manager.work_schedules.my_scale', compact(
            'collaborator',
            'carbonDate',
            'schedules',
            'weeks',
            'startOfMonth'
        ));
    }


    public function releases(Request $request)
    {
        // Pega o valor do mês/ano vindo do filtro ou usa o mês atual como padrão
        $dateInput = $request->input('date', now()->format('m-y'));

        try {
            $carbonDate = Carbon::createFromFormat('m-y', $dateInput)->startOfMonth();
        } catch (\Exception $e) {
            try {
                $carbonDate = Carbon::createFromFormat('Y-m', $dateInput)->startOfMonth();
            } catch (\Exception $ex) {
                $carbonDate = now()->startOfMonth();
            }
        }

        $startOfMonth = $carbonDate->copy()->startOfMonth();
        $endOfMonth = $carbonDate->copy()->endOfMonth();

        // Verifica se o checkbox de exibir folgas está marcado (padrão: marcado/true)
        $showDayOff = $request->has('show_day_off') ? $request->input('show_day_off') == '1' : true;

        // Define quais tipos buscar com base no checkbox
        $types = ['medical_leave', 'maternity_leave', 'lack'];
        if ($showDayOff) {
            $types[] = 'day_off';
        }

        // Paginação com filtro de período e tipos
        $perPage = 15;
        $releases = WorkSchedule::with('collaborator')
            ->whereIn('type', $types)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->paginate($perPage)
            ->appends($request->all()); // Mantém os filtros na paginação

        $collaborators = Collaborator::where('status', 'active')->orderBy('name')->get();

        // Formato para o input type="month" (YYYY-MM)
        $yearMonthValue = $carbonDate->format('Y-m');

        return view('vacation_manager.work_schedules.releases', compact(
            'releases',
            'collaborators',
            'carbonDate',
            'yearMonthValue',
            'showDayOff'
        ));
    }

    public function storeRelease(Request $request)
    {
        $request->validate([
            'collaborator_id' => 'required|exists:collaborators,id',
            'start_date' => 'required|date',
            // Adicionado 'lack' aqui
            'type' => 'required|in:medical_leave,maternity_leave,day_off,lack',
            'days_count' => 'required|integer|min:1',
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $daysCount = (int) $request->input('days_count');
        $collaboratorId = $request->input('collaborator_id');
        $type = $request->input('type');

        for ($i = 0; $i < $daysCount; $i++) {
            $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');

            WorkSchedule::updateOrCreate(
                [
                    'collaborator_id' => $collaboratorId,
                    'date' => $currentDate,
                ],
                [
                    'type' => $type
                ]
            );
        }

        return redirect()->route('vacation_manager.work_schedules.releases')
            ->with('success', 'Lançamento(s) cadastrado(s) com sucesso!');
    }

    public function destroyRelease($id)
    {
        $schedule = WorkSchedule::findOrFail($id);
        $oldData = $schedule->toArray();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($schedule)
            ->withProperties([
                'old' => $oldData,
                'new' => null,
            ])
            ->log('Lançamento Removido');
        $schedule->delete();



        return redirect()->route('vacation_manager.work_schedules.releases')
            ->with('success', 'Lançamento removido com sucesso!');
    }
}
