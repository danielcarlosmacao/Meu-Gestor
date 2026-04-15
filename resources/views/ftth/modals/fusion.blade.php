<div class="modal fade" id="modalFusion">

    <div class="modal-dialog">

        <form method="POST" action="{{ route('fusion.store') }}">
            @csrf

            <input type="hidden" name="fiber_box_id" value="{{ $box->id }}">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalFiberLabel">
                        Registrar Fusao
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>


                <div class="modal-body">

                    {{-- FIBRA 1 --}}
                    <label class="form-label fw-semibold">
                        Fibra 1
                    </label>

                    <select id="fiber1" name="fiber1" class="form-select mb-3 shadow-sm">
                        <option value="" selected disabled>Selecione uma fibra</option>

                        @foreach ($fibers as $fiber)
                            @if ($fiber->status == 'unused')
                                <option value="{{ $fiber->id }}">
                                    {{ $fiber->fiber_identification }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    {{-- FIBRA 2 --}}
                    <label class="form-label fw-semibold">
                        Fibra 2
                    </label>

                    <select id="fiber2" name="fiber2" class="form-select mb-3 shadow-sm">
                        <option value="" selected disabled>Selecione uma fibra</option>

                        @foreach ($fibers as $fiber)
                            @if ($fiber->status == 'unused')
                                <option value="{{ $fiber->id }}">
                                    {{ $fiber->fiber_identification }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    {{-- INFO --}}
                    <label for="info" class="form-label fw-semibold">
                        Informação
                    </label>

                    <textarea name="info" id="info" class="form-control shadow-sm" rows="3"
                        placeholder="Descreva a fusão (opcional)"></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Salvar</button>
                </div>

            </div>

        </form>

    </div>

</div>
