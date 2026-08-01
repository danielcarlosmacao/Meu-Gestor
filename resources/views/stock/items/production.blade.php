@extends('layouts.header')
@section('title', 'Equipamentos em Produção e Estoque')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-shell">
        <div class="stock-header">
            <div class="stock-title-wrap">
                <div class="stock-title-icon"><i class="bi bi-cpu"></i></div>
                <div>
                    <h1 class="stock-title">Produção e estoque</h1>
                    <p class="stock-subtitle">Compare equipamentos ativos em produção com a disponibilidade em estoque.</p>
                </div>
            </div>
        </div>

        @if (!is_null($totalProductionValue))
            <div class="stock-body pb-0">
                <div class="stock-summary-grid">
                    <div class="stock-summary-card">
                        <div class="stock-summary-icon"><i class="bi bi-cash-stack"></i></div>
                        <div><span>Valor total em produção</span><strong>R$ {{ number_format($totalProductionValue, 2, ',', '.') }}</strong></div>
                    </div>
                    <div class="stock-summary-card">
                        <div class="stock-summary-icon"><i class="bi bi-cpu-fill"></i></div>
                        <div><span>Equipamentos listados</span><strong>{{ count($data) }}</strong></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="stock-table-wrap">
            <table class="table stock-table">
                <thead>
                    <tr>
                        <th>Equipamento</th>
                        <th>Potência</th>
                        <th>Produção ativa</th>
                        <th>Estoque</th>
                        <th>Preço</th>
                        @if (!is_null($totalProductionValue))<th>Valor em produção</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        @php
                            $numericPrice = is_numeric($item['price']) ? (float) $item['price'] : null;
                            $productionTotal = !is_null($numericPrice) ? $numericPrice * $item['in_production'] : null;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $item['equipment_name'] }}</td>
                            <td>{{ $item['watts'] }} W</td>
                            <td><span class="stock-badge {{ $item['in_production'] > 0 ? 'stock-badge-active' : 'stock-badge-neutral' }}">{{ $item['in_production'] }}</span></td>
                            <td>
                                @if ($item['stock_qty'] > 0)
                                    <span class="stock-badge stock-badge-movement">{{ $item['stock_qty'] }} disponível(is)</span>
                                @elseif($item['status'] === 'not_found')
                                    <span class="stock-badge stock-badge-neutral">Não cadastrado</span>
                                @else
                                    <span class="stock-badge stock-badge-warning">Sem estoque</span>
                                @endif
                            </td>
                            <td>{{ !is_null($numericPrice) ? 'R$ ' . number_format($numericPrice, 2, ',', '.') : '-' }}</td>
                            @if (!is_null($totalProductionValue))
                                <td>{{ !is_null($productionTotal) ? 'R$ ' . number_format($productionTotal, 2, ',', '.') : '-' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="6" class="stock-empty"><i class="bi bi-cpu"></i><strong>Nenhum equipamento encontrado</strong></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
