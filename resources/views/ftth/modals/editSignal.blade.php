<div class="modal fade" id="editSignalModal" tabindex="-1">

    <div class="modal-dialog">

        <form id="formEditSignal" method="POST">

            @csrf
            @method('PUT')

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Alterar Sinal da Fibra
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Fibra</label>
                        <input type="text" id="fiber_name" class="form-control" disabled>
                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Sinal Atual
                        </label>

                        <input type="text" class="form-control" id="old_signal" disabled>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Novo Sinal
                        </label>

                        <input type="number" step="0.01" class="form-control" name="optical_power" id="new_signal"
                            required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button class="btn btn-primary">

                        Salvar

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
