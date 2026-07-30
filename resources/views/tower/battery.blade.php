@extends('layouts.header')

@section('title', 'Baterias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tower-resource-index.css') }}">
@endpush

@section('content')

    <div class="resource-page">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <header class="resource-page-header">

            <div>

                <div class="resource-page-eyebrow">
                    <i class="bi bi-battery-charging"></i>
                    Equipamentos da rede
                </div>

                <h1 class="resource-page-title">
                    Baterias
                </h1>

                <p class="resource-page-description">
                    Gerencie os modelos de baterias utilizados nas torres
                    e acompanhe seus relatórios de produção.
                </p>

            </div>

            <div class="resource-header-actions">

                @can('towers.create')
                    <button type="button" class="btn dcm-btn-primary resource-primary-button" data-bs-toggle="modal"
                        data-bs-target="#addBatteryModal">

                        <i class="bi bi-plus-lg"></i>
                        Nova bateria
                    </button>
                @endcan

            </div>

        </header>

        {{-- ============================================================
            CARDS DE RESUMO
        ============================================================= --}}

        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-battery-charging"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Baterias cadastradas
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $batterys->total() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-lightning-charge"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Voltagens nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $batterys->pluck('voltage')->filter()->unique()->count() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-tags"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Marcas nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $batterys->pluck('mark')->filter()->unique()->count() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ============================================================
            TABELA
        ============================================================= --}}

        <section class="resource-table-card">

            <div class="resource-table-header">

                <div>

                    <h2 class="resource-table-title">
                        Baterias cadastradas
                    </h2>

                    <p class="resource-table-subtitle">
                        Clique no nome para acessar o relatório de produção.
                    </p>

                </div>

                <div class="resource-table-tools">

                    <div class="resource-search">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" data-resource-search="#batteryTable"
                            placeholder="Pesquisar bateria..." autocomplete="off">

                    </div>

                </div>

            </div>

            <div class="resource-table-responsive">

                <table id="batteryTable" class="table resource-table align-middle">

                    <thead>

                        <tr>

                            <th class="resource-sortable ps-4" data-sortable="true">

                                <span>
                                    Nome
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Marca
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Tipo
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Voltagem
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Capacidade
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="text-center pe-4">
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($batterys as $battery)
                            <tr data-resource-row
                                data-search="{{ mb_strtolower(
                                    implode(' ', [$battery->name, $battery->mark, $battery->type, $battery->voltage, $battery->amps]),
                                ) }}">

                                {{-- NOME --}}
                                <td class="ps-4">

                                    <a href="{{ route('batteryproduction.report', $battery->id) }}" class="resource-name">

                                        <span class="resource-name-icon">
                                            <i class="bi bi-battery-charging"></i>
                                        </span>

                                        <span>

                                            <strong>
                                                {{ $battery->name }}
                                            </strong>

                                            <small>
                                                Ver relatório de produção
                                            </small>

                                        </span>

                                    </a>

                                </td>

                                {{-- MARCA --}}
                                <td>

                                    @if ($battery->mark)
                                        {{ $battery->mark }}
                                    @else
                                        <span class="resource-muted">
                                            Não informada
                                        </span>
                                    @endif

                                </td>

                                {{-- TIPO --}}
                                <td>

                                    @if ($battery->type)
                                        <span class="resource-value-badge">
                                            {{ __('baterry.' . $battery->type) }}
                                        </span>
                                    @else
                                        <span class="resource-muted">
                                            Não informado
                                        </span>
                                    @endif

                                </td>

                                {{-- VOLTAGEM --}}
                                <td data-value="{{ $battery->voltage }}">

                                    <span class="resource-value-badge">
                                        {{ $battery->voltage }} V
                                    </span>

                                </td>

                                {{-- AMPERES --}}
                                <td data-value="{{ $battery->amps }}">

                                    <span class="resource-value-badge">
                                        {{ number_format((float) $battery->amps, 2, ',', '.') }}
                                        Ah
                                    </span>

                                </td>

                                {{-- AÇÕES --}}
                                <td class="text-center pe-4">

                                    <div class="btn-group">

                                        <button type="button"
                                            class="btn btn-sm dcm-btn-primary
                                                dropdown-toggle resource-actions-button"
                                            data-bs-toggle="dropdown" aria-expanded="false">

                                            <i class="bi bi-gear"></i>
                                            Ações
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>

                                                <a href="{{ route('batteryproduction.report', $battery->id) }}"
                                                    class="dropdown-item">

                                                    <i class="bi bi-bar-chart"></i>
                                                    Relatório
                                                </a>

                                            </li>

                                            @can('towers.edit')
                                                <li>

                                                    <button type="button" class="dropdown-item"
                                                        data-resource-edit="#editBatteryModal"
                                                        data-update-url="{{ route('battery.update', $battery->id) }}"
                                                        data-name="{{ $battery->name }}" data-mark="{{ $battery->mark }}"
                                                        data-type="{{ $battery->type }}"
                                                        data-voltage="{{ $battery->voltage }}"
                                                        data-amps="{{ $battery->amps }}" data-bs-toggle="modal"
                                                        data-bs-target="#editBatteryModal">

                                                        <i class="bi bi-pencil-square"></i>
                                                        Editar
                                                    </button>

                                                </li>
                                            @endcan

                                            @can('towers.delete')
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                <li>

                                                    <button type="button" class="dropdown-item resource-delete-item"
                                                        data-resource-delete="{{ route('battery.destroy', $battery->id) }}"
                                                        data-delete-title="Deseja excluir esta bateria?"
                                                        data-delete-description="Essa alteração não poderá ser desfeita.">

                                                        <i class="bi bi-trash"></i>
                                                        Excluir
                                                    </button>

                                                </li>
                                            @endcan

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="resource-empty-state">

                                    <span class="resource-empty-icon">
                                        <i class="bi bi-battery"></i>
                                    </span>

                                    <h3>
                                        Nenhuma bateria cadastrada
                                    </h3>

                                    <p>
                                        Cadastre a primeira bateria para começar.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                        {{-- SEM RESULTADO NA PESQUISA --}}
                        <tr class="d-none" data-search-empty>

                            <td colspan="6" class="resource-empty-state">

                                <span class="resource-empty-icon">
                                    <i class="bi bi-search"></i>
                                </span>

                                <h3>
                                    Nenhuma bateria encontrada
                                </h3>

                                <p>
                                    Tente pesquisar usando outro nome, marca ou tipo.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            {{-- PAGINAÇÃO --}}
            @if ($batterys->hasPages())
                <div class="resource-pagination">
                    {{ $batterys->withQueryString()->links() }}
                </div>
            @endif

        </section>

    </div>

    {{-- ================================================================
        MODAL ADICIONAR
    ================================================================= --}}

    @can('towers.create')

        <div class="modal fade resource-modal" id="addBatteryModal" tabindex="-1" aria-labelledby="addBatteryModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <form method="POST" action="{{ route('battery.store') }}" class="modal-content js-resource-form"
                    novalidate>

                    @csrf

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-battery-charging"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="addBatteryModalLabel">

                                    Nova bateria
                                </h5>

                                <p class="resource-modal-description">
                                    Informe os dados do modelo da bateria.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="battery_name" class="form-label">

                                Nome
                            </label>

                            <input type="text"
                                class="form-control
                                    @error('name') is-invalid @enderror"
                                id="battery_name" name="name" value="{{ old('name') }}" maxlength="150" required
                                autofocus>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe o nome da bateria.
                                </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label for="battery_mark" class="form-label">

                                Marca
                            </label>

                            <input type="text"
                                class="form-control
                                    @error('mark') is-invalid @enderror"
                                id="battery_mark" name="mark" value="{{ old('mark') }}" maxlength="150" required>

                            @error('mark')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe a marca da bateria.
                                </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label for="battery_type" class="form-label">

                                Tipo
                            </label>

                            <select class="form-select
                                    @error('type') is-invalid @enderror"
                                id="battery_type" name="type" required>

                                <option value="">
                                    Selecione
                                </option>

                                <option value="Automotive" @selected(old('type') === 'Automotive')>

                                    Automotiva
                                </option>

                                <option value="stationary" @selected(old('type') === 'stationary')>

                                    Estacionária
                                </option>

                                <option value="LiFePO4" @selected(old('type') === 'LiFePO4')>

                                    LiFePO4
                                </option>

                                <option value="others" @selected(old('type') === 'others')>

                                    Outro
                                </option>

                            </select>

                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Selecione o tipo da bateria.
                                </div>
                            @enderror

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="battery_voltage" class="form-label">

                                    Voltagem
                                </label>

                                <div class="input-group">

                                    <input type="number"
                                        class="form-control
                                            @error('voltage') is-invalid @enderror"
                                        id="battery_voltage" name="voltage" value="{{ old('voltage') }}" min="12"
                                        max="1000" step="12" required>

                                    <span class="input-group-text">
                                        V
                                    </span>

                                    @error('voltage')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                @if (!$errors->has('voltage'))
                                    <div class="invalid-feedback">
                                        Informe a voltagem da bateria.
                                    </div>
                                @endif

                            </div>

                            <div class="col-md-6">

                                <label for="battery_amps" class="form-label">

                                    Capacidade
                                </label>

                                <div class="input-group">

                                    <input type="number"
                                        class="form-control
                                            @error('amps') is-invalid @enderror"
                                        id="battery_amps" name="amps" value="{{ old('amps') }}" min="0"
                                        max="10000" step="0.01" required>

                                    <span class="input-group-text">
                                        Ah
                                    </span>

                                    @error('amps')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                @if (!$errors->has('amps'))
                                    <div class="invalid-feedback">
                                        Informe a capacidade da bateria.
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                            </span>

                            <i class="bi bi-check-lg" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar bateria
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endcan

    {{-- ================================================================
        MODAL EDITAR
    ================================================================= --}}

    @can('towers.edit')
        <div class="modal fade resource-modal" id="editBatteryModal" tabindex="-1" aria-labelledby="editBatteryModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <form method="POST" class="modal-content js-resource-form" data-edit-form novalidate>

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editBatteryModalLabel">

                                    Editar bateria
                                </h5>

                                <p class="resource-modal-description">
                                    Atualize as informações da bateria.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="edit_battery_name" class="form-label">

                                Nome
                            </label>

                            <input type="text" class="form-control" id="edit_battery_name" name="name"
                                data-edit-field="name" maxlength="150" required>

                            <div class="invalid-feedback">
                                Informe o nome da bateria.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="edit_battery_mark" class="form-label">

                                Marca
                            </label>

                            <input type="text" class="form-control" id="edit_battery_mark" name="mark"
                                data-edit-field="mark" maxlength="150" required>

                            <div class="invalid-feedback">
                                Informe a marca da bateria.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="edit_battery_type" class="form-label">

                                Tipo
                            </label>

                            <select class="form-select" id="edit_battery_type" name="type" data-edit-field="type"
                                required>

                                <option value="Automotive">
                                    Automotiva
                                </option>

                                <option value="stationary">
                                    Estacionária
                                </option>

                                <option value="LiFePO4">
                                    LiFePO4
                                </option>

                                <option value="others">
                                    Outro
                                </option>

                            </select>

                            <div class="invalid-feedback">
                                Selecione o tipo da bateria.
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="edit_battery_voltage" class="form-label">

                                    Voltagem
                                </label>

                                <div class="input-group">

                                    <input type="number" class="form-control" id="edit_battery_voltage" name="voltage"
                                        data-edit-field="voltage" min="12" max="1000" step="12" required>

                                    <span class="input-group-text">
                                        V
                                    </span>

                                </div>

                                <div class="invalid-feedback">
                                    Informe a voltagem.
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="edit_battery_amps" class="form-label">

                                    Capacidade
                                </label>

                                <div class="input-group">

                                    <input type="number" class="form-control" id="edit_battery_amps" name="amps"
                                        data-edit-field="amps" min="0" max="10000" step="0.01" required>

                                    <span class="input-group-text">
                                        Ah
                                    </span>

                                </div>

                                <div class="invalid-feedback">
                                    Informe a capacidade.
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                            </span>

                            <i class="bi bi-check-lg" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar alterações
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>
    @endcan

@endsection

@push('scripts')
    <script>
        window.resourceIndexConfig = {
            openModalOnValidationError: @json($errors->any()),
            validationModal: '#addBatteryModal'
        };
    </script>

    <script src="{{ asset('js/tower-resource-index.js') }}"></script>
@endpush
