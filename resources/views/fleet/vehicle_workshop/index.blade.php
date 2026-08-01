@extends('layouts.header')

@section('title', 'Oficinas e Mecânicas')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/fleet-module.css') }}">

    @php
        $vehicleTypes = [
            'motorcycle' => 'Moto',
            'car' => 'Carro',
            'truck' => 'Caminhão',
            'others' => 'Outros',
            'all' => 'Todos',
        ];
    @endphp

    <div class="fleet-page">

        <div class="fleet-container">

            {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

            <div class="fleet-page-header">

                <div class="fleet-page-heading">

                    <span class="fleet-page-icon">
                        <i class="bi bi-building-gear"></i>
                    </span>

                    <div>

                        <h2 class="fleet-page-title">
                            Oficinas e Mecânicas
                        </h2>

                        <p class="fleet-page-subtitle">
                            Gerencie as oficinas disponíveis para manutenção da frota.
                        </p>

                    </div>

                </div>

                <div class="fleet-page-actions">

                    <div class="fleet-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar oficina..."
                            data-fleet-table-search="#fleetWorkshopsTable">

                    </div>

                    @can('fleets.create')
                        <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createWorkshopModal">

                            <i class="bi bi-plus-lg"></i>
                            Nova oficina
                        </button>
                    @endcan

                </div>

            </div>

            {{-- ============================================================
            LISTAGEM
        ============================================================= --}}

            <div class="fleet-card">

                <div class="fleet-card-header">

                    <div>

                        <h5 class="fleet-card-title">
                            Oficinas cadastradas
                        </h5>

                        <p class="fleet-card-subtitle">
                            Oficinas e mecânicas disponíveis por tipo de veículo.
                        </p>

                    </div>

                    <span class="badge text-bg-light border">

                        {{ $services->total() }}

                        {{ $services->total() === 1 ? 'oficina' : 'oficinas' }}

                    </span>

                </div>

                <div class="fleet-card-body-flush">

                    @if ($services->count())

                        <div class="fleet-table-responsive">

                            <table class="table fleet-table" id="fleetWorkshopsTable" data-fleet-sortable>

                                <thead>

                                    <tr>

                                        <th data-fleet-sort="text">
                                            Oficina
                                        </th>

                                        <th data-fleet-sort="text">
                                            Informações
                                        </th>

                                        <th data-fleet-sort="text">
                                            Tipo de veículo
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($services as $service)
                                        <tr>

                                            {{-- Nome --}}

                                            <td>

                                                <div class="d-flex align-items-center gap-2">

                                                    <span class="fleet-summary-icon">
                                                        <i class="bi bi-wrench-adjustable"></i>
                                                    </span>

                                                    <div>

                                                        <strong class="d-block">
                                                            {{ $service->name }}
                                                        </strong>

                                                        <small class="text-secondary">
                                                            Oficina mecânica
                                                        </small>

                                                    </div>

                                                </div>

                                            </td>

                                            {{-- Informações --}}

                                            <td>

                                                @if ($service->info)
                                                    <span title="{{ $service->info }}">
                                                        {{ \Illuminate\Support\Str::limit($service->info, 90) }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Tipo de veículo --}}

                                            <td>

                                                @switch($service->vehicle_type)
                                                    @case('car')
                                                        <span class="fleet-badge fleet-badge-info">
                                                            <i class="bi bi-car-front"></i>
                                                            Carro
                                                        </span>
                                                    @break

                                                    @case('motorcycle')
                                                        <span class="fleet-badge fleet-badge-warning">
                                                            <i class="bi bi-bicycle"></i>
                                                            Moto
                                                        </span>
                                                    @break

                                                    @case('truck')
                                                        <span class="fleet-badge fleet-badge-secondary">
                                                            <i class="bi bi-truck"></i>
                                                            Caminhão
                                                        </span>
                                                    @break

                                                    @case('all')
                                                        <span class="fleet-badge fleet-badge-success">
                                                            <i class="bi bi-check2-all"></i>
                                                            Todos
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="fleet-badge fleet-badge-secondary">
                                                            <i class="bi bi-three-dots"></i>
                                                            Outros
                                                        </span>
                                                @endswitch

                                            </td>

                                            {{-- Ações --}}

                                            <td>

                                                <div class="fleet-actions">

                                                    @can('fleets.edit')
                                                        <button type="button" class="btn btn-warning btn-sm fleet-btn-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editWorkshopModal{{ $service->id }}"
                                                            title="Editar oficina">

                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    @endcan

                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>
                    @else
                        <div class="fleet-card-body">

                            <div class="fleet-empty-state">

                                <span class="fleet-empty-icon">
                                    <i class="bi bi-building-gear"></i>
                                </span>

                                <h5 class="fw-bold">
                                    Nenhuma oficina cadastrada
                                </h5>

                                <p class="mb-0">
                                    Cadastre oficinas para vinculá-las às manutenções dos veículos.
                                </p>

                                @can('fleets.create')
                                    <button type="button" class="btn dcm-btn-primary mt-3" data-bs-toggle="modal"
                                        data-bs-target="#createWorkshopModal">

                                        <i class="bi bi-plus-lg"></i>
                                        Cadastrar oficina
                                    </button>
                                @endcan

                            </div>

                        </div>

                    @endif

                </div>

            </div>

            {{-- ============================================================
            PAGINAÇÃO
        ============================================================= --}}

            @if ($services->hasPages())
                <div class="fleet-pagination">
                    {{ $services->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================= --}}

    @foreach ($services as $service)
        <div class="modal fade fleet-modal" id="editWorkshopModal{{ $service->id }}" tabindex="-1"
            aria-labelledby="editWorkshopModalLabel{{ $service->id }}" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-md">

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="fleet-modal-heading">

                            <span class="fleet-modal-icon warning">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editWorkshopModalLabel{{ $service->id }}">

                                    Editar oficina
                                </h5>

                                <p class="fleet-modal-subtitle">
                                    Atualize as informações da oficina.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <form action="{{ route('fleet.vehicle_workshop.update', $service->id) }}" method="POST"
                        class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <div class="mb-3">

                                <label for="edit_workshop_name_{{ $service->id }}" class="form-label">

                                    Nome da oficina

                                    <span class="fleet-required">*</span>
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-building"></i>
                                    </span>

                                    <input type="text" name="name" id="edit_workshop_name_{{ $service->id }}"
                                        class="form-control" value="{{ old('name', $service->name) }}" maxlength="255"
                                        required>

                                    <div class="invalid-feedback">
                                        Informe o nome da oficina.
                                    </div>

                                </div>

                            </div>

                            <div class="mb-3">

                                <label for="edit_workshop_info_{{ $service->id }}" class="form-label">

                                    Informações

                                    <span class="fleet-required">*</span>
                                </label>

                                <textarea name="info" id="edit_workshop_info_{{ $service->id }}" class="form-control" rows="3"
                                    maxlength="1000" required>{{ old('info', $service->info) }}</textarea>

                                <div class="invalid-feedback">
                                    Informe os dados da oficina.
                                </div>

                                <div class="fleet-form-help">
                                    Telefone, endereço, contato ou outras observações.
                                </div>

                            </div>

                            <div class="mb-0">

                                <label for="edit_workshop_vehicle_type_{{ $service->id }}" class="form-label">

                                    Tipo de veículo

                                    <span class="fleet-required">*</span>
                                </label>

                                <select name="vehicle_type" id="edit_workshop_vehicle_type_{{ $service->id }}"
                                    class="form-select" required>

                                    <option value="">
                                        Selecione
                                    </option>

                                    @foreach ($vehicleTypes as $key => $label)
                                        <option value="{{ $key }}" @selected(old('vehicle_type', $service->vehicle_type) === $key)>

                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>

                                <div class="invalid-feedback">
                                    Selecione o tipo de veículo.
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer d-flex justify-content-between">

                            <div>

                                @can('fleets.delete')
                                    <button type="submit" class="btn btn-danger"
                                        form="deleteWorkshopForm{{ $service->id }}">

                                        <i class="bi bi-trash"></i>
                                        Excluir
                                    </button>
                                @endcan

                            </div>

                            <div class="d-flex gap-2">

                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                                    Cancelar
                                </button>

                                <button type="submit" class="btn dcm-btn-primary" data-fleet-submit
                                    data-loading-text="Atualizando...">

                                    <span class="spinner-border spinner-border-sm d-none" data-submit-spinner
                                        aria-hidden="true">
                                    </span>

                                    <i class="bi bi-save" data-submit-icon>
                                    </i>

                                    <span data-submit-text>
                                        Atualizar
                                    </span>

                                </button>

                            </div>

                        </div>

                    </form>

                    @can('fleets.delete')
                        <form id="deleteWorkshopForm{{ $service->id }}"
                            action="{{ route('fleet.vehicle_workshop.destroy', $service->id) }}" method="POST"
                            class="d-none fleet-delete-form" data-confirm-delete
                            data-confirm-message="Tem certeza que deseja excluir a oficina {{ $service->name }}?">

                            @csrf
                            @method('DELETE')

                        </form>
                    @endcan

                </div>

            </div>

        </div>
    @endforeach

    {{-- ================================================================
    MODAL DE CADASTRO
================================================================= --}}

    <div class="modal fade fleet-modal" id="createWorkshopModal" tabindex="-1"
        aria-labelledby="createWorkshopModalLabel" aria-hidden="true"
        data-open-on-error="{{ $errors->any() ? 'true' : 'false' }}">

        <div class="modal-dialog modal-dialog-centered modal-md">

            <div class="modal-content">

                <div class="modal-header">

                    <div class="fleet-modal-heading">

                        <span class="fleet-modal-icon">
                            <i class="bi bi-building-add"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="createWorkshopModalLabel">

                                Nova oficina
                            </h5>

                            <p class="fleet-modal-subtitle">
                                Cadastre uma nova oficina ou mecânica.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form action="{{ route('fleet.vehicle_workshop.store') }}" method="POST"
                    class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())

                            <div class="fleet-alert fleet-alert-danger mb-3">

                                <i class="bi bi-exclamation-triangle-fill"></i>

                                <div>

                                    <strong>
                                        Não foi possível cadastrar a oficina.
                                    </strong>

                                    <ul class="mb-0 mt-2 ps-3">

                                        @foreach ($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach

                                    </ul>

                                </div>

                            </div>

                        @endif

                        <div class="mb-3">

                            <label for="create_workshop_name" class="form-label">

                                Nome da oficina

                                <span class="fleet-required">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-building"></i>
                                </span>

                                <input type="text" name="name" id="create_workshop_name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    maxlength="255" data-autofocus required>

                                <div class="invalid-feedback">
                                    @error('name')
                                        {{ $message }}
                                    @else
                                        Informe o nome da oficina.
                                    @enderror
                                </div>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="create_workshop_info" class="form-label">

                                Informações

                                <span class="fleet-required">*</span>
                            </label>

                            <textarea name="info" id="create_workshop_info" class="form-control @error('info') is-invalid @enderror"
                                rows="3" maxlength="1000" required>{{ old('info') }}</textarea>

                            <div class="invalid-feedback">
                                @error('info')
                                    {{ $message }}
                                @else
                                    Informe os dados da oficina.
                                @enderror
                            </div>

                            <div class="fleet-form-help">
                                Telefone, endereço, contato ou outras observações.
                            </div>

                        </div>

                        <div class="mb-0">

                            <label for="create_workshop_vehicle_type" class="form-label">

                                Tipo de veículo

                                <span class="fleet-required">*</span>
                            </label>

                            <select name="vehicle_type" id="create_workshop_vehicle_type"
                                class="form-select @error('vehicle_type') is-invalid @enderror" required>

                                <option value="">
                                    Selecione
                                </option>

                                @foreach ($vehicleTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('vehicle_type') === $key)>

                                        {{ $label }}
                                    </option>
                                @endforeach

                            </select>

                            <div class="invalid-feedback">
                                @error('vehicle_type')
                                    {{ $message }}
                                @else
                                    Selecione o tipo de veículo.
                                @enderror
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-fleet-submit
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                            </span>

                            <i class="bi bi-save" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar oficina
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script src="{{ asset('js/fleet-module.js') }}"></script>

@endsection
