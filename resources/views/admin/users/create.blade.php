@extends('layouts.header')
@section('title', 'Criar Novo Usuário')
@section('content')

<div class="container mt-5">
    <h3>Novo Usuário</h3>

    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <div class="mb-3">
            <label for="roles" class="form-label">Papéis</label><br>
            @foreach($roles as $role)
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $role->name }}">
                    <label class="form-check-label">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>

@php
    $selectedPermissions = isset($user) ? $user->permissions->pluck('name')->toArray() : [];
@endphp

<div class="mb-4">
    <label class="form-label fw-bold">Permissões Individuais</label>

    <div class="row">
        {{-- Grupo: Torres --}}
        <div class="col-md-4 mb-3">
            <div class="border rounded p-3 shadow-sm h-100">
                <h6 class="fw-bold">Gestor de Torres</h6>
                @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'towers')) as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->name }}"
                               {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Grupo: Frota --}}
        <div class="col-md-4 mb-3">
            <div class="border rounded p-3 shadow-sm h-100">
                <h6 class="fw-bold">Gestor de Frota</h6>
                @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'fleets')) as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->name }}"
                               {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- Grupo: Ferias --}}
        <div class="col-md-4 mb-3">
            <div class="border rounded p-3 shadow-sm h-100">
                <h6 class="fw-bold">Gestor de Ferias</h6>
                @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'vacations')|| str_contains($p->name, 'vacation_manager') || str_contains($p->name, 'collaborators')) as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->name }}"
                               {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        

        {{-- Grupo: Usuários --}}
        <div class="col-md-4 mb-3">
            <div class="border rounded p-3 shadow-sm h-100">
                <h6 class="fw-bold">Administrador</h6>
                @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'user') || str_contains($p->name, 'admin')) as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->name }}"
                               {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                    </div>
                @endforeach
            </div>
        </div></div>



        <button type="submit" class="btn btn-primary">Criar</button>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
