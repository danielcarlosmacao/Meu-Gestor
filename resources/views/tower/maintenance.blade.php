@extends('layouts.header')

@section('title', 'Manutenções de Torres')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tower-resource-index.css') }}">
@endpush

@section('content')

    @php
        $hoje = \Carbon\Carbon::today();

        $totalPendentes = $maintenances->where('status', 'pending')->count();

        $totalConcluidas = $maintenances->where('status', 'completed')->count();

        $totalArquivadas = $maintenances->where('status', 'archived')->count();
    @endphp

    <div class="resource-page">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <header class="resource-page-header">

            <div>

                <div class="resource-page-eyebrow">
                    <i class="bi bi-tools"></i>
                    Gestão das torres
                </div>

                <h1 class="resource-page-title">
                    Manutenções de Torres
                </h1>

                <p class="resource-page-description">
                    Acompanhe manutenções pendentes, concluídas, arquivadas
                    e os próximos serviços programados.
                </p>

            </div>

            <div class="resource-header-actions">

                <button type="button" class="btn btn-light resource-filter-button" data-filter-toggle="#maintenanceFilters"
                    aria-expanded="{{ request()->filled('status') ? 'true' : 'false' }}">

                    <i class="bi bi-funnel"></i>

                    <span data-filter-label>
                        {{ request()->filled('status') ? 'Ocultar filtros' : 'Filtros' }}
                    </span>

                </button>

                @can('towers.maintenance')
                    <button type="button" class="btn dcm-btn-primary resource-primary-button" data-bs-toggle="modal"
                        data-bs-target="#addMaintenanceModal">

                        <i class="bi bi-plus-lg"></i>
                        Nova manutenção
                    </button>
                @endcan

            </div>

        </header>

        {{-- ============================================================
            CARDS
        ============================================================= --}}

        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Total cadastrado
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $maintenances->total() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-lg-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-clock-history"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Pendentes na página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $totalPendentes }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-lg-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-check-circle"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Concluídas na página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $totalConcluidas }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-lg-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-archive"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Arquivadas na página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $totalArquivadas }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ============================================================
            FILTROS
        ============================================================= --}}

        <section id="maintenanceFilters"
            class="resource-filter-panel
                {{ request()->filled('status') ? 'is-visible' : '' }}">

            <form method="GET" action="{{ route('maintenance.index') }}" class="resource-filter-form">

                <div class="resource-filter-field">

                    <label for="maintenance_status_filter" class="form-label">

                        Situação
                    </label>

                    <select name="status" id="maintenance_status_filter" class="form-select" data-auto-submit>

                        <option value="">
                            Todas
                        </option>

                        <option value="pending" @selected(request('status') === 'pending')>

                            Pendentes
                        </option>

                        <option value="completed" @selected(request('status') === 'completed')>

                            Concluídas
                        </option>

                        <option value="archived" @selected(request('status') === 'archived')>

                            Arquivadas
                        </option>

                    </select>

                </div>

                @if (request()->filled('status'))
                    <div class="resource-filter-actions">

                        <a href="{{ route('maintenance.index') }}" class="btn btn-light">

                            <i class="bi bi-x-lg"></i>
                            Limpar filtro
                        </a>

                    </div>
                @endif

            </form>

        </section>

        {{-- ============================================================
            TABELA
        ============================================================= --}}

        <section class="resource-table-card">

            <div class="resource-table-header">

                <div>

                    <h2 class="resource-table-title">
                        Manutenções cadastradas
                    </h2>

                    <p class="resource-table-subtitle">
                        Datas em destaque indicam serviços próximos ou atrasados.
                    </p>

                </div>

                <div class="resource-table-tools">

                    <div class="resource-search">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" data-resource-search="#maintenanceTable"
                            placeholder="Pesquisar manutenção..." autocomplete="off">

                    </div>

                </div>

            </div>

            <div class="resource-table-responsive">

                <table id="maintenanceTable" class="table resource-table align-middle">

                    <thead>

                        <tr>

                            <th class="resource-sortable ps-4" data-sortable="true">

                                <span>
                                    Torre
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Informação
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Data da manutenção
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Próxima manutenção
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Situação
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="text-center pe-4">
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($maintenances as $maintenance)

                            @php
                                $maintenanceDate = $maintenance->maintenance_date
                                    ? \Carbon\Carbon::parse($maintenance->maintenance_date)
                                    : null;

                                $nextMaintenanceDate = $maintenance->next_maintenance_date
                                    ? \Carbon\Carbon::parse($maintenance->next_maintenance_date)
                                    : null;

                                /*
                                | Manutenção pendente cuja data chegou
                                | ou acontecerá nos próximos cinco dias.
                                */
                                $maintenanceAlert =
                                    $maintenance->status === 'pending' &&
                                    $maintenanceDate &&
                                    $maintenanceDate->lte($hoje->copy()->addDays(5));

                                /*
                                | Manutenção concluída cuja próxima manutenção
                                | chegou ou acontecerá nos próximos cinco dias.
                                */
                                $nextMaintenanceAlert =
                                    $maintenance->status === 'completed' &&
                                    $nextMaintenanceDate &&
                                    $nextMaintenanceDate->lte($hoje->copy()->addDays(5));

                                $statusIcon = match ($maintenance->status) {
                                    'pending' => 'bi-clock-history',
                                    'completed' => 'bi-check-circle',
                                    'archived' => 'bi-archive',
                                    default => 'bi-circle',
                                };

                                $statusLabel = match ($maintenance->status) {
                                    'pending' => 'Pendente',
                                    'completed' => 'Concluída',
                                    'archived' => 'Arquivada',
                                    default => ucfirst($maintenance->status),
                                };
                            @endphp

                            <tr data-resource-row
                                data-search="{{ mb_strtolower(
                                    implode(' ', [
                                        $maintenance->tower->name ?? '',
                                        $maintenance->info,
                                        $statusLabel,
                                        $maintenanceDate?->format('d/m/Y') ?? '',
                                        $nextMaintenanceDate?->format('d/m/Y') ?? '',
                                    ]),
                                ) }}">

                                {{-- TORRE --}}
                                <td class="ps-4">

                                    <div class="resource-name">

                                        <span class="resource-name-icon">
                                            <i class="bi bi-broadcast-pin"></i>
                                        </span>

                                        <span>

                                            <strong>
                                                {{ $maintenance->tower->name ?? 'Torre não encontrada' }}
                                            </strong>

                                            <small>
                                                Manutenção de torre
                                            </small>

                                        </span>

                                    </div>

                                </td>

                                {{-- INFORMAÇÃO --}}
                                <td>

                                    <div style="min-width: 220px; max-width: 420px;">
                                        {{ $maintenance->info }}
                                    </div>

                                </td>

                                {{-- DATA --}}
                                <td data-value="{{ $maintenanceDate?->format('Y-m-d') ?? '' }}">

                                    @if ($maintenanceDate)
                                        <span
                                            class="resource-value-badge
                                                {{ $maintenanceAlert ? 'text-danger fw-bold' : '' }}">

                                            @if ($maintenanceAlert)
                                                <i class="bi bi-exclamation-triangle"></i>
                                            @else
                                                <i class="bi bi-calendar-event"></i>
                                            @endif

                                            {{ $maintenanceDate->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="resource-muted">
                                            Não informada
                                        </span>
                                    @endif

                                </td>

                                {{-- PRÓXIMA DATA --}}
                                <td data-value="{{ $nextMaintenanceDate?->format('Y-m-d') ?? '' }}">

                                    @if ($nextMaintenanceDate)
                                        <span
                                            class="resource-value-badge
                                                {{ $nextMaintenanceAlert ? 'text-danger fw-bold' : '' }}">

                                            @if ($nextMaintenanceAlert)
                                                <i class="bi bi-exclamation-triangle"></i>
                                            @else
                                                <i class="bi bi-calendar-check"></i>
                                            @endif

                                            {{ $nextMaintenanceDate->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="resource-muted">
                                            Não programada
                                        </span>
                                    @endif

                                </td>

                                {{-- STATUS --}}
                                <td data-value="{{ $statusLabel }}">

                                    <span class="resource-value-badge">

                                        <i class="bi {{ $statusIcon }}"></i>
                                        {{ $statusLabel }}

                                    </span>

                                </td>

                                {{-- AÇÕES --}}
                                <td class="text-center pe-4">

                                    @can('towers.maintenance')
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

                                                    <button type="button" class="dropdown-item"
                                                        data-resource-edit="#editMaintenanceModal"
                                                        data-update-url="{{ route('maintenance.update', $maintenance->id) }}"
                                                        data-tower_id="{{ $maintenance->tower_id }}"
                                                        data-info="{{ $maintenance->info }}"
                                                        data-maintenance_date="{{ $maintenanceDate?->format('Y-m-d') }}"
                                                        data-next_maintenance_date="{{ $nextMaintenanceDate?->format('Y-m-d') }}"
                                                        data-status="{{ $maintenance->status }}" data-bs-toggle="modal"
                                                        data-bs-target="#editMaintenanceModal">

                                                        <i class="bi bi-pencil-square"></i>
                                                        Editar
                                                    </button>

                                                </li>

                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                <li>

                                                    <button type="button" class="dropdown-item resource-delete-item"
                                                        data-resource-delete="{{ route('maintenance.destroy', $maintenance->id) }}"
                                                        data-delete-title="Deseja excluir esta manutenção?"
                                                        data-delete-description="Essa alteração não poderá ser desfeita.">

                                                        <i class="bi bi-trash"></i>
                                                        Excluir
                                                    </button>

                                                </li>

                                            </ul>

                                        </div>
                                    @endcan

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="resource-empty-state">

                                    <span class="resource-empty-icon">
                                        <i class="bi bi-tools"></i>
                                    </span>

                                    <h3>
                                        Nenhuma manutenção encontrada
                                    </h3>

                                    <p>
                                        Não existem manutenções cadastradas para este filtro.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                        <tr class="d-none" data-search-empty>

                            <td colspan="6" class="resource-empty-state">

                                <span class="resource-empty-icon">
                                    <i class="bi bi-search"></i>
                                </span>

                                <h3>
                                    Nenhuma manutenção encontrada
                                </h3>

                                <p>
                                    Tente pesquisar por outra torre, informação ou situação.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            @if ($maintenances->hasPages())
                <div class="resource-pagination">
                    {{ $maintenances->withQueryString()->links() }}
                </div>
            @endif

        </section>

    </div>

    {{-- ================================================================
        MODAL ADICIONAR
    ================================================================= --}}

    @can('towers.maintenance')

        <div class="modal fade resource-modal" id="addMaintenanceModal" tabindex="-1"
            aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">

                <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data"
                    class="modal-content js-resource-form" novalidate>

                    @csrf

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-tools"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="addMaintenanceModalLabel">

                                    Nova manutenção
                                </h5>

                                <p class="resource-modal-description">
                                    Informe a torre, as datas e os detalhes da manutenção.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="maintenance_tower_id" class="form-label">

                                Torre
                            </label>

                            <select
                                class="form-select
                                    @error('tower_id') is-invalid @enderror"
                                id="maintenance_tower_id" name="tower_id" required>

                                <option value="">
                                    Selecione a torre
                                </option>

                                @foreach ($towers as $tower)
                                    <option value="{{ $tower->id }}" @selected(old('tower_id') == $tower->id)>

                                        {{ $tower->name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('tower_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Selecione a torre.
                                </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label for="maintenance_info" class="form-label">

                                Informação
                            </label>

                            <textarea class="form-control
                                    @error('info') is-invalid @enderror"
                                id="maintenance_info" name="info" rows="3" required>{{ old('info') }}</textarea>

                            @error('info')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe os detalhes da manutenção.
                                </div>
                            @enderror

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="maintenance_date" class="form-label">

                                    Data da manutenção
                                </label>

                                <input type="date"
                                    class="form-control
                                        @error('maintenance_date') is-invalid @enderror"
                                    id="maintenance_date" name="maintenance_date" value="{{ old('maintenance_date') }}"
                                    required>

                                @error('maintenance_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Informe a data da manutenção.
                                    </div>
                                @enderror

                            </div>

                            <div class="col-md-6">

                                <label for="next_maintenance_date" class="form-label">

                                    Próxima manutenção
                                </label>

                                <input type="date"
                                    class="form-control
                                        @error('next_maintenance_date') is-invalid @enderror"
                                    id="next_maintenance_date" name="next_maintenance_date"
                                    value="{{ old('next_maintenance_date') }}">

                                @error('next_maintenance_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                        <div class="mt-3">

                            <label for="maintenance_status" class="form-label">

                                Situação
                            </label>

                            <select
                                class="form-select
                                    @error('status') is-invalid @enderror"
                                id="maintenance_status" name="status" required>

                                <option value="pending" @selected(old('status', 'pending') === 'pending')>

                                    Pendente
                                </option>

                                <option value="completed" @selected(old('status') === 'completed')>

                                    Concluída
                                </option>

                                <option value="archived" @selected(old('status') === 'archived')>

                                    Arquivada
                                </option>

                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="mt-3">

                            <label for="maintenance_images" class="form-label">

                                Anexar imagens
                            </label>

                            <input type="file"
                                class="form-control
                                    @error('images') is-invalid @enderror
                                    @error('images.*') is-invalid @enderror"
                                id="maintenance_images" name="images[]" multiple accept="image/*">

                            <div class="form-text">
                                Você pode selecionar várias imagens.
                            </div>

                            @error('images')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            @error('images.*')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner>
                            </span>

                            <i class="bi bi-check-lg" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar manutenção
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

    @can('towers.maintenance')

        <div class="modal fade resource-modal" id="editMaintenanceModal" tabindex="-1"
            aria-labelledby="editMaintenanceModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">

                <form method="POST" class="modal-content js-resource-form" data-edit-form novalidate>

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editMaintenanceModalLabel">

                                    Editar manutenção
                                </h5>

                                <p class="resource-modal-description">
                                    Atualize os dados e a situação da manutenção.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="edit_maintenance_tower_id" class="form-label">

                                Torre
                            </label>

                            <select class="form-select" id="edit_maintenance_tower_id" name="tower_id"
                                data-edit-field="tower_id" required>

                                <option value="">
                                    Selecione a torre
                                </option>

                                @foreach ($towers as $tower)
                                    <option value="{{ $tower->id }}">
                                        {{ $tower->name }}
                                    </option>
                                @endforeach

                            </select>

                            <div class="invalid-feedback">
                                Selecione a torre.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="edit_maintenance_info" class="form-label">

                                Informação
                            </label>

                            <textarea class="form-control" id="edit_maintenance_info" name="info" data-edit-field="info" rows="3"
                                required></textarea>

                            <div class="invalid-feedback">
                                Informe os detalhes da manutenção.
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="edit_maintenance_date" class="form-label">

                                    Data da manutenção
                                </label>

                                <input type="date" class="form-control" id="edit_maintenance_date"
                                    name="maintenance_date" data-edit-field="maintenance_date" required>

                                <div class="invalid-feedback">
                                    Informe a data da manutenção.
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="edit_next_maintenance_date" class="form-label">

                                    Próxima manutenção
                                </label>

                                <input type="date" class="form-control" id="edit_next_maintenance_date"
                                    name="next_maintenance_date" data-edit-field="next_maintenance_date">

                            </div>

                        </div>

                        <div class="mt-3">

                            <label for="edit_maintenance_status" class="form-label">

                                Situação
                            </label>

                            <select class="form-select" id="edit_maintenance_status" name="status" data-edit-field="status"
                                required>

                                <option value="pending">
                                    Pendente
                                </option>

                                <option value="completed">
                                    Concluída
                                </option>

                                <option value="archived">
                                    Arquivada
                                </option>

                            </select>

                            <div class="invalid-feedback">
                                Selecione a situação.
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner>
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
            validationModal: '#addMaintenanceModal'
        };
    </script>

    <script src="{{ asset('js/tower-resource-index.js') }}"></script>
@endpush
