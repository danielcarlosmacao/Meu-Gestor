@extends('layouts.header')
@section('title', 'Movimentações de Estoque')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-shell">
        <div class="stock-header">
            <div class="stock-title-wrap">
                <div class="stock-title-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <h1 class="stock-title">Movimentações</h1>
                    <p class="stock-subtitle">Histórico de entradas, saídas e movimentos internos.</p>
                </div>
            </div>

            <div class="stock-header-actions">
                <a href="{{ route('stock.movements.reportForm') }}" class="btn btn-outline-secondary stock-btn"><i class="bi bi-receipt"></i><span>Relatório</span></a>
                @can('stock.movements.create')
                    <a href="{{ route('stock.movements.create') }}" class="btn dcm-btn-primary stock-btn"><i class="bi bi-plus-lg"></i><span>Nova movimentação</span></a>
                @endcan
            </div>
        </div>

        <div class="stock-table-wrap">
            <table class="table stock-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Usuário</th>
                        <th>Data</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td>
                                <span class="stock-badge {{ $m->type === 'input' ? 'stock-badge-input' : ($m->type === 'movement' ? 'stock-badge-movement' : 'stock-badge-output') }}">
                                    <i class="bi {{ $m->type === 'input' ? 'bi-box-arrow-in-down' : ($m->type === 'movement' ? 'bi-arrow-left-right' : 'bi-box-arrow-up') }}"></i>
                                    {{ $m->type === 'input' ? 'Entrada' : ($m->type === 'movement' ? 'Movimento' : 'Saída') }}
                                </span>
                            </td>
                            <td>{{ $m->description ?? '-' }}</td>
                            <td>{{ $m->user->name ?? '-' }}</td>
                            <td class="text-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td><div class="stock-actions"><a href="{{ route('stock.movements.show', $m->id) }}" class="btn btn-outline-primary stock-btn"><i class="bi bi-eye"></i>Ver</a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="stock-empty"><i class="bi bi-arrow-left-right"></i><strong>Nenhuma movimentação registrada</strong></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($movements, 'hasPages') && $movements->hasPages())
            <div class="stock-pagination">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
