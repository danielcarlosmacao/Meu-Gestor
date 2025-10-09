@if(is_null($maintenance))
<!-- Modal Adicionar -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('service.maintenances.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Nova Manutenção</h5></div>
                <div class="modal-body">
                    @include('service.forms.fieldsmaintenance')
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<!-- Modal Edição -->
<div class="modal fade" id="editMaintenanceModal{{ $maintenance->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('service.maintenances.update', $maintenance->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Editar Manutenção</h5></div>
                <div class="modal-body">
                    @include('service.forms.fieldsmaintenance', ['maintenance' => $maintenance])
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary">Atualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
