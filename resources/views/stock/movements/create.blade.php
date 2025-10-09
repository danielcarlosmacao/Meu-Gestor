@extends('layouts.header')
@section('title', 'Nova Movimentação')

@section('content')
    <div class="container mt-4">
        <h1>Nova Movimentação</h1>

        <form action="{{ route('stock.movements.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="type" class="form-label">Tipo</label>
                <select name="type" id="type" class="form-control" onchange="togglePriceFields()">
                    <option value="input">Entrada</option>
                    <option value="output">Saída</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <input type="text" name="description" id="description" class="form-control">
            </div>

            <div class="mb-3">
                <label for="extra_items" class="form-label">Itens Extras (não contabilizados no estoque)</label>
                <textarea name="extra_items" id="extra_items" class="form-control"></textarea>
            </div>

            <h4>Itens</h4>
            <div id="items-container">
                <div class="item-row mb-2 d-flex gap-2 align-items-center">
                    <select name="items[0][id]" class="form-select">
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>

                    <input type="number" name="items[0][quantity]" class="form-control" placeholder="Quantidade"
                        min="1" required>

                    <input type="number" step="0.01" name="items[0][price]" class="form-control price-field"
                        placeholder="Preço Unitário">

                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">-</button>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" onclick="addItem()"><i class="bi bi-plus"></i> Adicionar</button>

            <div class="mt-3">
                <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                <a href="{{ route('stock.movements.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        function addItem() {
            const container = document.getElementById('items-container');
            const newRow = document.createElement('div');
            newRow.classList.add('item-row', 'mb-2', 'd-flex', 'gap-2', 'align-items-center');
            newRow.innerHTML = `
        <select name="items[${itemIndex}][id]" class="form-select">
            @foreach ($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Quantidade" min="1" required>
        <input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control price-field"
               placeholder="Preço Unitário">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">-</button>
    `;
            container.appendChild(newRow);
            itemIndex++;
            togglePriceFields();
        }

        function removeItem(button) {
            const row = button.closest('.item-row');
            row.remove();
        }

        function togglePriceFields() {
            const type = document.getElementById('type').value;
            const priceFields = document.querySelectorAll('.price-field');
            priceFields.forEach(field => {
                if (type === 'input') {
                    field.style.display = 'block';
                    field.removeAttribute('disabled');
                } else {
                    field.style.display = 'none';
                    field.value = '';
                    field.setAttribute('disabled', true);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', togglePriceFields);
    </script>
@endsection
