@extends('layouts.header')
@section('title', 'Oficinas-mecanicas')
@section('content')

<div class="container mt-5">
    <h2 class="text-center">
        Oficinas e Mecanicas
        @can('fleets.create')
            <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                <i class="bi bi-plus-lg"></i>
            </button>
        @endcan
    </h2>


    <table class="table table-striped mt-3">
        <thead  class="bgc-primary">
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
                                data-bs-target="#editServiceModal{{ $service->id }}">
                                Editar
                            </button>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
    </div>
</div>

{{-- Modais de edição fora da tabela para evitar problemas de HTML --}}
@foreach ($services as $service)
<div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1" aria-labelledby="editServiceModalLabel{{ $service->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bgc-primary text-white rounded-top-4 border-0">
                <h5 class="modal-title fw-bold" id="editServiceModalLabel{{ $service->id }}">Editar Serviço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="{{ route('fleet.vehicle_workshop.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name{{ $service->id }}" class="form-label fw-semibold">Nome do Serviço</label>
                        <input type="text" name="name" id="name{{ $service->id }}" class="form-control rounded-pill" value="{{ $service->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="info{{ $service->id }}" class="form-label fw-semibold">Informações</label>
                        <input type="text" name="info" id="info{{ $service->id }}" class="form-control rounded-pill" value="{{ $service->info }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_type{{ $service->id }}" class="form-label fw-semibold">Tipo de Veículo</label>
                        <select name="vehicle_type" id="vehicle_type{{ $service->id }}" class="form-select rounded-pill" required>
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
                                <option value="{{ $key }}" {{ $service->vehicle_type === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-between">
                    <button type="submit" class="btn dcm-btn-primary rounded-pill">
                        <i class="bi bi-save"></i> Atualizar
                    </button>
            </form>

            @can('fleets.delete')
                <form action="{{ route('fleet.vehicle_workshop.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Excluir esta oficina?');" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            @endcan

                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                </div>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Criar --}}
<div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bgc-primary text-white rounded-top-4 border-0">
                <h5 class="modal-title fw-bold" id="createServiceModalLabel">Nova Oficina</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form action="{{ route('fleet.vehicle_workshop.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nome da Oficina</label>
                        <input type="text" name="name" id="name" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label for="info" class="form-label fw-semibold">Informações</label>
                        <input type="text" name="info" id="info" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label fw-semibold">Tipo de Veículo</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-select rounded-pill" required>
                            <option value="">Selecione</option>
                            <option value="car">Carro</option>
                            <option value="motorcycle">Moto</option>
                            <option value="truck">Caminhão</option>
                            <option value="others">Outros</option>
                            <option value="all">Todos</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn dcm-btn-primary rounded-pill">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
