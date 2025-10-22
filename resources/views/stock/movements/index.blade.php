@extends('layouts.header')
@section('title', 'Movimentações de Estoque')

@section('content')
<div class="container mt-4">
        <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">Movimentações
            @can('stock.movements.create')
                <a href="{{ route('stock.movements.create') }}" class="btn dcm-btn-primary mb-3"><i class="bi bi-plus"></i></a>
            @endcan
                <a href="{{ route('stock.movements.reportForm') }}" class="btn dcm-btn-primary mb-3"><i class="bi bi-receipt"></i></a>
        </h2>

    </div>

    

    <table class="table table-bordered">
        <thead class="bgc-primary text-white">
            <tr>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Usuário</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $m)
            <tr>
                <td>
                    <span class="badge {{ $m->type === 'input' ? 'bg-success' : 'bg-danger' }}">
                        {{ $m->type === 'input' ? 'Entrada' : 'Saída' }}
                    </span>
                </td>
                <td>{{ $m->description ?? '-' }}</td>
                <td>{{ $m->user->name ?? '-' }}</td>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('stock.movements.show', $m->id) }}" class="btn btn-info btn-sm">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $movements->links() }}
</div>
@endsection
