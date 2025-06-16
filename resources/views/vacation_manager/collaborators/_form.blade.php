@php
    $isEdit = !is_null($collaborator);
    $modalId = $isEdit ? 'editCollaboratorModal' . $collaborator->id : 'addCollaboratorModal';
    $action = $isEdit
        ? route('vacation_manager.collaborators.update', $collaborator->id)
        : route('vacation_manager.collaborators.store');
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Editar Colaborador' : 'Novo Colaborador' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ $collaborator->name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Data de Admissão</label>
                        <input type="date" name="admission_date" class="form-control" value="{{ $collaborator->admission_date ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Cor</label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ $collaborator->color ?? '#000000' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ isset($collaborator) && $collaborator->status === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ isset($collaborator) && $collaborator->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar' : 'Cadastrar' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
