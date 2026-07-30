@if (is_null($maintenance))
    {{-- ============================================================
        MODAL ADICIONAR MANUTENÇÃO
    ============================================================= --}}

    <div class="modal fade service-modal" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <form action="{{ route('service.maintenances.store') }}" method="POST" class="needs-validation" novalidate>

                @csrf

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="service-modal-heading">

                            <span class="service-modal-icon">
                                <i class="bi bi-tools"></i>
                            </span>

                            <div>
                                <h5 class="modal-title" id="addMaintenanceModalLabel">

                                    Nova Manutenção

                                </h5>

                                <p class="service-modal-subtitle">
                                    Registre um novo serviço realizado para o cliente.
                                </p>
                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.fieldsmaintenance', [
                            'maintenance' => null,
                        ])

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            <i class="bi bi-x-lg"></i>
                            Cancelar

                        </button>

                        <button type="submit" class="btn dcm-btn-primary">

                            <i class="bi bi-check-lg"></i>
                            Salvar manutenção

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
@else
    {{-- ============================================================
        MODAL EDITAR MANUTENÇÃO
    ============================================================= --}}

    <div class="modal fade service-modal" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1"
        aria-labelledby="editMaintenanceModalLabel{{ $maintenance->id }}" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <form action="{{ route('service.maintenances.update', $maintenance->id) }}" method="POST"
                class="needs-validation" novalidate>

                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">

                        <div class="service-modal-heading">

                            <span class="service-modal-icon warning">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>
                                <h5 class="modal-title" id="editMaintenanceModalLabel{{ $maintenance->id }}">

                                    Editar Manutenção

                                </h5>

                                <p class="service-modal-subtitle">
                                    Atualize o serviço, os custos e a data da manutenção.
                                </p>
                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.fieldsmaintenance', [
                            'maintenance' => $maintenance,
                        ])

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">

                            <i class="bi bi-x-lg"></i>
                            Cancelar

                        </button>

                        <button type="submit" class="btn dcm-btn-primary">

                            <i class="bi bi-check-lg"></i>
                            Atualizar

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
@endif
