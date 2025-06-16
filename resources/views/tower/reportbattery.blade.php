@extends('layouts.header')
@section('title', 'Relatório de Produção')

@section('content')
<div class="container mt-4">
    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">{{ $productions[0]->battery->name ?? 'Desconhecida' }}</h2>
    </div>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="todas" {{ $status == 'todas' ? 'selected' : '' }}>Todas</option>
                    <option value="ativas" {{ $status == 'ativas' ? 'selected' : '' }}>Apenas Ativas</option>
                    <option value="inativas" {{ $status == 'inativas' ? 'selected' : '' }}>Apenas Inativas</option>
                </select>
            </div>
        </div>
    </form>
    

    <p>
        Média de tempo em produção de {{ $mediaAnos }} anos da bateria 
        <strong></strong>
    </p>
    <div class="container table-responsive">
        <table class="table table-striped">
            <thead class="bgc-primary text-white">
                <tr>
                    <th>Torre</th>
                    <th>Voltagem</th>
                    <th>Quant</th>
                    <th>Data Instalação</th>
                    <th>Data Remoção</th>
                    <th>Tempo em Produção</th>
                    <th>% Produção</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productions as $p)
                    @php
                        $start = \Carbon\Carbon::parse($p->installation_date);
                        $end = $p->removal_date ? \Carbon\Carbon::parse($p->removal_date) : now();
                        $tempoDias = $start->diffInDays($end);
                        $anos = floor($tempoDias / 365);
                        $meses = floor(($tempoDias % 365) / 30);

                        $percentual = $p->production_percentage;
                        $linha_negrito = is_null($p->removal_date);
                    @endphp
                    <tr class="{{ $linha_negrito ? 'fw-bold' : '' }}">
                        <td>{{ $p->tower->name ?? 'Torre não encontrada' }}</td>
                        <td>{{ $p->tower->voltage ?? 'V' }}</td>
                        <td>{{ $p->amount }}</td>
                        <td>{{ $p->data_instalacao_formatada }}</td>
                        <td>{{ $p->data_remocao_formatada }}</td>
                        <td>{{ $p->tempo_formatado }}</td>
                        <td>{{ $p->production_percentage ? number_format($p->production_percentage, 2) : '' }}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
