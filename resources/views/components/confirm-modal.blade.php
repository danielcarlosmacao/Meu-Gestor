<form id="confirmForm" method="POST">
    @csrf
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold">
                        Confirmação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="confirmMessage" class="mb-1 fw-semibold"></p>
                    <small id="confirmSubMessage" class="text-muted"></small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">
                        Confirmar
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>
