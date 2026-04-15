<div class="modal fade" id="modalFiber" tabindex="-1" aria-labelledby="modalFiberLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <form method="POST" action="{{ route('fiber.store') }}">
            @csrf

            <input type="hidden" name="fiber_box_id" value="{{ $box->id }}">

            <div class="modal-content border-0 shadow">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalFiberLabel">
                        Registrar Fibras
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- SELEÇÃO DO CABO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cabo</label>

                        <select id="cable_select" name="cable_id" class="form-select" required>
                            <option value="" disabled selected>Selecione um Cabo</option>

                            @foreach ($cables as $cable)
                                <option value="{{ $cable->id }}" data-fibers="{{ $cable->number_fiber }}"
                                    data-info="{{ $cable->info }}" data-output-box="{{ $cable->output_fiber_box_id }}">
                                    CABO {{ $cable->info }} ({{ $cable->number_fiber }} fibras)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CONTAINER DE FIBRAS --}}
                    <div id="fibers_container" class="mb-3"></div>

                    {{-- DIREÇÃO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Direção</label>

                        <select name="direction" class="form-select" required>
                            <option value="input">Entrada</option>
                            <option value="output">Saída</option>
                        </select>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn dcm-btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Salvar
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>
