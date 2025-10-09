<!-- Modal de Placa -->
<div id="plateModal" style="
  display: none;
  position: fixed;
  z-index: 1055;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow-y: auto;
  background-color: rgba(0,0,0,0.5);
">
  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="modal-content shadow" style="max-width: 450px; width: 90%; background-color: #fff; padding: 20px; border-radius: 8px; position: relative;">

      <!-- Cabeçalho -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 text-bgc-primary">Adicionar Placa à Torre</h5>
        <button class="btn-close" onclick="document.getElementById('plateModal').style.display='none'" aria-label="Fechar"></button>
      </div>

      <!-- Formulário -->
      <form action="{{ url("/towers/{$tower->id}/plate") }}" method="POST">
        @csrf

        <div class="mb-3">
          <label for="plate_id" class="form-label">Placa:</label>
          <select name="plate_id" id="plate_id" class="form-select" required>
            @foreach ($plates as $plate)
              <option value="{{ $plate->id }}">{{ $plate->name }} - {{ $plate->amps }}A - {{ $plate->watts }}W</option>
            @endforeach
          </select>
        </div>

        <div class="mb-4">
          <label for="installation_date" class="form-label">Data de Instalação:</label>
          <input type="date" name="installation_date" id="installation_date" class="form-control">
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn dcm-btn-primary">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>
