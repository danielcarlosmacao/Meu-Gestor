@extends('layouts.header')
@section('title', 'Editar Permissões')
@section('content')

    <div class="container mt-5">
        <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
            <h2 class="text-center">
                {{ $user->name }}
            </h2>

        </div>
        <h3></h3>

        <form method="POST" action="{{ route('admin.usuarios.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="roles" class="form-label">Perfil:</label><br>
                @foreach ($roles as $role)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ $user->roles->contains('name', $role->name) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>

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
                                        {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
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
                                        {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
          

                    {{-- Grupo: Frota --}}
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 shadow-sm h-100">
                            <h6 class="fw-bold">Gestor de Serviços</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'service')) as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
          
                    {{-- Grupo: Ferias --}}
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 shadow-sm h-100">
                            <h6 class="fw-bold">Gestor Ferias</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'vacations') || str_contains($p->name, 'vacation_manager') || str_contains($p->name, 'collaborators')) as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Grupo: Usuários --}}
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 shadow-sm h-100">
                            <h6 class="fw-bold">Administrador</h6>
                            @foreach ($permissions->filter(fn($p) => str_contains($p->name, 'user') || str_contains($p->name, 'admin') || str_contains($p->name, 'api')) as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ $user->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

@endsection
