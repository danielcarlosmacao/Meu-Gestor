@extends('layouts.header')
@section('title', 'Detalhes da Movimentação')

@section('content')
<div class="container mt-4">
    <h2>
        Movimentação de {{ ucfirst(__('typeMoviment.' . $movement->type)) }} - 
        {{ $movement->created_at->format('d/m/Y') }}
    </h2>

    <p><strong>Descrição:</strong> {{ $movement->description ?? '-' }}</p>
    <p><strong>Itens extras:</strong> {{ $movement->extra_items ?? '-' }}</p>

    {{-- Calcula total --}}
    @php
        $total = 0;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Itens movimentados</h4>
        <span class="badge bgc-primary fs-5">
            Total: R$ 
            @foreach($movement->items as $item)
                @php
                    $total += ($item->pivot->price ?? 0) * $item->pivot->quantity;
                @endphp
            @endforeach
            {{ number_format($total, 2, ',', '.') }}
        </span>
    </div>

    <table class="table table-bordered">
        <thead class="bgc-primary text-white">
            <tr>
                <th>Item</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movement->items as $item)
                @php
                    $subtotal = ($item->pivot->price ?? 0) * $item->pivot->quantity;
                @endphp
                <tr>
                    <td>{{ $item->name ?? 'Item deletado' }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>R$ {{ number_format($item->pivot->price ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('stock.movements.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@endsection
