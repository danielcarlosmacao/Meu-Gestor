@extends('layouts.header')
@section('title', 'Detalhes da Movimentação')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
@php
    $isMovement = $movement->type === 'movement';
    $total = $movement->items->sum(fn($item) => ($item->pivot->price ?? 0) * $item->pivot->quantity);
    $typeLabel = $movement->type === 'input' ? 'Entrada' : ($isMovement ? 'Movimento' : 'Saída');
@endphp

<div class="container stock-page">
    <div class="stock-shell">
        <div class="stock-header">
            <div class="stock-title-wrap">
                <div class="stock-title-icon"><i class="bi bi-receipt"></i></div>
                <div>
                    <h1 class="stock-title">{{ $typeLabel }} de estoque</h1>
                    <p class="stock-subtitle">Registrada em {{ $movement->created_at->format('d/m/Y H:i') }}.</p>
                </div>
            </div>
            <span class="stock-badge {{ $movement->type === 'input' ? 'stock-badge-input' : ($isMovement ? 'stock-badge-movement' : 'stock-badge-output') }}">{{ $typeLabel }}</span>
        </div>

        <div class="stock-body">
            <div class="stock-detail-grid mb-4">
                <div class="stock-detail-item"><span>Descrição</span><strong>{{ $movement->description ?? '-' }}</strong></div>
                <div class="stock-detail-item"><span>Itens extras</span><strong>{{ $movement->extra_items ?? '-' }}</strong></div>
                <div class="stock-detail-item"><span>Usuário</span><strong>{{ $movement->user->name ?? '-' }}</strong></div>
                @unless($isMovement)
                    <div class="stock-detail-item"><span>Valor total</span><strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></div>
                @endunless
            </div>

            <h2 class="stock-section-title"><i class="bi bi-box-seam"></i>Itens movimentados</h2>
        </div>

        <div class="stock-table-wrap">
            <table class="table stock-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantidade</th>
                        @unless($isMovement)
                            <th>Valor unitário</th>
                            <th>Subtotal</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movement->items as $item)
                        @php $subtotal = ($item->pivot->price ?? 0) * $item->pivot->quantity; @endphp
                        <tr>
                            <td class="fw-semibold">{{ $item->name ?? 'Item deletado' }}</td>
                            <td>{{ $item->pivot->quantity }}</td>
                            @unless($isMovement)
                                <td>R$ {{ number_format($item->pivot->price ?? 0, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                            @endunless
                        </tr>
                    @empty
                        <tr><td colspan="4" class="stock-empty"><i class="bi bi-box-seam"></i>Nenhum item nesta movimentação.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="stock-form-footer">
            <a href="{{ route('stock.movements.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
            @if ($movement->type === 'output')
                <form action="{{ route('movements.updatePrices', $movement->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning" data-confirm="Tem certeza que deseja atualizar os valores dos itens com base no estoque atual?"><i class="bi bi-arrow-repeat me-1"></i>Atualizar preços</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
