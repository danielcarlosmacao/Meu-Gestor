@extends('layouts.header')
@section('title', 'Gerenciar Usuários')
@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
   @if (session('error'))
    <div class="alert alert-danger mt-2">
        <pre class="mb-0">{{ session('error') }}</pre>
    </div>
@endif


    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Usuários do Sistema</h3>
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-success">+ Adicionar Usuário</a>
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Papéis</th>
                    <th>Permissões</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($user->permissions as $permission)
                                <span class="badge bg-secondary">{{ __('permissions.' . $permission->name) }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.usuarios.edit', $user->id) }}" class="btn btn-sm btn-warning">Editar</a>

                            <form action="{{ route('admin.usuarios.toggle', $user->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="btn btn-sm {{ $user->active ? 'btn-danger' : 'btn-success' }}">
                                    {{ $user->active ? 'Desativar' : 'Reativar' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                    Excluir
                                </button>
                            </form>
                            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST"
                                onsubmit="return confirm('Tem certeza que deseja resetar a senha deste usuário?')">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Resetar Senha</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

@endsection
