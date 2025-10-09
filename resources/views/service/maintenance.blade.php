@extends('layouts.header')
@section('title', 'Manutenções Gerais')

@section('content')
    <div class="container mt-4">
        <h2>Manutenções Gerais
            @can('service.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>


        <table class="table table-bordered mt-3">
            <thead class="bgc-primary">
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Serviço</th>
                    <th>Custo Empresa</th>
                    <th>Custo Cliente</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        <td>{{ $maintenance->serviceClient->name ?? '-' }}</td>
                        <td>{{ $maintenance->date_maintenance->format('d/m/Y') }}</td>
                        <td>{{ $maintenance->maintenance }}</td>
                        <td>R$ {{ number_format($maintenance->cost_enterprise, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($maintenance->cost_client, 2, ',', '.') }}</td>
                        <td>
                            @can('service.edit')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">Editar</button>
                            @endcan
                            @can('service.delete')
                                <form action="{{ route('service.maintenances.destroy', $maintenance->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Deseja excluir?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @include('service.forms.maintenance', [
                        'maintenance' => $maintenance,
                        'clients' => $clients,
                    ])
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $maintenances->links() }}
        </div>
    </div>

    @include('service.forms.maintenance', ['maintenance' => null, 'clients' => $clients])
@endsection
