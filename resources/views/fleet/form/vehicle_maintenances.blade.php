<!-- Modal Adicionar Manutenção -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('fleet.vehicle_maintenances.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bgc-primary text-white rounded-top-4 border-0">
                    <h5 class="modal-title fw-bold">Nova Manutenção</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    @include('fleet.form.fieldsvehicle_maintenances', [
                        'maintenance' => null,
                        'vehicles' => $vehicles,
                        'vehicleServices' => $vehicleServices,
                        'workshops' => $workshops,
                    ])
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn dcm-btn-primary rounded-pill">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach ($maintenances as $maintenance)
    <div class="modal fade" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1"
        aria-labelledby="editMaintenanceModalLabel{{ $maintenance->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bgc-primary text-white rounded-top-4 border-0">
                    <h5 class="modal-title fw-bold">Editar Manutenção</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('fleet.vehicle_maintenances.update', $maintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('fleet.form.fieldsvehicle_maintenances', [
                            'maintenance' => $maintenance,
                            'vehicles' => $vehicles,
                            'vehicleServices' => $vehicleServices,
                            'workshops' => $workshops,
                        ])

                        <div class="modal-footer border-0 px-0 d-flex justify-content-between">
                            <button type="submit" class="btn dcm-btn-primary rounded-pill">
                                <i class="bi bi-save"></i> Atualizar
                            </button>


                    </form> @can('fleets.delete')
                        <!-- Botão de Excluir (fora do form de edição) -->
                        <form action="{{ route('fleet.vehicle_maintenances.destroy', $maintenance->id) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir esta manutenção?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    </div>
@endforeach



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            const vehicleSelect = form.querySelector('.vehicle-select');
            const serviceCheckboxes = form.querySelectorAll('#services-checkboxes .service-checkbox');
            const workshopSelect = form.querySelector('.workshop-select');
            const allWorkshopOptions = workshopSelect ? Array.from(workshopSelect.options) : [];

            if (!vehicleSelect) return;

            // === Filtra serviços realizados ===
            function filterServices() {
                const selectedVehicleType = vehicleSelect.options[vehicleSelect.selectedIndex]?.dataset
                    .vehicleType?.toLowerCase();
                if (!selectedVehicleType) return;

                serviceCheckboxes.forEach(div => {
                    const serviceType = div.dataset.serviceType?.toLowerCase();
                    if (serviceType === selectedVehicleType || serviceType === 'all') {
                        div.style.display = 'block';
                    } else {
                        div.style.display = 'none';
                        const checkbox = div.querySelector('input[type="checkbox"]');
                        if (checkbox) checkbox.checked = false;
                    }
                });
            }

            // === Filtra oficinas ===
            function filterWorkshops() {
                if (!workshopSelect) return;

                const selectedVehicleType = vehicleSelect.options[vehicleSelect.selectedIndex]?.dataset
                    .vehicleType?.toLowerCase();
                if (!selectedVehicleType) return;

                // Limpa opções antigas
                workshopSelect.innerHTML = '';

                // Filtra oficinas compatíveis
                const filteredOptions = allWorkshopOptions.filter(opt => {
                    const type = opt.dataset.workshopType?.toLowerCase();
                    return type === selectedVehicleType || type === 'all';
                });

                // Adiciona novamente as opções filtradas
                filteredOptions.forEach(opt => workshopSelect.appendChild(opt));
            }
            // === Atualiza o campo de quilometragem com base na maior já registrada ===
            function updateMileageHint() {
                const selectedId = vehicleSelect.value;
                const mileageInput = form.querySelector('input[name="mileage"]');
                const label = form.querySelector('label[for="mileage-label"]');

                if (selectedId && maxMileages[selectedId]) {
                    mileageInput.placeholder = "Última: " + maxMileages[selectedId] + " km";
                    if (label) {
                        label.innerText = "Quilometragem (última: " + maxMileages[selectedId] + " km)";
                    }
                } else {
                    mileageInput.placeholder = "";
                    if (label) {
                        label.innerText = "Quilometragem";
                    }
                }
            }


            // === Reage ao mudar o veículo
            vehicleSelect.addEventListener('change', () => {
                filterServices();
                filterWorkshops();
                updateMileageHint(); // ← Adicione aqui
            });

            // === Reage ao abrir modal, se estiver dentro de um
            const modal = form.closest('.modal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', () => {
                    filterServices();
                    filterWorkshops();
                });
            }

            // === Executa ao carregar a página
            filterServices();
            filterWorkshops();
        });
    });
</script>
