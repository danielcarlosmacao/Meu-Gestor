@extends('layouts.header')
@section('title', 'Editar Usuário')
@section('content')
<div class="container mt-5">
    <h2 class="text-center">{{ $user->name }}</h2>

    <form method="POST" action="{{ route('admin.usuarios.update', $user->id) }}">
        @csrf
        @method('PUT')

        @include('admin.users._form_permissions', ['permissions' => $permissions, 'selectedPermissions' => $selectedPermissions ?? []])

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
