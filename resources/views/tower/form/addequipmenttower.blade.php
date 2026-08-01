{{-- ================================================================
    MODAL — ADICIONAR EQUIPAMENTO À TORRE
================================================================= --}}

<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">

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
                            color: var(--bs-primary);
                            background: rgba(var(--bs-primary-rgb), 0.1);
                        ">

                        <i class="bi bi-router fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="equipmentModalLabel">

                            Adicionar equipamento
                        </h5>

                        <p class="text-secondary small mb-0">
                            Adicione um equipamento à torre
                            <strong>{{ $tower->name }}</strong>.
                        </p>

                    </div>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                </button>

            </div>

            {{-- ========================================================
                FORMULÁRIO
            ========================================================= --}}

            <form action="{{ url("/towers/{$tower->id}/equipment") }}" method="POST" class="needs-validation"
                novalidate>

                @csrf

                <div class="modal-body p-4">

                    {{-- ====================================================
                        ERROS
                    ===================================================== --}}

                    @if ($errors->has('equipment_id') || $errors->has('identification') || $errors->has('active'))
                        <div class="alert alert-danger d-flex gap-3" role="alert">

                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                            <div>

                                <strong>
                                    Não foi possível adicionar o equipamento.
                                </strong>

                                <ul class="mb-0 mt-2 ps-3">

                                    @error('equipment_id')
                                        <li>{{ $message }}</li>
                                    @enderror

                                    @error('identification')
                                        <li>{{ $message }}</li>
                                    @enderror

                                    @error('active')
                                        <li>{{ $message }}</li>
                                    @enderror

                                </ul>

                            </div>

                        </div>
                    @endif

                    <div class="row g-3">

                        {{-- =================================================
                            EQUIPAMENTO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="tower_equipment_id" class="form-label fw-semibold">

                                Equipamento
                                <span class="text-danger">*</span>
                            </label>

                            <select name="equipment_id" id="tower_equipment_id"
                                class="form-select
                                    @error('equipment_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    Selecione um equipamento
                                </option>

                                @foreach ($equipments as $equipment)
                                    <option value="{{ $equipment->id }}" @selected(old('equipment_id') == $equipment->id)>

                                        {{ $equipment->name }}
                                        —
                                        {{ number_format((float) $equipment->watts, 2, ',', '.') }}
                                        W

                                    </option>
                                @endforeach

                            </select>

                            @error('equipment_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Selecione um equipamento.
                                </div>
                            @enderror

                            <div class="form-text">
                                Escolha o equipamento que será instalado nesta torre.
                            </div>

                        </div>

                        {{-- =================================================
                            IDENTIFICAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="tower_equipment_identification" class="form-label fw-semibold">

                                Identificação
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>

                                <input type="text" name="identification" id="tower_equipment_identification"
                                    class="form-control
                                        @error('identification') is-invalid @enderror"
                                    value="{{ old('identification') }}" maxlength="255"
                                    placeholder="Ex.: Rádio principal, switch rack 1...">

                                @error('identification')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                        {{-- =================================================
                            SITUAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="tower_equipment_active" class="form-label fw-semibold">

                                Situação
                                <span class="text-danger">*</span>
                            </label>

                            <select name="active" id="tower_equipment_active"
                                class="form-select
                                    @error('active') is-invalid @enderror"
                                required>

                                <option value="yes" @selected(old('active', 'yes') === 'yes')>

                                    Ativo
                                </option>

                                <option value="no" @selected(old('active') === 'no')>

                                    Inativo
                                </option>

                            </select>

                            @error('active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- ====================================================
                    RODAPÉ
                ===================================================== --}}

                <div class="modal-footer border-top">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary" data-equipment-submit-button>

                        <span class="spinner-border spinner-border-sm d-none" data-equipment-submit-spinner
                            aria-hidden="true">
                        </span>

                        <i class="bi bi-check-lg" data-equipment-submit-icon>
                        </i>

                        <span data-equipment-submit-text>
                            Salvar equipamento
                        </span>

                    </button>

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

                const equipmentModal =
                    document.getElementById('equipmentModal');

                const equipmentForm =
                    equipmentModal?.querySelector('form');

                if (!equipmentForm) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDAÇÃO E BOTÃO DE ENVIO
                |--------------------------------------------------------------------------
                */

                equipmentForm.addEventListener(
                    'submit',
                    function(event) {

                        if (!equipmentForm.checkValidity()) {

                            event.preventDefault();
                            event.stopPropagation();

                            equipmentForm.classList.add(
                                'was-validated'
                            );

                            return;
                        }

                        const submitButton =
                            equipmentForm.querySelector(
                                '[data-equipment-submit-button]'
                            );

                        const spinner =
                            equipmentForm.querySelector(
                                '[data-equipment-submit-spinner]'
                            );

                        const icon =
                            equipmentForm.querySelector(
                                '[data-equipment-submit-icon]'
                            );

                        const text =
                            equipmentForm.querySelector(
                                '[data-equipment-submit-text]'
                            );

                        if (submitButton) {
                            submitButton.disabled = true;
                        }

                        spinner?.classList.remove('d-none');
                        icon?.classList.add('d-none');

                        if (text) {
                            text.textContent = 'Salvando...';
                        }
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | REMOVER VALIDAÇÃO AO FECHAR
                |--------------------------------------------------------------------------
                */

                equipmentModal?.addEventListener(
                    'hidden.bs.modal',
                    function() {

                        equipmentForm.classList.remove(
                            'was-validated'
                        );
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | ABRIR MODAL QUANDO O ERRO FOR DE EQUIPAMENTO
                |--------------------------------------------------------------------------
                */

                @if ($errors->has('equipment_id') || $errors->has('identification') || $errors->has('active'))

                    if (
                        typeof bootstrap !== 'undefined' &&
                        equipmentModal
                    ) {
                        bootstrap.Modal
                            .getOrCreateInstance(equipmentModal)
                            .show();
                    }
                @endif
            }
        );
    </script>
@endpush
