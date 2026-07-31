@extends('layouts.header')
@section('title', 'Nova Movimentação')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-form-card">
        <div class="stock-form-header">
            <div class="stock-title-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div>
                <h1 class="stock-title">Nova movimentação</h1>
                <p class="stock-subtitle">Registre entrada, saída ou movimentação de itens.</p>
            </div>
        </div>

        <form action="{{ route('stock.movements.store') }}" method="POST" data-submit-lock data-stock-movement-form>
            @csrf

            <div class="stock-form-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo da movimentação</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="input" @selected(old('type') === 'input')>Entrada</option>
                            <option value="output" @selected(old('type') === 'output')>Saída</option>
                            <option value="movement" @selected(old('type') === 'movement')>Movimento</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="description" class="form-label">Descrição</label>
                        <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" placeholder="Informe o motivo ou referência da movimentação">
                    </div>
                    <div class="col-12">
                        <label for="extra_items" class="form-label">Itens extras</label>
                        <textarea name="extra_items" id="extra_items" class="form-control" rows="3" placeholder="Itens não contabilizados no estoque">{{ old('extra_items') }}</textarea>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="stock-section-title mb-0"><i class="bi bi-box-seam"></i>Itens</h2>
                    <button type="button" class="btn btn-outline-secondary stock-btn" data-add-stock-item><i class="bi bi-plus-lg"></i>Adicionar item</button>
                </div>

                <div id="items-container" data-next-index="1">
                    <div class="stock-item-row">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Equipamento</label>
                                <select name="items[0][id]" class="form-select" required>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 movement-column" hidden>
                                <label class="form-label">Tipo</label>
                                <select name="items[0][movement_type]" class="form-select movement-field">
                                    <option value="input">Entrada</option>
                                    <option value="output">Saída</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade</label>
                                <input type="number" min="1" required name="items[0][quantity]" class="form-control">
                            </div>
                            <div class="col-md-3 price-column">
                                <label class="form-label">Preço unitário</label>
                                <div class="input-group"><span class="input-group-text">R$</span><input type="number" step="0.01" min="0" name="items[0][price]" class="form-control price-field"></div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 stock-btn" data-remove-stock-item title="Remover"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <template id="stock-item-row-template">
                    <div class="stock-item-row">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Equipamento</label>
                                <select name="items[__INDEX__][id]" class="form-select" required>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 movement-column" hidden>
                                <label class="form-label">Tipo</label>
                                <select name="items[__INDEX__][movement_type]" class="form-select movement-field">
                                    <option value="input">Entrada</option>
                                    <option value="output">Saída</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade</label>
                                <input type="number" min="1" required name="items[__INDEX__][quantity]" class="form-control">
                            </div>
                            <div class="col-md-3 price-column">
                                <label class="form-label">Preço unitário</label>
                                <div class="input-group"><span class="input-group-text">R$</span><input type="number" step="0.01" min="0" name="items[__INDEX__][price]" class="form-control price-field"></div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 stock-btn" data-remove-stock-item title="Remover"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="stock-form-footer">
                <a href="{{ route('stock.movements.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
                <button type="submit" class="btn dcm-btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar movimentação</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
