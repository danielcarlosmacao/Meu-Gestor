@extends('layouts.header')
@section('title', 'Clientes NFE')

@section('content')
<div class="container py-4">

    <h1 class="mb-4 fw-bold text-bgc-primary">Clientes NFE</h1>

    {{-- Checkbox de validação --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url('/api/mk/clinfe') }}" class="row g-3 align-items-end">

                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check">
                        <input type="hidden" name="validate" value="n">
                        <input class="form-check-input" type="checkbox" name="validate" id="validate" value="y"
                            {{ request('validate', 'n') === 'y' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="validate">
                            Validar clientes
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn dcm-btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Atualizar
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Mensagem --}}
    @if ($mensagem)
        <div class="alert alert-danger shadow-sm rounded-3">
            {{ $mensagem }}
        </div>
    @endif

    {{-- Tabela de clientes --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold rounded-top">
            <i class="bi bi-list-ul me-2"></i> Lista de Clientes
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Cidade IBGE</th>
                        <th>Local DICI</th>
                        <th>Plano</th>
                        <th>Tipo</th>
                        <th>Vel. (Mbps)</th>
                        <th>Ativado</th>
                        <th>Bloqueado</th>
                        <th>Gerar NFE</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td class="text-center">{{ $cli['id'] }}</td>
                            <td>{{ $cli['cliente_nome'] }}</td>
                            <td class="text-center">{{ $cli['cidade_ibge'] }}</td>
                            <td class="text-center">{{ $cli['local_dici'] }}</td>
                            <td>{{ $cli['plano_nome'] }}</td>
                            <td class="text-center">{{ $cli['tipo'] }}</td>
                            <td class="text-end">
                                <span class="badge bg-info text-dark">
                                    {{ $cli['veldown'] / 1000 }}
                                </span>
                            </td>
                            <td class="text-center">{{ $cli['cli_ativado'] }}</td>
                            <td class="text-center">{{ $cli['bloqueado'] }}</td>
                            <td class="text-center">{{ $cli['geranfe'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                Nenhum cliente encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
