{{-- ================================================================
    MODAL DE CRIAÇÃO OU EDIÇÃO DE VEÍCULO
================================================================= --}}

@if (is_null($vehicle))

    {{-- ============================================================
        MODAL DE CADASTRO
    ============================================================= --}}

    <div class="modal fade fleet-modal" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel"
        aria-hidden="true" data-open-on-error="{{ $errors->any() ? 'true' : 'false' }}">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <div class="fleet-modal-heading">

                        <span class="fleet-modal-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="addVehicleModalLabel">

                                Novo veículo
                            </h5>

                            <p class="fleet-modal-subtitle">
                                Preencha os dados para cadastrar um veículo na frota.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form action="{{ route('fleet.vehicles.store') }}" method="POST"
                    class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())

                            <div class="fleet-alert fleet-alert-danger mb-4">

                                <i class="bi bi-exclamation-triangle-fill"></i>

                                <div>

                                    <strong>
                                        Não foi possível cadastrar o veículo.
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

                        @include('fleet.form.fieldsvehicles', [
                            'vehicle' => null,
                        ])

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            <i class="bi bi-x-lg"></i>
                            Cancelar
                        </button>

                        <button type="submit" class="btn dcm-btn-primary" data-fleet-submit
                            data-loading-text="Salvando...">

                            <span class="spinner-border spinner-border-sm d-none" data-submit-spinner
                                aria-hidden="true">
                            </span>

                            <i class="bi bi-save" data-submit-icon>
                            </i>

                            <span data-submit-text>
                                Salvar veículo
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@else
    {{-- ============================================================
        MODAL DE EDIÇÃO
    ============================================================= --}}

    <div class="modal fade fleet-modal" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1"
        aria-labelledby="editVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <div class="fleet-modal-heading">

                        <span class="fleet-modal-icon warning">
                            <i class="bi bi-pencil-square"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="editVehicleModalLabel{{ $vehicle->id }}">

                                Editar veículo
                            </h5>

                            <p class="fleet-modal-subtitle">
                                {{ $vehicle->license_plate }}
                                —
                                {{ $vehicle->brand }}
                                {{ $vehicle->model }}
                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form action="{{ route('fleet.vehicles.update', $vehicle->id) }}" method="POST"
                    class="fleet-submit-form needs-validation" data-fleet-form novalidate>

                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        @include('fleet.form.fieldsvehicles', [
                            'vehicle' => $vehicle,
                        ])

                    </div>

                    <div class="modal-footer d-flex justify-content-between">

                        <div>

                            @can('fleets.delete')
                                <button type="submit" class="btn btn-danger" form="deleteVehicleForm{{ $vehicle->id }}">

                                    <i class="bi bi-trash"></i>
                                    Excluir
                                </button>
                            @endcan

                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                                <i class="bi bi-x-lg"></i>
                                Cancelar
                            </button>

                            <button type="submit" class="btn dcm-btn-primary" data-fleet-submit
                                data-loading-text="Atualizando...">

                                <span class="spinner-border spinner-border-sm d-none" data-submit-spinner
                                    aria-hidden="true">
                                </span>

                                <i class="bi bi-save" data-submit-icon>
                                </i>

                                <span data-submit-text>
                                    Atualizar veículo
                                </span>

                            </button>

                        </div>

                    </div>

                </form>

                @can('fleets.delete')
                    <form id="deleteVehicleForm{{ $vehicle->id }}"
                        action="{{ route('fleet.vehicles.destroy', $vehicle->id) }}" method="POST"
                        class="d-none fleet-delete-form" data-confirm-delete
                        data-confirm-message="Tem certeza que deseja excluir o veículo {{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}?">

                        @csrf
                        @method('DELETE')

                    </form>
                @endcan

            </div>

        </div>

    </div>

@endif
