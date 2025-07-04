<!-- Modal Adicionar Manutenção -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('fleet.vehicle_maintenances.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addMaintenanceModalLabel">Nova Manutenção</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          @include('fleet.form.fieldsvehicle_maintenances', [
              'maintenance' => null,
              'vehicles' => $vehicles,
              'vehicleServices' => $vehicleServices
          ])
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn dcm-btn-primary">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Manutenção -->
@foreach($maintenances as $maintenance)
  <div class="modal fade" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1" aria-labelledby="editMaintenanceModalLabel{{ $maintenance->id }}" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('fleet.vehicle_maintenances.update', $maintenance->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editMaintenanceModalLabel{{ $maintenance->id }}">Editar Manutenção</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            @include('fleet.form.fieldsvehicle_maintenances', [
                'maintenance' => $maintenance,
                'vehicles' => $vehicles,
                'vehicleServices' => $vehicleServices
            ])
          </div>
          <div class="modal-footer d-flex justify-content-between">
            <button type="submit" class="btn dcm-btn-primary">Atualizar</button>
          </form>
@can('fleets.delete')
          <form action="{{ route('fleet.vehicle_maintenances.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta manutenção?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Excluir</button>
          </form>
          @endcan
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
            const selectedVehicleType = vehicleSelect.options[vehicleSelect.selectedIndex]?.dataset.vehicleType?.toLowerCase();
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

            const selectedVehicleType = vehicleSelect.options[vehicleSelect.selectedIndex]?.dataset.vehicleType?.toLowerCase();
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

        // === Reage ao mudar o veículo
        vehicleSelect.addEventListener('change', () => {
            filterServices();
            filterWorkshops();
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

