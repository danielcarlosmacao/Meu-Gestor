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
        $vehicles = Vehicle::where('status', 'active')->get();
        $vehicleServices = VehicleService::orderby('name', 'asc')->get();
        $workshops = Workshop::all();
        $maintenances = VehicleMaintenance::with(['vehicle', 'services'])->orderBy('maintenance_date', 'desc')->paginate($perPage);

        // Subquery para obter o maior mileage por vehicle_id
        $maxMileages = DB::table('vehicle_maintenances as vm')
            ->select('vm.vehicle_id', 'vm.mileage')
            ->whereNull('vm.deleted_at')
            ->whereIn('vm.id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('vehicle_maintenances')
                    ->whereNull('deleted_at')
                    ->groupBy('vehicle_id');
            })
            ->pluck('mileage', 'vehicle_id');

        return view('fleet.vehicles.vehicle_maintenances', compact('vehicles', 'vehicleServices', 'maintenances', 'workshops', 'maxMileages'));
    }

    public function store(Request $request)
    {
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
                'date_format:d/m/Y',
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
    |
    | Esse valor pode ser usado na validação da quilometragem.
    | Como normalmente não existe uma coluna no banco com esse nome,
    | ele será removido antes de criar a manutenção.
    |
    */
        $allowLowerMileage = $request->boolean(
            'allow_lower_mileage'
        );

        /*
    |--------------------------------------------------------------------------
    | CONVERTE A DATA PARA O FORMATO DO BANCO
    |--------------------------------------------------------------------------
    |
    | Recebe: 23/05/2026
    | Salva:  2026-05-23
    |
    */
        $data['maintenance_date'] = \Carbon\Carbon::createFromFormat(
            'd/m/Y',
            $data['maintenance_date']
        )->format('Y-m-d');

        /*
    |--------------------------------------------------------------------------
    | REMOVE CAMPOS QUE NÃO PERTENCEM À TABELA
    |--------------------------------------------------------------------------
    */

        $vehicleServices = $data['vehicle_services'] ?? [];

        unset(
            $data['vehicle_services'],
            $data['allow_lower_mileage']
        );

        /*
    |--------------------------------------------------------------------------
    | CRIA A MANUTENÇÃO
    |--------------------------------------------------------------------------
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

                'allow_lower_mileage' => $allowLowerMileage,
            ])
            ->log('Manutenção de Veículo Criada');

        return redirect()
            ->back()
            ->with(
                'success',
                'Manutenção adicionada com sucesso!'
            );
    }

    public function update(Request $request, $id)
    {
        $maintenance = VehicleMaintenance::findOrFail($id);

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
                'date_format:d/m/Y',
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
    | CONVERTE A DATA PARA O FORMATO DO BANCO
    |--------------------------------------------------------------------------
    |
    | Recebe: 23/05/2026
    | Salva:  2026-05-23
    |
    */

        $data['maintenance_date'] = \Carbon\Carbon::createFromFormat(
            'd/m/Y',
            $data['maintenance_date']
        )->format('Y-m-d');

        /*
    |--------------------------------------------------------------------------
    | GUARDA OS SERVIÇOS E REMOVE CAMPOS EXTRAS
    |--------------------------------------------------------------------------
    */

        $vehicleServices = $data['vehicle_services'] ?? [];

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

        $maintenance->update($data);

        /*
    |--------------------------------------------------------------------------
    | SINCRONIZA OS SERVIÇOS
    |--------------------------------------------------------------------------
    */

        $maintenance
            ->services()
            ->sync($vehicleServices);

        /*
    |--------------------------------------------------------------------------
    | RECARREGA OS DADOS ATUALIZADOS
    |--------------------------------------------------------------------------
    */

        $maintenance->load('services');

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

                'new' => $maintenance->toArray(),

                'allow_lower_mileage' => $allowLowerMileage,
            ])
            ->log('Manutenção de Veículo Atualizada');

        return redirect()
            ->back()
            ->with(
                'success',
                'Manutenção atualizada com sucesso!'
            );
    }

    public function destroy($id)
    {
        $maintenance = VehicleMaintenance::findOrFail($id);

        $oldData = $maintenance->load('services')->toArray();

        $maintenance->delete();

        // 🔹 Log de exclusão
        activity()
            ->causedBy(auth()->user())
            ->performedOn($maintenance)
            ->withProperties([
                'old' => $oldData
            ])
            ->log('Manutenção de veículo Deletado');

        return redirect()->back()->with('success', 'Manutenção excluída com sucesso!');
    }

    public function byVehicle(Request $request, $vehicleId, SettingService $settingService)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $perPage = $settingService->getPerPage();

        // Cria a query (AINDA não executa)
        $query = $vehicle->maintenances()
            ->with('services')
            ->orderBy('maintenance_date', 'desc');

        // Aplica os filtros
        if ($request->filled('start_date')) {
            $query->whereDate('maintenance_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('maintenance_date', '<=', $request->end_date);
        }

        $totalCost = (clone $query)->sum('cost');

        // Agora sim: executa e pagina
        $maintenances = $query->paginate($perPage);

        return view('fleet.vehicles.by_vehicle', compact('vehicle', 'maintenances', 'totalCost'));
    }

    public function handlePdfReport(Request $request, SettingService $settingService)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $action = $request->input('action', 'view');

        if (!$startDate || !$endDate) {
            abort(400, 'Período inválido.');
        }

        // Obter todos os veículos ativos
        $vehicles = Vehicle::where('status', 'active')->get();

        // Obter todos os serviços de veículos ordenados por nome
        $vehicleServices = VehicleService::orderBy('name', 'asc')->get();

        // Obter todos os workshops
        $workshops = Workshop::all();

        // Obter as manutenções de veículos no intervalo de datas
        $maintenances = VehicleMaintenance::with(['vehicle', 'services'])
            ->whereBetween('maintenance_date', [$startDate, $endDate])
            ->orderBy('maintenance_date', 'desc')
            ->get();

        // Obter as maiores quilometragens por veículo no intervalo de datas
        $maxMileages = DB::table('vehicle_maintenances')
            ->select('vehicle_id', DB::raw('MAX(mileage) as max_mileage'))
            ->whereBetween('maintenance_date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->groupBy('vehicle_id')
            ->pluck('max_mileage', 'vehicle_id');

        // Obter as menores quilometragens por veículo no intervalo de datas
        $minMileages = DB::table('vehicle_maintenances')
            ->select('vehicle_id', DB::raw('MIN(mileage) as min_mileage'))
            ->whereBetween('maintenance_date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->groupBy('vehicle_id')
            ->pluck('min_mileage', 'vehicle_id');

        // Calcular a quilometragem rodada para cada veículo (máxima - mínima)
        $kmWheeled = [];
        foreach ($maxMileages as $vehicleId => $maxMileage) {
            $minMileage = $minMileages->get($vehicleId, 0); // Valor padrão de 0 caso não tenha mínimo
            $kmWheeled[$vehicleId] = $maxMileage - $minMileage;
        }
        // Passar todos os dados para a view
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

        // Gerar o PDF
        $pdf = PDF::loadView('fleet.vehicles.vehicle_maintenances_pdf', $data);

        // Se a ação for 'download', gerar o download do PDF, caso contrário, stream
        if ($action === 'download') {
            return $pdf->download("relatorio_manutencoes_{$startDate}_a_{$endDate}.pdf");
        }

        return $pdf->stream("relatorio_manutencoes_{$startDate}_a_{$endDate}.pdf");
    }
}
