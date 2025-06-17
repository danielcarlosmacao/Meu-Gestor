@extends('layouts.header')
@section('title', 'Recursos do sistema')

@section('content')
    <div class="container py-4">
        <h2>Gestao do sistema</h2>

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        <!-- Exportar Banco -->
        <div class="card mt-4">
            <div class="card-body">
                <form action="{{ route('database.export') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Exportar Banco de Dados</button>
                </form>
            </div>
        </div>

        <!-- Importar Banco -->
        <div class="card mt-4">
            <div class="card-body">
                <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="sql_file" class="form-label">Importar banco (.sql)</label>
                        <input type="file" name="sql_file" id="sql_file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Importar Banco</button>
                </form>

            </div>
        </div>


       <!-- Atualizar Sistema com Animação -->
<div class="card mt-4">
    <div class="card-body">
        <form id="update-form" action="{{ route('system.update') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning" id="update-btn">
                <span id="btn-text">Atualizar Sistema via Git</span>
                <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </div>
</div>
    </div>


    @push('scripts')
<script>
    document.getElementById('update-form').addEventListener('submit', function () {
        const btn = document.getElementById('update-btn');
        document.getElementById('btn-text').classList.add('d-none');
        document.getElementById('btn-spinner').classList.remove('d-none');
        btn.disabled = true;
    });
</script>
@endpush
@endsection
