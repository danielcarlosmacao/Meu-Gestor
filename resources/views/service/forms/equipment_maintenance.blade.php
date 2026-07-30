@if (is_null($maintenance))
    {{-- ============================================================
        MODAL ADICIONAR MANUTENÇÃO
    ============================================================= --}}

    <div class="modal fade service-modal" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <form action="{{ route('service.equipment_maintenances.store') }}" method="POST" class="needs-validation"
                novalidate>

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
                                    Registre o envio e acompanhamento de um equipamento.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.fields_equipment_maintenance', [
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

            <form action="{{ route('service.equipment_maintenances.update', $maintenance->id) }}" method="POST"
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
                                    Atualize as informações do equipamento e do atendimento.
                                </p>

                            </div>

                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">
                        </button>

                    </div>

                    <div class="modal-body">

                        @include('service.forms.fields_equipment_maintenance', [
                            'maintenance' => $maintenance,
                        ])

                    </div>

                    <div class="modal-footer">

                        <div class="d-flex justify-content-between align-items-center w-100 gap-2 flex-wrap">

                            <div>

                                @can('service.delete')
                                    <button type="submit" class="btn btn-outline-danger"
                                        form="deleteEquipmentMaintenanceForm{{ $maintenance->id }}">

                                        <i class="bi bi-trash"></i>
                                        Excluir

                                    </button>
                                @endcan

                            </div>

                            <div class="d-flex gap-2">

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

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- ============================================================
        FORMULÁRIO DE EXCLUSÃO
    ============================================================= --}}

    @can('service.delete')
        <form id="deleteEquipmentMaintenanceForm{{ $maintenance->id }}"
            action="{{ route('service.equipment_maintenances.destroy', $maintenance->id) }}" method="POST" class="d-none"
            data-confirm-delete data-confirm-message="Deseja realmente excluir esta manutenção de equipamento?">

            @csrf
            @method('DELETE')

        </form>
    @endcan
@endif
