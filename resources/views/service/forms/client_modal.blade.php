@if(is_null($client))
<!-- Modal Adicionar -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('service.clients.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Novo Cliente</h5></div>
                <div class="modal-body">
                    @include('service.forms.client_fields')
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary" type="submit">Salvar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<!-- Modal Editar -->
<div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('service.clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Editar Cliente</h5></div>
                <div class="modal-body">
                    @include('service.forms.client_fields', ['client' => $client])
                </div>
                <div class="modal-footer">
                    <button class="btn dcm-btn-primary" type="submit">Atualizar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
