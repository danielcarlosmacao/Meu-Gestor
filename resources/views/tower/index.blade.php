@extends('layouts.header')

@section('title', 'Controle de Torres')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tower-index.css') }}">
@endpush

@section('content')

    @php
        $totalTowers = method_exists($pagination, 'total') ? $pagination->total() : count($towerData);

        $totalEquipment = collect($towerData)->sum(function ($tower) {
            return (int) ($tower['equipments'] ?? 0);
        });

        $totalBattery = collect($towerData)->sum(function ($tower) {
            return (int) ($tower['battery_quant'] ?? 0);
        });

        $totalSolarWatts = collect($towerData)->sum(function ($tower) {
            return (float) ($tower['total_watts_placa'] ?? 0);
        });
    @endphp

    <div class="container-fluid tower-page px-3 px-lg-4 py-4">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <section class="tower-page-header mb-4">

            <div
                class="d-flex flex-column flex-lg-row
                        align-items-lg-center justify-content-between gap-3">

                <div>

                    <div class="tower-page-eyebrow">
                        <i class="bi bi-broadcast-pin"></i>
                        Gestão da rede
                    </div>

                    <h1 class="tower-page-title">
                        Controle de torres
                    </h1>

                    <p class="tower-page-description">
                        Acompanhe equipamentos, baterias, consumo e produção
                        de energia dos pontos da rede.
                    </p>

                </div>

                @can('towers.create')
                    <button type="button" class="btn dcm-btn-primary tower-add-button" data-bs-toggle="modal"
                        data-bs-target="#addTower">

                        <i class="bi bi-plus-lg"></i>
                        Nova torre
                    </button>
                @endcan

            </div>

        </section>

        {{-- ============================================================
            CARDS DE RESUMO
        ============================================================= --}}

        <section class="row g-3 mb-4">

            <div class="col-6 col-xl-3">

                <div class="card tower-summary-card h-100">

                    <div class="card-body">

                        <div class="tower-summary-content">

                            <span class="tower-summary-icon">
                                <i class="bi bi-broadcast"></i>
                            </span>

                            <div>

                                <span class="tower-summary-label">
                                    Torres
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format($totalTowers, 0, ',', '.') }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="card tower-summary-card h-100">

                    <div class="card-body">

                        <div class="tower-summary-content">

                            <span class="tower-summary-icon">
                                <i class="bi bi-router"></i>
                            </span>

                            <div>

                                <span class="tower-summary-label">
                                    Equipamentos
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format($totalEquipment, 0, ',', '.') }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="card tower-summary-card h-100">

                    <div class="card-body">

                        <div class="tower-summary-content">

                            <span class="tower-summary-icon">
                                <i class="bi bi-battery-charging"></i>
                            </span>

                            <div>

                                <span class="tower-summary-label">
                                    Baterias
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format($totalBattery, 0, ',', '.') }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="card tower-summary-card h-100">

                    <div class="card-body">

                        <div class="tower-summary-content">

                            <span class="tower-summary-icon">
                                <i class="bi bi-sun"></i>
                            </span>

                            <div>

                                <span class="tower-summary-label">
                                    Produção solar
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format($totalSolarWatts, 0, ',', '.') }} W
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {{-- ============================================================
            TABELA
        ============================================================= --}}

        <section class="card tower-table-card">

            <div class="card-header tower-table-header">

                <div>

                    <h2 class="tower-table-title">
                        Torres cadastradas
                    </h2>

                    <p class="tower-table-subtitle">
                        Clique no nome de uma torre para visualizar os detalhes.
                    </p>

                </div>

                <div class="tower-table-tools">

                    <div class="tower-search-box">

                        <i class="bi bi-search"></i>

                        <input type="search" id="towerSearch" class="form-control" placeholder="Pesquisar torre..."
                            autocomplete="off">

                    </div>

                    <select id="towerPerPage" class="form-select tower-per-page"
                        aria-label="Quantidade de registros por página">

                        @foreach ([10, 25, 50, 100, 500] as $size)
                            <option value="{{ $size }}" @selected((int) request('perPage', 10) === $size)>

                                {{ $size }} por página
                            </option>
                        @endforeach

                    </select>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table id="towersTable" class="table tower-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th class="sortable-column ps-4" data-sortable="true">

                                    <span>
                                        Nome
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Voltagem
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Equipamentos
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Consumo
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Quant. baterias
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Modelo da bateria
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Carga
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Instalação
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Produção
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Placas solares
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="sortable-column" data-sortable="true">

                                    <span>
                                        Utilização
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </span>
                                </th>

                                <th class="text-center pe-4" data-sortable="false">

                                    Ações
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($towerData as $tower)
                                @php
                                    $batteryPercentage = (float) ($tower['battery_percentage'] ?? 0);
                                    $platePercentage = (float) ($tower['plate_percentage'] ?? 0);

                                    $batteryClass = match (true) {
                                        $batteryPercentage >= 70 => 'success',
                                        $batteryPercentage >= 40 => 'warning',
                                        default => 'danger',
                                    };

                                    $plateClass = match (true) {
                                        $platePercentage <= 70 => 'success',
                                        $platePercentage <= 90 => 'warning',
                                        default => 'danger',
                                    };
                                @endphp

                                <tr class="tower-row"
                                    data-search="{{ mb_strtolower(implode(' ', [$tower['name'] ?? '', $tower['battery'] ?? '', $tower['voltage'] ?? ''])) }}">

                                    {{-- NOME --}}
                                    <td class="ps-4">

                                        <a href="{{ route('tower.show', $tower['id']) }}" class="tower-name-link">

                                            <span class="tower-name-icon">
                                                <i class="bi bi-broadcast-pin"></i>
                                            </span>

                                            <span>

                                                <strong>
                                                    {{ $tower['name'] }}
                                                </strong>

                                                <small>
                                                    Visualizar detalhes
                                                </small>

                                            </span>

                                        </a>

                                    </td>

                                    {{-- VOLTAGEM --}}
                                    <td data-value="{{ $tower['voltage'] ?? 0 }}">

                                        <span class="tower-value-badge">
                                            {{ $tower['voltage'] ?? 0 }} V
                                        </span>

                                    </td>

                                    {{-- EQUIPAMENTOS --}}
                                    <td data-value="{{ $tower['equipments'] ?? 0 }}">

                                        <span class="tower-count">

                                            <i class="bi bi-router"></i>

                                            {{ $tower['equipments'] ?? 0 }}
                                        </span>

                                    </td>

                                    {{-- CONSUMO --}}
                                    <td data-value="{{ $tower['watts_equipments'] ?? 0 }}">

                                        <span class="text-nowrap fw-semibold">
                                            {{ number_format((float) ($tower['watts_equipments'] ?? 0), 0, ',', '.') }}
                                            W
                                        </span>

                                    </td>

                                    {{-- QUANTIDADE DE BATERIAS --}}
                                    <td data-value="{{ $tower['battery_quant'] ?? 0 }}">

                                        {{ $tower['battery_quant'] ?? '—' }}

                                    </td>

                                    {{-- BATERIA --}}
                                    <td>

                                        @if (!empty($tower['battery']))
                                            <div class="tower-battery">

                                                <i class="bi bi-battery-charging"></i>

                                                <span>
                                                    {{ $tower['battery'] }}
                                                </span>

                                            </div>
                                        @else
                                            <span class="tower-empty-value">
                                                Não informada
                                            </span>
                                        @endif

                                    </td>

                                    {{-- PERCENTUAL DA BATERIA --}}
                                    <td data-value="{{ $batteryPercentage }}">

                                        <div class="tower-progress">

                                            <div class="d-flex justify-content-between gap-2">

                                                <span class="tower-progress-label">
                                                    {{ number_format($batteryPercentage, 1, ',', '.') }}%
                                                </span>

                                            </div>

                                            <div class="progress" role="progressbar"
                                                aria-valuenow="{{ $batteryPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">

                                                <div class="progress-bar bg-{{ $batteryClass }}"
                                                    style="width: {{ min(100, max(0, $batteryPercentage)) }}%">
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- DATA DA INSTALAÇÃO --}}
                                    <td data-value="{{ $tower['battery_install_ord'] ?? '' }}">

                                        @if (!empty($tower['battery_install_date']))
                                            <span class="tower-date">

                                                <i class="bi bi-calendar3"></i>

                                                {{ $tower['battery_install_date'] }}
                                            </span>
                                        @else
                                            <span class="tower-empty-value">
                                                Não informada
                                            </span>
                                        @endif

                                    </td>

                                    {{-- TEMPO DE PRODUÇÃO --}}
                                    <td data-value="{{ $tower['production_ord'] ?? 0 }}">

                                        @if (!empty($tower['production_time']))
                                            <span class="tower-production-time">

                                                <i class="bi bi-clock-history"></i>

                                                {{ $tower['production_time'] }}
                                            </span>
                                        @else
                                            <span class="tower-empty-value">
                                                —
                                            </span>
                                        @endif

                                    </td>

                                    {{-- PLACAS SOLARES --}}
                                    <td data-value="{{ $tower['total_watts_placa'] ?? 0 }}">

                                        <div class="tower-solar-value">

                                            <strong>
                                                {{ number_format((float) ($tower['total_watts_placa'] ?? 0), 0, ',', '.') }}
                                                W
                                            </strong>

                                            <small>
                                                {{ number_format((float) ($tower['total_amps_placa'] ?? 0), 1, ',', '.') }}
                                                A
                                            </small>

                                        </div>

                                    </td>

                                    {{-- PERCENTUAL DA PLACA --}}
                                    <td data-value="{{ $platePercentage }}">

                                        <div class="tower-progress">

                                            <span class="tower-progress-label">
                                                {{ number_format($platePercentage, 1, ',', '.') }}%
                                            </span>

                                            <div class="progress" role="progressbar"
                                                aria-valuenow="{{ $platePercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">

                                                <div class="progress-bar bg-{{ $plateClass }}"
                                                    style="width: {{ min(100, max(0, $platePercentage)) }}%">
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- AÇÕES --}}
                                    <td class="text-center pe-4">

                                        <div class="d-inline-flex align-items-center gap-1">

                                            <a href="{{ route('tower.show', $tower['id']) }}"
                                                class="btn btn-sm tower-action-button" title="Visualizar torre"
                                                aria-label="Visualizar torre">

                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @can('towers.delete')
                                                <button type="button"
                                                    class="btn btn-sm tower-action-button
                                                        tower-action-danger"
                                                    onclick="openConfirmModal(
                                                        '{{ route('tower.destroy', $tower['id']) }}',
                                                        'Deseja excluir esta torre?',
                                                        'Essa alteração não poderá ser desfeita.',
                                                        'DELETE'
                                                    )"
                                                    title="Excluir torre" aria-label="Excluir torre">

                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr id="towerEmptyRow">

                                    <td colspan="12" class="tower-empty-state">

                                        <span class="tower-empty-icon">
                                            <i class="bi bi-broadcast"></i>
                                        </span>

                                        <h3>
                                            Nenhuma torre cadastrada
                                        </h3>

                                        <p>
                                            Cadastre a primeira torre para começar
                                            a controlar sua infraestrutura.
                                        </p>

                                        @can('towers.create')
                                            <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addTower">

                                                <i class="bi bi-plus-lg"></i>
                                                Cadastrar torre
                                            </button>
                                        @endcan

                                    </td>

                                </tr>
                            @endforelse

                            <tr id="towerSearchEmpty" class="d-none">

                                <td colspan="12" class="tower-empty-state">

                                    <span class="tower-empty-icon">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <h3>
                                        Nenhum resultado encontrado
                                    </h3>

                                    <p>
                                        Tente pesquisar usando outro nome,
                                        bateria ou voltagem.
                                    </p>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- PAGINAÇÃO --}}
            @if ($pagination->hasPages())
                <div class="card-footer tower-pagination">

                    <div class="tower-pagination-info">

                        Exibindo

                        <strong>
                            {{ $pagination->firstItem() }}
                        </strong>

                        até

                        <strong>
                            {{ $pagination->lastItem() }}
                        </strong>

                        de

                        <strong>
                            {{ $pagination->total() }}
                        </strong>

                        registros

                    </div>

                    <div>
                        {{ $pagination->withQueryString()->links() }}
                    </div>

                </div>
            @endif

        </section>

    </div>

    {{-- ================================================================
        MODAL DE CADASTRO
    ================================================================= --}}

    <div class="modal fade" id="addTower" tabindex="-1" aria-labelledby="addTowerLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content tower-modal">

                <div class="modal-header tower-modal-header">

                    <div class="d-flex align-items-center gap-3">

                        <span class="tower-modal-icon">
                            <i class="bi bi-broadcast-pin"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="addTowerLabel">

                                Nova torre
                            </h5>

                            <p>
                                Informe os dados básicos da torre.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form id="towerCreateForm" action="{{ route('tower.store') }}" method="POST" novalidate>

                    @csrf

                    <div class="modal-body p-4">

                        <div class="mb-4">

                            <label for="name" class="form-label">

                                Nome da torre

                                <span class="text-danger">
                                    *
                                </span>
                            </label>

                            <div class="tower-input-group">

                                <i class="bi bi-broadcast"></i>

                                <input type="text"
                                    class="form-control
                                        @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required maxlength="150"
                                    placeholder="Ex.: Torre Centro">

                            </div>

                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe o nome da torre.
                                </div>
                            @enderror

                        </div>

                        <div>

                            <label for="voltage" class="form-label">

                                Voltagem

                                <span class="text-danger">
                                    *
                                </span>
                            </label>

                            <div class="tower-input-group">

                                <i class="bi bi-lightning-charge"></i>

                                <input type="number"
                                    class="form-control
                                        @error('voltage') is-invalid @enderror"
                                    id="voltage" name="voltage" value="{{ old('voltage') }}" min="12"
                                    max="1000" step="12" required placeholder="Ex.: 12, 24 ou 48">

                                <span class="tower-input-suffix">
                                    V
                                </span>

                            </div>

                            @error('voltage')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe uma voltagem válida.
                                </div>
                            @enderror

                            <div class="form-text">
                                Informe a tensão nominal utilizada na torre.
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer tower-modal-footer">

                        <button type="button" class="btn btn-light tower-modal-cancel" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" id="towerSubmitButton">

                            <span id="towerSubmitSpinner"
                                class="spinner-border spinner-border-sm
                                    d-none"
                                aria-hidden="true">
                            </span>

                            <i id="towerSubmitIcon" class="bi bi-check-lg">
                            </i>

                            <span id="towerSubmitText">
                                Salvar torre
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        window.towerIndexConfig = {
            hasValidationErrors: @json($errors->any()),
            perPageParameter: 'perPage'
        };
    </script>

    <script src="{{ asset('js/tower-index.js') }}"></script>
@endpush
