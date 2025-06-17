@extends('layouts.header')
@section('title', 'Manutenção de Equipamentos')
@section('content')

<div class="container mt-5">
    <h2>Manutenção de Equipamentos
        @can('service.create')   
        <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
            <i class="bi bi-plus-lg"></i>
        </button>
     @endcan
    </h2>

    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Assistência Tecnica</th>
                <th>Equipamento</th>
                <th>Erro</th>
                <th>Data Envio</th>
                <th>Data Recebimento</th>
                <th>Data Manutenção</th>
                <th>Solução</th>
                <th>Custo Empresa</th>
                <th>Custo Cliente</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintenances as $maintenance)
            <tr>
                <td>{{ $maintenance->serviceClient->name ?? 'N/D' }}</td>
                <td>{{ $maintenance->assistance }}</td>
                <td>{{ $maintenance->equipment }}</td>
                <td>{{ $maintenance->erro }}</td>
                <td>{{ optional($maintenance->date_send)->format('d/m/Y') }}</td>
                <td>{{ optional($maintenance->date_received)->format('d/m/Y') }}</td>
                <td>{{ optional($maintenance->date_maintenance)->format('d/m/Y') }}</td>
                <td>{{ $maintenance->solution }}</td>
                <td>R$ {{ number_format($maintenance->cost_enterprise, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($maintenance->cost_client, 2, ',', '.') }}</td>
                <td>
                    @can('service.edit') 
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                        data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">Editar</button>
                   @endcan

                    @can('service.delete')
                    <form action="{{ route('service.equipment_maintenances.destroy', $maintenance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir manutenção?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                    @endcan
                </td>
            </tr>

            {{-- Modal Editar --}}
            @include('service.forms.equipment_maintenance', ['maintenance' => $maintenance])
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $maintenances->links() }}
    </div>

    {{-- Modal Adicionar --}}
    @include('service.forms.equipment_maintenance', ['maintenance' => null])
</div>

@endsection
