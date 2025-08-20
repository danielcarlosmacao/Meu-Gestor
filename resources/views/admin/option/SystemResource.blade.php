@extends('layouts.header')
@section('title', 'Recursos do sistema')

@section('content')
    <div class="container py-4">
        <h2>Gestão do sistema</h2>


        <!-- Exportar Banco -->
        <div class="card mt-4">
            <div class="card-body">
                <form id="export-form" action="{{ route('database.export') }}" method="POST">
                    @csrf
                    <button type="button" class="btn dcm-btn-primary" onclick="confirmExport()">Exportar Banco de
                        Dados</button>
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
                <form id="update-form" action="{{ route('system.update') }}" method="POST">
                    @csrf
                    <label for="upadte" class="form-label">Versão:
                        {{ trim(file_get_contents(base_path('VERSION'))) }}</label><br>
                    <button type="button" class="btn btn-warning" id="update-btn" onclick="confirmUpdate()">
                        <span id="btn-text">Atualizar Sistema via Git</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Ver Logs do Sistema -->
        <div class="card mt-4">
            <div class="card-body">
                <label class="form-label">Logs do sistema</label><br>
                <a href="{{ route('systemlogs.index') }}" class="btn btn-primary mt-2">
                    Ver Logs
                </a>
            </div>
        </div>
    </div>


    <!-- Modal de confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <p id="modal-message">Tem certeza que deseja continuar?</p>
                    <div id="password-field" class="mt-3 d-none">
                        <label for="confirm-password" class="form-label">Digite sua senha para confirmar:</label>
                        <input type="password" id="confirm-password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-danger me-auto" id="password-error" style="display:none;"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-action-btn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentAction = null;
            let requirePassword = false;

            function confirmExport() {
                requirePassword = false;
                currentAction = () => document.getElementById('export-form').submit();
                showModal("Tem certeza que deseja exportar o banco de dados?");
            }

            function confirmImport() {
                const fileInput = document.getElementById('sql_file');
                if (!fileInput.value) {
                    alert("Por favor, selecione um arquivo .sql");
                    return;
                }

                requirePassword = true;
                currentAction = () => {
                    const password = document.getElementById('confirm-password').value;

                    if (!password) {
                        document.getElementById('password-error').innerText = "Senha é obrigatória.";
                        document.getElementById('password-error').style.display = "block";
                        return;
                    }

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'password';
                    hiddenInput.value = password;
                    document.getElementById('import-form').appendChild(hiddenInput);

                    document.getElementById('import-form').submit();
                };

                showModal("Tem certeza que deseja importar o banco de dados?");
            }

            function confirmUpdate() {
                requirePassword = false;
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
                document.getElementById('password-error').style.display = "none";
                document.getElementById('confirm-password').value = "";

                const passwordField = document.getElementById('password-field');
                if (requirePassword) {
                    passwordField.classList.remove('d-none');
                } else {
                    passwordField.classList.add('d-none');
                }

                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();

                document.getElementById('confirm-action-btn').onclick = function() {
                    if (requirePassword && !document.getElementById('confirm-password').value) {
                        document.getElementById('password-error').innerText = "Senha é obrigatória.";
                        document.getElementById('password-error').style.display = "block";
                        return;
                    }

                    modal.hide();
                    currentAction();
                };
            }
        </script>
    @endpush
@endsection
