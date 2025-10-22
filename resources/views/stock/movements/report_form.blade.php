@extends('layouts.header')

@section('content')
<div class="container">
    <h2>Relatório de Movimentações de Estoque</h2>
    <form action="{{ route('stock.movements.reportView') }}" method="GET" class="row g-3"  target="_blank">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Data Inicial</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label for="end_date" class="form-label">Data Final</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label for="type" class="form-label">Tipo</label>
            <select name="type" class="form-select">
                <option value="all">Todos</option>
                <option value="input">Entradas</option>
                <option value="output">Saídas</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary mt-3" type="submit" >
                <i class="bi bi-search"></i> Gerar Relatório
            </button>
        </div>
    </form>
</div>
@endsection
