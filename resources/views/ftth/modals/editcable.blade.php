<div class="modal fade" id="editCableModal" tabindex="-1" aria-labelledby="editCableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <form id="editCableForm" method="POST" action="" class="modal-content shadow-lg border-0 rounded-3">
            @csrf
            @method('PUT')

            {{-- HEADER --}}
            <div class="modal-header bgc-primary text-white">

                <h5 class="modal-title fw-bold" id="editCableModalLabel">
                    <i class="bi bi-pencil-square me-1"></i>
                    Editar cabo
                </h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>

            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <input type="hidden" id="editCableId">

                <div class="mb-3">

                    <label for="editCableInfo" class="form-label fw-semibold">
                        Nome ou informação do cabo
                    </label>

                    <input type="text" id="editCableInfo" name="info" class="form-control" maxlength="255"
                        required>

                </div>

                <div class="mb-3">

                    <label for="editCableColorHex" class="form-label fw-semibold">
                        Cor do cabo
                    </label>

                    <div class="d-flex align-items-center gap-2">

                        <input type="color" id="editCableColorPicker" class="form-control form-control-color"
                            value="#000000" title="Escolha uma cor">

                        <input type="text" id="editCableColorHex" name="color" class="form-control" value="#000000"
                            maxlength="7" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#000000" required>

                    </div>

                    <div class="form-text">
                        Essa cor será utilizada no mapa.
                    </div>

                </div>

                <div class="cable-edit-preview">

                    <span id="editCablePreviewColor" class="cable-edit-preview-color"></span>

                    <div>

                        <strong id="editCablePreviewName">
                            Cabo sem descrição
                        </strong>

                        <small class="d-block text-muted">
                            Pré-visualização
                        </small>

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bg-light">

                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>

                <button type="submit" class="btn dcm-btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>
                    Salvar alterações
                </button>

            </div>

        </form>

    </div>
</div>
