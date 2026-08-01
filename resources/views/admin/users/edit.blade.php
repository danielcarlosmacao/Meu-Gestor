@extends('layouts.header')
@section('title', 'Editar Usuário')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-module.css') }}">
@endpush
@section('content')
<div class="admin-module-scope">
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

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-module.js') }}"></script>
@endpush
