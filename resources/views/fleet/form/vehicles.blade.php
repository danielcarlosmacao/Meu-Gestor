{{-- Modal de Criação ou Edição --}}
@if(is_null($vehicle))
<!-- Modal Adicionar -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('fleet.vehicles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Novo Veículo</h5></div>
                <div class="modal-body">
                    @include('fleet.form.fieldsvehicles')
                </div>
                <div class="modal-footer">
                  <button class="btn dcm-btn-primary" type="submit">Salvar</button>
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<!-- Modal Edição -->
<div class="modal fade" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('fleet.vehicles.update', $vehicle->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Editar Veículo</h5></div>
                <div class="modal-body">
                    @include('fleet.form.fieldsvehicles', ['vehicle' => $vehicle])
                </div>
                <div class="modal-footer">
                  <button class="btn dcm-btn-primary" type="submit">Atualizar</button>
                    <button class="btn btn-danger" form="deleteForm{{ $vehicle->id }}">Deletar</button>
                    <button  type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
        @can('fleets.delete')
        <form id="deleteForm{{ $vehicle->id }}" action="{{ route('fleet.vehicles.destroy', $vehicle->id) }}" method="POST">
            @csrf @method('DELETE')
        </form>
        @endcan
    </div>
</div>
@endif
