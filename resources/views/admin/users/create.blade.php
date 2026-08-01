@extends('layouts.header')
@section('title', 'Criar Novo Usuário')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-module.css') }}">
@endpush
@section('content')
<div class="admin-module-scope">
<div class="container mt-5">
    <h3>Novo Usuário</h3>

    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        @include('admin.users._form_permissions', ['permissions' => $permissions, 'selectedPermissions' => $selectedPermissions ?? []])


        <button type="submit" class="btn btn-primary">Criar</button>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-module.js') }}"></script>
@endpush
