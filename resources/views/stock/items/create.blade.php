@extends('layouts.header')
@section('title', 'Novo Item de Estoque')

@section('content')
    <div class="container mt-4">
        <h2>Novo Item</h2>

        <form action="{{ route('stock.items.store') }}" method="POST">
            @csrf
            {{-- Checkbox de status em destaque --}}
            <div class="mb-4 d-flex align-items-center gap-2">
                <label for="status" class="fw-bold fs-5 mb-0">Status:</label>
                <div class="form-check form-switch mb-0">
                    <input type="checkbox" name="status" id="status" class="form-check-input" checked>
                </div>
            </div>


            <div class="mb-3">
                <label for="name" class="form-label">Nome *</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="min_stock" class="form-label">Estoque Mínimo</label>
                <input type="number" name="min_stock" id="min_stock" class="form-control">
            </div>

            <div class="mb-3">
                <label for="current_stock" class="form-label">Estoque Inicial</label>
                <input type="number" name="current_stock" id="current_stock" value="0" class="form-control">
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control">
            </div>

            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
            <a href="{{ route('stock.items.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
