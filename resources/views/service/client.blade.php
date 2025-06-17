@extends('layouts.header')
@section('title', 'Clientes de Serviço')

@section('content')
<div class="container mt-5">
    <h2>Clientes
        @can('service.create')
        <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addClientModal">
            + Novo Cliente
        </button>
        @endcan
    </h2>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>{{ __('status.' . $client->status) }}</td>
                <td>
                    @can('service.edit')
                    <!-- Botão para editar -->
                    <button
                        class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editClientModal{{ $client->id }}"
                    >Editar</button>
                    @endcan
                    @can('service.delete')
                    <!-- Form delete -->
                    <form action="{{ route('service.clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirma exclusão?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                    @endcan
                </td>
            </tr>

            @include('service.forms.client_modal', ['client' => $client])

            @endforeach
        </tbody>
    </table>

    {{ $clients->links() }}

</div>

@include('service.forms.client_modal', ['client' => null]) {{-- Modal para criação --}}

@endsection
