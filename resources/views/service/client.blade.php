@extends('layouts.header')

@section('title', 'Clientes de Serviço')

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
                        <i class="bi bi-people"></i>
                    </span>

                    <div>

                        <h2 class="service-page-title">
                            Clientes de Serviço
                        </h2>

                        <p class="service-page-subtitle">
                            Gerencie os clientes vinculados às manutenções e serviços.
                        </p>

                    </div>

                </div>

                <div class="service-page-actions">

                    <div class="service-search-wrapper">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" placeholder="Pesquisar cliente..."
                            aria-label="Pesquisar cliente" data-service-table-search="#serviceClientsTable">

                    </div>

                    @can('service.create')
                        <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addClientModal">

                            <i class="bi bi-plus-lg"></i>

                            Novo cliente

                        </button>
                    @endcan

                </div>

            </div>

            {{-- ============================================================
            RESUMO
        ============================================================= --}}

            <div class="service-summary-grid">

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-people"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Total de clientes
                        </span>

                        <span class="service-summary-value">
                            {{ $clients->total() }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-person-check"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Ativos nesta página
                        </span>

                        <span class="service-summary-value">
                            {{ $clients->where('status', 'active')->count() }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-person-dash"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Inativos nesta página
                        </span>

                        <span class="service-summary-value">
                            {{ $clients->where('status', 'inactive')->count() }}
                        </span>

                    </div>

                </div>

                <div class="service-summary-card">

                    <span class="service-summary-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>

                    <div>

                        <span class="service-summary-label">
                            Página atual
                        </span>

                        <span class="service-summary-value">
                            {{ $clients->currentPage() }}
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
                            Lista de clientes
                        </h5>

                        <p class="service-card-subtitle">
                            Clientes cadastrados no módulo de serviços.
                        </p>

                    </div>

                    <span class="service-badge service-badge-info">
                        {{ $clients->count() }}
                        nesta página
                    </span>

                </div>

                <div class="service-card-body-flush">

                    @if ($clients->count())

                        <div class="service-table-responsive">

                            <table id="serviceClientsTable" class="service-table" data-service-sortable>

                                <thead>

                                    <tr>

                                        <th data-service-sort="text">
                                            Cliente
                                        </th>

                                        <th data-service-sort="text">
                                            Status
                                        </th>

                                        <th class="text-end">
                                            Ações
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($clients as $client)
                                        <tr data-search-value="{{ $client->name }} {{ __('status.' . $client->status) }}">

                                            <td>

                                                <div class="service-record">

                                                    <span class="service-record-icon">
                                                        <i class="bi bi-person"></i>
                                                    </span>

                                                    <div>

                                                        <span class="service-record-name">
                                                            {{ $client->name }}
                                                        </span>

                                                        <span class="service-record-detail">
                                                            Cliente #{{ $client->id }}
                                                        </span>

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                @if ($client->status === 'active')
                                                    <span class="service-badge service-badge-success">

                                                        <i class="bi bi-check-circle-fill"></i>

                                                        {{ __('status.' . $client->status) }}

                                                    </span>
                                                @elseif ($client->status === 'inactive')
                                                    <span class="service-badge service-badge-secondary">

                                                        <i class="bi bi-dash-circle-fill"></i>

                                                        {{ __('status.' . $client->status) }}

                                                    </span>
                                                @else
                                                    <span class="service-badge service-badge-warning">

                                                        {{ __('status.' . $client->status) }}

                                                    </span>
                                                @endif

                                            </td>

                                            <td class="text-end">

                                                <div class="service-actions">

                                                    @can('service.edit')
                                                        <button type="button" class="btn btn-outline-warning service-btn-icon"
                                                            title="Editar cliente"
                                                            aria-label="Editar cliente {{ $client->name }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editClientModal{{ $client->id }}">

                                                            <i class="bi bi-pencil-square"></i>

                                                        </button>
                                                    @endcan

                                                    @can('service.delete')
                                                        <button type="submit" class="btn btn-outline-danger service-btn-icon"
                                                            title="Excluir cliente"
                                                            aria-label="Excluir cliente {{ $client->name }}"
                                                            form="deleteClientForm{{ $client->id }}">

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
                                <i class="bi bi-people"></i>
                            </span>

                            <h5>
                                Nenhum cliente cadastrado
                            </h5>

                            <p class="mb-3">
                                Cadastre o primeiro cliente para começar a registrar serviços.
                            </p>

                            @can('service.create')
                                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addClientModal">

                                    <i class="bi bi-plus-lg"></i>

                                    Cadastrar cliente

                                </button>
                            @endcan

                        </div>

                    @endif

                </div>

            </div>

            {{-- ============================================================
            PAGINAÇÃO
        ============================================================= --}}

            @if ($clients->hasPages())
                <div class="service-pagination">
                    {{ $clients->withQueryString()->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================ --}}

    @foreach ($clients as $client)
        @include('service.forms.client_modal', [
            'client' => $client,
        ])
    @endforeach

    {{-- ================================================================
    FORMULÁRIOS DE EXCLUSÃO
================================================================ --}}

    @foreach ($clients as $client)
        @can('service.delete')
            <form id="deleteClientForm{{ $client->id }}" action="{{ route('service.clients.destroy', $client->id) }}"
                method="POST" class="d-none service-delete-form" data-confirm-delete
                data-confirm-message="Deseja realmente excluir o cliente {{ $client->name }}?">

                @csrf
                @method('DELETE')

            </form>
        @endcan
    @endforeach

    {{-- ================================================================
    MODAL DE CRIAÇÃO
================================================================ --}}

    @include('service.forms.client_modal', [
        'client' => null,
    ])

    <script src="{{ asset('js/service-module.js') }}"></script>

@endsection
