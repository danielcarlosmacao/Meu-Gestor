@extends('layouts.header')

@section('title', 'Manutenções do Veículo')

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
                            Manutenções do veículo
                        </h2>

                        <p class="fleet-page-subtitle">
                            {{ $vehicle->license_plate }}
                            —
                            {{ $vehicle->brand }}
                            {{ $vehicle->model }}
                        </p>

                    </div>

                </div>

                <div class="fleet-page-actions">

                    <button type="button" class="btn btn-light" data-fleet-filter-toggle="#fleetMaintenanceFilter"
                        aria-expanded="{{ request('start_date') || request('end_date') ? 'true' : 'false' }}">

                        <i class="bi bi-funnel"></i>

                        Filtros

                        <i class="bi bi-chevron-down fleet-filter-icon"></i>
                    </button>

                    <a href="{{ route('fleet.vehicles.index') }}" class="btn btn-light">

                        <i class="bi bi-arrow-left"></i>
                        Voltar
                    </a>

                </div>

            </div>

            {{-- ============================================================
            FILTROS
        ============================================================= --}}

            <div class="fleet-filter-card">

                <div id="fleetMaintenanceFilter" class="fleet-filter-body" @if (!request('start_date') && !request('end_date')) hidden @endif>

                    <form method="GET" action="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}"
                        class="row g-3 pt-3">

                        <div class="col-12 col-md-4">

                            <label for="start_date" class="form-label">

                                Data inicial
                            </label>

                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">

                        </div>

                        <div class="col-12 col-md-4">

                            <label for="end_date" class="form-label">

                                Data final
                            </label>

                            <input type="date" id="end_date" name="end_date" class="form-control"
                                value="{{ request('end_date') }}">

                        </div>

                        <div class="col-12 col-md-4 d-flex align-items-end gap-2">

                            <button type="submit" class="btn dcm-btn-primary">

                                <i class="bi bi-search"></i>
                                Filtrar
                            </button>

                            <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}" class="btn btn-light">

                                <i class="bi bi-x-lg"></i>
                                Limpar
                            </a>

                        </div>

                    </form>

                </div>

            </div>

            {{-- ============================================================
            RESUMO
        ============================================================= --}}

            <div class="fleet-summary-grid">

                <div class="fleet-summary-card">

                    <span class="fleet-summary-icon">
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>

                        <span class="fleet-summary-label">
                            Manutenções exibidas
                        </span>

                        <strong class="fleet-summary-value">
                            {{ $maintenances->total() }}
                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <span class="fleet-summary-icon success">
                        <i class="bi bi-cash-coin"></i>
                    </span>

                    <div>

                        <span class="fleet-summary-label">
                            Total de custos
                        </span>

                        <strong class="fleet-summary-value">
                            R$ {{ number_format($totalCost, 2, ',', '.') }}
                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <span class="fleet-summary-icon warning">
                        <i class="bi bi-speedometer2"></i>
                    </span>

                    <div>

                        <span class="fleet-summary-label">
                            Última quilometragem
                        </span>

                        <strong class="fleet-summary-value">

                            @php
                                $lastMileage = $maintenances->whereNotNull('mileage')->max('mileage');
                            @endphp

                            @if ($lastMileage)
                                {{ number_format($lastMileage, 0, ',', '.') }} km
                            @else
                                —
                            @endif

                        </strong>

                    </div>

                </div>

                <div class="fleet-summary-card">

                    <span class="fleet-summary-icon info">
                        <i class="bi bi-calendar-check"></i>
                    </span>

                    <div>

                        <span class="fleet-summary-label">
                            Última manutenção
                        </span>

                        <strong class="fleet-summary-value">

                            @php
                                $lastMaintenance = $maintenances->sortByDesc('maintenance_date')->first();
                            @endphp

                            @if ($lastMaintenance)
                                {{ \Carbon\Carbon::parse($lastMaintenance->maintenance_date)->format('d/m/Y') }}
                            @else
                                —
                            @endif

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
                            Serviços, custos e quilometragem registrados para este veículo.
                        </p>

                    </div>

                    <div class="fleet-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar manutenção..."
                            data-fleet-table-search="#vehicleMaintenanceTable">

                    </div>

                </div>

                <div class="fleet-card-body-flush">

                    @if ($maintenances->count())

                        <div class="fleet-table-responsive">

                            <table class="table fleet-table" id="vehicleMaintenanceTable" data-fleet-sortable>

                                <thead>

                                    <tr>

                                        <th data-fleet-sort="text">
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

                                        <th>
                                            Serviços
                                        </th>

                                        <th>
                                            Informações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($maintenances as $maintenance)
                                        <tr>

                                            {{-- Data --}}

                                            <td data-sort-value="{{ $maintenance->maintenance_date }}">

                                                {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}

                                            </td>

                                            {{-- Tipo --}}

                                            <td>

                                                @if ($maintenance->type === 'preventive')
                                                    <span class="fleet-badge fleet-badge-info">
                                                        {{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}
                                                    </span>
                                                @else
                                                    <span class="fleet-badge fleet-badge-warning">
                                                        {{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Quilometragem --}}

                                            <td class="fleet-mileage" data-sort-value="{{ $maintenance->mileage ?? 0 }}">

                                                @if ($maintenance->mileage !== null)
                                                    {{ number_format($maintenance->mileage, 0, ',', '.') }}
                                                    km
                                                @else
                                                    —
                                                @endif

                                            </td>

                                            {{-- Valor --}}

                                            <td class="fleet-cost" data-sort-value="{{ $maintenance->cost ?? 0 }}">

                                                R$
                                                {{ number_format($maintenance->cost ?? 0, 2, ',', '.') }}

                                            </td>

                                            {{-- Status --}}

                                            <td>

                                                @if ($maintenance->status === 'pending')
                                                    <span class="fleet-badge fleet-badge-warning">
                                                        Pendente
                                                    </span>
                                                @else
                                                    <span class="fleet-badge fleet-badge-success">
                                                        Concluída
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Serviços --}}

                                            <td>

                                                @if ($maintenance->services->count())
                                                    <div class="fleet-service-list">

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

                                            <td>

                                                @if ($maintenance->parts_used)
                                                    <span title="{{ $maintenance->parts_used }}">

                                                        {{ \Illuminate\Support\Str::limit($maintenance->parts_used, 60) }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary">
                                                        —
                                                    </span>
                                                @endif

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
                                    Nenhuma manutenção encontrada
                                </h5>

                                <p class="mb-0">

                                    @if (request('start_date') || request('end_date'))
                                        Não existem registros no período informado.
                                    @else
                                        Nenhuma manutenção foi registrada para este veículo.
                                    @endif

                                </p>

                                @if (request('start_date') || request('end_date'))
                                    <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}"
                                        class="btn btn-light mt-3">

                                        <i class="bi bi-x-lg"></i>
                                        Limpar filtros
                                    </a>
                                @endif

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

    <script src="{{ asset('js/fleet-module.js') }}"></script>

@endsection
