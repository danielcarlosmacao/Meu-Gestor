@if(is_null($maintenance))
<!-- Modal Adicionar -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('service.equipment_maintenances.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaintenanceModalLabel">Nova Manutenção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    @include('service.forms.fields_equipment_maintenance')
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary" type="submit">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<!-- Modal Editar -->
<div class="modal fade" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1" aria-labelledby="editMaintenanceModalLabel{{ $maintenance->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('service.equipment_maintenances.update', $maintenance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMaintenanceModalLabel{{ $maintenance->id }}">Editar Manutenção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    @include('service.forms.fields_equipment_maintenance', ['maintenance' => $maintenance])
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary" type="submit">Atualizar</button>
                    <button class="btn btn-danger" form="deleteForm{{ $maintenance->id }}">Excluir</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>

        @can('services.delete')
        <form id="deleteForm{{ $maintenance->id }}" action="{{ route('service.equipment_maintenances.destroy', $maintenance->id) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
        @endcan
    </div>
</div>
@endif
