@extends('layouts.header')

@section('title', 'Relatório de Produção de Baterias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tower-resource-index.css') }}">

    <style>
        .report-battery-progress {
            min-width: 120px;
        }

        .report-battery-progress .progress {
            height: 7px;
            background: rgba(0, 0, 0, 0.08);
        }

        .report-battery-progress-value {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 5px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .report-battery-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .report-battery-status.is-active {
            color: #157347;
            background: rgba(25, 135, 84, 0.12);
        }

        .report-battery-status.is-inactive {
            color: #6c757d;
            background: rgba(108, 117, 125, 0.12);
        }

        .report-battery-active-row {
            background: rgba(25, 135, 84, 0.025);
        }

        .report-battery-filter {
            display: flex;
            align-items: end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .report-battery-filter-field {
            width: 100%;
            max-width: 280px;
        }

        .report-battery-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .report-battery-info small {
            color: var(--bs-secondary-color);
        }

        @media (max-width: 767.98px) {
            .report-battery-filter-field {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $firstProduction = $productions->first();
        $batteryName = $firstProduction?->battery?->name ?? 'Bateria não encontrada';

        $activeProductions = $productions->filter(fn($production) => is_null($production->removal_date))->count();

        $inactiveProductions = $productions->filter(fn($production) => !is_null($production->removal_date))->count();

        $totalAmount = $productions->sum('amount');

        $averagePercentage = $productions
            ->filter(fn($production) => !is_null($production->production_percentage))
            ->avg('production_percentage');
    @endphp

    <div class="resource-page">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <header class="resource-page-header">

            <div>

                <div class="resource-page-eyebrow">
                    <i class="bi bi-battery-charging"></i>
                    Relatório de baterias
                </div>

                <h1 class="resource-page-title">
                    {{ $batteryName }}
                </h1>

                <p class="resource-page-description">
                    Histórico de instalação, remoção e tempo de produção
                    deste modelo de bateria.
                </p>

            </div>

            <div class="resource-header-actions">

                <a href="{{ url()->previous() }}" class="btn btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Voltar
                </a>

            </div>

        </header>

        {{-- ============================================================
            CARDS DE RESUMO
        ============================================================= --}}

        <div class="row g-3 mb-4">

            <div class="col-6 col-xl-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-list-check"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Registros
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $productions->count() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-battery-charging"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Em produção
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $activeProductions }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-battery"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Quantidade instalada
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $totalAmount }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-xl-3">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-clock-history"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Média em produção
                                </span>

                                <strong class="resource-summary-value">
                                    {{ number_format((float) $mediaAnos, 1, ',', '.') }}
                                    anos
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ============================================================
            FILTRO
        ============================================================= --}}

        <section class="resource-filter-panel is-visible mb-4">

            <form method="GET" action="{{ url()->current() }}" class="report-battery-filter">

                <div class="report-battery-filter-field">

                    <label for="status" class="form-label">

                        Situação da produção
                    </label>

                    <select name="status" id="status" class="form-select" onchange="this.form.submit()">

                        <option value="todas" @selected($status === 'todas')>

                            Todas as produções
                        </option>

                        <option value="ativas" @selected($status === 'ativas')>

                            Apenas ativas
                        </option>

                        <option value="inativas" @selected($status === 'inativas')>

                            Apenas removidas
                        </option>

                    </select>

                </div>

                @if ($status !== 'todas')
                    <a href="{{ url()->current() }}" class="btn btn-light">

                        <i class="bi bi-x-lg"></i>
                        Limpar filtro
                    </a>
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
                        Histórico de produção
                    </h2>

                    <p class="resource-table-subtitle">

                        @if ($averagePercentage !== null)
                            Média de aproveitamento:

                            <strong>
                                {{ number_format($averagePercentage, 2, ',', '.') }}%
                            </strong>
                        @else
                            Consulte as instalações realizadas nas torres.
                        @endif

                    </p>

                </div>

                <div class="resource-table-tools">

                    <div class="resource-search">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" data-resource-search="#batteryProductionTable"
                            placeholder="Pesquisar torre..." autocomplete="off">

                    </div>

                </div>

            </div>

            <div class="resource-table-responsive">

                <table id="batteryProductionTable" class="table resource-table align-middle">

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
                                    Tensão
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Quantidade
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Instalação
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Remoção
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Tempo em produção
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Aproveitamento
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th>
                                Situação
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($productions as $production)
                            @php
                                $installationDate = $production->installation_date
                                    ? \Carbon\Carbon::parse($production->installation_date)
                                    : null;

                                $removalDate = $production->removal_date
                                    ? \Carbon\Carbon::parse($production->removal_date)
                                    : null;

                                $isActive = is_null($production->removal_date);

                                $percentage = $production->production_percentage;

                                $progressPercentage = $percentage !== null ? max(0, min(100, (float) $percentage)) : 0;
                            @endphp

                            <tr class="{{ $isActive ? 'report-battery-active-row' : '' }}" data-resource-row
                                data-search="{{ mb_strtolower(
                                    implode(' ', [
                                        $production->tower->name ?? '',
                                        $production->tower->voltage ?? '',
                                        $production->amount,
                                        $production->data_instalacao_formatada ?? '',
                                        $production->data_remocao_formatada ?? '',
                                        $production->tempo_formatado ?? '',
                                        $isActive ? 'ativa em produção' : 'removida inativa',
                                    ]),
                                ) }}">

                                {{-- TORRE --}}
                                <td class="ps-4">

                                    <div class="resource-name">

                                        <span class="resource-name-icon">
                                            <i class="bi bi-broadcast-pin"></i>
                                        </span>

                                        <span class="report-battery-info">

                                            <strong>
                                                {{ $production->tower->name ?? 'Torre não encontrada' }}
                                            </strong>

                                            <small>
                                                {{ $isActive ? 'Bateria em operação' : 'Bateria removida' }}
                                            </small>

                                        </span>

                                    </div>

                                </td>

                                {{-- VOLTAGEM --}}
                                <td data-value="{{ $production->tower->voltage ?? 0 }}">

                                    @if (!is_null($production->tower?->voltage))
                                        <span class="resource-value-badge">

                                            <i class="bi bi-lightning-charge"></i>

                                            {{ number_format((float) $production->tower->voltage, 0, ',', '.') }}
                                            V

                                        </span>
                                    @else
                                        <span class="resource-muted">
                                            Não informada
                                        </span>
                                    @endif

                                </td>

                                {{-- QUANTIDADE --}}
                                <td data-value="{{ $production->amount }}">

                                    <span class="resource-value-badge">
                                        {{ $production->amount }}
                                    </span>

                                </td>

                                {{-- INSTALAÇÃO --}}
                                <td data-value="{{ $installationDate?->format('Y-m-d') ?? '' }}">

                                    @if ($installationDate)
                                        <div class="report-battery-info">

                                            <strong>
                                                {{ $production->data_instalacao_formatada ?? $installationDate->format('d/m/Y') }}
                                            </strong>

                                            <small>
                                                {{ $installationDate->format('H:i') !== '00:00' ? $installationDate->format('H:i') : 'Data de entrada' }}
                                            </small>

                                        </div>
                                    @else
                                        <span class="resource-muted">
                                            Não informada
                                        </span>
                                    @endif

                                </td>

                                {{-- REMOÇÃO --}}
                                <td data-value="{{ $removalDate?->format('Y-m-d') ?? '' }}">

                                    @if ($removalDate)
                                        <div class="report-battery-info">

                                            <strong>
                                                {{ $production->data_remocao_formatada ?? $removalDate->format('d/m/Y') }}
                                            </strong>

                                            <small>
                                                Finalizada
                                            </small>

                                        </div>
                                    @else
                                        <span class="report-battery-status is-active">

                                            <i class="bi bi-arrow-repeat"></i>
                                            Em produção

                                        </span>
                                    @endif

                                </td>

                                {{-- TEMPO --}}
                                <td>

                                    <span class="resource-value-badge">

                                        <i class="bi bi-clock-history"></i>

                                        {{ $production->tempo_formatado ?? 'Não calculado' }}

                                    </span>

                                </td>

                                {{-- PERCENTUAL --}}
                                <td data-value="{{ $percentage ?? 0 }}">

                                    @if (!is_null($percentage))
                                        <div class="report-battery-progress">

                                            <div class="report-battery-progress-value">

                                                <span>
                                                    Produção
                                                </span>

                                                <strong>
                                                    {{ number_format((float) $percentage, 2, ',', '.') }}%
                                                </strong>

                                            </div>

                                            <div class="progress" role="progressbar"
                                                aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100">

                                                <div class="progress-bar" style="width: {{ $progressPercentage }}%">
                                                </div>

                                            </div>

                                        </div>
                                    @else
                                        <span class="resource-muted">
                                            Não calculado
                                        </span>
                                    @endif

                                </td>

                                {{-- SITUAÇÃO --}}
                                <td>

                                    @if ($isActive)
                                        <span class="report-battery-status is-active">

                                            <i class="bi bi-check-circle"></i>
                                            Ativa

                                        </span>
                                    @else
                                        <span class="report-battery-status is-inactive">

                                            <i class="bi bi-archive"></i>
                                            Removida

                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="resource-empty-state">

                                    <span class="resource-empty-icon">
                                        <i class="bi bi-battery"></i>
                                    </span>

                                    <h3>
                                        Nenhuma produção encontrada
                                    </h3>

                                    <p>
                                        Não existem registros para o filtro selecionado.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                        <tr class="d-none" data-search-empty>

                            <td colspan="8" class="resource-empty-state">

                                <span class="resource-empty-icon">
                                    <i class="bi bi-search"></i>
                                </span>

                                <h3>
                                    Nenhum resultado encontrado
                                </h3>

                                <p>
                                    Tente pesquisar utilizando o nome de outra torre.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            @if (method_exists($productions, 'hasPages') && $productions->hasPages())
                <div class="resource-pagination">
                    {{ $productions->withQueryString()->links() }}
                </div>
            @endif

        </section>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/tower-resource-index.js') }}"></script>
@endpush
