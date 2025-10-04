@extends('layouts.header')
@section('title', 'Detalhes do Item')

@section('content')
<div class="container mt-4">
    <h2>Detalhes do Item</h2>

    <table class="table table-bordered">
        <tr>
            <th>Nome</th>
            <td>{{ $item->name }}</td>
        </tr>
        <tr>
            <th>Estoque Atual</th>
            <td>{{ $item->current_stock }}</td>
        </tr>
        <tr>
            <th>Estoque Mínimo</th>
            <td>{{ $item->min_stock ?? '-' }}</td>
        </tr>
        <tr>
            <th>Preço</th>
            <td>{{ $item->price ? 'R$ '.number_format($item->price,2,',','.') : '-' }}</td>
        </tr>
        <tr>
            <th>Criado em</th>
            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Atualizado em</th>
            <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <a href="{{ route('stock.items.index') }}" class="btn btn-secondary">Voltar</a>
    <a href="{{ route('stock.items.edit', $item->id) }}" class="btn btn-warning">Editar</a>
</div>
@endsection
