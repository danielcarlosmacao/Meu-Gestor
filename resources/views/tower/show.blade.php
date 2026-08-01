@extends('layouts.header')

@section('title', $tower->name ?? 'Detalhes da Torre')

@push('styles')

    <style>
        /*
        |--------------------------------------------------------------------------
        | PÁGINA
        |--------------------------------------------------------------------------
        */

        .tower-page {
            width: 100%;
            padding: 1.5rem clamp(1rem, 2vw, 2rem) 2.5rem;
        }

        .tower-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .tower-page-eyebrow {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 0.35rem;
            color: var(--bs-primary);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .tower-page-title {
            margin: 0;
            font-size: clamp(1.55rem, 3vw, 2.25rem);
            font-weight: 750;
        }

        .tower-page-description {
            margin: 0.4rem 0 0;
            color: var(--bs-secondary-color);
        }

        .tower-header-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        /*
        |--------------------------------------------------------------------------
        | GRADE DOS CARDS
        |--------------------------------------------------------------------------
        */

        .tower-cards-row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 1.5rem;
        }

        .tower-section-card {
            width: 100%;
            min-width: 0;
            background: var(--bs-body-bg);
            border: 2px solid var(--bs-border-color);
            border-radius: 16px;
            overflow: hidden;

            box-shadow:
                0 4px 14px rgba(0, 0, 0, 0.055),
                0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .tower-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 76px;
            padding: 1rem 1.15rem;
            border-bottom: 2px solid var(--bs-border-color);
            background: var(--bs-body-bg);
        }

        .tower-section-title-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .tower-section-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), 0.11);
            font-size: 1.1rem;
        }

        .tower-section-title {
            margin: 0;
            color: var(--bs-body-color);
            font-size: 1rem;
            font-weight: 750;
        }

        .tower-section-description {
            display: block;
            margin-top: 0.15rem;
            color: var(--bs-secondary-color);
            font-size: 0.76rem;
        }

        .tower-section-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        /*
        |--------------------------------------------------------------------------
        | TABELAS
        |--------------------------------------------------------------------------
        */

        .tower-table-area {
            padding: 0.75rem;
        }

        .tower-table {
            width: 100%;
            margin-bottom: 0;
            table-layout: fixed;
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            overflow: hidden;
        }

        .tower-table thead th {
            padding: 0.65rem 0.55rem;
            color: var(--bs-secondary-color);
            background: var(--bs-tertiary-bg);
            border-bottom: 1px solid var(--bs-border-color);
            font-size: 0.66rem;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            vertical-align: middle;
        }

        .tower-table tbody td {
            padding: 0.75rem 0.55rem;
            border-color: var(--bs-border-color);
            vertical-align: middle;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        .tower-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .tower-table tbody tr:hover {
            background: var(--bs-tertiary-bg);
        }

        .tower-active-row {
            background: rgba(25, 135, 84, 0.035);
        }

        .tower-table-primary {
            display: block;
            color: var(--bs-body-color);
            font-size: 0.86rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .tower-table-secondary {
            display: block;
            margin-top: 0.15rem;
            color: var(--bs-secondary-color);
            font-size: 0.7rem;
            font-weight: 400;
            line-height: 1.25;
        }

        .tower-table-date {
            display: block;
            font-size: 0.76rem;
            line-height: 1.35;
        }

        /*
        |--------------------------------------------------------------------------
        | COLUNAS DA TABELA DE BATERIAS
        |--------------------------------------------------------------------------
        */

        .battery-col-info {
            width: 17%;
        }

        .battery-col-battery {
            width: 19%;
        }

        .battery-col-dates {
            width: 18%;
        }

        .battery-col-capacity {
            width: 17%;
        }

        .battery-col-production {
            width: 19%;
        }

        .battery-col-actions {
            width: 10%;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | BADGES
        |--------------------------------------------------------------------------
        */

        .tower-value-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            max-width: 100%;
            padding: 0.28rem 0.52rem;
            border-radius: 8px;
            color: var(--bs-body-color);
            background: var(--bs-tertiary-bg);
            font-size: 0.73rem;
            font-weight: 700;
        }

        .tower-status {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.27rem 0.55rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .tower-status-active {
            color: #157347;
            background: rgba(25, 135, 84, 0.13);
        }

        .tower-status-inactive {
            color: #6c757d;
            background: rgba(108, 117, 125, 0.13);
        }

        .tower-voltage-badge {
            display: inline-flex;
            margin-left: 0.2rem;
            padding: 0.12rem 0.32rem;
            border-radius: 5px;
            color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), 0.1);
            font-size: 0.62rem;
            font-weight: 750;
        }

        /*
        |--------------------------------------------------------------------------
        | RESUMO COMPACTO
        |--------------------------------------------------------------------------
        */

        .tower-summary-card .tower-section-header {
            min-height: 68px;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }

        .tower-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 0.75rem;
            gap: 0.65rem;
        }

        .tower-summary-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            min-width: 0;
            min-height: 64px;
            padding: 0.65rem 0.7rem;
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            background: var(--bs-body-bg);
        }

        .tower-summary-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 9px;
            color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), 0.1);
            font-size: 0.88rem;
        }

        .tower-summary-content {
            min-width: 0;
        }

        .tower-summary-label {
            display: block;
            margin-bottom: 0.08rem;
            color: var(--bs-secondary-color);
            font-size: 0.65rem;
            line-height: 1.2;
        }

        .tower-summary-value {
            display: block;
            color: var(--bs-body-color);
            font-size: 0.84rem;
            font-weight: 750;
            line-height: 1.2;
        }

        .tower-summary-detail {
            display: block;
            margin-top: 0.1rem;
            color: var(--bs-secondary-color);
            font-size: 0.61rem;
            line-height: 1.2;
        }

        /*
        |--------------------------------------------------------------------------
        | ESTADO VAZIO
        |--------------------------------------------------------------------------
        */

        .tower-empty-state {
            padding: 2.5rem 1rem !important;
            color: var(--bs-secondary-color) !important;
            text-align: center;
        }

        .tower-empty-state i {
            display: block;
            margin-bottom: 0.6rem;
            font-size: 1.45rem;
            opacity: 0.6;
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVIDADE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1199.98px) {
            .tower-table thead th {
                font-size: 0.61rem;
            }

            .tower-table tbody td {
                padding-right: 0.4rem;
                padding-left: 0.4rem;
                font-size: 0.77rem;
            }

            .tower-value-badge {
                font-size: 0.68rem;
            }
        }

        @media (max-width: 991.98px) {
            .tower-page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .tower-header-actions {
                width: 100%;
            }

            .tower-table {
                table-layout: auto;
            }

            .battery-col-info,
            .battery-col-battery,
            .battery-col-dates,
            .battery-col-capacity,
            .battery-col-production,
            .battery-col-actions {
                width: auto;
            }
        }

        @media (max-width: 767.98px) {
            .tower-table-area {
                overflow-x: auto;
            }

            .tower-table {
                min-width: 720px;
            }

            .tower-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .tower-page {
                padding: 1rem 0.75rem 2rem;
            }

            .tower-section-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .tower-section-actions {
                width: 100%;
                justify-content: stretch;
            }

            .tower-section-actions .btn {
                flex: 1;
            }

            .tower-header-actions .btn {
                flex: 1;
            }
        }
    </style>

@endpush

@section('content')

    @php
        $today = now();

        $batteryProductions = $tower->batteryProductions ?? collect();
        $equipmentProductions = $tower->equipmentProductions ?? collect();
        $plateProductions = $tower->plateProductions ?? collect();

        $towerVoltage = (float) ($tower->voltage ?? 0);
        $wattsPlate = (float) ($summary->watts_plate ?? 0);
        $ampsPlate = (float) ($summary->amps_plate ?? 0);
        $batteryRequired = (float) ($summary->battery_required ?? 0);
        $timeAhConsumption = (float) ($summary->time_ah_consumption ?? 0);
        $consumptionAhDay = (float) ($summary->consumption_ah_day ?? 0);
        $hoursGeneration = (float) ($hours_Generation ?? 0);
        $hoursAutonomy = (float) ($hours_autonomy ?? 0);
        $plateRequired = (float) ($platerrequire ?? 0);

        $wattsInAmps = $towerVoltage > 0
            ? $wattsPlate / $towerVoltage
            : 0;

        $plateRequirementPercentage = $ampsPlate > 0
            ? ($plateRequired / $ampsPlate) * 100
            : 0;

        $wattsRequirementPercentage = $wattsInAmps > 0
            ? ($plateRequired / $wattsInAmps) * 100
            : 0;
    @endphp

    <div class="tower-page">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <header class="tower-page-header">

            <div>

                <div class="tower-page-eyebrow">
                    <i class="bi bi-broadcast-pin"></i>
                    Detalhes da torre
                </div>

                <h1 class="tower-page-title">
                    {{ $tower->name }}
                </h1>

                <p class="tower-page-description">
                    Gerencie baterias, equipamentos e placas solares da torre.
                </p>

            </div>

            <div class="tower-header-actions">

                <a
                    href="{{ route('tower.gallery.index', $tower->id) }}"
                    class="btn btn-light">

                    <i class="bi bi-images"></i>
                    Galeria
                </a>

                @can('towers.edit')

                    <button
                        type="button"
                        class="btn dcm-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal{{ $tower->id }}">

                        <i class="bi bi-pencil-square"></i>
                        Editar torre
                    </button>

                @endcan

            </div>

        </header>

        <div class="row tower-cards-row align-items-start">

            {{-- ========================================================
                CARD 1 — BATERIAS
            ========================================================= --}}

            <div class="col-12 col-lg-6">

                <section class="tower-section-card">

                    <div class="tower-section-header">

                        <div class="tower-section-title-area">

                            <span class="tower-section-icon">
                                <i class="bi bi-battery-charging"></i>
                            </span>

                            <div>

                                <h2 class="tower-section-title">
                                    Baterias
                                </h2>

                                <span class="tower-section-description">
                                    Banco de baterias e histórico de produção.
                                </span>

                            </div>

                        </div>

                        <div class="tower-section-actions">

                            @can('towers.manage')

                                <button
                                    type="button"
                                    class="btn btn-light btn-sm"
                                    id="recalculateBatteriesButton"
                                    data-recalculate-url="{{ route('tower.recalcular.baterias', $tower->id) }}">

                                    <i class="bi bi-arrow-repeat"></i>
                                    Recalcular %
                                </button>

                                <button
                                    type="button"
                                    class="btn dcm-btn-primary btn-sm"
                                    onclick="openTowerModal('batteryModal')">

                                    <i class="bi bi-plus-lg"></i>
                                    Adicionar
                                </button>

                            @endcan

                        </div>

                    </div>

                    <div class="tower-table-area">

                        <table class="table tower-table">

                            <thead>

                                <tr>
                                    <th class="battery-col-info">
                                        Informação
                                    </th>

                                    <th class="battery-col-battery">
                                        Bateria
                                    </th>

                                    <th class="battery-col-dates">
                                        Período
                                    </th>

                                    <th class="battery-col-capacity">
                                        Capacidade
                                    </th>

                                    <th class="battery-col-production">
                                        Produção
                                    </th>

                                    <th class="battery-col-actions">
                                        Ações
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse ($batteryProductions as $bp)

                                    @php
                                        $batteryVoltage = (float) ($bp->battery?->voltage ?? 0);
                                        $batteryAmps = (float) ($bp->battery?->amps ?? 0);
                                        $amount = (int) ($bp->amount ?? 0);

                                        $voltageRatio = $batteryVoltage > 0
                                            ? $towerVoltage / $batteryVoltage
                                            : 0;

                                        $totalAmp = $voltageRatio > 0
                                            ? ($amount * $batteryAmps) / $voltageRatio
                                            : 0;

                                        if (
                                            $bp->production_percentage !== null &&
                                            $bp->production_percentage !== ''
                                        ) {
                                            $productionPercentage =
                                                (float) $bp->production_percentage;
                                        } else {
                                            $productionPercentage = $totalAmp > 0
                                                ? ($batteryRequired / $totalAmp) * 100
                                                : 0;
                                        }

                                        $installationDate = $bp->installation_date
                                            ? \Carbon\Carbon::parse($bp->installation_date)
                                            : null;

                                        $removalDate = $bp->removal_date
                                            ? \Carbon\Carbon::parse($bp->removal_date)
                                            : null;

                                        $isActive =
                                            $bp->active === 'yes' &&
                                            is_null($removalDate);

                                        $endDate = $removalDate
                                            ?? ($isActive ? $today : null);

                                        $productionTime = null;

                                        if ($installationDate && $endDate) {
                                            $difference =
                                                $installationDate->diff($endDate);

                                            $productionParts = [];

                                            if ($difference->y > 0) {
                                                $productionParts[] =
                                                    $difference->y . ' ' .
                                                    (
                                                        $difference->y === 1
                                                            ? 'ano'
                                                            : 'anos'
                                                    );
                                            }

                                            if ($difference->m > 0) {
                                                $productionParts[] =
                                                    $difference->m . ' ' .
                                                    (
                                                        $difference->m === 1
                                                            ? 'mês'
                                                            : 'meses'
                                                    );
                                            }

                                            if (empty($productionParts)) {
                                                $days = $installationDate
                                                    ->diffInDays($endDate);

                                                $productionParts[] =
                                                    $days . ' ' .
                                                    (
                                                        $days === 1
                                                            ? 'dia'
                                                            : 'dias'
                                                    );
                                            }

                                            $productionTime =
                                                implode(' e ', $productionParts);
                                        }
                                    @endphp

                                    <tr class="{{ $isActive ? 'tower-active-row' : '' }}">

                                        {{-- INFORMAÇÃO --}}
                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $bp->info ?: 'Sem informação' }}
                                            </span>

                                            <span class="tower-table-secondary">
                                                Quantidade: {{ $amount }}
                                            </span>

                                        </td>

                                        {{-- BATERIA --}}
                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $bp->battery?->mark ?? 'Não encontrada' }}
                                            </span>

                                            <span class="tower-table-secondary">

                                                {{ $bp->battery?->name ?? 'Modelo não informado' }}

                                                @if ($batteryVoltage > 0)
                                                    · {{ number_format(
                                                        $batteryVoltage,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }} V
                                                @endif

                                                · {{ number_format(
                                                    $batteryAmps,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }} Ah

                                            </span>

                                        </td>

                                        {{-- PERÍODO --}}
                                        <td>

                                            <span class="tower-table-date">
                                                <strong>Inst.:</strong>
                                                {{ $installationDate?->format('d/m/Y') ?? '—' }}
                                            </span>

                                            <span class="tower-table-date">
                                                <strong>Rem.:</strong>
                                                {{ $removalDate?->format('d/m/Y') ?? '—' }}
                                            </span>

                                        </td>

                                        {{-- CAPACIDADE --}}
                                        <td>

                                            <span class="tower-value-badge">
                                                {{ number_format(
                                                    $totalAmp,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }} Ah
                                            </span>

                                            @if (
                                                $batteryVoltage > 0 &&
                                                $batteryVoltage != 12
                                            )

                                                <span class="tower-voltage-badge">
                                                    {{ number_format(
                                                        $batteryVoltage,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }} V
                                                </span>

                                            @endif

                                            <span class="tower-table-secondary">
                                                Uso:
                                                {{ number_format(
                                                    $productionPercentage,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) }}%
                                            </span>

                                        </td>

                                        {{-- PRODUÇÃO --}}
                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $productionTime ?? 'Não calculado' }}
                                            </span>

                                            <span class="tower-table-secondary">

                                                @if ($isActive)
                                                    Em produção
                                                @else
                                                    Produção encerrada
                                                @endif

                                            </span>

                                            <div class="mt-1">

                                                @if ($isActive)

                                                    <span class="tower-status tower-status-active">
                                                        <i class="bi bi-check-circle"></i>
                                                        Ativa
                                                    </span>

                                                @else

                                                    <span class="tower-status tower-status-inactive">
                                                        <i class="bi bi-archive"></i>
                                                        Removida
                                                    </span>

                                                @endif

                                            </div>

                                        </td>

                                        {{-- AÇÕES --}}
                                        <td class="text-center">

                                            @can('towers.manage')

                                                <button
                                                    type="button"
                                                    class="edit-btn btn btn-light btn-sm"
                                                    data-id="{{ $bp->id }}"
                                                    title="Editar bateria">

                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                            @endcan

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6" class="tower-empty-state">

                                            <i class="bi bi-battery"></i>

                                            Nenhuma bateria adicionada nesta torre.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>

            </div>

            {{-- ========================================================
                CARD 2 — RESUMO ENERGÉTICO
            ========================================================= --}}

            <div class="col-12 col-lg-6">

                <section class="tower-section-card tower-summary-card">

                    <div class="tower-section-header">

                        <div class="tower-section-title-area">

                            <span class="tower-section-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </span>

                            <div>

                                <h2 class="tower-section-title">
                                    Resumo energético
                                </h2>

                                <span class="tower-section-description">
                                    Consumo, autonomia e geração estimada.
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="tower-summary-grid">

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-sunrise"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Horas de geração
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $hoursGeneration,
                                        2,
                                        ',',
                                        '.'
                                    ) }} h
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-clock-history"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Horas de autonomia
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $hoursAutonomy,
                                        2,
                                        ',',
                                        '.'
                                    ) }} h
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-lightning"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Consumo
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $timeAhConsumption,
                                        2,
                                        ',',
                                        '.'
                                    ) }} Ah/h
                                </strong>

                                <span class="tower-summary-detail">
                                    {{ number_format(
                                        $timeAhConsumption * 24,
                                        2,
                                        ',',
                                        '.'
                                    ) }} Ah/dia
                                </span>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-battery-half"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Bateria necessária
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $batteryRequired,
                                        2,
                                        ',',
                                        '.'
                                    ) }} Ah
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-plug"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Consumo em watts
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $consumptionAhDay,
                                        2,
                                        ',',
                                        '.'
                                    ) }} W
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-speedometer2"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Geração necessária em 5h
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $plateRequired,
                                        2,
                                        ',',
                                        '.'
                                    ) }} Ah
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-sun"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Painéis solares
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $wattsPlate,
                                        0,
                                        ',',
                                        '.'
                                    ) }} W
                                </strong>

                                <span class="tower-summary-detail">
                                    {{ number_format(
                                        $ampsPlate,
                                        2,
                                        ',',
                                        '.'
                                    ) }} A
                                </span>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-activity"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Capacidade das placas
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $plateRequirementPercentage,
                                        2,
                                        ',',
                                        '.'
                                    ) }}%
                                </strong>

                                <span class="tower-summary-detail">
                                    do necessário
                                </span>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-calendar-day"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Geração diária
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $wattsInAmps * $hoursGeneration,
                                        0,
                                        ',',
                                        '.'
                                    ) }} Ah
                                </strong>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-lightning-charge"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Geração instantânea
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $wattsInAmps,
                                        2,
                                        ',',
                                        '.'
                                    ) }} A
                                </strong>

                                <span class="tower-summary-detail">
                                    {{ number_format(
                                        $wattsRequirementPercentage,
                                        2,
                                        ',',
                                        '.'
                                    ) }}% necessário
                                </span>

                            </div>

                        </div>

                        <div class="tower-summary-item">

                            <span class="tower-summary-icon">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </span>

                            <div class="tower-summary-content">

                                <span class="tower-summary-label">
                                    Tensão da torre
                                </span>

                                <strong class="tower-summary-value">
                                    {{ number_format(
                                        $towerVoltage,
                                        0,
                                        ',',
                                        '.'
                                    ) }} V
                                </strong>

                            </div>

                        </div>

                    </div>

                </section>

            </div>
                        {{-- ========================================================
                CARD 3 — EQUIPAMENTOS
            ========================================================= --}}

            <div class="col-12 col-lg-6">

                <section class="tower-section-card">

                    <div class="tower-section-header">

                        <div class="tower-section-title-area">

                            <span class="tower-section-icon">
                                <i class="bi bi-router"></i>
                            </span>

                            <div>

                                <h2 class="tower-section-title">
                                    Equipamentos
                                </h2>

                                <span class="tower-section-description">
                                    Equipamentos instalados e consumo individual.
                                </span>

                            </div>

                        </div>

                        <div class="tower-section-actions">

                            @can('towers.manage')

                                <button
                                    type="button"
                                    class="btn dcm-btn-primary btn-sm"
                                    onclick="openTowerModal('equipmentModal')">

                                    <i class="bi bi-plus-lg"></i>
                                    Adicionar
                                </button>

                            @endcan

                        </div>

                    </div>

                    <div class="tower-table-area">

                        <table class="table tower-table">

                            <thead>

                                <tr>
                                    <th style="width: 24%">
                                        Identificação
                                    </th>

                                    <th style="width: 30%">
                                        Equipamento
                                    </th>

                                    <th style="width: 18%">
                                        Consumo
                                    </th>

                                    <th style="width: 18%">
                                        Situação
                                    </th>

                                    <th
                                        style="width: 10%"
                                        class="text-center">

                                        Ações
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse ($equipmentProductions as $ep)

                                    @php
                                        $equipmentActive =
                                            $ep->active === 'yes';
                                    @endphp

                                    <tr class="{{ $equipmentActive ? 'tower-active-row' : '' }}">

                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $ep->identification ?: 'Sem identificação' }}
                                            </span>

                                        </td>

                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $ep->equipment?->name ?? 'Não encontrado' }}
                                            </span>

                                        </td>

                                        <td>

                                            <span class="tower-value-badge">

                                                <i class="bi bi-lightning-charge"></i>

                                                {{ number_format(
                                                    $ep->equipment?->watts ?? 0,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) }} W

                                            </span>

                                        </td>

                                        <td>

                                            @if ($equipmentActive)

                                                <span class="tower-status tower-status-active">
                                                    <i class="bi bi-check-circle"></i>
                                                    Ativo
                                                </span>

                                            @else

                                                <span class="tower-status tower-status-inactive">
                                                    <i class="bi bi-pause-circle"></i>
                                                    Inativo
                                                </span>

                                            @endif

                                        </td>

                                        <td class="text-center">

                                            @can('towers.manage')

                                                <button
                                                    type="button"
                                                    class="edit-equipment-btn btn btn-light btn-sm"
                                                    data-id="{{ $ep->id }}"
                                                    title="Editar equipamento">

                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                            @endcan

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="5" class="tower-empty-state">

                                            <i class="bi bi-router"></i>

                                            Nenhum equipamento adicionado nesta torre.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>

            </div>

            {{-- ========================================================
                CARD 4 — PLACAS SOLARES
            ========================================================= --}}

            <div class="col-12 col-lg-6">

                <section class="tower-section-card">

                    <div class="tower-section-header">

                        <div class="tower-section-title-area">

                            <span class="tower-section-icon">
                                <i class="bi bi-sun"></i>
                            </span>

                            <div>

                                <h2 class="tower-section-title">
                                    Placas solares
                                </h2>

                                <span class="tower-section-description">
                                    Placas instaladas e capacidade de geração.
                                </span>

                            </div>

                        </div>

                        <div class="tower-section-actions">

                            @can('towers.manage')

                                <button
                                    type="button"
                                    class="btn dcm-btn-primary btn-sm"
                                    onclick="openTowerModal('plateModal')">

                                    <i class="bi bi-plus-lg"></i>
                                    Adicionar
                                </button>

                            @endcan

                        </div>

                    </div>

                    <div class="tower-table-area">

                        <table class="table tower-table">

                            <thead>

                                <tr>
                                    <th style="width: 29%">
                                        Placa
                                    </th>

                                    <th style="width: 23%">
                                        Instalação
                                    </th>

                                    <th style="width: 19%">
                                        Corrente
                                    </th>

                                    <th style="width: 19%">
                                        Potência
                                    </th>

                                    <th
                                        style="width: 10%"
                                        class="text-center">

                                        Ações
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse ($plateProductions as $pp)

                                    @php
                                        $plateInstallationDate =
                                            $pp->installation_date
                                                ? \Carbon\Carbon::parse(
                                                    $pp->installation_date
                                                )
                                                : null;
                                    @endphp

                                    <tr>

                                        <td>

                                            <span class="tower-table-primary">
                                                {{ $pp->plate?->name ?? 'Não encontrada' }}
                                            </span>

                                        </td>

                                        <td>

                                            <span class="tower-table-date">
                                                {{ $plateInstallationDate?->format('d/m/Y') ?? 'Não informada' }}
                                            </span>

                                        </td>

                                        <td>

                                            <span class="tower-value-badge">

                                                {{ number_format(
                                                    $pp->plate?->amps ?? 0,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) }} A

                                            </span>

                                        </td>

                                        <td>

                                            <span class="tower-value-badge">

                                                {{ number_format(
                                                    $pp->plate?->watts ?? 0,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }} W

                                            </span>

                                        </td>

                                        <td class="text-center">

                                            @can('towers.manage')

                                                <button
                                                    type="button"
                                                    class="delete-plate-btn btn btn-outline-danger btn-sm"
                                                    data-id="{{ $pp->id }}"
                                                    title="Excluir placa">

                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            @endcan

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="5" class="tower-empty-state">

                                            <i class="bi bi-sun"></i>

                                            Nenhuma placa solar adicionada nesta torre.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>

            </div>

        </div>

    </div>

    {{-- ================================================================
        MODAIS
    ================================================================= --}}

    @include('tower.form.addequipmenttower')
    @include('tower.form.addbatterytower')
    @include('tower.form.addplatetower')
    @include('tower.form.editbatterytower')
    @include('tower.form.editequipmenttower')
    @include('tower.form.edittower')

@push('scripts')

    <script>
        function openTowerModal(modalId) {

            const modalElement =
                document.getElementById(modalId);

            if (!modalElement) {
                console.error(
                    'Modal não encontrado:',
                    modalId
                );

                return;
            }

            if (
                typeof bootstrap !== 'undefined' &&
                modalElement.classList.contains('modal')
            ) {
                bootstrap.Modal
                    .getOrCreateInstance(modalElement)
                    .show();

                return;
            }

            modalElement.style.display = 'block';
        }

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const routes = {
                    battery: {
                        edit: @json(
                            route(
                                'batteryproduction.edit',
                                ['id' => '__ID__']
                            )
                        ),

                        update: @json(
                            route(
                                'batteryproduction.update',
                                ['id' => '__ID__']
                            )
                        ),

                        destroy: @json(
                            route(
                                'batteryproduction.destroy',
                                ['id' => '__ID__']
                            )
                        )
                    },

                    equipment: {
                        edit: @json(
                            route(
                                'equipmentproduction.edit',
                                ['id' => '__ID__']
                            )
                        ),

                        update: @json(
                            route(
                                'equipmentproduction.update',
                                ['id' => '__ID__']
                            )
                        ),

                        destroy: @json(
                            route(
                                'equipmentproduction.destroy',
                                ['id' => '__ID__']
                            )
                        )
                    },

                    plate: {
                        destroy: @json(
                            route(
                                'plateproduction.destroy',
                                ['id' => '__ID__']
                            )
                        )
                    }
                };

                const csrf =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content ||
                    document.querySelector(
                        'input[name="_token"]'
                    )?.value;

                function routeWithId(route, id) {

                    return route.replace(
                        '__ID__',
                        id
                    );
                }

                function formatDateForInput(dateString) {

                    if (!dateString) {
                        return '';
                    }

                    return String(dateString)
                        .split('T')[0];
                }

                async function requestJson(
                    url,
                    options = {}
                ) {

                    const response = await fetch(
                        url,
                        {
                            ...options,

                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                ...(options.headers || {})
                            }
                        }
                    );

                    let data = {};

                    try {
                        data = await response.json();
                    } catch (error) {
                        data = {};
                    }

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            'Não foi possível concluir a operação.'
                        );
                    }

                    return data;
                }

                /*
                |--------------------------------------------------------------------------
                | RECALCULAR BATERIAS
                |--------------------------------------------------------------------------
                */

                const recalculateButton =
                    document.getElementById(
                        'recalculateBatteriesButton'
                    );

                if (recalculateButton) {

                    recalculateButton.addEventListener(
                        'click',
                        function () {

                            const confirmed = confirm(
                                'Deseja fixar o percentual nas baterias antigas?'
                            );

                            if (!confirmed) {
                                return;
                            }

                            window.location.href =
                                recalculateButton.dataset
                                    .recalculateUrl;
                        }
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | EXCLUIR PLACA
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll(
                        '.delete-plate-btn'
                    )
                    .forEach(function (button) {

                        button.addEventListener(
                            'click',
                            async function () {

                                const confirmed = confirm(
                                    'Tem certeza que deseja excluir esta placa?'
                                );

                                if (!confirmed) {
                                    return;
                                }

                                const originalHtml =
                                    button.innerHTML;

                                button.disabled = true;

                                button.innerHTML = `
                                    <span
                                        class="spinner-border spinner-border-sm"
                                        aria-hidden="true">
                                    </span>
                                `;

                                try {

                                    await requestJson(
                                        routeWithId(
                                            routes.plate.destroy,
                                            button.dataset.id
                                        ),
                                        {
                                            method: 'POST',

                                            headers: {
                                                'X-CSRF-TOKEN':
                                                    csrf,

                                                'X-HTTP-Method-Override':
                                                    'DELETE'
                                            }
                                        }
                                    );

                                    window.location.reload();

                                } catch (error) {

                                    alert(error.message);

                                    button.disabled = false;
                                    button.innerHTML =
                                        originalHtml;
                                }
                            }
                        );
                    });

                /*
                |--------------------------------------------------------------------------
                | ABRIR EDIÇÃO DA BATERIA
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('.edit-btn')
                    .forEach(function (button) {

                        button.addEventListener(
                            'click',
                            async function () {

                                button.disabled = true;

                                try {

                                    const batteryProduction =
                                        await requestJson(
                                            routeWithId(
                                                routes.battery.edit,
                                                button.dataset.id
                                            )
                                        );

                                    const editId =
                                        document.getElementById(
                                            'edit_id'
                                        );

                                    const editBattery =
                                        document.getElementById(
                                            'edit_battery_id'
                                        );

                                    const editInfo =
                                        document.getElementById(
                                            'edit_info'
                                        );

                                    const editAmount =
                                        document.getElementById(
                                            'edit_amount'
                                        );

                                    const editInstallation =
                                        document.getElementById(
                                            'edit_installation_date'
                                        );

                                    const editRemoval =
                                        document.getElementById(
                                            'edit_removal_date'
                                        );

                                    const editActive =
                                        document.getElementById(
                                            'edit_active'
                                        );

                                    if (editId) {
                                        editId.value =
                                            batteryProduction.id ?? '';
                                    }

                                    if (editBattery) {
                                        editBattery.value =
                                            batteryProduction
                                                .battery_id ?? '';
                                    }

                                    if (editInfo) {
                                        editInfo.value =
                                            batteryProduction.info ?? '';
                                    }

                                    if (editAmount) {
                                        editAmount.value =
                                            batteryProduction.amount ?? '';
                                    }

                                    if (editInstallation) {
                                        editInstallation.value =
                                            formatDateForInput(
                                                batteryProduction
                                                    .installation_date
                                            );
                                    }

                                    if (editRemoval) {
                                        editRemoval.value =
                                            formatDateForInput(
                                                batteryProduction
                                                    .removal_date
                                            );
                                    }

                                    if (editActive) {
                                        editActive.value =
                                            batteryProduction.active ??
                                            'yes';
                                    }

                                    openTowerModal(
                                        'editModal'
                                    );

                                } catch (error) {

                                    alert(error.message);

                                } finally {

                                    button.disabled = false;
                                }
                            }
                        );
                    });

                /*
                |--------------------------------------------------------------------------
                | ATUALIZAR BATERIA
                |--------------------------------------------------------------------------
                */

                const batteryForm =
                    document.getElementById(
                        'editForm'
                    );

                if (batteryForm) {

                    batteryForm.addEventListener(
                        'submit',
                        async function (event) {

                            event.preventDefault();

                            const submitButton =
                                batteryForm.querySelector(
                                    '[type="submit"]'
                                );

                            if (submitButton) {
                                submitButton.disabled = true;
                            }

                            try {

                                const id =
                                    document.getElementById(
                                        'edit_id'
                                    )?.value;

                                await requestJson(
                                    routeWithId(
                                        routes.battery.update,
                                        id
                                    ),
                                    {
                                        method: 'POST',

                                        headers: {
                                            'X-CSRF-TOKEN':
                                                csrf,

                                            'X-HTTP-Method-Override':
                                                'PUT'
                                        },

                                        body:
                                            new FormData(
                                                batteryForm
                                            )
                                    }
                                );

                                window.location.reload();

                            } catch (error) {

                                alert(error.message);

                                if (submitButton) {
                                    submitButton.disabled =
                                        false;
                                }
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | EXCLUIR BATERIA
                    |--------------------------------------------------------------------------
                    */

                    const deleteBatteryButton =
                        document.getElementById(
                            'deleteButton'
                        );

                    if (deleteBatteryButton) {

                        deleteBatteryButton
                            .addEventListener(
                                'click',
                                async function () {

                                    const confirmed =
                                        confirm(
                                            'Tem certeza que deseja excluir esta bateria?'
                                        );

                                    if (!confirmed) {
                                        return;
                                    }

                                    deleteBatteryButton
                                        .disabled = true;

                                    try {

                                        const id =
                                            document.getElementById(
                                                'edit_id'
                                            )?.value;

                                        await requestJson(
                                            routeWithId(
                                                routes.battery.destroy,
                                                id
                                            ),
                                            {
                                                method: 'POST',

                                                headers: {
                                                    'X-CSRF-TOKEN':
                                                        csrf,

                                                    'X-HTTP-Method-Override':
                                                        'DELETE'
                                                }
                                            }
                                        );

                                        window.location.reload();

                                    } catch (error) {

                                        alert(error.message);

                                        deleteBatteryButton
                                            .disabled = false;
                                    }
                                }
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ABRIR EDIÇÃO DO EQUIPAMENTO
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll(
                        '.edit-equipment-btn'
                    )
                    .forEach(function (button) {

                        button.addEventListener(
                            'click',
                            async function () {

                                button.disabled = true;

                                try {

                                    const equipmentProduction =
                                        await requestJson(
                                            routeWithId(
                                                routes.equipment.edit,
                                                button.dataset.id
                                            )
                                        );

                                    const hiddenId =
                                        document.getElementById(
                                            'edit_equipment_id_hidden'
                                        );

                                    const equipmentId =
                                        document.getElementById(
                                            'edit_equipment_id'
                                        );

                                    const identification =
                                        document.getElementById(
                                            'edit_identification'
                                        );

                                    const active =
                                        document.getElementById(
                                            'edit_equipment_active'
                                        );

                                    if (hiddenId) {
                                        hiddenId.value =
                                            equipmentProduction.id ??
                                            '';
                                    }

                                    if (equipmentId) {
                                        equipmentId.value =
                                            equipmentProduction
                                                .equipment_id ?? '';
                                    }

                                    if (identification) {
                                        identification.value =
                                            equipmentProduction
                                                .identification ?? '';
                                    }

                                    if (active) {
                                        active.value =
                                            equipmentProduction
                                                .active ?? 'yes';
                                    }

                                    openTowerModal(
                                        'editEquipmentModal'
                                    );

                                } catch (error) {

                                    alert(error.message);

                                } finally {

                                    button.disabled = false;
                                }
                            }
                        );
                    });

                /*
                |--------------------------------------------------------------------------
                | ATUALIZAR EQUIPAMENTO
                |--------------------------------------------------------------------------
                */

                const equipmentForm =
                    document.getElementById(
                        'editEquipmentForm'
                    );

                if (equipmentForm) {

                    equipmentForm.addEventListener(
                        'submit',
                        async function (event) {

                            event.preventDefault();

                            const submitButton =
                                equipmentForm.querySelector(
                                    '[type="submit"]'
                                );

                            if (submitButton) {
                                submitButton.disabled = true;
                            }

                            try {

                                const id =
                                    document.getElementById(
                                        'edit_equipment_id_hidden'
                                    )?.value;

                                await requestJson(
                                    routeWithId(
                                        routes.equipment.update,
                                        id
                                    ),
                                    {
                                        method: 'POST',

                                        headers: {
                                            'X-CSRF-TOKEN':
                                                csrf,

                                            'X-HTTP-Method-Override':
                                                'PUT'
                                        },

                                        body:
                                            new FormData(
                                                equipmentForm
                                            )
                                    }
                                );

                                window.location.reload();

                            } catch (error) {

                                alert(error.message);

                                if (submitButton) {
                                    submitButton.disabled =
                                        false;
                                }
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | EXCLUIR EQUIPAMENTO
                    |--------------------------------------------------------------------------
                    */

                    const deleteEquipmentButton =
                        document.getElementById(
                            'deleteEquipmentBtn'
                        );

                    if (deleteEquipmentButton) {

                        deleteEquipmentButton
                            .addEventListener(
                                'click',
                                async function () {

                                    const confirmed =
                                        confirm(
                                            'Deseja realmente excluir este equipamento?'
                                        );

                                    if (!confirmed) {
                                        return;
                                    }

                                    deleteEquipmentButton
                                        .disabled = true;

                                    try {

                                        const id =
                                            document.getElementById(
                                                'edit_equipment_id_hidden'
                                            )?.value;

                                        await requestJson(
                                            routeWithId(
                                                routes.equipment.destroy,
                                                id
                                            ),
                                            {
                                                method: 'POST',

                                                headers: {
                                                    'X-CSRF-TOKEN':
                                                        csrf,

                                                    'X-HTTP-Method-Override':
                                                        'DELETE'
                                                }
                                            }
                                        );

                                        window.location.reload();

                                    } catch (error) {

                                        alert(error.message);

                                        deleteEquipmentButton
                                            .disabled = false;
                                    }
                                }
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ABRIR MODAL QUANDO HOUVER ERRO DE VALIDAÇÃO
                |--------------------------------------------------------------------------
                */

                @if ($errors->any())

                    openTowerModal('batteryModal');

                @endif
            }
        );
    </script>

@endpush
@endsection