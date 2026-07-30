{{-- ================================================================
    MODAL DE CADASTRO DE MANUTENÇÃO
================================================================= --}}

<div class="modal fade fleet-modal" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel"
    aria-hidden="true" data-open-on-error="{{ $errors->any() ? 'true' : 'false' }}">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <div class="fleet-modal-heading">

                    <span class="fleet-modal-icon">
                        <i class="bi bi-tools"></i>
                    </span>

                    <div>

                        <h5 class="modal-title" id="addMaintenanceModalLabel">

                            Nova manutenção
                        </h5>

                        <p class="fleet-modal-subtitle">
                            Registre uma nova manutenção realizada ou programada.
                        </p>

                    </div>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                </button>

            </div>

            <form action="{{ route('fleet.vehicle_maintenances.store') }}" method="POST"
                class="fleet-submit-form needs-validation" data-fleet-form data-maintenance-form novalidate>

                @csrf

                <div class="modal-body">

                    @if ($errors->any())

                        <div class="fleet-alert fleet-alert-danger mb-4">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <div>

                                <strong>
                                    Não foi possível cadastrar a manutenção.
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

                    @include('fleet.form.fieldsvehicle_maintenances', [
                        'maintenance' => null,
                        'vehicles' => $vehicles,
                        'vehicleServices' => $vehicleServices,
                        'workshops' => $workshops,
                    ])

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                        <i class="bi bi-x-lg"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary" data-fleet-submit
                        data-loading-text="Salvando...">

                        <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true">
                        </span>

                        <i class="bi bi-save" data-submit-icon>
                        </i>

                        <span data-submit-text>
                            Salvar manutenção
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- ================================================================
    MODAIS DE EDIÇÃO
================================================================= --}}

@foreach ($maintenances as $maintenance)
    <div class="modal fade fleet-modal" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1"
        aria-labelledby="editMaintenanceModalLabel{{ $maintenance->id }}" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-xl">

            <div class="modal-content">

                <div class="modal-header">

                    <div class="fleet-modal-heading">

                        <span class="fleet-modal-icon warning">
                            <i class="bi bi-pencil-square"></i>
                        </span>

                        <div>

                            <h5 class="modal-title" id="editMaintenanceModalLabel{{ $maintenance->id }}">

                                Editar manutenção
                            </h5>

                            <p class="fleet-modal-subtitle">

                                @if ($maintenance->vehicle)
                                    {{ $maintenance->vehicle->license_plate }}
                                    —
                                    {{ $maintenance->vehicle->brand }}
                                    {{ $maintenance->vehicle->model }}
                                @else
                                    Atualize os dados da manutenção.
                                @endif

                            </p>

                        </div>

                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                    </button>

                </div>

                <form action="{{ route('fleet.vehicle_maintenances.update', $maintenance->id) }}" method="POST"
                    class="fleet-submit-form needs-validation" data-fleet-form data-maintenance-form novalidate>

                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        @include('fleet.form.fieldsvehicle_maintenances', [
                            'maintenance' => $maintenance,
                            'vehicles' => $vehicles,
                            'vehicleServices' => $vehicleServices,
                            'workshops' => $workshops,
                        ])

                    </div>

                    <div class="modal-footer d-flex justify-content-between">

                        <div>

                            @can('fleets.delete')
                                <button type="submit" class="btn btn-danger"
                                    form="deleteMaintenanceForm{{ $maintenance->id }}">

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
                                    Atualizar manutenção
                                </span>

                            </button>

                        </div>

                    </div>

                </form>

                @can('fleets.delete')
                    <form id="deleteMaintenanceForm{{ $maintenance->id }}"
                        action="{{ route('fleet.vehicle_maintenances.destroy', $maintenance->id) }}" method="POST"
                        class="d-none fleet-delete-form" data-confirm-delete
                        data-confirm-message="Tem certeza que deseja excluir esta manutenção?">

                        @csrf
                        @method('DELETE')

                    </form>
                @endcan

            </div>

        </div>

    </div>
@endforeach
