<!-- Modal -->
<div id="equipmentModal" style="
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
        <h5 class="m-0 text-bgc-primary">Adicionar Equipamento</h5>
        <button class="btn-close" onclick="document.getElementById('equipmentModal').style.display='none'" aria-label="Fechar"></button>
      </div>

      <!-- Formulário -->
      <form action="{{ url("/towers/{$tower->id}/equipment") }}" method="POST">
        @csrf

        <div class="mb-3">
          <label for="equipment_id" class="form-label">Equipamento:</label>
          <select name="equipment_id" class="form-select" required>
            @foreach ($equipments as $equipment)
              <option value="{{ $equipment->id }}">
                {{ $equipment->name }} - {{ $equipment->watts }}W
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label for="identification" class="form-label">Identificação:</label>
          <input type="text" name="identification" class="form-control">
        </div>

        <div class="mb-4">
          <label for="active" class="form-label">Ativo?</label>
          <select name="active" class="form-select" required>
            <option value="yes">Sim</option>
            <option value="no">Não</option>
          </select>
        </div>

        <div class="text-end">
          <button type="submit" class="btn dcm-btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
