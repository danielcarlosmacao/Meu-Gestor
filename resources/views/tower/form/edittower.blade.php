{{-- ================================================================
    MODAL — EDITAR TORRE
================================================================= --}}

<div class="modal fade" id="editModal{{ $tower->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $tower->id }}"
    aria-hidden="true">

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

                        <i class="bi bi-broadcast-pin fs-5"></i>

                    </span>

                    <div>

                        <h5 class="modal-title fw-bold mb-1" id="editModalLabel{{ $tower->id }}">

                            Editar torre
                        </h5>

                        <p class="small text-secondary mb-0">
                            Atualize o nome e a tensão da torre.
                        </p>

                    </div>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                </button>

            </div>

            {{-- ========================================================
                FORMULÁRIO
            ========================================================= --}}

            <form method="POST" action="{{ route('tower.update', $tower->id) }}" class="needs-validation"
                id="editTowerForm{{ $tower->id }}" novalidate>

                @csrf
                @method('PUT')

                <div class="modal-body p-4">

                    <div class="row g-3">

                        {{-- =================================================
                            NOME
                        ================================================== --}}

                        <div class="col-12">

                            <label for="name{{ $tower->id }}" class="form-label fw-semibold">

                                Nome
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-broadcast-pin"></i>
                                </span>

                                <input type="text"
                                    class="form-control
                                        @error('name') is-invalid @enderror"
                                    id="name{{ $tower->id }}" name="name" value="{{ old('name', $tower->name) }}"
                                    maxlength="255" placeholder="Ex.: Torre Centro" required>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Informe o nome da torre.
                                    </div>
                                @enderror

                            </div>

                        </div>

                        {{-- =================================================
                            TENSÃO
                        ================================================== --}}

                        <div class="col-12">

                            <label for="voltage{{ $tower->id }}" class="form-label fw-semibold">

                                Tensão
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lightning-charge"></i>
                                </span>

                                <input type="number"
                                    class="form-control
                                        @error('voltage') is-invalid @enderror"
                                    id="voltage{{ $tower->id }}" name="voltage" min="12" max="1000"
                                    step="12" value="{{ old('voltage', $tower->voltage) }}" required>

                                <span class="input-group-text">
                                    V
                                </span>

                                @error('voltage')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @else
                                    <div class="invalid-feedback">
                                        Informe uma tensão válida entre 12 V e 1000 V.
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                Utilize valores múltiplos de 12 V.
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

                    <button type="submit" class="btn dcm-btn-primary" id="editTowerSubmitButton{{ $tower->id }}">

                        <span class="spinner-border spinner-border-sm d-none" id="editTowerSpinner{{ $tower->id }}"
                            aria-hidden="true">
                        </span>

                        <i class="bi bi-check-lg" id="editTowerIcon{{ $tower->id }}">
                        </i>

                        <span id="editTowerText{{ $tower->id }}">
                            Salvar alterações
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById(
                'editModal{{ $tower->id }}'
            );

            const form = document.getElementById(
                'editTowerForm{{ $tower->id }}'
            );

            const submitButton = document.getElementById(
                'editTowerSubmitButton{{ $tower->id }}'
            );

            const spinner = document.getElementById(
                'editTowerSpinner{{ $tower->id }}'
            );

            const icon = document.getElementById(
                'editTowerIcon{{ $tower->id }}'
            );

            const text = document.getElementById(
                'editTowerText{{ $tower->id }}'
            );

            if (!form) {
                return;
            }

            form.addEventListener('submit', function(event) {

                if (!form.checkValidity()) {

                    event.preventDefault();
                    event.stopPropagation();

                    form.classList.add('was-validated');

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
            });

            modal?.addEventListener('hidden.bs.modal', function() {

                form.classList.remove('was-validated');

                if (submitButton) {
                    submitButton.disabled = false;
                }

                spinner?.classList.add('d-none');
                icon?.classList.remove('d-none');

                if (text) {
                    text.textContent = 'Salvar alterações';
                }
            });

            @if ($errors->has('name') || $errors->has('voltage'))

                if (
                    typeof bootstrap !== 'undefined' &&
                    modal
                ) {
                    bootstrap.Modal
                        .getOrCreateInstance(modal)
                        .show();
                }
            @endif
        });
    </script>
@endpush
