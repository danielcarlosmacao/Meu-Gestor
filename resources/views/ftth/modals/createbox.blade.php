{{-- MODAL --}}
<div class="modal fade" id="modalBox">

    <div class="modal-dialog">

        <form method="POST" action="{{ route('fiberbox.store') }}">

            @csrf

            <input type="hidden" name="pon_id" value="{{ $pon->id }}">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold">Nova Caixa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- NUMERO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Número</label>
                        <span class="badge bg-success">
                            Menor disponivel: {{ $nextnumber }}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <input name="number" type="number" class="form-control shadow-sm" step="1"
                                min="{{ $nextnumber }}" required value="{{ $nextnumber }}">
                        </div>
                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <input name="info" class="form-control shadow-sm">
                    </div>

                    {{-- COORDENADAS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coordenadas</label>
                        <input name="coordinates" class="form-control shadow-sm">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Salvar
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>
