@extends('layouts.header')
@section('title', 'Manutenções de Veículos')
@section('content')

    <div class="container mt-5">
        <h2 class="text-center">Manutenções de Veículos
            @can('fleets.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

        <table class="table table-striped mt-4">
            <thead class="bgc-primary">
                <tr>
                    <th></th>
                    <th>Veículo</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Quilometragem</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Oficina</th>
                    <th>Serviços</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        <td>
                            <div
                                style="width: 20px; height: 20px; background-color:{{ $maintenance->vehicle->color ?? '' }} ; border-radius: 4px;">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('fleet.vehicle.maintenances', $maintenance->vehicle->id) }}"
                                class="btn btn-sm">
                                {{ $maintenance->vehicle->model ?? '-' }} {{ $maintenance->vehicle->year ?? '-' }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}</td>
                        <td>{{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}</td>
                        <td>{{ number_format($maintenance->mileage, 0, ',', '.') }} km</td>
                        <td>R$ {{ number_format($maintenance->cost, 2, ',', '.') }}</td>
                        <td>{{ __('status.' . $maintenance->status) }}</td>
                        <td>{{ $maintenance->workshop }}</td>
                        <td>
                            @foreach ($maintenance->services as $service)
                                <span class="badge bg-secondary">{{ $service->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @can('fleets.edit')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">
                                    Editar
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $maintenances->links() }}
        </div>
        <br>
    </div>

    @include('fleet.form.vehicle_maintenances', [
        'vehicles' => $vehicles,
        'vehicleServices' => $vehicleServices,
        'maintenances' => $maintenances,
    ])
    <script>
        const maxMileages = @json($maxMileages);
    </script>

@endsection
