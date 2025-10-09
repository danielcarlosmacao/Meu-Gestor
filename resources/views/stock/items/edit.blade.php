@extends('layouts.header')
@section('title', 'Editar Item de Estoque')

@section('content')
    <div class="container mt-4">
        <h2>Editar Item</h2>

        <form action="{{ route('stock.items.update', $item->id) }}" method="POST" id="editItemForm">
            @csrf
            @method('PUT')

            {{-- Checkbox de status em destaque --}}
            <div class="mb-4 d-flex align-items-center gap-2">
                <label for="status" class="fw-bold fs-5 mb-0">Status:</label>
                <div class="form-check form-switch mb-0">
                    <input type="checkbox" name="status" id="status" class="form-check-input"
                        {{ $item->status === 'active' ? 'checked' : '' }}>
                </div>
            </div>


            {{-- Campos do formulário --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nome *</label>
                <input type="text" name="name" id="name" value="{{ $item->name }}" class="form-control"
                    required>
            </div>

            <div class="mb-3">
                <label for="min_stock" class="form-label">Estoque Mínimo</label>
                <input type="number" name="min_stock" id="min_stock" value="{{ $item->min_stock }}" class="form-control">
            </div>

            <div class="mb-3">
                <label for="current_stock" class="form-label">Estoque Atual</label>
                <input type="number" id="current_stock" value="{{ $item->current_stock }}" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ $item->price }}"
                    class="form-control">
            </div>

            {{-- Botões --}}
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn dcm-btn-primary">Atualizar</button>
                <a href="{{ route('stock.items.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusCheckbox = document.getElementById('status');
            const formFields = document.querySelectorAll('#editItemForm input:not([type=checkbox])');

            function toggleFields() {
                const isActive = statusCheckbox.checked;
                formFields.forEach(field => {
                    field.disabled = !isActive;
                });
            }

            // Inicializa
            toggleFields();

            // Atualiza ao mudar o checkbox
            statusCheckbox.addEventListener('change', toggleFields);
        });
    </script>
@endsection
