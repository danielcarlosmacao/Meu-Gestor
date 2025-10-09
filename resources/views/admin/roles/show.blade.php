@extends('layouts.header')
@section('title', 'Detalhes do Papel')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3 fw-bold text-primary">{{ $role->name }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold">Permissões</h5>
            @if($role->permissions->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($role->permissions as $permission)
                        <li class="list-group-item">
                            {{ __('permissions.' . $permission->name) }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Este papel não possui permissões atribuídas.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
