<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use App\Models\VehicleService;
use App\Models\Workshop;
use App\Services\SettingService;
use App\Models\VehicleMaintenanceFile;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


        return $pdf->stream(
            "relatorio_manutencoes_{$startDate}_a_{$endDate}.pdf"
        );
    }


    public function getFiles()
    {
        $files = VehicleMaintenanceFile::with([
            'maintenance.vehicle',
        ])
            ->latest()
            ->paginate(30);

        return view(
            'fleet.vehicles.vehicle_maintenance_files',
            compact('files')
        );
    }


    public function uploadFiles(Request $request)
    {
        $data = $request->validate([
            'vehicle_maintenance_id' => [
                'required',
                'exists:vehicle_maintenances,id',
            ],

            'files' => [
                'required',
                'array',
                'min:1',
            ],

            'files.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:20480',
            ],
        ], [
            'vehicle_maintenance_id.required' =>
            'Selecione uma manutenção.',

            'vehicle_maintenance_id.exists' =>
            'A manutenção selecionada não existe.',

            'files.required' =>
            'Selecione pelo menos um arquivo.',

            'files.array' =>
            'Os arquivos enviados são inválidos.',

            'files.min' =>
            'Selecione pelo menos um arquivo.',

            'files.*.file' =>
            'Um dos arquivos enviados é inválido.',

            'files.*.mimes' =>
            'Os arquivos devem ser PDF, JPG, JPEG, PNG ou WEBP.',

            'files.*.max' =>
            'Cada arquivo pode ter no máximo 20 MB.',
        ]);

        $maintenance = VehicleMaintenance::findOrFail(
            $data['vehicle_maintenance_id']
        );

        foreach ($request->file('files', []) as $file) {

            $originalName = $file->getClientOriginalName();

            $extension = strtolower(
                $file->getClientOriginalExtension()
            );

            $fileName = Str::uuid()
                . '.' . $extension;

            $path = $file->storeAs(
                'vehicle-maintenance-files',
                $fileName,
                'public'
            );

            VehicleMaintenanceFile::create([
                'vehicle_maintenance_id' => $maintenance->id,
                'original_name' => $originalName,
                'file_name' => $fileName,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Arquivo(s) enviado(s) com sucesso!'
            );
    }


    public function viewFile(string $token)
    {
        $file = VehicleMaintenanceFile::with([
            'maintenance.vehicle',
        ])
            ->where('token', $token)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($file->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return view(
            'fleet.vehicles.vehicle_maintenance_file_view',
            compact('file')
        );
    }

    public function deleteFile(string $token)
    {
        $file = VehicleMaintenanceFile::where('token', $token)
            ->firstOrFail();

        if (
            $file->path &&
            Storage::disk('public')->exists($file->path)
        ) {
            Storage::disk('public')->delete($file->path);
        }

        $file->delete();

        return response()->view(
            'fleet.vehicles.vehicle_maintenance_file_deleted'
        );
    }
}
