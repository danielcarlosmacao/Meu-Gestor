@php
    $isEdit = !is_null($collaborator);

    $modalId = $isEdit ? 'editCollaboratorModal' . $collaborator->id : 'addCollaboratorModal';

    $action = $isEdit
        ? route('vacation_manager.collaborators.update', $collaborator->id)
        : route('vacation_manager.collaborators.store');
@endphp

<div class="modal fade vm-modal" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <form action="{{ $action }}" method="POST" data-submit-lock>

            @csrf

            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-person-badge-fill me-2"></i>

                        {{ $isEdit ? 'Editar Colaborador' : 'Novo Colaborador' }}

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Nome
                        </label>

                        <input type="text" name="name" class="form-control"
                            value="{{ $collaborator->name ?? '' }}" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Data de admissão
                        </label>

                        <input type="date" name="admission_date" class="form-control"
                            value="{{ $collaborator->admission_date ?? '' }}" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Cor do colaborador
                        </label>

                        <div class="d-flex align-items-center gap-3">

                            <input type="color" name="color" class="form-control form-control-color"
                                value="{{ $collaborator->color ?? '#24b153' }}" title="Escolha uma cor" required>

                            <small class="text-muted">
                                Esta cor será utilizada em listas e calendários.
                            </small>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select" required>

                            <option value="active" @selected(($collaborator->status ?? 'active') == 'active')>
                                Ativo
                            </option>

                            <option value="inactive" @selected(($collaborator->status ?? '') == 'inactive')>
                                Inativo
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>

                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">

                        <i class="bi bi-check-lg me-1"></i>

                        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar colaborador' }}

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
