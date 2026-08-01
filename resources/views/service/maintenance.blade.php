@extends('layouts.header')

@section('title', 'Manutenções Gerais')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/service-module.css') }}">

    <div class="service-page">

        <div class="service-container">

            {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

            <div class="service-page-header">

                <div class="service-page-heading">

                    <span class="service-page-icon">
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>

                        <h2 class="service-page-title">
                            Manutenções Gerais
                        </h2>

                        <p class="service-page-subtitle">
                            Acompanhe os serviços realizados, custos internos e valores cobrados.
                        </p>

                    </div>

                </div>

                <div class="service-page-actions">

                    <div class="service-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar manutenção..."
                            aria-label="Pesquisar manutenção" data-service-table-search="#generalMaintenancesTable">

                    </div>

                    @can('service.create')
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

            @php
                $pageEnterpriseCost = $maintenances->sum('cost_enterprise');
                $pageClientCost = $maintenances->sum('cost_client');
                $pageResult = $pageClientCost - $pageEnterpriseCost;
            @endphp

            <div class="service-summary-grid">

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Total de registros
                        </span>

                        <span class="service-summary-value">
                            {{ $maintenances->total() }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-building"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Custo da empresa nesta página
                        </span>

                        <span class="service-summary-value">
                            R$ {{ number_format($pageEnterpriseCost, 2, ',', '.') }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-person-check"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Cobrado nesta página
                        </span>

                        <span class="service-summary-value">
                            R$ {{ number_format($pageClientCost, 2, ',', '.') }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Resultado nesta página
                        </span>

                        <span
                            class="service-summary-value {{ $pageResult < 0 ? 'service-money-negative' : 'service-money-positive' }}">

                            R$ {{ number_format($pageResult, 2, ',', '.') }}

                        </span>

                    </div>

                </div>

            </div>

            {{-- ============================================================
            MENSAGENS
        ============================================================= --}}

            @if (session('success'))
                <div class="service-alert service-alert-success mb-3" data-service-auto-dismiss="5000">

                    <i class="bi bi-check-circle-fill"></i>

                    <div>
                        {{ session('success') }}
                    </div>

                </div>
            @endif

            @if (session('error'))
                <div class="service-alert service-alert-danger mb-3">

                    <i class="bi bi-exclamation-circle-fill"></i>

                    <div>
                        {{ session('error') }}
                    </div>

                </div>
            @endif

            {{-- ============================================================
            TABELA
        ============================================================= --}}

            <div class="service-card">

                <div class="service-card-header">

                    <div>

                        <h5 class="service-card-title">
                            Lista de manutenções
                        </h5>

                        <p class="service-card-subtitle">
                            Serviços gerais registrados no módulo de atendimento.
                        </p>

                    </div>

                    <span class="service-badge service-badge-info">
                        {{ $maintenances->count() }}
                        nesta página
                    </span>

                </div>

                <div class="service-card-body-flush">

                    @if ($maintenances->count())

                        <div class="service-table-responsive">

                            <table id="generalMaintenancesTable" class="service-table" data-service-sortable>

                                <thead>

                                    <tr>

                                        <th data-service-sort="text">
                                            Cliente
                                        </th>

                                        <th data-service-sort="date">
                                            Data
                                        </th>

                                        <th data-service-sort="text">
                                            Serviço
                                        </th>

                                        <th data-service-sort="number">
                                            Custo empresa
                                        </th>

                                        <th data-service-sort="number">
                                            Custo cliente
                                        </th>

                                        <th data-service-sort="number">
                                            Resultado
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($maintenances as $maintenance)
                                        @php
                                            $enterpriseCost = (float) ($maintenance->cost_enterprise ?? 0);
                                            $clientCost = (float) ($maintenance->cost_client ?? 0);
                                            $result = $clientCost - $enterpriseCost;

                                            $serviceText = $maintenance->maintenance ?? '';
                                            $shortServiceText = \Illuminate\Support\Str::limit($serviceText, 70, '...');
                                        @endphp

                                        <tr
                                            data-search-value="
                                            {{ $maintenance->serviceClient->name ?? '' }}
                                            {{ $maintenance->maintenance }}
                                            {{ optional($maintenance->date_maintenance)->format('d/m/Y') }}
                                        ">

                                            <td>

                                                <div class="service-record">

                                                    <span class="service-record-icon">
                                                        <i class="bi bi-person"></i>
                                                    </span>

                                                    <div>

                                                        <span class="service-record-name">
                                                            {{ $maintenance->serviceClient->name ?? 'Não informado' }}
                                                        </span>

                                                        <span class="service-record-detail">
                                                            Manutenção #{{ $maintenance->id }}
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>

                                            <td class="service-date"
                                                data-sort-value="{{ optional($maintenance->date_maintenance)->format('Y-m-d') }}">

                                                @if ($maintenance->date_maintenance)
                                                    {{ $maintenance->date_maintenance->format('d/m/Y') }}
                                                @else
                                                    <span class="service-muted">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            <td>

                                                @if (\Illuminate\Support\Str::length($serviceText) > 70)
                                                    <div class="service-expandable-text" data-service-expandable>

                                                        <span data-text-short>
                                                            {{ $shortServiceText }}
                                                        </span>

                                                        <span class="d-none" data-text-full>

                                                            {{ $serviceText }}

                                                        </span>

                                                        <button type="button" class="service-text-toggle ms-1"
                                                            data-text-toggle data-more-text="Mostrar mais"
                                                            data-less-text="Mostrar menos" aria-expanded="false">

                                                            Mostrar mais

                                                        </button>

                                                    </div>
                                                @else
                                                    {{ $serviceText ?: 'Não informado' }}
                                                @endif

                                            </td>

                                            <td class="service-money service-money-negative"
                                                data-sort-value="{{ $enterpriseCost }}">

                                                R$ {{ number_format($enterpriseCost, 2, ',', '.') }}

                                            </td>

                                            <td class="service-money service-money-positive"
                                                data-sort-value="{{ $clientCost }}">

                                                R$ {{ number_format($clientCost, 2, ',', '.') }}

                                            </td>

                                            <td class="service-money {{ $result < 0 ? 'service-money-negative' : 'service-money-positive' }}"
                                                data-sort-value="{{ $result }}">

                                                R$ {{ number_format($result, 2, ',', '.') }}

                                            </td>

                                            <td class="text-end">

                                                <div class="service-actions">

                                                    @can('service.edit')
                                                        <button type="button" class="btn btn-outline-warning service-btn-icon"
                                                            title="Editar manutenção"
                                                            aria-label="Editar manutenção {{ $maintenance->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">

                                                            <i class="bi bi-pencil-square"></i>

                                                        </button>
                                                    @endcan

                                                    @can('service.delete')
                                                        <button type="submit" class="btn btn-outline-danger service-btn-icon"
                                                            title="Excluir manutenção"
                                                            aria-label="Excluir manutenção {{ $maintenance->id }}"
                                                            form="deleteMaintenanceForm{{ $maintenance->id }}">

                                                            <i class="bi bi-trash"></i>

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
                        <div class="service-empty-state">

                            <span class="service-empty-icon">
                                <i class="bi bi-tools"></i>
                            </span>

                            <h5>
                                Nenhuma manutenção cadastrada
                            </h5>

                            <p class="mb-3">
                                Cadastre uma manutenção para acompanhar os serviços realizados.
                            </p>

                            @can('service.create')
                                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addMaintenanceModal">

                                    <i class="bi bi-plus-lg"></i>

                                    Cadastrar manutenção

                                </button>
                            @endcan

                        </div>

                    @endif

                </div>

            </div>

            {{-- ============================================================
            PAGINAÇÃO
        ============================================================= --}}

            @if ($maintenances->hasPages())
                <div class="service-pagination">
                    {{ $maintenances->withQueryString()->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================ --}}

    @foreach ($maintenances as $maintenance)
        @include('service.forms.maintenance', [
            'maintenance' => $maintenance,
            'clients' => $clients,
        ])
    @endforeach

    {{-- ================================================================
    FORMULÁRIOS DE EXCLUSÃO
================================================================ --}}

    @foreach ($maintenances as $maintenance)
        @can('service.delete')
            <form id="deleteMaintenanceForm{{ $maintenance->id }}"
                action="{{ route('service.maintenances.destroy', $maintenance->id) }}" method="POST"
                class="d-none service-delete-form" data-confirm-delete
                data-confirm-message="Deseja realmente excluir esta manutenção?">

                @csrf
                @method('DELETE')

            </form>
        @endcan
    @endforeach

    {{-- ================================================================
    MODAL DE CRIAÇÃO
================================================================ --}}

    @include('service.forms.maintenance', [
        'maintenance' => null,
        'clients' => $clients,
    ])

    <script src="{{ asset('js/service-module.js') }}"></script>

@endsection
