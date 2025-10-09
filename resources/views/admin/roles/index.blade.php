@extends('layouts.header')
@section('title', 'Gerenciar Roles')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-bgc-primary">Roles do Sistema</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn dcm-btn-primary">
            <i class="bi bi-plus-circle"></i> Adicionar Role
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="fw-semibold">{{ $role->name }}</td>
                            <td>{{ $role->description ?? '-' }}</td>
<td class="text-center align-middle p-1">
    <div class="btn-group">
        <button type="button" class="btn btn-sm dcm-btn-primary dropdown-toggle"
            data-bs-toggle="dropdown">
            <i class="bi bi-gear"></i> Ações
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="dropdown-item">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
            </li>
            <li>
                @if ($role->name !== 'administrator')
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" 
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item"
                            onclick="return confirm('Tem certeza que deseja excluir esta role?');">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </form>
                @else
                    <button class="dropdown-item text-muted" disabled>
                        <i class="bi bi-shield-lock"></i> Protegida
                    </button>
                @endif
            </li>
        </ul>
    </div>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 text-muted">
                                Nenhuma role cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
