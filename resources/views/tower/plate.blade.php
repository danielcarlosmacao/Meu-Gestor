@extends('layouts.header')

@section('title', 'Placas solares')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tower-resource-index.css') }}">
@endpush

@section('content')

    <div class="resource-page">

        {{-- ============================================================
            CABEÇALHO
        ============================================================= --}}

        <header class="resource-page-header">

            <div>

                <div class="resource-page-eyebrow">
                    <i class="bi bi-sun"></i>
                    Energia solar das torres
                </div>

                <h1 class="resource-page-title">
                    Placas solares
                </h1>

                <p class="resource-page-description">
                    Gerencie os modelos de placas solares utilizados nas torres,
                    sua potência e corrente elétrica.
                </p>

            </div>

            <div class="resource-header-actions">

                @can('towers.create')
                    <button type="button" class="btn dcm-btn-primary resource-primary-button" data-bs-toggle="modal"
                        data-bs-target="#addPlateModal">

                        <i class="bi bi-plus-lg"></i>
                        Nova placa
                    </button>
                @endcan

            </div>

        </header>

        {{-- ============================================================
            CARDS DE RESUMO
        ============================================================= --}}

        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-sun"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Placas cadastradas
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $plates->total() }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-lightning-charge"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Potência nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ number_format((float) $plates->sum('watts'), 0, ',', '.') }}
                                    W
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-4">

                <div class="resource-summary-card">

                    <div class="card-body">

                        <div class="resource-summary-content">

                            <span class="resource-summary-icon">
                                <i class="bi bi-speedometer2"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Corrente nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ number_format((float) $plates->sum('amps'), 2, ',', '.') }}
                                    A
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ============================================================
            TABELA
        ============================================================= --}}

        <section class="resource-table-card">

            <div class="resource-table-header">

                <div>

                    <h2 class="resource-table-title">
                        Placas cadastradas
                    </h2>

                    <p class="resource-table-subtitle">
                        Consulte a potência e corrente dos modelos cadastrados.
                    </p>

                </div>

                <div class="resource-table-tools">

                    <div class="resource-search">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" data-resource-search="#plateTable"
                            placeholder="Pesquisar placa..." autocomplete="off">

                    </div>

                </div>

            </div>

            <div class="resource-table-responsive">

                <table id="plateTable" class="table resource-table align-middle">

                    <thead>

                        <tr>

                            <th class="resource-sortable ps-4" data-sortable="true">

                                <span>
                                    Nome
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Potência
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Corrente
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="text-center pe-4">
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($plates as $plate)

                            <tr data-resource-row
                                data-search="{{ mb_strtolower(implode(' ', [$plate->name, $plate->watts, $plate->amps])) }}">

                                {{-- NOME --}}
                                <td class="ps-4">

                                    <div class="resource-name">

                                        <span class="resource-name-icon">
                                            <i class="bi bi-sun"></i>
                                        </span>

                                        <span>

                                            <strong>
                                                {{ $plate->name }}
                                            </strong>

                                            <small>
                                                Placa solar
                                            </small>

                                        </span>

                                    </div>

                                </td>

                                {{-- POTÊNCIA --}}
                                <td data-value="{{ $plate->watts }}">

                                    <span class="resource-value-badge">
                                        {{ number_format((float) $plate->watts, 0, ',', '.') }}
                                        W
                                    </span>

                                </td>

                                {{-- CORRENTE --}}
                                <td data-value="{{ $plate->amps }}">

                                    <span class="resource-value-badge">
                                        {{ number_format((float) $plate->amps, 2, ',', '.') }}
                                        A
                                    </span>

                                </td>

                                {{-- AÇÕES --}}
                                <td class="text-center pe-4">

                                    <div class="btn-group">

                                        <button type="button"
                                            class="btn btn-sm dcm-btn-primary
                                                dropdown-toggle resource-actions-button"
                                            data-bs-toggle="dropdown" aria-expanded="false">

                                            <i class="bi bi-gear"></i>
                                            Ações
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            @can('towers.edit')
                                                <li>

                                                    <button type="button" class="dropdown-item"
                                                        data-resource-edit="#editPlateModal"
                                                        data-update-url="{{ route('plate.update', $plate->id) }}"
                                                        data-name="{{ $plate->name }}" data-watts="{{ $plate->watts }}"
                                                        data-amps="{{ $plate->amps }}" data-bs-toggle="modal"
                                                        data-bs-target="#editPlateModal">

                                                        <i class="bi bi-pencil-square"></i>
                                                        Editar
                                                    </button>

                                                </li>
                                            @endcan

                                            @can('towers.delete')
                                                @can('towers.edit')
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                @endcan

                                                <li>

                                                    <button type="button" class="dropdown-item resource-delete-item"
                                                        data-resource-delete="{{ route('plate.destroy', $plate->id) }}"
                                                        data-delete-title="Deseja excluir esta placa solar?"
                                                        data-delete-description="Essa alteração não poderá ser desfeita.">

                                                        <i class="bi bi-trash"></i>
                                                        Excluir
                                                    </button>

                                                </li>
                                            @endcan

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="resource-empty-state">

                                    <span class="resource-empty-icon">
                                        <i class="bi bi-sun"></i>
                                    </span>

                                    <h3>
                                        Nenhuma placa cadastrada
                                    </h3>

                                    <p>
                                        Cadastre a primeira placa solar para começar.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                        {{-- SEM RESULTADO NA PESQUISA --}}
                        <tr class="d-none" data-search-empty>

                            <td colspan="4" class="resource-empty-state">

                                <span class="resource-empty-icon">
                                    <i class="bi bi-search"></i>
                                </span>

                                <h3>
                                    Nenhuma placa encontrada
                                </h3>

                                <p>
                                    Tente pesquisar usando outro nome ou valor.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            {{-- PAGINAÇÃO --}}
            @if ($plates->hasPages())
                <div class="resource-pagination">
                    {{ $plates->withQueryString()->links() }}
                </div>
            @endif

        </section>

    </div>

    {{-- ================================================================
        MODAL ADICIONAR
    ================================================================= --}}

    @can('towers.create')
        <div class="modal fade resource-modal" id="addPlateModal" tabindex="-1" aria-labelledby="addPlateModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <form action="{{ route('plate.store') }}" method="POST" class="modal-content js-resource-form" novalidate>

                    @csrf

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-sun"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="addPlateModalLabel">

                                    Nova placa solar
                                </h5>

                                <p class="resource-modal-description">
                                    Informe a potência e a corrente da placa.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="plate_name" class="form-label">

                                Nome
                            </label>

                            <input type="text"
                                class="form-control
                                    @error('name') is-invalid @enderror"
                                id="plate_name" name="name" value="{{ old('name') }}" maxlength="150" required
                                autofocus>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe o nome da placa solar.
                                </div>
                            @enderror

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="plate_watts" class="form-label">

                                    Potência
                                </label>

                                <div class="input-group">

                                    <input type="number"
                                        class="form-control
                                            @error('watts') is-invalid @enderror"
                                        id="plate_watts" name="watts" value="{{ old('watts') }}" min="0"
                                        max="10000" step="0.01" required>

                                    <span class="input-group-text">
                                        W
                                    </span>

                                    @error('watts')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="plate_amps" class="form-label">

                                    Corrente
                                </label>

                                <div class="input-group">

                                    <input type="number"
                                        class="form-control
                                            @error('amps') is-invalid @enderror"
                                        id="plate_amps" name="amps" value="{{ old('amps') }}" min="0"
                                        max="1000" step="0.01" required>

                                    <span class="input-group-text">
                                        A
                                    </span>

                                    @error('amps')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                            </span>

                            <i class="bi bi-check-lg" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar placa
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

    @can('towers.edit')
        <div class="modal fade resource-modal" id="editPlateModal" tabindex="-1" aria-labelledby="editPlateModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <form method="POST" class="modal-content js-resource-form" data-edit-form novalidate>

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="editPlateModalLabel">

                                    Editar placa solar
                                </h5>

                                <p class="resource-modal-description">
                                    Atualize as informações da placa solar.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="edit_plate_name" class="form-label">

                                Nome
                            </label>

                            <input type="text" class="form-control" id="edit_plate_name" name="name"
                                data-edit-field="name" maxlength="150" required>

                            <div class="invalid-feedback">
                                Informe o nome da placa solar.
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="edit_plate_watts" class="form-label">

                                    Potência
                                </label>

                                <div class="input-group">

                                    <input type="number" class="form-control" id="edit_plate_watts" name="watts"
                                        data-edit-field="watts" min="0" max="10000" step="0.01" required>

                                    <span class="input-group-text">
                                        W
                                    </span>

                                </div>

                                <div class="invalid-feedback">
                                    Informe a potência da placa.
                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="edit_plate_amps" class="form-label">

                                    Corrente
                                </label>

                                <div class="input-group">

                                    <input type="number" class="form-control" id="edit_plate_amps" name="amps"
                                        data-edit-field="amps" min="0" max="1000" step="0.01" required>

                                    <span class="input-group-text">
                                        A
                                    </span>

                                </div>

                                <div class="invalid-feedback">
                                    Informe a corrente da placa.
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-submit-button
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
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
            validationModal: '#addPlateModal'
        };
    </script>

    <script src="{{ asset('js/tower-resource-index.js') }}"></script>
@endpush
