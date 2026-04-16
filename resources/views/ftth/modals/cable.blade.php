<div class="modal fade" id="modalCable">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('cable.store') }}">
            @csrf

            <input type="hidden" name="input_fiber_box_id" value="{{ $box->id }}">

            <div class="modal-content shadow-lg border-0 rounded-3">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plug"></i>
                        Novo Cabo
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="row g-3">

                        {{-- INFO --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Informação</label>
                            <input name="info" class="form-control shadow-sm" placeholder="Ex: Cabo principal CTO-01"
                                required>
                        </div>
                        {{-- COR --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cor do Cabo</label>

                            <div class="d-flex gap-2">
                                <!-- Seletor de cor -->
                                <input type="color" id="colorPicker" value="{{ $colorCablePon }}" class="form-control form-control-color shadow-sm"
                                    style="height: 38px; width: 60px;">

                                <!-- Campo para digitar o HEX -->
                                <input type="text" id="colorHex" name="color" class="form-control shadow-sm"
                                    placeholder="#000000">
                            </div>
                        </div>

                        {{-- FIBRAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nº de Fibras</label>
                            <input type="number" name="number_fiber" class="form-control shadow-sm" min="1"
                                placeholder="Ex: 12" required>
                        </div>

                        {{-- SAÍDA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Saída (Box)</label>
                            <select name="output_fiber_box_id" class="form-select shadow-sm">
                                <option value="">Sem saída</option>

                                @foreach ($boxesPon as $b)
                                    <option value="{{ $b->id }}">
                                        CTO {{ $b->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- INFO EXTRA --}}
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            Dica: a cor do cabo será usada no mapa para identificar o link óptico.
                        </small>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light d-flex justify-content-between">

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button class="btn btn-primary px-4">
                            Salvar Cabo
                        </button>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>