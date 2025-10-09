@extends('layouts.header')
@section('title', 'Relatório de Manutenções por Período')
@section('content')

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h3 class="text-center mb-4">Gerar Relatório de Manutenções</h3>

            <form method="GET" action="{{ route('vehicle-maintenance.report.pdf') }}" target="_blank" class="row g-3 justify-content-center">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-bold">Data Inicial</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ date('Y-m-01') }}" 
                           class="form-control shadow-sm" required>
                </div>

                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-bold">Data Final</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="{{ date('Y-m-d') }}" 
                           class="form-control shadow-sm" required>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-center gap-2">
                    <button type="submit" name="action" value="view" class="btn btn-outline-primary w-50">
                        <i class="bi bi-eye"></i> Visualizar
                    </button>
                    <button type="submit" name="action" value="download" class="btn btn-outline-success w-50">
                        <i class="bi bi-download"></i> Baixar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
