@extends('layouts.header')
@section('title', 'Editar Role')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-bgc-primary mb-4">Editar Role: {{ $role->name }}</h1>

    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ $role->name }}" 
                   {{ $role->name === 'administrator' ? 'readonly' : '' }}>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Permissões</label>
            <div class="row">
                @php
                    $grupos = [
                        'Gestor de Torres' => fn($p) => str_contains($p->name, 'towers'),
                        'Gestor de Frota' => fn($p) => str_contains($p->name, 'fleets'),
                        'Gestor de Serviços' => fn($p) => str_contains($p->name, 'service'),
                        'Gestor de Férias' => fn($p) => str_contains($p->name, 'vacations') || str_contains($p->name, 'vacation_manager') || str_contains($p->name, 'collaborators'),
                        'Gestor Extras' => fn($p) => str_contains($p->name, 'recipients') || str_contains($p->name, 'notification') || str_contains($p->name, 'api'),
                        'Administrador' => fn($p) => str_contains($p->name, 'user') || str_contains($p->name, 'admin'),
                    ];
                @endphp

                @foreach($grupos as $titulo => $filtro)
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 shadow-sm h-100">
                            <h6 class="fw-bold">{{ $titulo }}</h6>
                            @foreach ($permissions->filter($filtro) as $permission)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                           name="permissions[]"
                                           value="{{ $permission->name }}"
                                           {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}
                                           {{ $role->name === 'administrator' ? 'disabled' : '' }}>
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn dcm-btn-primary"
                {{ $role->name === 'administrator' ? 'disabled' : '' }}>
            <i class="bi bi-save"></i> Salvar
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
