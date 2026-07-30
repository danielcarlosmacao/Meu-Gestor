@extends('layouts.header')

@section('title', 'Serviços de Veículos')

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
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>

                        <h2 class="fleet-page-title">
                            Serviços de Veículos
                        </h2>

                        <p class="fleet-page-subtitle">
                            Gerencie os serviços disponíveis para as manutenções da frota.
                        </p>

                    </div>

                </div>

                <div class="fleet-page-actions">

                    <div class="fleet-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar serviço..."
                            data-fleet-table-search="#fleetServicesTable">

                    </div>

                    @can('fleets.create')
                        <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createServiceModal">

                            <i class="bi bi-plus-lg"></i>
                            Novo serviço
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
                            Serviços cadastrados
                        </h5>

                        <p class="fleet-card-subtitle">
                            Serviços que podem ser vinculados às manutenções dos veículos.
                        </p>

                    </div>

                    <span class="badge text-bg-light border">

                        {{ $services->total() }}

                        {{ $services->total() === 1 ? 'serviço' : 'serviços' }}

                    </span>

                </div>

                <div class="fleet-card-body-flush">

                    @if ($services->count())

                        <div class="fleet-table-responsive">

                            <table class="table fleet-table" id="fleetServicesTable" data-fleet-sortable>

                                <thead>

                                    <tr>

                                        <th data-fleet-sort="text">
                                            Serviço
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

                                            {{-- Nome do serviço --}}

                                            <td>

                                                <div class="d-flex align-items-center gap-3">

                                                    <span class="fleet-summary-icon">
                                                        <i class="bi bi-wrench-adjustable"></i>
                                                    </span>

                                                    <div>

                                                        <strong class="d-block">
                                                            {{ $service->name }}
                                                        </strong>

                                                        <small class="text-secondary">
                                                            Serviço de manutenção
                                                        </small>

                                                    </div>

                                                </div>

                                            </td>

                                            {{-- Tipo de veículo --}}

                                            <td data-sort-value="{{ $service->vehicle_type }}">

                                                @switch(strtolower($service->vehicle_type))
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
                                                            data-bs-target="#editServiceModal{{ $service->id }}"
                                                            title="Editar serviço">

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
                                    <i class="bi bi-tools"></i>
                                </span>

                                <h5 class="fw-bold">
                                    Nenhum serviço cadastrado
                                </h5>

                                <p class="mb-0">
                                    Cadastre os serviços utilizados nas manutenções dos veículos.
                                </p>

                                @can('fleets.create')
                                    <button type="button" class="btn dcm-btn-primary mt-3" data-bs-toggle="modal"
                                        data-bs-target="#createServiceModal">

                                        <i class="bi bi-plus-lg"></i>
                                        Cadastrar serviço
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
    MODAL DE CADASTRO
================================================================= --}}

    <div class="modal fade fleet-modal" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel"
        aria-hidden="true" data-open-on-error="{{ $errors->any() ? 'true' : 'false' }}">

        <div class="modal-dialog modal-dialog-centered modal-md">

            <div class="modal-content">

                <div class="modal-header">

                    <div class="fleet-modal-heading">

                        <span class="fleet-modal-icon">
                            <i class="bi bi-tools"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="createServiceModalLabel">

                                Novo serviço
                            </h5>

                            <p class="fleet-modal-subtitle">
                                Cadastre um novo serviço de manutenção.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form action="{{ route('fleet.vehicle_services.store') }}" method="POST"
                    class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())

                            <div class="fleet-alert fleet-alert-danger mb-3">

                                <i class="bi bi-exclamation-triangle-fill"></i>

                                <div>

                                    <strong>
                                        Não foi possível cadastrar o serviço.
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

                            <label for="create_service_name" class="form-label">

                                Nome do serviço

                                <span class="fleet-required">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-wrench-adjustable"></i>
                                </span>

                                <input type="text" name="name" id="create_service_name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    maxlength="255" placeholder="Ex.: Troca de óleo" data-autofocus required>

                                <div class="invalid-feedback">

                                    @error('name')
                                        {{ $message }}
                                    @else
                                        Informe o nome do serviço.
                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="mb-0">

                            <label for="create_service_vehicle_type" class="form-label">

                                Tipo de veículo

                                <span class="fleet-required">*</span>
                            </label>

                            <select name="vehicle_type" id="create_service_vehicle_type"
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

                            <div class="fleet-form-help">
                                Escolha “Todos” quando o serviço puder ser utilizado em qualquer veículo.
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
                                Salvar serviço
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    {{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================= --}}

    @foreach ($services as $service)
        <div class="modal fade fleet-modal" id="editServiceModal{{ $service->id }}" tabindex="-1"
            aria-labelledby="editServiceModalLabel{{ $service->id }}" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-md">

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="fleet-modal-heading">

                            <span class="fleet-modal-icon warning">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editServiceModalLabel{{ $service->id }}">

                                    Editar serviço
                                </h5>

                                <p class="fleet-modal-subtitle">
                                    Atualize os dados do serviço de manutenção.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <form action="{{ route('fleet.vehicle_services.update', $service->id) }}" method="POST"
                        class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <div class="mb-3">

                                <label for="edit_service_name_{{ $service->id }}" class="form-label">

                                    Nome do serviço

                                    <span class="fleet-required">*</span>
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-wrench-adjustable"></i>
                                    </span>

                                    <input type="text" name="name" id="edit_service_name_{{ $service->id }}"
                                        class="form-control" value="{{ $service->name }}" maxlength="255" required>

                                    <div class="invalid-feedback">
                                        Informe o nome do serviço.
                                    </div>

                                </div>

                            </div>

                            <div class="mb-0">

                                <label for="edit_service_vehicle_type_{{ $service->id }}" class="form-label">

                                    Tipo de veículo

                                    <span class="fleet-required">*</span>
                                </label>

                                <select name="vehicle_type" id="edit_service_vehicle_type_{{ $service->id }}"
                                    class="form-select" required>

                                    <option value="">
                                        Selecione
                                    </option>

                                    @foreach ($vehicleTypes as $key => $label)
                                        <option value="{{ $key }}" @selected(strtolower($service->vehicle_type) === $key)>

                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>

                                <div class="invalid-feedback">
                                    Selecione o tipo de veículo.
                                </div>

                                <div class="fleet-form-help">
                                    Escolha “Todos” quando o serviço puder ser usado em qualquer veículo.
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer d-flex justify-content-between">

                            <div>

                                @can('fleets.delete')
                                    <button type="submit" class="btn btn-danger"
                                        form="deleteServiceForm{{ $service->id }}">

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
                        <form id="deleteServiceForm{{ $service->id }}"
                            action="{{ route('fleet.vehicle_services.destroy', $service->id) }}" method="POST"
                            class="d-none fleet-delete-form" data-confirm-delete
                            data-confirm-message="Tem certeza que deseja excluir o serviço {{ $service->name }}?">

                            @csrf
                            @method('DELETE')

                        </form>
                    @endcan

                </div>

            </div>

        </div>
    @endforeach

    <script src="{{ asset('js/fleet-module.js') }}"></script>

@endsection
