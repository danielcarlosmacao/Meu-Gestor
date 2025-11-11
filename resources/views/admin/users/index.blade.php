@extends('layouts.header')
@section('title', 'Gerenciar Usuários')

@section('content')


<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Usuários do Sistema</h2>
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Adicionar Usuário
        </a>
    </div>
    @if(session('new_password'))
    <div class="alert alert-warning shadow-sm">
        <strong>Nova senha gerada:</strong> 
        <span style="font-size: 1.2em">{{ session('new_password') }}</span>
        <br>
        <small>Atualize a página para ocultar esta mensagem.</small>
    </div>
@endif


    <div class="table-responsive shadow-sm rounded">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Papéis</th>
                    <th>Permissões</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="{{ !$user->active ? 'user-inactive' : '' }}">
                    <td><span class="fw-semibold fs-5">{{ $user->name }}</span></td>
                    <td>{{ $user->email}}</td>
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
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm dcm-btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i> Ações
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.usuarios.edit', $user->id) }}">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.usuarios.toggle', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja mudar o status desse usuario?')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi {{ $user->active ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                            {{ $user->active ? 'Desativar' : 'Reativar' }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="confirmAction(this,'Realmente deseja resetar a senha desse usuario?')">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-key"></i> Resetar Senha
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" onsubmit="confirmAction(this, 'Deseja realmente Excluir esse usuario?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmAction(form, message) {
        event.preventDefault();
        Swal.fire({
            title: 'Confirmação',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush

