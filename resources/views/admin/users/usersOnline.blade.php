@extends('layouts.header')
@section('title', 'Usuários Online')

@section('content')
    <div class="container mt-5">
        <h3>Usuários Logados</h3>
        @if (count($users))
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>IP</th>
                        <th>Navegador</th>
                        <th>Última Atividade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>{{ $user['ip_address'] }}</td>
                            <td>{{ Str::limit($user['user_agent'], 50) }}</td>
                            <td>{{ $user['last_activity'] }}</td>
                            <td>
    <form method="POST" action="{{ route('admin.sessions.destroy', $user['id']) }}" onsubmit="return confirm('Tem certeza que deseja encerrar esta sessão?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">Remover Sessão</button>
    </form>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-warning">Nenhum usuário online.</div>
        @endif
    </div>
@endsection
