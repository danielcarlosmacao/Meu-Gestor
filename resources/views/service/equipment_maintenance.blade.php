@extends('layouts.header')

@section('title', 'Manutenção de Equipamentos')

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
                        <i class="bi bi-pc-display-horizontal"></i>
                    </span>

                    <div>

                        <h2 class="service-page-title">
                            Manutenção de Equipamentos
                        </h2>

                        <p class="service-page-subtitle">
                            Acompanhe equipamentos enviados para assistência, custos e soluções.
                        </p>

                    </div>

                </div>

                <div class="service-page-actions">

                    <div class="service-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar equipamento..."
                            aria-label="Pesquisar manutenção de equipamento"
                            data-service-table-search="#equipmentMaintenancesTable">

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

                $pendingReceiptCount = $maintenances
                    ->filter(function ($maintenance) {
                        return $maintenance->date_send && !$maintenance->date_received;
                    })
                    ->count();
            @endphp

            <div class="service-summary-grid">

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-pc-display"></i>
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
                        <i class="bi bi-truck"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Aguardando recebimento
                        </span>

                        <span class="service-summary-value">
                            {{ $pendingReceiptCount }}
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
                            Equipamentos em manutenção
                        </h5>

                        <p class="service-card-subtitle">
                            Registros de envio, recebimento e solução dos equipamentos.
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

                            <table id="equipmentMaintenancesTable" class="service-table" data-service-sortable>

                                <thead>

                                    <tr>

                                        <th data-service-sort="text">
                                            Cliente
                                        </th>

                                        <th data-service-sort="text">
                                            Assistência técnica
                                        </th>

                                        <th data-service-sort="text">
                                            Equipamento
                                        </th>

                                        <th data-service-sort="text">
                                            Erro
                                        </th>

                                        <th data-service-sort="date">
                                            Manutenção
                                        </th>

                                        <th data-service-sort="date">
                                            Envio
                                        </th>

                                        <th data-service-sort="date">
                                            Recebimento
                                        </th>

                                        <th data-service-sort="text">
                                            Solução
                                        </th>

                                        <th data-service-sort="number">
                                            Custo empresa
                                        </th>

                                        <th data-service-sort="number">
                                            Custo cliente
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($maintenances as $maintenance)
                                        @php
                                            $fullText = $maintenance->solution ?? '';

                                            $shortText = \Illuminate\Support\Str::limit($fullText, 60, '...');

                                            $enterpriseCost = (float) ($maintenance->cost_enterprise ?? 0);

                                            $clientCost = (float) ($maintenance->cost_client ?? 0);
                                        @endphp

                                        <tr
                                            data-search-value="
                                            {{ $maintenance->serviceClient->name ?? '' }}
                                            {{ $maintenance->assistance }}
                                            {{ $maintenance->equipment }}
                                            {{ $maintenance->erro }}
                                            {{ $maintenance->solution }}
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
                                                            Registro #{{ $maintenance->id }}
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                @if ($maintenance->assistance)
                                                    {{ $maintenance->assistance }}
                                                @else
                                                    <span class="service-muted">
                                                        Não informada
                                                    </span>
                                                @endif

                                            </td>

                                            <td>

                                                <span class="service-record-name">
                                                    {{ $maintenance->equipment ?? 'Não informado' }}
                                                </span>

                                            </td>

                                            <td>

                                                @if ($maintenance->erro)
                                                    <span class="service-badge service-badge-danger">
                                                        <i class="bi bi-exclamation-triangle"></i>

                                                        {{ $maintenance->erro }}
                                                    </span>
                                                @else
                                                    <span class="service-muted">
                                                        —
                                                    </span>
                                                @endif

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

                                            <td class="service-date"
                                                data-sort-value="{{ optional($maintenance->date_send)->format('Y-m-d') }}">

                                                @if ($maintenance->date_send)
                                                    {{ $maintenance->date_send->format('d/m/Y') }}
                                                @else
                                                    <span class="service-muted">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            <td class="service-date"
                                                data-sort-value="{{ optional($maintenance->date_received)->format('Y-m-d') }}">

                                                @if ($maintenance->date_received)
                                                    <span class="service-badge service-badge-success">

                                                        <i class="bi bi-check-circle-fill"></i>

                                                        {{ $maintenance->date_received->format('d/m/Y') }}

                                                    </span>
                                                @elseif ($maintenance->date_send)
                                                    <span class="service-badge service-badge-warning">

                                                        <i class="bi bi-hourglass-split"></i>

                                                        Aguardando

                                                    </span>
                                                @else
                                                    <span class="service-muted">
                                                        —
                                                    </span>
                                                @endif

                                            </td>

                                            <td>

                                                @if (\Illuminate\Support\Str::length($fullText) > 60)
                                                    <div class="service-expandable-text" data-service-expandable>

                                                        <span data-text-short>
                                                            {{ $shortText }}
                                                        </span>

                                                        <span class="d-none" data-text-full>

                                                            {{ $fullText }}

                                                        </span>

                                                        <button type="button" class="service-text-toggle ms-1"
                                                            data-text-toggle data-more-text="Mostrar mais"
                                                            data-less-text="Mostrar menos" aria-expanded="false">

                                                            Mostrar mais

                                                        </button>

                                                    </div>
                                                @elseif ($fullText)
                                                    {{ $fullText }}
                                                @else
                                                    <span class="service-muted">
                                                        Sem solução registrada
                                                    </span>
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

                                            <td class="text-end">

                                                <div class="service-actions">

                                                    @can('service.edit')
                                                        <button type="button"
                                                            class="btn btn-outline-warning service-btn-icon"
                                                            title="Editar manutenção"
                                                            aria-label="Editar manutenção {{ $maintenance->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMaintenanceModal{{ $maintenance->id }}">

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
                        <div class="service-empty-state">

                            <span class="service-empty-icon">
                                <i class="bi bi-pc-display-horizontal"></i>
                            </span>

                            <h5>
                                Nenhuma manutenção cadastrada
                            </h5>

                            <p class="mb-3">
                                Cadastre um equipamento para iniciar o acompanhamento da manutenção.
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
        @include('service.forms.equipment_maintenance', [
            'maintenance' => $maintenance,
        ])
    @endforeach

    {{-- ================================================================
    MODAL DE CRIAÇÃO
================================================================ --}}

    @include('service.forms.equipment_maintenance', [
        'maintenance' => null,
    ])

    <script src="{{ asset('js/service-module.js') }}"></script>

@endsection
