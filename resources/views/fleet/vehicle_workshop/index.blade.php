@extends('layouts.header')
@section('title', 'Oficinas-mecanicas')
@section('content')

    <div class="container mt-5">
        <h2>Oficinas e Mecanicas
            @can('fleets.create')
            <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#createServiceModal">
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
                    <th>Nome</th>
                    <th>Informações</th>
                    <th>Tipo de Veículo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->info }}</td>
                        <td>{{ ucfirst(__('vehicle_types.' . $service->vehicle_type)) }}</td>
                        <td>
                            @can('fleets.edit')
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editServiceModal{{ $service->id }}"
                                data-service-name="{{ $service->name }}" data-service-info="{{ $service->info }}"
                                data-service-type="{{ $service->vehicle_type }}">
                                Editar
                            </button>
                            @endcan
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1"
                        aria-labelledby="editServiceModalLabel{{ $service->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('fleet.vehicle_workshop.update', $service->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editServiceModalLabel{{ $service->id }}">Editar
                                            Serviço</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name{{ $service->id }}" class="form-label">Nome do Serviço</label>
                                            <input type="text" name="name" id="name{{ $service->id }}"
                                                class="form-control" value="{{ $service->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="info{{ $service->id }}" class="form-label">Informações</label>
                                            <input type="text" name="info" id="info{{ $service->id }}"
                                                class="form-control" value="{{ $service->info }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="vehicle_type{{ $service->id }}" class="form-label">Tipo de
                                                Veículo</label>
                                            <select name="vehicle_type" id="vehicle_type{{ $service->id }}"
                                                class="form-control" required>
                                                @php
                                                    $types = [
                                                        'motorcycle' => 'Moto',
                                                        'car' => 'Carro',
                                                        'truck' => 'Caminhão',
                                                        'others' => 'Outros',
                                                        'all' => 'Todos',
                                                    ];
                                                @endphp
                                                <option value="">Selecione</option>
                                                @foreach ($types as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ $service->vehicle_type === $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn dcm-btn-primary">Atualizar</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                            </form>
                            @can('fleets.delete')
                            <form action="{{ route('fleet.vehicle_workshop.destroy', $service->id) }}" method="POST"
                                style="display:inline-block;" onsubmit="return confirm('Excluir esta oficina?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                            @endcan
                        </div>
                    </div>
    </div>
    </div>
    @endforeach
    </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
    </div>
    <br>
    </div>

    <!-- Modal Criar -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('fleet.vehicle_workshop.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createServiceModalLabel">Novo Serviço</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da oficia</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="info" class="form-label">Informações</label>
                            <input type="text" name="info" id="info" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Tipo de Veículo</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="car">Carro</option>
                                <option value="motorcycle">Moto</option>
                                <option value="truck">Caminhão</option>
                                <option value="others">Outros</option>
                                <option value="all">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
