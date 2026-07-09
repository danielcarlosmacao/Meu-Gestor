@extends('layouts.header')
@section('title', 'Nova Movimentação')

@section('content')
<div class="container mt-4">
    <h1>Nova Movimentação</h1>

    <form action="{{ route('stock.movements.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="type" class="form-label">Tipo da Movimentação</label>
            <select name="type" id="type" class="form-control" onchange="toggleFields()">
                <option value="input">Entrada</option>
                <option value="output">Saída</option>
                <option value="movement">Movimento</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <input type="text"
                   name="description"
                   id="description"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label for="extra_items" class="form-label">
                Itens Extras (não contabilizados no estoque)
            </label>
            <textarea name="extra_items"
                      id="extra_items"
                      class="form-control"
                      rows="3"></textarea>
        </div>

        <hr>

        <h4>Itens</h4>

        <div id="items-container">

            <div class="item-row row g-2 align-items-end mb-3">

                <div class="col-md-5">
                    <label class="form-label">Equipamento</label>
                    <select name="items[0][id]" class="form-select">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 movement-column" style="display:none;">
                    <label class="form-label">Tipo</label>
                    <select name="items[0][movement_type]"
                            class="form-select movement-field">
                        <option value="input">Entrada</option>
                        <option value="output">Saída</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number"
                           min="1"
                           required
                           name="items[0][quantity]"
                           class="form-control">
                </div>

                <div class="col-md-3 price-column">
                    <label class="form-label">Preço Unitário</label>
                    <input type="number"
                           step="0.01"
                           name="items[0][price]"
                           class="form-control price-field">
                </div>

                <div class="col-md-1">
                    <button type="button"
                            class="btn btn-danger w-100"
                            onclick="removeItem(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                

            </div>

        </div>

        <button type="button"
                class="btn btn-secondary"
                onclick="addItem()">
            <i class="bi bi-plus"></i>
            Adicionar Item
        </button>

        <hr>

        <button class="btn dcm-btn-primary">
            Salvar
        </button>

        <a href="{{ route('stock.movements.index') }}"
           class="btn btn-secondary">
            Cancelar
        </a>

    </form>
</div>

<script>

let itemIndex = 1;
function addItem() {

    const container = document.getElementById('items-container');

    const newRow = document.createElement('div');

    newRow.className = 'item-row row g-2 align-items-end mb-3';

    newRow.innerHTML = `

        <div class="col-md-5">
            <label class="form-label">Equipamento</label>
            <select name="items[${itemIndex}][id]" class="form-select">
                @foreach($items as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 movement-column" style="display:none;">
            <label class="form-label">Tipo</label>
            <select name="items[${itemIndex}][movement_type]" class="form-select movement-field">
                <option value="input">Entrada</option>
                <option value="output">Saída</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Quantidade</label>
            <input type="number"
                   min="1"
                   required
                   name="items[${itemIndex}][quantity]"
                   class="form-control">
        </div>

        <div class="col-md-3 price-column">
            <label class="form-label">Preço Unitário</label>
            <input type="number"
                   step="0.01"
                   name="items[${itemIndex}][price]"
                   class="form-control price-field">
        </div>

        <div class="col-md-1">
            <button type="button"
                    class="btn btn-danger w-100"
                    onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>

    `;

    container.appendChild(newRow);

    itemIndex++;

    toggleFields();
}

function removeItem(button) {

    const rows = document.querySelectorAll('.item-row');

    if (rows.length > 1) {
        button.closest('.item-row').remove();
    } else {
        alert('É necessário manter pelo menos um item.');
    }

}

function toggleFields() {

    const type = document.getElementById('type').value;

    const movementColumns = document.querySelectorAll('.movement-column');
    const movementFields = document.querySelectorAll('.movement-field');

    const priceColumns = document.querySelectorAll('.price-column');
    const priceFields = document.querySelectorAll('.price-field');

    if (type === 'input') {

        movementColumns.forEach(col => col.style.display = 'none');
        movementFields.forEach(field => field.disabled = true);

        priceColumns.forEach(col => col.style.display = 'block');
        priceFields.forEach(field => {
            field.disabled = false;
        });

    } else if (type === 'output') {

        movementColumns.forEach(col => col.style.display = 'none');
        movementFields.forEach(field => field.disabled = true);

        priceColumns.forEach(col => col.style.display = 'none');
        priceFields.forEach(field => {
            field.disabled = true;
            field.value = '';
        });

    } else if (type === 'movement') {

        movementColumns.forEach(col => col.style.display = 'block');
        movementFields.forEach(field => field.disabled = false);

        priceColumns.forEach(col => col.style.display = 'none');
        priceFields.forEach(field => {
            field.disabled = true;
            field.value = '';
        });

    }

}

document.addEventListener('DOMContentLoaded', function () {
    toggleFields();
});

</script>

@endsection