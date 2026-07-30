{{-- ================================================================
    MODAL — ADICIONAR BATERIA À TORRE
================================================================= --}}

<div class="modal fade" id="batteryModal" tabindex="-1" aria-labelledby="batteryModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

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

                        <i class="bi bi-battery-charging fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="batteryModalLabel">

                            Adicionar bateria
                        </h5>

                        <p class="text-secondary small mb-0">
                            Adicione uma bateria à torre
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

            <form action="{{ url("/towers/{$tower->id}/battery") }}" method="POST" class="needs-validation" novalidate>

                @csrf

                <div class="modal-body p-4">

                    {{-- ====================================================
                        ERROS
                    ===================================================== --}}

                    @if ($errors->any())

                        <div class="alert alert-danger d-flex gap-3" role="alert">

                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                            <div>

                                <strong>
                                    Não foi possível salvar a bateria.
                                </strong>

                                <ul class="mb-0 mt-2 ps-3">

                                    @foreach ($errors->all() as $error)
                                        <li>
                                            {{ $error }}
                                        </li>
                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    @endif

                    <div class="row g-3">

                        {{-- =================================================
                            BATERIA
                        ================================================== --}}

                        <div class="col-12">

                            <label for="battery_id" class="form-label fw-semibold">

                                Bateria
                                <span class="text-danger">*</span>
                            </label>

                            <select name="battery_id" id="battery_id"
                                class="form-select
                                    @error('battery_id') is-invalid @enderror"
                                required>

                                <option value="">
                                    Selecione uma bateria
                                </option>

                                @foreach ($batteries as $battery)
                                    <option value="{{ $battery->id }}" @selected(old('battery_id') == $battery->id)>

                                        {{ $battery->name }}
                                        — {{ $battery->mark }}
                                        —
                                        {{ number_format((float) $battery->amps, 0, ',', '.') }}
                                        Ah

                                        @if (!empty($battery->voltage))
                                            —
                                            {{ number_format((float) $battery->voltage, 0, ',', '.') }}
                                            V
                                        @endif

                                    </option>
                                @endforeach

                            </select>

                            @error('battery_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Selecione uma bateria.
                                </div>
                            @enderror

                            <div class="form-text">
                                Escolha o modelo que será instalado nesta torre.
                            </div>

                        </div>

                        {{-- =================================================
                            INFORMAÇÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="battery_info" class="form-label fw-semibold">

                                Informação
                            </label>

                            <input type="text" name="info" id="battery_info"
                                class="form-control
                                    @error('info') is-invalid @enderror"
                                value="{{ old('info') }}" maxlength="255"
                                placeholder="Ex.: Banco principal, sala técnica...">

                            @error('info')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- =================================================
                            QUANTIDADE
                        ================================================== --}}

                        <div class="col-12 col-md-6">

                            <label for="battery_amount" class="form-label fw-semibold">

                                Quantidade
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-hash"></i>
                                </span>

                                <input type="number" name="amount" id="battery_amount"
                                    class="form-control
                                        @error('amount') is-invalid @enderror"
                                    value="{{ old('amount', 1) }}" min="1" step="1" required>

                                @error('amount')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Informe uma quantidade válida.
                                    </div>
                                @enderror

                            </div>

                        </div>

                        {{-- =================================================
                            SITUAÇÃO
                        ================================================== --}}

                        <div class="col-12 col-md-6">

                            <label for="battery_active" class="form-label fw-semibold">

                                Situação
                                <span class="text-danger">*</span>
                            </label>

                            <select name="active" id="battery_active"
                                class="form-select
                                    @error('active') is-invalid @enderror"
                                required>

                                <option value="yes" @selected(old('active', 'yes') === 'yes')>

                                    Ativa
                                </option>

                                <option value="no" @selected(old('active') === 'no')>

                                    Inativa
                                </option>

                            </select>

                            @error('active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- =================================================
                            DATA DE INSTALAÇÃO
                        ================================================== --}}

                        <div class="col-12 col-md-6">

                            <label for="battery_installation_date" class="form-label fw-semibold">

                                Data de instalação
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-calendar-check"></i>
                                </span>

                                <input type="date" name="installation_date" id="battery_installation_date"
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

                        {{-- =================================================
                            DATA DE REMOÇÃO
                        ================================================== --}}

                        <div class="col-12 col-md-6">

                            <label for="battery_removal_date" class="form-label fw-semibold">

                                Data de remoção
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-calendar-x"></i>
                                </span>

                                <input type="date" name="removal_date" id="battery_removal_date"
                                    class="form-control
                                        @error('removal_date') is-invalid @enderror"
                                    value="{{ old('removal_date') }}">

                                @error('removal_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                Deixe vazio enquanto a bateria estiver instalada.
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

                    <button type="submit" class="btn dcm-btn-primary" data-submit-button>

                        <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                        </span>

                        <i class="bi bi-check-lg" data-submit-icon>
                        </i>

                        <span data-submit-text>
                            Salvar bateria
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

                const batteryModal =
                    document.getElementById('batteryModal');

                const batteryForm =
                    batteryModal?.querySelector('form');

                const activeField =
                    document.getElementById('battery_active');

                const removalDateField =
                    document.getElementById(
                        'battery_removal_date'
                    );

                /*
                |--------------------------------------------------------------------------
                | SITUAÇÃO X DATA DE REMOÇÃO
                |--------------------------------------------------------------------------
                */

                function synchronizeBatteryStatus() {

                    if (!activeField || !removalDateField) {
                        return;
                    }

                    if (removalDateField.value) {
                        activeField.value = 'no';
                    }
                }

                removalDateField?.addEventListener(
                    'change',
                    synchronizeBatteryStatus
                );

                /*
                |--------------------------------------------------------------------------
                | VALIDAÇÃO E BOTÃO DE ENVIO
                |--------------------------------------------------------------------------
                */

                if (batteryForm) {

                    batteryForm.addEventListener(
                        'submit',
                        function(event) {

                            if (!batteryForm.checkValidity()) {

                                event.preventDefault();
                                event.stopPropagation();

                                batteryForm.classList.add(
                                    'was-validated'
                                );

                                return;
                            }

                            const submitButton =
                                batteryForm.querySelector(
                                    '[data-submit-button]'
                                );

                            const spinner =
                                batteryForm.querySelector(
                                    '[data-submit-spinner]'
                                );

                            const icon =
                                batteryForm.querySelector(
                                    '[data-submit-icon]'
                                );

                            const text =
                                batteryForm.querySelector(
                                    '[data-submit-text]'
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
                }

                /*
                |--------------------------------------------------------------------------
                | LIMPAR VALIDAÇÃO AO FECHAR
                |--------------------------------------------------------------------------
                */

                batteryModal?.addEventListener(
                    'hidden.bs.modal',
                    function() {

                        batteryForm?.classList.remove(
                            'was-validated'
                        );
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | ABRIR QUANDO HOUVER ERRO
                |--------------------------------------------------------------------------
                */

                @if ($errors->any())

                    if (
                        typeof bootstrap !== 'undefined' &&
                        batteryModal
                    ) {
                        bootstrap.Modal
                            .getOrCreateInstance(batteryModal)
                            .show();
                    }
                @endif
            }
        );
    </script>
@endpush
