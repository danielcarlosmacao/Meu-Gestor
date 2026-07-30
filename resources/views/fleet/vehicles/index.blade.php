@extends('layouts.header')

@section('title', 'Controle de frota')

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
                        <i class="bi bi-truck"></i>
                    </span>

                    <div>
                        <h2 class="fleet-page-title">
                            Controle de Frotas
                        </h2>

                        <p class="fleet-page-subtitle">
                            Gerencie os veículos e acompanhe suas manutenções.
                        </p>
                    </div>

                </div>

                <div class="fleet-page-actions">

                    <div class="fleet-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar veículo..."
                            data-fleet-table-search="#fleetVehiclesTable">

                    </div>

                    @can('fleets.create')
                        <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addVehicleModal">

                            <i class="bi bi-plus-lg"></i>
                            Novo veículo
                        </button>
                    @endcan

                </div>

            </div>

            {{-- ============================================================
            TABELA
        ============================================================= --}}

            <div class="fleet-card">

                <div class="fleet-card-header">

                    <div>
                        <h5 class="fleet-card-title">
                            Veículos cadastrados
                        </h5>

                        <p class="fleet-card-subtitle">
                            Clique no modelo para visualizar as manutenções.
                        </p>
                    </div>

                    <span class="badge text-bg-light border">
                        {{ $vehicles->total() }} veículo{{ $vehicles->total() === 1 ? '' : 's' }}
                    </span>

                </div>

                <div class="fleet-card-body-flush">

                    @if ($vehicles->count())

                        <div class="fleet-table-responsive">

                            <table class="table fleet-table" id="fleetVehiclesTable" data-fleet-sortable>

                                <thead>
                                    <tr>
                                        <th style="width: 55px;" aria-label="Cor">
                                        </th>

                                        <th data-fleet-sort="text">
                                            Modelo
                                        </th>

                                        <th data-fleet-sort="number">
                                            Ano
                                        </th>

                                        <th data-fleet-sort="text">
                                            Placa
                                        </th>

                                        <th data-fleet-sort="text">
                                            Marca
                                        </th>

                                        <th data-fleet-sort="text">
                                            Tipo
                                        </th>

                                        <th data-fleet-sort="text">
                                            Combustível
                                        </th>

                                        <th data-fleet-sort="text">
                                            Status
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($vehicles as $vehicle)
                                        <tr>

                                            {{-- Cor --}}

                                            <td>

                                                <span class="fleet-color-preview"
                                                    style="background-color: {{ $vehicle->color ?: '#000000' }};"
                                                    title="Cor do veículo">
                                                </span>

                                            </td>

                                            {{-- Modelo --}}

                                            <td>

                                                <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}"
                                                    class="fleet-table-link">

                                                    {{ $vehicle->model }}

                                                    <i class="bi bi-box-arrow-up-right small ms-1"></i>
                                                </a>

                                            </td>

                                            {{-- Ano --}}

                                            <td data-sort-value="{{ $vehicle->year }}">
                                                {{ $vehicle->year }}
                                            </td>

                                            {{-- Placa --}}

                                            <td data-sort-value="{{ $vehicle->license_plate }}">

                                                <span class="fleet-plate">
                                                    {{ $vehicle->license_plate }}
                                                </span>

                                            </td>

                                            {{-- Marca --}}

                                            <td>
                                                {{ $vehicle->brand }}
                                            </td>

                                            {{-- Tipo --}}

                                            <td>
                                                {{ __('vehicle_types.' . $vehicle->type) }}
                                            </td>

                                            {{-- Combustível --}}

                                            <td>
                                                {{ $vehicle->fuel_type }}
                                            </td>

                                            {{-- Status --}}

                                            <td>

                                                @if ($vehicle->status === 'active')
                                                    <span class="fleet-badge fleet-badge-success">
                                                        {{ __('status.' . $vehicle->status) }}
                                                    </span>
                                                @else
                                                    <span class="fleet-badge fleet-badge-danger">
                                                        {{ __('status.' . $vehicle->status) }}
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Ações --}}

                                            <td>

                                                <div class="fleet-actions">

                                                    <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}"
                                                        class="btn btn-light btn-sm fleet-btn-icon" data-bs-toggle="tooltip"
                                                        title="Ver manutenções">

                                                        <i class="bi bi-tools"></i>
                                                    </a>

                                                    @can('fleets.edit')
                                                        <button type="button" class="btn btn-warning btn-sm fleet-btn-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editVehicleModal{{ $vehicle->id }}"
                                                            title="Editar veículo">

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
                                    <i class="bi bi-truck"></i>
                                </span>

                                <h5 class="fw-bold">
                                    Nenhum veículo cadastrado
                                </h5>

                                <p class="mb-0">
                                    Cadastre o primeiro veículo para iniciar o controle da frota.
                                </p>

                                @can('fleets.create')
                                    <button type="button" class="btn dcm-btn-primary mt-3" data-bs-toggle="modal"
                                        data-bs-target="#addVehicleModal">

                                        <i class="bi bi-plus-lg"></i>
                                        Cadastrar veículo
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

            @if ($vehicles->hasPages())
                <div class="fleet-pagination">
                    {{ $vehicles->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================= --}}

    @foreach ($vehicles as $vehicle)
        @include('fleet.form.vehicles', [
            'vehicle' => $vehicle,
        ])
    @endforeach

    {{-- ================================================================
    MODAL DE ADIÇÃO
================================================================= --}}

    @include('fleet.form.vehicles', [
        'vehicle' => null,
    ])

    <script src="{{ asset('js/fleet-module.js') }}"></script>

@endsection
