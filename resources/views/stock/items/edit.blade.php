@extends('layouts.header')
@section('title', 'Editar Item de Estoque')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-form-card">
        <div class="stock-form-header">
            <div class="stock-title-icon"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h1 class="stock-title">Editar item</h1>
                <p class="stock-subtitle">Atualize os dados de {{ $item->name }}.</p>
            </div>
        </div>

        <form action="{{ route('stock.items.update', $item->id) }}" method="POST" data-submit-lock data-stock-item-form>
            @csrf
            @method('PUT')

            <div class="stock-form-body">
                <div class="stock-status-box">
                    <div>
                        <strong>Item ativo</strong>
                        <div class="small text-muted">Ao desativar, os demais campos ficam bloqueados nesta tela.</div>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input type="checkbox" name="status" id="status" class="form-check-input" {{ old('status', $item->status === 'active') ? 'checked' : '' }}>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required data-disable-when-inactive>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="min_stock" class="form-label">Estoque mínimo</label>
                        <input type="number" name="min_stock" id="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ old('min_stock', $item->min_stock) }}" min="0" data-disable-when-inactive>
                        @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="current_stock" class="form-label">Estoque atual</label>
                        <input type="number" name="current_stock" id="current_stock" class="form-control @error('current_stock') is-invalid @enderror" value="{{ old('current_stock', $item->current_stock) }}" min="0" data-disable-when-inactive>
                        @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="price" class="form-label">Preço</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" min="0" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $item->price) }}" data-disable-when-inactive>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="stock-form-footer">
                <a href="{{ route('stock.items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
                <button type="submit" class="btn dcm-btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
