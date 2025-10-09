@extends('layouts.header')
@section('title', 'Criar Papel')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-bgc-primary mb-4">Criar Novo Papel</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nome do Papel</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ex: gestor, atendente" required>
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
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                    <label class="form-check-label">{{ __('permissions.' . $permission->name) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
        </div>
    </form>
</div>
@endsection
