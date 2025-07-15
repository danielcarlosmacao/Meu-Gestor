@extends('layouts.header')
@section('title', 'Relatório de Manutenções')
@section('content')

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h3 class="text-center mb-4">Gerar Relatório de Manutenções</h3>

            <form method="GET" action="{{ route('vehicle-maintenance.report.pdf') }}" target="_blank" class="row g-3 justify-content-center">
                <div class="col-md-3">
                    <label for="month" class="form-label fw-bold">Mês</label>
                    <select name="month" id="month" class="form-select shadow-sm" required>
                        <option value="" disabled selected>Selecione</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="year" class="form-label fw-bold">Ano</label>
                    <select name="year" id="year" class="form-select shadow-sm" required>
                        <option value="" disabled selected>Selecione</option>
                        @for ($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
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
