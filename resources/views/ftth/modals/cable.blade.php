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
                    {{-- CHECKBOX --}}
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="toggleSelect">
                            <label class="form-check-label">
                                Cabo interPONs
                            </label>
                        </div>
                    </div>

                    <div class="row g-3">

                        {{-- INFO --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Informação</label>
                            <input name="info" class="form-control shadow-sm"
                                placeholder="Ex: Cabo principal CTO-01" required>
                        </div>

                        {{-- COR --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cor do Cabo</label>

                            <div class="d-flex gap-2">
                                <input type="color" id="colorPicker" value="{{ $colorCablePon }}"
                                    class="form-control form-control-color shadow-sm"
                                    style="height: 38px; width: 60px;">

                                <input type="text" id="colorHex" name="color"
                                    class="form-control shadow-sm" placeholder="#000000">
                            </div>
                        </div>

                        {{-- FIBRAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nº de Fibras</label>
                            <input type="number" name="number_fiber"
                                class="form-control shadow-sm" min="1"
                                placeholder="Ex: 12" required>
                        </div>

                        {{-- PON (DEFAULT VISÍVEL) --}}
                        <div class="col-md-6" id="selectPon">
                            <label class="form-label fw-semibold">Saída PON(Box)</label>
                            <select id="ponSelect" class="form-select shadow-sm">
                                <option value="">Sem saída</option>

                                @foreach ($boxesPon as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->info }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- EXTERNA (ESCONDIDO INICIALMENTE) --}}
                        <div class="col-md-6" id="selectExterna" style="display:none;">
                            <label class="form-label fw-semibold">Saída Externa(Box)</label>
                            <select id="externaSelect" class="form-select shadow-sm">
                                <option value="">Sem saída</option>

                                @foreach ($boxesall as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->number }}  {{ $b->pon->info }}   {{ $b->info }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            Dica: a cor do cabo será usada no mapa para identificar o link óptico.
                        </small>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light d-flex justify-content-between">

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
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
<script>
    const checkbox = document.getElementById('toggleSelect');
    const pon = document.getElementById('selectPon');
    const externa = document.getElementById('selectExterna');

    const ponSelect = document.getElementById('ponSelect');
    const externaSelect = document.getElementById('externaSelect');

    function atualizarSelect() {
        if (checkbox.checked) {
            // MARCADO → EXTERNA
            pon.style.display = 'none';
            externa.style.display = 'block';

            externaSelect.setAttribute('name', 'output_fiber_box_id');
            ponSelect.removeAttribute('name');

        } else {
            // DESMARCADO → PON (PADRÃO)
            pon.style.display = 'block';
            externa.style.display = 'none';

            ponSelect.setAttribute('name', 'output_fiber_box_id');
            externaSelect.removeAttribute('name');
        }
    }

    checkbox.addEventListener('change', atualizarSelect);

    // inicializa corretamente ao abrir
    atualizarSelect();
</script>