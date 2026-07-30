{{-- ================================================================
    MODAL — EDITAR EQUIPAMENTO
================================================================= --}}

<div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            {{-- ========================================================
                CABEÇALHO
            ========================================================= --}}

            <div class="modal-header border-bottom">

                <div class="d-flex align-items-center gap-3">

                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                        style="
                            width: 44px;
                            height: 44px;
                            color: var(--bs-warning);
                            background: rgba(var(--bs-warning-rgb), 0.15);
                        ">

                        <i class="bi bi-pencil-square fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="editEquipmentModalLabel">

                            Editar equipamento
                        </h5>

                        <p class="small text-secondary mb-0">
                            Atualize o equipamento instalado nesta torre.
                        </p>

                    </div>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                </button>

            </div>

            {{-- ========================================================
                FORMULÁRIO
            ========================================================= --}}

            <form id="editEquipmentForm" class="needs-validation" novalidate>

                @csrf

                <input type="hidden" id="edit_equipment_id_hidden">

                <div class="modal-body p-4">

                    <div class="row g-3">

                        {{-- =================================================
                            EQUIPAMENTO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="edit_equipment_id" class="form-label fw-semibold">

                                Equipamento
                                <span class="text-danger">*</span>
                            </label>

                            <select id="edit_equipment_id" name="equipment_id" class="form-select" required>

                                <option value="">
                                    Selecione um equipamento
                                </option>

                                @foreach ($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">

                                        {{ $equipment->name }}
                                        —
                                        {{ number_format((float) $equipment->watts, 2, ',', '.') }}
                                        W

                                    </option>
                                @endforeach

                            </select>

                            <div class="invalid-feedback">
                                Selecione um equipamento.
                            </div>

                        </div>

                        {{-- =================================================
                            IDENTIFICAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="edit_identification" class="form-label fw-semibold">

                                Identificação
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>

                                <input type="text" id="edit_identification" name="identification"
                                    class="form-control" maxlength="255"
                                    placeholder="Ex.: Rádio principal, switch rack 1...">

                            </div>

                        </div>

                        {{-- =================================================
                            SITUAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="edit_equipment_active" class="form-label fw-semibold">

                                Situação
                                <span class="text-danger">*</span>
                            </label>

                            <select id="edit_equipment_active" name="active" class="form-select" required>

                                <option value="yes">
                                    Ativo
                                </option>

                                <option value="no">
                                    Inativo
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

                {{-- ====================================================
                    RODAPÉ
                ===================================================== --}}

                <div class="modal-footer justify-content-between border-top">

                    <button type="button" id="deleteEquipmentBtn" class="btn btn-outline-danger">

                        <i class="bi bi-trash"></i>
                        Excluir
                    </button>

                    <div class="d-flex gap-2">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" id="editEquipmentSubmitButton">

                            <span class="spinner-border spinner-border-sm d-none" id="editEquipmentSpinner"
                                aria-hidden="true">
                            </span>

                            <i class="bi bi-check-lg" id="editEquipmentIcon">
                            </i>

                            <span id="editEquipmentText">
                                Salvar alterações
                            </span>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const modal =
                    document.getElementById(
                        'editEquipmentModal'
                    );

                const form =
                    document.getElementById(
                        'editEquipmentForm'
                    );

                const submitButton =
                    document.getElementById(
                        'editEquipmentSubmitButton'
                    );

                const spinner =
                    document.getElementById(
                        'editEquipmentSpinner'
                    );

                const icon =
                    document.getElementById(
                        'editEquipmentIcon'
                    );

                const text =
                    document.getElementById(
                        'editEquipmentText'
                    );

                if (!form) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDAÇÃO E ESTADO DO BOTÃO
                |--------------------------------------------------------------------------
                |
                | O envio AJAX continua sendo realizado pelo JavaScript
                | existente no show.blade.php.
                |
                */

                form.addEventListener(
                    'submit',
                    function(event) {

                        if (!form.checkValidity()) {

                            event.preventDefault();
                            event.stopImmediatePropagation();

                            form.classList.add(
                                'was-validated'
                            );

                            return;
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                        }

                        spinner?.classList.remove('d-none');
                        icon?.classList.add('d-none');

                        if (text) {
                            text.textContent = 'Salvando...';
                        }
                    },
                    true
                );

                /*
                |--------------------------------------------------------------------------
                | RESTAURAR MODAL AO FECHAR
                |--------------------------------------------------------------------------
                */

                modal?.addEventListener(
                    'hidden.bs.modal',
                    function() {

                        form.classList.remove(
                            'was-validated'
                        );

                        if (submitButton) {
                            submitButton.disabled = false;
                        }

                        spinner?.classList.add('d-none');
                        icon?.classList.remove('d-none');

                        if (text) {
                            text.textContent =
                                'Salvar alterações';
                        }
                    }
                );

            }
        );
    </script>
@endpush
