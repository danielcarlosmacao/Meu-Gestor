<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleService;
use App\Models\Workshop;
use App\Services\SettingService;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleMaintenanceController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();

        $vehicles = Vehicle::where('status', 'active')
            ->get();

        $vehicleServices = VehicleService::orderBy('name', 'asc')
            ->get();

        $workshops = Workshop::all();

        $maintenances = VehicleMaintenance::with([
            'vehicle',
            'services'
        ])
            ->orderBy('maintenance_date', 'desc')
            ->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMA QUILOMETRAGEM REGISTRADA POR VEÍCULO
        |--------------------------------------------------------------------------
        |
        | Busca a quilometragem do último registro cadastrado de cada veículo,
        | utilizando o maior ID da manutenção.
        |
        */

        $maxMileages = DB::table('vehicle_maintenances as vm')
            ->select(
                'vm.vehicle_id',
                'vm.mileage'
            )
            ->whereNull('vm.deleted_at')
            ->whereIn('vm.id', function ($query) {

                $query->select(
                    DB::raw('MAX(id)')
                )
                    ->from('vehicle_maintenances')
                    ->whereNull('deleted_at')
                    ->groupBy('vehicle_id');
            })
            ->pluck(
                'mileage',
                'vehicle_id'
            );

        return view(
            'fleet.vehicles.vehicle_maintenances',
            compact(
                'vehicles',
                'vehicleServices',
                'maintenances',
                'workshops',
                'maxMileages'
            )
        );
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDAÇÃO
        |--------------------------------------------------------------------------
        |
        | O input HTML type="date" envia a data no formato:
        |
        | 2026-08-28
        |
        | Por isso utilizamos Y-m-d.
        |
        */

        $data = $request->validate([
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

            'type' => [
                'required',
                'in:preventive,corrective',
            ],

            'maintenance_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'cost' => [
                'nullable',
                'numeric',
            ],

            'status' => [
                'required',
                'in:pending,completed',
            ],

            'mileage' => [
                'nullable',
                'integer',
            ],

            'parts_used' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'workshop' => [
                'nullable',
                'string',
                'max:255',
            ],

            'vehicle_services' => [
                'nullable',
                'array',
            ],

            'vehicle_services.*' => [
                'exists:vehicle_services,id',
            ],

            'allow_lower_mileage' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PERMITIR QUILOMETRAGEM MENOR
        |--------------------------------------------------------------------------
        */

        $allowLowerMileage = $request->boolean(
            'allow_lower_mileage'
        );

        /*
        |--------------------------------------------------------------------------
        | SERVIÇOS
        |--------------------------------------------------------------------------
        */

        $vehicleServices =
            $data['vehicle_services'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | REMOVE CAMPOS QUE NÃO PERTENCEM À TABELA
        |--------------------------------------------------------------------------
        */

        unset(
            $data['vehicle_services'],
            $data['allow_lower_mileage']
        );

        /*
        |--------------------------------------------------------------------------
        | CRIA A MANUTENÇÃO
        |--------------------------------------------------------------------------
        |
        | maintenance_date já chega no formato correto do MySQL:
        |
        | YYYY-MM-DD
        |
        */

        $maintenance = VehicleMaintenance::create(
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | VINCULA OS SERVIÇOS
        |--------------------------------------------------------------------------
        */

        if (!empty($vehicleServices)) {

            $maintenance
                ->services()
                ->attach($vehicleServices);
        }

        /*
        |--------------------------------------------------------------------------
        | LOG DE CRIAÇÃO
        |--------------------------------------------------------------------------
        */

        activity()
            ->causedBy(auth()->user())
            ->performedOn($maintenance)
            ->withProperties([
                'new' => $maintenance
                    ->load('services')
                    ->toArray(),

                'allow_lower_mileage' =>
                $allowLowerMileage,
            ])
            ->log(
                'Manutenção de Veículo Criada'
            );

        return redirect()
            ->back()
            ->with(
                'success',
                'Manutenção adicionada com sucesso!'
            );
    }

    public function update(Request $request, $id)
    {
        $maintenance =
            VehicleMaintenance::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDAÇÃO
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
            ],

            'type' => [
                'required',
                'in:preventive,corrective',
            ],

            'maintenance_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'cost' => [
                'nullable',
                'numeric',
            ],

            'status' => [
                'required',
                'in:pending,completed',
            ],

            'mileage' => [
                'nullable',
                'integer',
            ],

            'parts_used' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'workshop' => [
                'nullable',
                'string',
                'max:255',
            ],

            'vehicle_services' => [
                'nullable',
                'array',
            ],

            'vehicle_services.*' => [
                'exists:vehicle_services,id',
            ],

            'allow_lower_mileage' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PERMITIR QUILOMETRAGEM MENOR
        |--------------------------------------------------------------------------
        */

        $allowLowerMileage =
            $request->boolean(
                'allow_lower_mileage'
            );

        /*
        |--------------------------------------------------------------------------
        | SERVIÇOS
        |--------------------------------------------------------------------------
        */

        $vehicleServices =
            $data['vehicle_services'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | REMOVE CAMPOS QUE NÃO PERTENCEM À TABELA
        |--------------------------------------------------------------------------
        */

        unset(
            $data['vehicle_services'],
            $data['allow_lower_mileage']
        );

        /*
        |--------------------------------------------------------------------------
        | DADOS ANTIGOS PARA O LOG
        |--------------------------------------------------------------------------
        */

        $oldData = $maintenance
            ->load('services')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | ATUALIZA A MANUTENÇÃO
        |--------------------------------------------------------------------------
        */

        $maintenance->update(
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | SINCRONIZA OS SERVIÇOS
        |--------------------------------------------------------------------------
        */

        $maintenance
            ->services()
            ->sync(
                $vehicleServices
            );

        /*
        |--------------------------------------------------------------------------
        | RECARREGA OS DADOS
        |--------------------------------------------------------------------------
        */

        $maintenance->load(
            'services'
        );

        /*
        |--------------------------------------------------------------------------
        | LOG DE ATUALIZAÇÃO
        |--------------------------------------------------------------------------
        */

        activity()
            ->causedBy(auth()->user())
            ->performedOn($maintenance)
            ->withProperties([
                'old' => $oldData,

                'new' =>
                $maintenance->toArray(),

                'allow_lower_mileage' =>
                $allowLowerMileage,
            ])
            ->log(
                'Manutenção de Veículo Atualizada'
            );

        return redirect()
            ->back()
            ->with(
                'success',
                'Manutenção atualizada com sucesso!'
            );
    }

    public function destroy($id)
    {
        $maintenance =
            VehicleMaintenance::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | DADOS ANTIGOS
        |--------------------------------------------------------------------------
        */

        $oldData = $maintenance
            ->load('services')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | EXCLUSÃO
        |--------------------------------------------------------------------------
        */

        $maintenance->delete();

        /*
        |--------------------------------------------------------------------------
        | LOG
        |--------------------------------------------------------------------------
        */

        activity()
            ->causedBy(auth()->user())
            ->performedOn($maintenance)
            ->withProperties([
                'old' => $oldData
            ])
            ->log(
                'Manutenção de veículo Deletado'
            );

        return redirect()
            ->back()
            ->with(
                'success',
                'Manutenção excluída com sucesso!'
            );
    }

    public function byVehicle(
        Request $request,
        $vehicleId,
        SettingService $settingService
    ) {
        $vehicle =
            Vehicle::findOrFail($vehicleId);

        $perPage =
            $settingService->getPerPage();

        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $query = $vehicle
            ->maintenances()
            ->with('services')
            ->orderBy(
                'maintenance_date',
                'desc'
            );

        /*
        |--------------------------------------------------------------------------
        | FILTRO DATA INICIAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_date')) {

            $query->whereDate(
                'maintenance_date',
                '>=',
                $request->start_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO DATA FINAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('end_date')) {

            $query->whereDate(
                'maintenance_date',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTO TOTAL
        |--------------------------------------------------------------------------
        */

        $totalCost =
            (clone $query)
            ->sum('cost');

        /*
        |--------------------------------------------------------------------------
        | PAGINAÇÃO
        |--------------------------------------------------------------------------
        */

        $maintenances =
            $query->paginate($perPage);

        return view(
            'fleet.vehicles.by_vehicle',
            compact(
                'vehicle',
                'maintenances',
                'totalCost'
            )
        );
    }

    public function handlePdfReport(
        Request $request,
        SettingService $settingService
    ) {
        $startDate =
            $request->input('start_date');

        $endDate =
            $request->input('end_date');

        $action =
            $request->input(
                'action',
                'view'
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDAÇÃO DO PERÍODO
        |--------------------------------------------------------------------------
        */

        if (!$startDate || !$endDate) {

            abort(
                400,
                'Período inválido.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VEÍCULOS
        |--------------------------------------------------------------------------
        */

        $vehicles =
            Vehicle::where(
                'status',
                'active'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SERVIÇOS
        |--------------------------------------------------------------------------
        */

        $vehicleServices =
            VehicleService::orderBy(
                'name',
                'asc'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | OFICINAS
        |--------------------------------------------------------------------------
        */

        $workshops =
            Workshop::all();

        /*
        |--------------------------------------------------------------------------
        | MANUTENÇÕES
        |--------------------------------------------------------------------------
        */

        $maintenances =
            VehicleMaintenance::with([
                'vehicle',
                'services'
            ])
            ->whereBetween(
                'maintenance_date',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->orderBy(
                'maintenance_date',
                'desc'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MAIOR QUILOMETRAGEM
        |--------------------------------------------------------------------------
        */

        $maxMileages =
            DB::table(
                'vehicle_maintenances'
            )
            ->select(
                'vehicle_id',
                DB::raw(
                    'MAX(mileage) as max_mileage'
                )
            )
            ->whereBetween(
                'maintenance_date',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->whereNull('deleted_at')
            ->groupBy('vehicle_id')
            ->pluck(
                'max_mileage',
                'vehicle_id'
            );

        /*
        |--------------------------------------------------------------------------
        | MENOR QUILOMETRAGEM
        |--------------------------------------------------------------------------
        */

        $minMileages =
            DB::table(
                'vehicle_maintenances'
            )
            ->select(
                'vehicle_id',
                DB::raw(
                    'MIN(mileage) as min_mileage'
                )
            )
            ->whereBetween(
                'maintenance_date',
                [
                    $startDate,
                    $endDate
                ]
            )
            ->whereNull('deleted_at')
            ->groupBy('vehicle_id')
            ->pluck(
                'min_mileage',
                'vehicle_id'
            );

        /*
        |--------------------------------------------------------------------------
        | QUILOMETRAGEM RODADA
        |--------------------------------------------------------------------------
        */

        $kmWheeled = [];

        foreach (
            $maxMileages
            as $vehicleId => $maxMileage
        ) {

            $minMileage =
                $minMileages->get(
                    $vehicleId,
                    0
                );

            $kmWheeled[$vehicleId] =
                $maxMileage - $minMileage;
        }

        /*
        |--------------------------------------------------------------------------
        | DADOS DO PDF
        |--------------------------------------------------------------------------
        */

        $data = compact(
            'vehicles',
            'vehicleServices',
            'maintenances',
            'workshops',
            'maxMileages',
            'minMileages',
            'kmWheeled',
            'startDate',
            'endDate'
        );

        /*
        |--------------------------------------------------------------------------
        | GERA PDF
        |--------------------------------------------------------------------------
        */

        $pdf = PDF::loadView(
            'fleet.vehicles.vehicle_maintenances_pdf',
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD
        |--------------------------------------------------------------------------
        */

        if ($action === 'download') {

            return $pdf->download(
                "relatorio_manutencoes_{$startDate}_a_{$endDate}.pdf"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VISUALIZAÇÃO
        |--------------------------------------------------------------------------
        */

        return $pdf->stream(
            "relatorio_manutencoes_{$startDate}_a_{$endDate}.pdf"
        );
    }
}
