@extends('layouts.header')
@section('title', 'Relatório de Movimentações')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/stock-module.css') }}">
@endpush

@section('content')
@php
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $today = \Carbon\Carbon::now()->format('Y-m-d');
@endphp

<div class="container stock-page">
    <div class="stock-form-card stock-report-card">
        <div class="stock-form-header">
            <div class="stock-title-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
            <div>
                <h1 class="stock-title">Relatório de movimentações</h1>
                <p class="stock-subtitle">Selecione o período e o tipo de movimentação.</p>
            </div>
        </div>

        <form action="{{ route('stock.movements.reportView') }}" method="GET" target="_blank" data-stock-report-form>
            <div class="stock-form-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Data inicial</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', $startOfMonth) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Data final</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', $today) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo</label>
                        <select name="type" id="type" class="form-select">
                            <option value="all" @selected(request('type') === 'all')>Todos</option>
                            <option value="input" @selected(request('type') === 'input')>Entradas</option>
                            <option value="output" @selected(request('type') === 'output')>Saídas</option>
                        </select>
                    </div>
                </div>

                <div class="stock-note mt-4">
                    <i class="bi bi-info-circle me-1"></i>
                    O relatório será aberto em uma nova aba, pronto para visualização ou impressão.
                </div>
            </div>

            <div class="stock-form-footer">
                <a href="{{ route('stock.movements.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
                <button class="btn dcm-btn-primary" type="submit"><i class="bi bi-search me-1"></i>Gerar relatório</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/stock-module.js') }}"></script>
@endpush
