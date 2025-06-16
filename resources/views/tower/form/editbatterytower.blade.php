<!-- Modal -->
<div id="editModal" style="
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1055;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  overflow-y: auto;
">
  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="modal-content shadow" style="max-width: 500px; width: 90%; background-color: white; padding: 20px; border-radius: 8px; position: relative;">

      <!-- Cabeçalho -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-bgc-primary m-0">Editar Bateria</h5>
        <button class="btn-close" onclick="document.getElementById('editModal').style.display='none'" aria-label="Fechar"></button>
      </div>

      <!-- Formulário -->
      <form id="editForm">
        @csrf
        <input type="hidden" id="edit_id">

        <div class="mb-3">
          <label class="form-label">Bateria</label>
          <select id="edit_battery_id" name="battery_id" class="form-select">
            @foreach ($batteries as $battery)
              <option value="{{ $battery->id }}">{{ $battery->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Info</label>
          <input type="text" id="edit_info" name="info" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Quantidade</label>
          <input type="number" id="edit_amount" name="amount" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Instalação</label>
          <input type="date" id="edit_installation_date" name="installation_date" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Remoção</label>
          <input type="date" id="edit_removal_date" name="removal_date" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Ativa?</label>
          <select id="edit_active" name="active" class="form-select">
            <option value="yes">Sim</option>
            <option value="no">Não</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn dcm-btn-primary">Salvar</button>
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').style.display='none'">Cancelar</button>
          <button type="button" id="deleteButton" class="btn btn-danger">Excluir</button>
        </div>
      </form>

    </div>
  </div>
</div>
