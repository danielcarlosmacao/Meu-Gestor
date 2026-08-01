{{-- ================================================================
    MODAL — EDITAR BATERIA
================================================================= --}}

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editBatteryModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow-lg">

            {{-- ========================================================
                CABEÇALHO
            ========================================================= --}}

            <div class="modal-header border-bottom">

                <div class="d-flex align-items-center gap-3">

                    <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                        style="
                            width:44px;
                            height:44px;
                            color:var(--bs-warning);
                            background:rgba(var(--bs-warning-rgb),.15);
                        ">

                        <i class="bi bi-pencil-square fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="editBatteryModalLabel">

                            Editar bateria

                        </h5>

                        <p class="small text-secondary mb-0">
                            Atualize as informações da bateria instalada.
                        </p>

                    </div>

                </div>

                <button class="btn-close" data-bs-dismiss="modal">
                </button>

            </div>

            {{-- ========================================================
                FORMULÁRIO
            ========================================================= --}}

            <form id="editForm" novalidate>

                @csrf

                <input type="hidden" id="edit_id">

                <div class="modal-body p-4">

                    <div class="row g-3">

                        {{-- BATERIA --}}

                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Bateria
                            </label>

                            <select id="edit_battery_id" name="battery_id" class="form-select">

                                @foreach ($batteries as $battery)
                                    <option value="{{ $battery->id }}">

                                        {{ $battery->name }}
                                        —
                                        {{ $battery->mark }}
                                        —
                                        {{ number_format($battery->amps, 0, ',', '.') }}Ah

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- INFO --}}

                        <div class="col-12">

                            <label class="form-label fw-semibold">

                                Informação

                            </label>

                            <input id="edit_info" name="info" class="form-control">

                        </div>

                        {{-- QUANTIDADE --}}

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Quantidade

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-hash"></i>

                                </span>

                                <input id="edit_amount" type="number" name="amount" class="form-control">

                            </div>

                        </div>

                        {{-- SITUAÇÃO --}}

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Situação

                            </label>

                            <select id="edit_active" name="active" class="form-select">

                                <option value="yes">
                                    Ativa
                                </option>

                                <option value="no">
                                    Inativa
                                </option>

                            </select>

                        </div>

                        {{-- INSTALAÇÃO --}}

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Data de instalação

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-calendar-check"></i>

                                </span>

                                <input id="edit_installation_date" type="date" name="installation_date"
                                    class="form-control">

                            </div>

                        </div>

                        {{-- REMOÇÃO --}}

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Data de remoção

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-calendar-x"></i>

                                </span>

                                <input id="edit_removal_date" type="date" name="removal_date" class="form-control">

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ====================================================
                    RODAPÉ
                ===================================================== --}}

                <div class="modal-footer justify-content-between">

                    <button id="deleteButton" type="button" class="btn btn-outline-danger">

                        <i class="bi bi-trash"></i>

                        Excluir

                    </button>

                    <div class="d-flex gap-2">

                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button class="btn dcm-btn-primary" type="submit">

                            <span class="spinner-border spinner-border-sm d-none" id="batteryEditSpinner">
                            </span>

                            <i class="bi bi-check-lg" id="batteryEditIcon">
                            </i>

                            <span id="batteryEditText">

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
        document.addEventListener('DOMContentLoaded', () => {

            const form = document.getElementById('editForm');

            form?.addEventListener('submit', () => {

                document
                    .getElementById('batteryEditSpinner')
                    ?.classList.remove('d-none');

                document
                    .getElementById('batteryEditIcon')
                    ?.classList.add('d-none');

                document
                    .getElementById('batteryEditText')
                    .innerHTML = 'Salvando...';

            });

            /*
            |--------------------------------------------------------------------------
            | DATA REMOÇÃO => INATIVA
            |--------------------------------------------------------------------------
            */

            document
                .getElementById('edit_removal_date')
                ?.addEventListener('change', function() {

                    if (this.value) {

                        document.getElementById('edit_active').value = 'no';

                    }

                });

        });
    </script>
@endpush
