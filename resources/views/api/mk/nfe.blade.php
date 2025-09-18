@extends('layouts.header')
@section('title', 'Notas Fiscais')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold text-bgc-primary">Notas Fiscais</h1>

        {{-- FORM DE BUSCA POR MÊS/ANO --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url('/api/mk/nfe') }}" class="row g-3 align-items-end">
    <div class="col-md-3">
        <label for="month" class="form-label fw-semibold">Mês</label>
        <select name="month" id="month" class="form-select">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                    {{ request('month') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-3">
        <label for="year" class="form-label fw-semibold">Ano</label>
        <select name="year" id="year" class="form-select">
            @for ($year = now()->year; $year >= now()->year - 5; $year--)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check">
            <input type="hidden" name="validate" value="n">
            <input class="form-check-input" type="checkbox" name="validate" id="validate" value="y"
                   {{ request('validate', 'n') === 'y' ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="validate">
                verificar NFE
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn dcm-btn-primary w-100">
            <i class="bi bi-search me-1"></i> Buscar
        </button>
    </div>
</form>

            </div>
        </div>


        @if ($mensagem)
            <div class="alert alert-danger shadow-sm rounded-3">
                {{ $mensagem }}
            </div>
        @endif


        {{-- TABELA DE RESUMO --}}

        <div class="card shadow-sm mb-4">
            <div class="card-header bgc-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Resumo Mensal por Cidade / CFOP / Velocidade / Tipo
                </h5>
                <span class="badge bg-light text-dark">
                    Total de Notas: {{ collect($agrupado)->flatten(3)->sum('quantidade') }}
                </span>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Mês</th>
                            <th class="text-center">IBGE</th>
                            <th class="text-center">CFOP</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Velocidade</th>
                            <th class="text-end">Qtd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agrupado as $cidade => $cfops)
                            @php $primeiraLinhaCidade = true; @endphp
                            @foreach ($cfops as $cfop => $vels)
                                @foreach ($vels as $vel => $tipos)
                                    @foreach ($tipos as $tipo => $dados)
                                        <tr
                                            class="
                                    @if (Str::endsWith($cfop, '03')) table-info
                                    @elseif(Str::endsWith($cfop, '07')) table-warning @endif
                                    {{ $primeiraLinhaCidade ? 'ibge-highlight' : '' }}
                                ">
                                            <td class="text-center">{{ $dados['mes'] }}</td>
                                            <td class="text-center">{{ $cidade }}</td>
                                            <td class="text-center">{{ $cfop }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $tipo === 'dedicado' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($tipo) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark px-3 py-2">
                                                    {{ number_format($dados['veldown'], 0) }} Mbps
                                                </span>
                                            </td>
                                            <td class="text-end">{{ $dados['quantidade'] }}</td>
                                        </tr>
                                        @php $primeiraLinhaCidade = false; @endphp
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CSS customizado para destacar IBGE --}}
        @push('styles')
            <style>
                .ibge-highlight {
                    font-weight: bold;
                    background-color: #f0f8ff !important;
                    /* azul bem suave */
                }

                .ibge-highlight td {
                    padding-top: 0.9rem !important;
                    padding-bottom: 0.9rem !important;
                }
            </style>
        @endpush



        {{-- TABELA DETALHADA --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-semibold rounded-top">
                <i class="bi bi-list-ul me-2"></i> Notas Detalhadas
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Número</th>
                            <th>Emissão</th>
                            <th>Login</th>
                            <th>Cliente</th>
                            <th>IBGE</th>
                            <th>Plano</th>
                            <th>Vel. (Mbps)</th>
                            <th>CFOP</th>
                            <th>Valor</th>
                            <th>Tecnologia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notas as $nota)
                            @php
                                $linhaInvalida = $validate === 'y' && empty($nota['cidade_ibge']);
                            @endphp

                            <tr class="{{ $linhaInvalida ? 'table-danger' : '' }}">
                                <td class="text-center">{{ $nota['id'] }}</td>
                                <td class="text-center">{{ $nota['numero'] ?? '-' }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($nota['emissao'])->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ $mkauthUrl }}/{{ $mkauthSearchLogin }}{{ $nota['login'] }}"
                                        target="_blank" style="color: inherit; text-decoration: none;">
                                        {{ $nota['login'] }}
                                    </a>
                                </td>
                                <td>{{ $nota['cliente_nome'] }}</td>
                                <td class="text-center">{{ $nota['cidade_ibge'] }}</td>
                                <td>{{ $nota['plano_nome'] }}</td>
                                <td class="text-end">
                                    <span class="badge bg-info text-dark">
                                        {{ $nota['veldown'] / 1000 }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $nota['cfop'] }}</td>
                                <td class="text-end fw-semibold">
                                    R$ {{ number_format($nota['valor_total'], 2, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $nota['tecnologia'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    Nenhuma nota encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
