<!-- Modal de edição de equipamento -->
<div id="editEquipmentModal" style="
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
        <h5 class="text-bgc-primary m-0">Editar Equipamento</h5>
        <button class="btn-close" onclick="document.getElementById('editEquipmentModal').style.display='none'" aria-label="Fechar"></button>
      </div>

      <!-- Formulário -->
      <form id="editEquipmentForm">
        @csrf
        <input type="hidden" id="edit_equipment_id_hidden">

        <div class="mb-3">
          <label class="form-label">Equipamento:</label>
          <select id="edit_equipment_id" name="equipment_id" class="form-select">
            @foreach ($equipments as $equipment)
              <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Identificação:</label>
          <input type="text" id="edit_identification" name="identification" class="form-control">
        </div>

        <div class="mb-4">
          <label class="form-label">Ativo?</label>
          <select id="edit_equipment_active" name="active" class="form-select">
            <option value="yes">Sim</option>
            <option value="no">Não</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn dcm-btn-primary">Salvar</button>
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('editEquipmentModal').style.display='none'">Cancelar</button>
          <button type="button" id="deleteEquipmentBtn" class="btn btn-danger">Excluir</button>
        </div>
      </form>
    </div>
  </div>
</div>
