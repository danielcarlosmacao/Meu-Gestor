@extends('layouts.header')
@section('title', 'Recursos do sistema')

@section('content')
<div class="container py-4">
    <h2>Gestão do sistema</h2>

    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

   @if (session('error'))
    <div class="alert alert-danger mt-2">
        <pre class="mb-0">{{ session('error') }}</pre>
    </div>
@endif


    <!-- Exportar Banco -->
    <div class="card mt-4">
        <div class="card-body">
            <form id="export-form" action="{{ route('database.export') }}" method="POST">
                @csrf
                <button type="button" class="btn dcm-btn-primary" onclick="confirmExport()">Exportar Banco de Dados</button>
            </form>
        </div>
    </div>

    <!-- Importar Banco -->
    <div class="card mt-4">
        <div class="card-body">
            <form id="import-form" action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="sql_file" class="form-label">Importar banco (.sql)</label>
                    <input type="file" name="sql_file" id="sql_file" class="form-control" required>
                </div>
                <button type="button" class="btn dcm-btn-primary" onclick="confirmImport()">Importar Banco</button>
            </form>
        </div>
    </div>

    <!-- Atualizar Sistema -->
    <div class="card mt-4">
        <div class="card-body">
 <form method="POST" action="{{ route('system.update') }}">
    @csrf
    <button class="btn btn-warning" onclick="return confirm('Deseja realmente atualizar o sistema?')">
        Atualizar Sistema
    </button>
</form>
        </div>
    </div>
</div>

<!-- Modais -->
<!-- Modal de confirmação genérica -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="confirmModalLabel">Confirmação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modal-message">
        Tem certeza que deseja continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirm-action-btn">Confirmar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    let currentAction = null;

    function confirmExport() {
        currentAction = () => document.getElementById('export-form').submit();
        showModal("Tem certeza que deseja exportar o banco de dados?");
    }

    function confirmImport() {
        const fileInput = document.getElementById('sql_file');
        if (!fileInput.value) {
            alert("Por favor, selecione um arquivo .sql");
            return;
        }
        currentAction = () => document.getElementById('import-form').submit();
        showModal("Tem certeza que deseja importar o banco de dados?");
    }

    function confirmUpdate() {
        currentAction = () => {
            document.getElementById('btn-text').classList.add('d-none');
            document.getElementById('btn-spinner').classList.remove('d-none');
            document.getElementById('update-btn').disabled = true;
            document.getElementById('update-form').submit();
        };
        showModal("Tem certeza que deseja atualizar o sistema?");
    }

    function showModal(message) {
        document.getElementById('modal-message').innerText = message;
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();

        // Liga a ação confirmada ao botão
        document.getElementById('confirm-action-btn').onclick = function () {
            modal.hide();
            currentAction();
        };
    }
</script>
@endpush
@endsection
