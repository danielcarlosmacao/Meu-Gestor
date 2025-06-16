@extends('layouts.header')
@section('title', 'Gerenciar Colaboradores')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Colaboradores
        @can('collaborators.create')
        <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addCollaboratorModal">
            <i class="bi bi-plus-lg"></i>
        </button>
        @endcan
    </h2>

    <table class="table table-striped mt-4">
        <thead class="bgc-primary">
            <tr>
                <th>Nome</th>
                <th>Data de Admissão</th>
                <th>Cor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collaborators as $collaborator)
            <tr>
                <td>{{ $collaborator->name }}</td>
                <td>{{ \Carbon\Carbon::parse($collaborator->admission_date)->format('d/m/Y') }}</td>
                <td>
                    <div style="width: 20px; height: 20px; background-color: {{ $collaborator->color }}; border-radius: 4px;"></div>
                </td>
                <td>{{ __('status.' . $collaborator->status) }}</td>
                <td>
                    @can('collaborators.edit')
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCollaboratorModal{{ $collaborator->id }}">
                        Editar
                    </button>
                    @endcan
                </td>
            </tr>

            {{-- Modal de Edição --}}
            @include('vacation_manager.collaborators._form', ['collaborator' => $collaborator])
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $collaborators->links() }}
    </div>
</div>

{{-- Modal de Adição --}}
@include('vacation_manager.collaborators._form', ['collaborator' => null])
@endsection
