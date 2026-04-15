<div class="modal fade" id="modalSplinter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">

        <form method="POST" action="{{ route('splinter.store') }}">
            @csrf

            <input type="hidden" name="fiber_box_id" value="{{ $box->id }}">

            <div class="modal-content border-0 shadow">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-diagram-3 me-1"></i>
                        Novo Splinter
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- NOME --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome</label>
                        <input name="name" class="form-control" placeholder="Ex: SPL-01" required>
                    </div>

                    {{-- TIPO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="network">Rede</option>
                            <option value="client">Clientes</option>
                        </select>
                    </div>

                    <div class="row">

                        {{-- FIBRA ENTRADA --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Fibra de Entrada</label>

                            <select name="splinter_input" class="form-select">
                                <option disabled selected>Selecione uma fibra</option>

                                @foreach ($fibers as $fiber)
                                    @if ($fiber->status == 'unused')
                                        <option value="{{ $fiber->id }}">
                                            {{ $fiber->fiber_identification }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- MODELO --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Modelo do Splinter</label>

                            <select name="splinter" class="form-select">
                                <option disabled selected>Selecione o modelo</option>

                                @foreach ($losses as $loss)
                                    <option value="{{ $loss->id }}">
                                        {{ $loss->type }}
                                        |
                                        {{ __('fiber.' . $loss->splinter_type) }}
                                        |
                                        {{ $loss->loss1 }}

                                        @if ($loss->splinter_type == 'unbalanced')
                                            / {{ $loss->loss2 }}
                                        @endif

                                        dB
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-light">
                    <button class="btn dcm-btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>
