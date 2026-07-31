@extends('layouts.header')
@section('title', 'Detalhes do Item')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
<div class="container stock-page">
    <div class="stock-form-card">
        <div class="stock-form-header">
            <div class="stock-title-icon"><i class="bi bi-box-seam"></i></div>
            <div>
                <h1 class="stock-title">{{ $item->name }}</h1>
                <p class="stock-subtitle">Detalhes completos do item de estoque.</p>
            </div>
        </div>

        <div class="stock-form-body">
            <div class="stock-detail-grid">
                <div class="stock-detail-item"><span>Estoque atual</span><strong>{{ $item->current_stock }}</strong></div>
                <div class="stock-detail-item"><span>Estoque mínimo</span><strong>{{ $item->min_stock ?? '-' }}</strong></div>
                <div class="stock-detail-item"><span>Preço</span><strong>{{ $item->price ? 'R$ '.number_format($item->price,2,',','.') : '-' }}</strong></div>
                <div class="stock-detail-item"><span>Status</span><strong>{{ __('status.' . $item->status) }}</strong></div>
                <div class="stock-detail-item"><span>Criado em</span><strong>{{ $item->created_at->format('d/m/Y H:i') }}</strong></div>
                <div class="stock-detail-item"><span>Atualizado em</span><strong>{{ $item->updated_at->format('d/m/Y H:i') }}</strong></div>
            </div>
        </div>

        <div class="stock-form-footer">
            <a href="{{ route('stock.items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
            @can('stock.items.edit')
                <a href="{{ route('stock.items.edit', $item->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square me-1"></i>Editar</a>
            @endcan
        </div>
    </div>
</div>
@endsection
