@extends('layouts.header')
@section('title', 'Editar Perfil')

@section('content')

<div class="container mt-5">
    <h2 class="text-center">Editar Perfil</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="mt-4 col-md-6 offset-md-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Usuario</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required readonly>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="current_password" class="form-label">Senha Atual</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nova Senha (opcional)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="d-flex justify-content-center">
            <button class="btn dcm-btn-primary" type="submit">
                <i class="bi bi-save"></i> Atualizar Perfil
            </button>
        </div>
    </form>
</div>

@endsection
