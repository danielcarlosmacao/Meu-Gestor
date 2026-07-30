@extends('layouts.header')

@section('title', 'Manutenções de Veículos')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/fleet-module.css') }}">

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
                            Manutenções de Veículos
                        </h2>

                        <p class="fleet-page-subtitle">
                            Acompanhe os serviços, custos e manutenções realizadas na frota.
                        </p>

                    </div>

                </div>

                <div class="fleet-page-actions">

                    <div class="fleet-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar manutenção..."
                            data-fleet-table-search="#fleetMaintenancesTable">

                    </div>

                    @can('fleets.create')
                        <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addMaintenanceModal">

                            <i class="bi bi-plus-lg"></i>
                            Nova manutenção
                        </button>
                    @endcan

                </div>

            </div>

            {{-- ============================================================
            RESUMO
        ============================================================= --}}

            <div class="fleet-summary-grid">

                <div class="fleet-summary-card">

                    <div class="fleet-summary-icon">
                        <i class="bi bi-tools"></i>
                    </div>

                    <div>

                        <span class="fleet-summary-label">
                            Manutenções
                        </span>

                        <strong class="fleet-summary-value">
                            {{ $maintenances->total() }}
                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <div class="fleet-summary-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>

                        <span class="fleet-summary-label">
                            Valor desta página
                        </span>

                        <strong class="fleet-summary-value">
                            R$ {{ number_format($maintenances->sum('cost'), 2, ',', '.') }}
                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <div class="fleet-summary-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>

                        <span class="fleet-summary-label">
                            Concluídas
                        </span>

                        <strong class="fleet-summary-value">
                            {{ $maintenances->filter(fn($item) => strtolower($item->status) === 'completed')->count() }}
                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <div class="fleet-summary-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div>

                        <span class="fleet-summary-label">
                            Pendentes
                        </span>

                        <strong class="fleet-summary-value">
                            {{ $maintenances->filter(fn($item) => strtolower($item->status) !== 'completed')->count() }}
                        </strong>

                    </div>

                </div>

            </div>

            {{-- ============================================================
            LISTAGEM
        ============================================================= --}}

            <div class="fleet-card">

                <div class="fleet-card-header">

                    <div>

                        <h5 class="fleet-card-title">
                            Histórico de manutenções
                        </h5>

                        <p class="fleet-card-subtitle">
                            Relação das manutenções cadastradas para os veículos.
                        </p>

                    </div>

                    <span class="badge text-bg-light border">

                        {{ $maintenances->total() }}

                        {{ $maintenances->total() === 1 ? 'registro' : 'registros' }}

                    </span>

                </div>

                <div class="fleet-card-body-flush">

                    @if ($maintenances->count())

                        <div class="fleet-table-responsive">

                            <table class="table fleet-table" id="fleetMaintenancesTable" data-fleet-sortable>

                                <thead>

                                    <tr>

                                        <th style="width: 45px;">
                                            Cor
                                        </th>

                                        <th data-fleet-sort="text">
                                            Veículo
                                        </th>

                                        <th data-fleet-sort="date">
                                            Data
                                        </th>

                                        <th data-fleet-sort="text">
                                            Tipo
                                        </th>

                                        <th data-fleet-sort="number">
                                            Quilometragem
                                        </th>

                                        <th data-fleet-sort="number">
                                            Valor
                                        </th>

                                        <th data-fleet-sort="text">
                                            Status
                                        </th>

                                        <th data-fleet-sort="text">
                                            Oficina
                                        </th>

                                        <th>
                                            Serviços
                                        </th>

                                        <th>
                                            Informações
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($maintenances as $maintenance)
                                        @php
                                            $fullInfo = $maintenance->parts_used ?? '';
                                            $shortInfo = \Illuminate\Support\Str::limit($fullInfo, 35, '...');

                                            $status = strtolower($maintenance->status ?? '');
                                            $type = strtolower($maintenance->type ?? '');
                                        @endphp

                                        <tr>

                                            {{-- Cor do veículo --}}
                                            <td>

                                                <div style="
                                                    width:22px;
                                                    height:22px;
                                                    border-radius:6px;
                                                    background:{{ $maintenance->vehicle->color ?? '#DDD' }};
                                                    border:1px solid #bdbdbd;
                                                    margin:auto;
                                                "
                                                    title="{{ $maintenance->vehicle->color }}">
                                                </div>

                                            </td>

                                            {{-- Veículo --}}

                                            <td>

                                                @if ($maintenance->vehicle)
                                                    <a href="{{ route('fleet.vehicle.maintenances', $maintenance->vehicle->id) }}"
                                                        class="text-decoration-none">

                                                        <strong class="d-block text-body">
                                                            {{ $maintenance->vehicle->model ?? 'Sem modelo' }}
                                                            {{ $maintenance->vehicle->year ?? '' }}
                                                        </strong>

                                                        <small class="text-secondary">

                                                            @if ($maintenance->vehicle->license_plate)
                                                                <span class="fleet-plate-badge">
                                                                    {{ strtoupper($maintenance->vehicle->license_plate) }}
                                                                </span>
                                                            @else
                                                                Placa não informada
                                                            @endif

                                                        </small>

                                                    </a>
                                                @else
                                                    <span class="text-secondary">
                                                        Veículo removido
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Data --}}

                                            <td data-sort-value="{{ $maintenance->maintenance_date }}">

                                                @if ($maintenance->maintenance_date)
                                                    <span class="d-block fw-semibold">
                                                        {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Tipo --}}

                                            <td data-sort-value="{{ $type }}">

                                                @switch($type)
                                                    @case('preventive')
                                                        <span class="fleet-badge fleet-badge-info">
                                                            <i class="bi bi-shield-check"></i>
                                                            {{ __('typemaintenances.' . $maintenance->type) }}
                                                        </span>
                                                    @break

                                                    @case('corrective')
                                                        <span class="fleet-badge fleet-badge-danger">
                                                            <i class="bi bi-wrench-adjustable"></i>
                                                            {{ __('typemaintenances.' . $maintenance->type) }}
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="fleet-badge fleet-badge-secondary">
                                                            <i class="bi bi-tools"></i>
                                                            {{ __('typemaintenances.' . $maintenance->type) }}
                                                        </span>
                                                @endswitch

                                            </td>

                                            {{-- Quilometragem --}}

                                            <td data-sort-value="{{ $maintenance->mileage ?? 0 }}">

                                                <span class="fw-semibold">
                                                    {{ number_format($maintenance->mileage ?? 0, 0, ',', '.') }}
                                                </span>

                                                <small class="text-secondary">
                                                    km
                                                </small>

                                            </td>

                                            {{-- Valor --}}

                                            <td data-sort-value="{{ $maintenance->cost ?? 0 }}">

                                                <strong>
                                                    R$ {{ number_format($maintenance->cost ?? 0, 2, ',', '.') }}
                                                </strong>

                                            </td>

                                            {{-- Status --}}

                                            <td data-sort-value="{{ $status }}">

                                                @switch($status)
                                                    @case('completed')
                                                    @case('concluded')

                                                    @case('finished')
                                                        <span class="fleet-badge fleet-badge-success">
                                                            <i class="bi bi-check-circle"></i>
                                                            {{ __('status.' . $maintenance->status) }}
                                                        </span>
                                                    @break

                                                    @case('pending')
                                                    @case('scheduled')
                                                        <span class="fleet-badge fleet-badge-warning">
                                                            <i class="bi bi-clock"></i>
                                                            {{ __('status.' . $maintenance->status) }}
                                                        </span>
                                                    @break

                                                    @case('canceled')
                                                    @case('cancelled')
                                                        <span class="fleet-badge fleet-badge-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ __('status.' . $maintenance->status) }}
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="fleet-badge fleet-badge-secondary">
                                                            {{ __('status.' . $maintenance->status) }}
                                                        </span>
                                                @endswitch

                                            </td>

                                            {{-- Oficina --}}

                                            <td>

                                                @if ($maintenance->workshop)
                                                    <div class="d-flex align-items-center gap-2">

                                                        <i class="bi bi-building-gear text-secondary"></i>

                                                        <span>
                                                            {{ is_object($maintenance->workshop) ? $maintenance->workshop->name : $maintenance->workshop }}
                                                        </span>

                                                    </div>
                                                @else
                                                    <span class="text-secondary">
                                                        Não informada
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Serviços --}}

                                            <td>

                                                @if ($maintenance->services->count())
                                                    <div class="d-flex flex-wrap gap-1">

                                                        @foreach ($maintenance->services as $service)
                                                            <span class="fleet-service-badge">
                                                                {{ $service->name }}
                                                            </span>
                                                        @endforeach

                                                    </div>
                                                @else
                                                    <span class="text-secondary">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Informações --}}

                                            <td style="min-width: 210px;">

                                                @if ($fullInfo)
                                                    <div class="fleet-expandable-text" data-expandable-text>

                                                        <span data-text-short>
                                                            {{ $shortInfo }}
                                                        </span>

                                                        <span data-text-full class="d-none">

                                                            {{ $fullInfo }}
                                                        </span>

                                                        @if (mb_strlen($fullInfo) > 35)
                                                            <button type="button" class="btn btn-link btn-sm p-0 ms-1"
                                                                data-text-toggle>

                                                                Mais
                                                            </button>
                                                        @endif

                                                    </div>
                                                @else
                                                    <span class="text-secondary">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Ações --}}

                                            <td>

                                                <div class="fleet-actions">

                                                    @can('fleets.edit')
                                                        <button type="button" class="btn btn-warning btn-sm fleet-btn-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMaintenanceModal{{ $maintenance->id }}"
                                                            title="Editar manutenção">

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
                                    Nenhuma manutenção cadastrada
                                </h5>

                                <p class="mb-0">
                                    Registre as manutenções realizadas nos veículos da frota.
                                </p>

                                @can('fleets.create')
                                    <button type="button" class="btn dcm-btn-primary mt-3" data-bs-toggle="modal"
                                        data-bs-target="#addMaintenanceModal">

                                        <i class="bi bi-plus-lg"></i>
                                        Cadastrar manutenção
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

            @if ($maintenances->hasPages())
                <div class="fleet-pagination">
                    {{ $maintenances->withQueryString()->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ================================================================
    MODAIS DE MANUTENÇÃO
================================================================= --}}

    @include('fleet.form.vehicle_maintenances', [
        'vehicles' => $vehicles,
        'vehicleServices' => $vehicleServices,
        'workshops' => $workshops,
        'maintenances' => $maintenances,
    ])

    <script>
        window.maxMileages = @json($maxMileages ?? []);
    </script>

    <script src="{{ asset('js/fleet-module.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expandableTexts = document.querySelectorAll('[data-expandable-text]');

            expandableTexts.forEach(function(container) {
                const shortText = container.querySelector('[data-text-short]');
                const fullText = container.querySelector('[data-text-full]');
                const toggleButton = container.querySelector('[data-text-toggle]');

                if (!shortText || !fullText || !toggleButton) {
                    return;
                }

                toggleButton.addEventListener('click', function() {
                    const isExpanded = !fullText.classList.contains('d-none');

                    if (isExpanded) {
                        fullText.classList.add('d-none');
                        shortText.classList.remove('d-none');
                        toggleButton.textContent = 'Mais';
                    } else {
                        shortText.classList.add('d-none');
                        fullText.classList.remove('d-none');
                        toggleButton.textContent = 'Menos';
                    }
                });
            });
        });
    </script>

@endsection
