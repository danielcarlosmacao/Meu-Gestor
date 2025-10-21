<!-- Modal de Bateria -->
<div id="batteryModal"
    style="display: none; position: fixed; z-index: 1055; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto;">

    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="modal-content shadow"
            style="max-width: 500px; width: 90%; background-color: white; position: relative; padding: 20px; border-radius: 8px;">

            <!-- Cabeçalho -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0 text-bgc-primary">Adicionar Bateria à Torre</h5>
                <button class="btn-close" onclick="document.getElementById('batteryModal').style.display = 'none'"
                    aria-label="Fechar"></button>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulário -->
            <form action="{{ url("/towers/{$tower->id}/battery") }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Bateria:</label>
                    <select name="battery_id" class="form-select" required>
                        @foreach ($batteries as $battery)
                            <option value="{{ $battery->id }}">
                                {{ $battery->name }} - {{ $battery->mark }} ({{ $battery->amps }}A)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Info:</label>
                    <input type="text" name="info" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantidade:</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Instalação:</label>
                    <input type="date" name="installation_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Remoção:</label>
                    <input type="date" name="removal_date" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">Ativa?</label>
                    <select name="active" class="form-select" required>
                        <option value="yes">Sim</option>
                        <option value="no" selected>Não</option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
