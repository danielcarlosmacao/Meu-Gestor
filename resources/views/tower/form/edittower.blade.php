<div class="container">
  
        <!-- Modal -->
        <div class="modal fade" id="editModal{{ $tower->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $tower->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('tower.update', $tower->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $tower->id }}">Editar Torre</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $tower->id }}" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name{{ $tower->id }}" name="name" value="{{ $tower->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="voltage{{ $tower->id }}" class="form-label">Tensão</label>
                                <input type="number" class="form-control" id="voltage{{ $tower->id }}" name="voltage" min="12" max="1000" step="12" value="{{ $tower->voltage }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

</div>