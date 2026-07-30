{{-- ================================================================
    MODAL — ADICIONAR PLACA SOLAR À TORRE
================================================================= --}}

<div class="modal fade" id="plateModal" tabindex="-1" aria-labelledby="plateModalLabel" aria-hidden="true">

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

                        <i class="bi bi-sun fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="plateModalLabel">

                            Adicionar placa solar
                        </h5>

                        <p class="text-secondary small mb-0">
                            Adicione uma placa solar à torre
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

            <form action="{{ url("/towers/{$tower->id}/plate") }}" method="POST" class="needs-validation" novalidate>

                @csrf

                <div class="modal-body p-4">

                    {{-- ====================================================
                        ERROS
                    ===================================================== --}}

                    @if ($errors->has('plate_id') || $errors->has('installation_date'))
                        <div class="alert alert-danger d-flex gap-3" role="alert">

                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                            <div>

                                <strong>
                                    Não foi possível adicionar a placa solar.
                                </strong>

                                <ul class="mb-0 mt-2 ps-3">

                                    @error('plate_id')
                                        <li>{{ $message }}</li>
                                    @enderror

                                    @error('installation_date')
                                        <li>{{ $message }}</li>
                                    @enderror

                                </ul>

                            </div>

                        </div>
                    @endif

                    <div class="row g-3">

                        {{-- =================================================
                            PLACA SOLAR
                        ================================================== --}}

                        <div class="col-12">

                            <label for="tower_plate_id" class="form-label fw-semibold">

                                Placa solar
                                <span class="text-danger">*</span>
                            </label>

                            <select name="plate_id" id="tower_plate_id"
                                class="form-select
                                    @error('plate_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    Selecione uma placa solar
                                </option>

                                @foreach ($plates as $plate)
                                    <option value="{{ $plate->id }}" @selected(old('plate_id') == $plate->id)>

                                        {{ $plate->name }}
                                        —
                                        {{ number_format((float) $plate->amps, 2, ',', '.') }}
                                        A
                                        —
                                        {{ number_format((float) $plate->watts, 0, ',', '.') }}
                                        W

                                    </option>
                                @endforeach

                            </select>

                            @error('plate_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Selecione uma placa solar.
                                </div>
                            @enderror

                            <div class="form-text">
                                Escolha o modelo que será instalado nesta torre.
                            </div>

                        </div>

                        {{-- =================================================
                            DATA DE INSTALAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="tower_plate_installation_date" class="form-label fw-semibold">

                                Data de instalação
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-calendar-check"></i>
                                </span>

                                <input type="date" name="installation_date" id="tower_plate_installation_date"
                                    class="form-control
                                        @error('installation_date') is-invalid @enderror"
                                    value="{{ old('installation_date', now()->format('Y-m-d')) }}">

                                @error('installation_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

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

                    <button type="submit" class="btn dcm-btn-primary" data-plate-submit-button>

                        <span class="spinner-border spinner-border-sm d-none" data-plate-submit-spinner
                            aria-hidden="true">
                        </span>

                        <i class="bi bi-check-lg" data-plate-submit-icon>
                        </i>

                        <span data-plate-submit-text>
                            Salvar placa
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

                const plateModal =
                    document.getElementById('plateModal');

                const plateForm =
                    plateModal?.querySelector('form');

                if (!plateForm) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDAÇÃO E BOTÃO DE ENVIO
                |--------------------------------------------------------------------------
                */

                plateForm.addEventListener(
                    'submit',
                    function(event) {

                        if (!plateForm.checkValidity()) {

                            event.preventDefault();
                            event.stopPropagation();

                            plateForm.classList.add(
                                'was-validated'
                            );

                            return;
                        }

                        const submitButton =
                            plateForm.querySelector(
                                '[data-plate-submit-button]'
                            );

                        const spinner =
                            plateForm.querySelector(
                                '[data-plate-submit-spinner]'
                            );

                        const icon =
                            plateForm.querySelector(
                                '[data-plate-submit-icon]'
                            );

                        const text =
                            plateForm.querySelector(
                                '[data-plate-submit-text]'
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

                plateModal?.addEventListener(
                    'hidden.bs.modal',
                    function() {

                        plateForm.classList.remove(
                            'was-validated'
                        );
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | ABRIR MODAL QUANDO O ERRO FOR DE PLACA
                |--------------------------------------------------------------------------
                */

                @if ($errors->has('plate_id') || $errors->has('installation_date'))

                    if (
                        typeof bootstrap !== 'undefined' &&
                        plateModal
                    ) {
                        bootstrap.Modal
                            .getOrCreateInstance(plateModal)
                            .show();
                    }
                @endif
            }
        );
    </script>
@endpush
