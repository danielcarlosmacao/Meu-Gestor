@extends('layouts.header')
@section('title', 'Recursos do sistema')
@section('content')

<div class="container py-4">
    <h2>Editar Recursos do sistema</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <form action="{{ route('options.resource.update') }}" method="POST" class="mt-4">
        @csrf

        <div class="form-group mb-3">
            <label for="hours_Generation">Horas de geração:</label>
            <input type="number" id="hours_Generation" name="hours_Generation" class="form-control" value="{{ $options['hours_Generation'] ?? '5' }}">
            @error('hours_Generation') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="hours_autonomy">Horas de autonomia</label>
            <input type="number" id="hours_autonomy" name="hours_autonomy" class="form-control" value="{{ $options['hours_autonomy'] ?? '48' }}">
            @error('hours_autonomy') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="form-group mb-3">
            <label for="pagination">Registro por paginas</label>
            <input type="number" id="pagination" name="pagination" class="form-control" value="{{ $options['pagination'] ?? '20' }}">
            @error('pagination') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
        <a href="" class="btn btn-warning" role="button" aria-pressed="true" onclick="mostrarPopupConfirmacao(); return false;">Reparar Torres</a>
    </form>
    <br>
<div class="card mb-4">
    <div class="card-header">
        <strong>Atualização do Sistema</strong>
    </div>
    <div class="card-body">
        <button id="deploy-button" class="btn btn-warning" disabled>
            Atualizar Sistema
        </button>

        <div id="deploy-result" class="mt-3"></div>
    </div>
</div>




</div>
@push('scripts')
<script>
document.getElementById('deploy-button').addEventListener('click', function () {
    if (!confirm('Deseja realmente atualizar o sistema?')) return;

    const button = this;
    button.disabled = true;
    button.innerText = 'Atualizando...';
    document.getElementById('deploy-result').innerHTML = '';

    fetch('{{ route('deploy.manual', ['token' => env('DEPLOY_TOKEN')]) }}')
        .then(async response => {
            const contentType = response.headers.get("content-type");

            if (!response.ok) {
                const text = await response.text();
                throw new Error(`Erro ${response.status}: ${text.slice(0, 200)}`);
            }

            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                const text = await response.text();
                throw new Error('Resposta inesperada: ' + text.slice(0, 200));
            }
        })
        .then(data => {
            document.getElementById('deploy-result').innerHTML = `
                <div class="alert alert-success">
                    <strong>Atualização concluída com sucesso!</strong><br>
                    <pre>${data.output.join('\n')}</pre>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('deploy-result').innerHTML = `
                <div class="alert alert-danger">
                    Erro ao atualizar: ${error.message}
                </div>
            `;
        })
        .finally(() => {
            button.disabled = false;
            button.innerText = 'Atualizar Sistema';
        });
});
</script>
@endpush


@endsection