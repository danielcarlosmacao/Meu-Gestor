@extends('layouts.header')

@section('title', 'Equipamentos')

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
                    <i class="bi bi-router"></i>
                    Equipamentos das torres
                </div>

                <h1 class="resource-page-title">
                    Equipamentos
                </h1>

                <p class="resource-page-description">
                    Gerencie os equipamentos utilizados nas torres,
                    seu consumo elétrico e a quantidade disponível em estoque.
                </p>

            </div>

            <div class="resource-header-actions">

                @can('towers.create')
                    <button type="button" class="btn dcm-btn-primary resource-primary-button" data-bs-toggle="modal"
                        data-bs-target="#addEquipmentModal">

                        <i class="bi bi-plus-lg"></i>
                        Novo equipamento
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
                                <i class="bi bi-router"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Equipamentos cadastrados
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $equipments->total() }}
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
                                <i class="bi bi-box-seam"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Estoque nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $equipments->sum('stock') }}
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
                                <i class="bi bi-broadcast-pin"></i>
                            </span>

                            <div>

                                <span class="resource-summary-label">
                                    Em produção nesta página
                                </span>

                                <strong class="resource-summary-value">
                                    {{ $equipments->sum('equipment_productions_count') }}
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
                        Equipamentos cadastrados
                    </h2>

                    <p class="resource-table-subtitle">
                        Consulte consumo, estoque disponível e quantidade em produção.
                    </p>

                </div>

                <div class="resource-table-tools">

                    <div class="resource-search">

                        <i class="bi bi-search"></i>

                        <input type="search" class="form-control" data-resource-search="#equipmentTable"
                            placeholder="Pesquisar equipamento..." autocomplete="off">

                    </div>

                </div>

            </div>

            <div class="resource-table-responsive">

                <table id="equipmentTable" class="table resource-table align-middle">

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
                                    Consumo
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Estoque
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="resource-sortable" data-sortable="true">

                                <span>
                                    Em produção
                                    <i class="bi bi-arrow-down-up resource-sort-icon"></i>
                                </span>

                            </th>

                            <th class="text-center pe-4">
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($equipments as $equipment)

                            <tr data-resource-row
                                data-search="{{ mb_strtolower(
                                    implode(' ', [$equipment->name, $equipment->watts, $equipment->stock, $equipment->equipment_productions_count]),
                                ) }}">

                                {{-- NOME --}}
                                <td class="ps-4">

                                    <div class="resource-name">

                                        <span class="resource-name-icon">
                                            <i class="bi bi-router"></i>
                                        </span>

                                        <span>

                                            <strong>
                                                {{ $equipment->name }}
                                            </strong>

                                            <small>
                                                Equipamento da torre
                                            </small>

                                        </span>

                                    </div>

                                </td>

                                {{-- WATTS --}}
                                <td data-value="{{ $equipment->watts }}">

                                    <span class="resource-value-badge">
                                        {{ number_format((float) $equipment->watts, 2, ',', '.') }}
                                        W
                                    </span>

                                </td>

                                {{-- ESTOQUE --}}
                                <td data-value="{{ $equipment->stock }}">

                                    <span class="resource-value-badge">
                                        {{ $equipment->stock }}
                                    </span>

                                </td>

                                {{-- EM PRODUÇÃO --}}
                                <td data-value="{{ $equipment->equipment_productions_count }}">

                                    <span class="resource-value-badge">
                                        {{ $equipment->equipment_productions_count }}
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
                                                        data-resource-edit="#editEquipmentModal"
                                                        data-update-url="{{ route('equipment.update', $equipment->id) }}"
                                                        data-name="{{ $equipment->name }}"
                                                        data-watts="{{ $equipment->watts }}"
                                                        data-stock="{{ $equipment->stock }}" data-bs-toggle="modal"
                                                        data-bs-target="#editEquipmentModal">

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
                                                        data-resource-delete="{{ route('equipment.destroy', $equipment->id) }}"
                                                        data-delete-title="Deseja excluir este equipamento?"
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

                                <td colspan="5" class="resource-empty-state">

                                    <span class="resource-empty-icon">
                                        <i class="bi bi-router"></i>
                                    </span>

                                    <h3>
                                        Nenhum equipamento cadastrado
                                    </h3>

                                    <p>
                                        Cadastre o primeiro equipamento para começar.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                        {{-- SEM RESULTADO NA PESQUISA --}}
                        <tr class="d-none" data-search-empty>

                            <td colspan="5" class="resource-empty-state">

                                <span class="resource-empty-icon">
                                    <i class="bi bi-search"></i>
                                </span>

                                <h3>
                                    Nenhum equipamento encontrado
                                </h3>

                                <p>
                                    Tente pesquisar utilizando outro nome ou valor.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            {{-- PAGINAÇÃO --}}
            @if ($equipments->hasPages())
                <div class="resource-pagination">
                    {{ $equipments->withQueryString()->links() }}
                </div>
            @endif

        </section>

    </div>

    {{-- ================================================================
        MODAL ADICIONAR
    ================================================================= --}}

    @can('towers.create')
        <div class="modal fade resource-modal" id="addEquipmentModal" tabindex="-1"
            aria-labelledby="addEquipmentModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <form action="{{ route('equipment.store') }}" method="POST" class="modal-content js-resource-form"
                    novalidate>

                    @csrf

                    <div class="modal-header">

                        <div class="resource-modal-title-area">

                            <span class="resource-modal-icon">
                                <i class="bi bi-router"></i>
                            </span>

                            <div>

                                <h5 class="modal-title" id="addEquipmentModalLabel">

                                    Novo equipamento
                                </h5>

                                <p class="resource-modal-description">
                                    Informe o consumo e o estoque do equipamento.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="equipment_name" class="form-label">

                                Nome
                            </label>

                            <input type="text"
                                class="form-control
                                    @error('name') is-invalid @enderror"
                                id="equipment_name" name="name" value="{{ old('name') }}" maxlength="150" required
                                autofocus>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Informe o nome do equipamento.
                                </div>
                            @enderror

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="equipment_watts" class="form-label">

                                    Consumo
                                </label>

                                <div class="input-group">

                                    <input type="number"
                                        class="form-control
                                            @error('watts') is-invalid @enderror"
                                        id="equipment_watts" name="watts" value="{{ old('watts') }}" min="0"
                                        max="1000" step="0.01" required>

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

                                <label for="equipment_stock" class="form-label">

                                    Estoque
                                </label>

                                <input type="number"
                                    class="form-control
                                        @error('stock') is-invalid @enderror"
                                    id="equipment_stock" name="stock" value="{{ old('stock') }}" min="0"
                                    max="1000" step="1" required>

                                @error('stock')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Informe a quantidade em estoque.
                                    </div>
                                @enderror

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
                                Salvar equipamento
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
        <div class="modal fade resource-modal" id="editEquipmentModal" tabindex="-1"
            aria-labelledby="editEquipmentModalLabel" aria-hidden="true">

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

                                <h5 class="modal-title" id="editEquipmentModalLabel">

                                    Editar equipamento
                                </h5>

                                <p class="resource-modal-description">
                                    Atualize as informações do equipamento.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="edit_equipment_name" class="form-label">

                                Nome
                            </label>

                            <input type="text" class="form-control" id="edit_equipment_name" name="name"
                                data-edit-field="name" maxlength="150" required>

                            <div class="invalid-feedback">
                                Informe o nome do equipamento.
                            </div>

                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label for="edit_equipment_watts" class="form-label">

                                    Consumo
                                </label>

                                <div class="input-group">

                                    <input type="number" class="form-control" id="edit_equipment_watts" name="watts"
                                        data-edit-field="watts" min="0" max="1000" step="0.01" required>

                                    <span class="input-group-text">
                                        W
                                    </span>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label for="edit_equipment_stock" class="form-label">

                                    Estoque
                                </label>

                                <input type="number" class="form-control" id="edit_equipment_stock" name="stock"
                                    data-edit-field="stock" min="0" max="1000" step="1" required>

                                <div class="invalid-feedback">
                                    Informe a quantidade em estoque.
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
            validationModal: '#addEquipmentModal'
        };
    </script>

    <script src="{{ asset('js/tower-resource-index.js') }}"></script>
@endpush
