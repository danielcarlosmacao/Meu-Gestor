<div class="modal fade" id="modalEditBox" tabindex="-1" aria-labelledby="modalEditBoxLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <form method="POST" id="formEditBox" action="">

            @csrf
            @method('PUT')

            <input type="hidden" name="box_id" id="editBoxId">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">

                    <h5 class="modal-title fw-bold" id="modalEditBoxLabel">

                        <i class="bi bi-pencil-square me-1"></i>
                        Editar Caixa
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar">
                    </button>

                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- PON --}}
                    <div class="mb-3">

                        <label for="editPonId" class="form-label fw-semibold">

                            PON
                        </label>

                        <select name="pon_id" id="editPonId" class="form-select shadow-sm" required>

                            <option value="">
                                Selecione uma PON
                            </option>

                            @foreach ($pons as $ponOption)
                                <option value="{{ $ponOption->id }}">

                                    {{ $ponOption->info }}

                                    @if ($ponOption->olt)
                                        - {{ $ponOption->olt }}
                                    @endif

                                </option>
                            @endforeach

                        </select>

                        <div class="form-text">
                            Selecione a PON para onde esta caixa será transferida.
                        </div>

                    </div>

                    {{-- NÚMERO --}}
                    <div class="mb-3">

                        <label for="editNumber" class="form-label fw-semibold">

                            Número
                        </label>

                        <input type="number" name="number" id="editNumber" class="form-control shadow-sm"
                            min="1" step="1" required>

                        <div class="form-text">
                            O número deve ser único e não pode estar sendo usado por outra caixa.
                        </div>

                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="mb-3">

                        <label for="editInfo" class="form-label fw-semibold">

                            Descrição
                        </label>

                        <input type="text" name="info" id="editInfo" class="form-control shadow-sm"
                            maxlength="255" placeholder="Ex.: CTO Rua Principal">

                    </div>

                    {{-- COORDENADAS --}}
                    <div class="mb-3">

                        <label for="editCoordinates" class="form-label fw-semibold">

                            Coordenadas
                        </label>

                        <input type="text" name="coordinates" id="editCoordinates" class="form-control shadow-sm"
                            maxlength="255" placeholder="Ex.: -8.761234, -63.901234">

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">

                        <i class="bi bi-check-lg me-1"></i>
                        Salvar alterações
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
