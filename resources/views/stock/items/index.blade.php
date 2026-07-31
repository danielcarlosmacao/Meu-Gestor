@extends('layouts.header')
@section('title', 'Itens de Estoque')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-shell">
        <div class="stock-header">
            <div class="stock-title-wrap">
                <div class="stock-title-icon"><i class="bi bi-boxes"></i></div>
                <div>
                    <h1 class="stock-title">Itens de estoque</h1>
                    <p class="stock-subtitle">Acompanhe quantidades, valores e níveis mínimos.</p>
                </div>
            </div>

            <div class="stock-header-actions">
                @can('stock.items.create')
                    <a href="{{ route('stock.items.create') }}" class="btn dcm-btn-primary stock-btn">
                        <i class="bi bi-plus-lg"></i><span>Novo item</span>
                    </a>
                @endcan
            </div>
        </div>

        <div class="stock-body">
            @if (!is_null($totalStockValue))
                <div class="stock-summary-grid">
                    <div class="stock-summary-card">
                        <div class="stock-summary-icon"><i class="bi bi-cash-stack"></i></div>
                        <div><span>Valor total do estoque</span><strong>R$ {{ number_format($totalStockValue, 2, ',', '.') }}</strong></div>
                    </div>
                    <div class="stock-summary-card">
                        <div class="stock-summary-icon"><i class="bi bi-box-seam"></i></div>
                        <div><span>Itens exibidos</span><strong>{{ $items->count() }}</strong></div>
                    </div>
                    <div class="stock-summary-card">
                        <div class="stock-summary-icon"><i class="bi bi-list-check"></i></div>
                        <div><span>Total cadastrado</span><strong>{{ method_exists($items, 'total') ? $items->total() : $items->count() }}</strong></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="stock-table-wrap">
            <table class="table stock-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Estoque atual</th>
                        <th>Estoque mínimo</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $isLow = !is_null($item->min_stock) && $item->current_stock <= $item->min_stock;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <small class="text-muted">Código #{{ $item->id }}</small>
                            </td>
                            <td><span class="stock-qty {{ $isLow ? 'low' : '' }}">{{ $item->current_stock }}</span></td>
                            <td>{{ $item->min_stock ?? '-' }}</td>
                            <td>{{ $item->price ? 'R$ ' . number_format($item->price, 2, ',', '.') : '-' }}</td>
                            <td>
                                <span class="stock-badge {{ $item->status === 'active' ? 'stock-badge-active' : 'stock-badge-inactive' }}">
                                    <i class="bi {{ $item->status === 'active' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                    {{ __('status.' . $item->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="stock-actions">
                                    <a href="{{ route('stock.items.show', $item->id) }}" class="btn btn-outline-primary stock-btn" title="Visualizar"><i class="bi bi-eye"></i></a>
                                    @can('stock.items.edit')
                                        <a href="{{ route('stock.items.edit', $item->id) }}" class="btn btn-warning stock-btn" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                    @endcan
                                    @can('stock.items.delete')
                                        <form action="{{ route('stock.items.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger stock-btn" data-confirm="Tem certeza que deseja deletar este item?" title="Excluir"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="stock-empty"><i class="bi bi-box-seam"></i><strong>Nenhum item cadastrado</strong><div class="mt-1">Cadastre o primeiro item para começar.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'hasPages') && $items->hasPages())
            <div class="stock-pagination">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
