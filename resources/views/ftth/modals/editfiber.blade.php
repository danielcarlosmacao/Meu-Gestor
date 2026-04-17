<div class="modal fade" id="modalEditFiber">
    <div class="modal-dialog">
        <form method="POST" id="formEditFiber">
            @csrf
            @method('PUT')

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Editar Sinal da Fibra</h5>
                </div>

                <div class="modal-body">

                    <div class="mb-2">
                        <label>Fibra</label>
                        <input id="editFiberName" class="form-control" disabled>
                    </div>

                    <div class="mb-2">
                        <label>Sinal (dBm)</label>
                        <input type="number" step="0.01" name="optical_power" id="editFiberPower"
                            class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Salvar</button>
                </div>

            </div>
        </form>
    </div>
</div>
