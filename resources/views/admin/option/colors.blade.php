@extends('layouts.header')
@section('title', 'Tema')
@section('content')

<div class="container py-4">
    <h2>Editar Cores do Sistema</h2>


    <form action="{{ route('options.colors.update') }}" method="POST" class="mt-4">
        @csrf

        <div class="form-group mb-3">
            <label for="color-primary">Cor Primária</label>
            <input type="color" id="color-primary" name="color-primary" class="form-control" value="{{ $options['color-primary'] ?? '#24b153' }}">
            @error('color-primary') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="color-secondary">Cor Secundária</label>
            <input type="color" id="color-secondary" name="color-secondary" class="form-control" value="{{ $options['color-secondary'] ?? '#6fbe89' }}">
            @error('color-secondary') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="color-text">Cor do Texto</label>
            <input type="color" id="color-text" name="color-text" class="form-control" value="{{ $options['color-text'] ?? '#0a6428' }}">
            @error('color-text') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="color-hover">Cor hover do Botao</label>
            <input type="color" id="color-hover" name="color-hover" class="form-control" value="{{ $options['color-hover'] ?? '#186d34' }}">
            @error('color-hover') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="color-primary-login">Cor degrade login</label>
            <input type="color" id="color-primary-login" name="color-primary-login" class="form-control" value="{{ $options['color-primary-login'] ?? '#186d34' }}">
            @error('color-primary-login') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="color-secondary-login">Cor degrade login</label>
            <input type="color" id="color-secondary-login" name="color-secondary-login" class="form-control" value="{{ $options['color-secondary-login'] ?? '#186d34' }}">
            @error('color-secondary-login') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
    </form>

</div>
<div class="container mt-5">
    <h3>Atualizar Logo do Sistema</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('options.logo.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="logo" class="form-label">Logo Atual</label><br>
            @if($logo)
                <img src="{{ asset($logo) }}" alt="Logo atual" style="height: 80px;">
            @else
                <p>Nenhuma logo cadastrada.</p>
            @endif
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Nova Logo (PNG, JPG, até 2MB)</label>
            <input type="file" name="logo" class="form-control">
            @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Salvar</button>
    </form>
</div>

@endsection