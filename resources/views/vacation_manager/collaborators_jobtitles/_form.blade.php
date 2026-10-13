@php
    $isEdit = isset($jobtitle) && !is_null($jobtitle);

    $modalId = $isEdit ? 'editCollaboratorJobtitleModal' . $jobtitle->id : 'addCollaboratorJobtitleModal';

    $action = $isEdit
        ? route('vacation_manager.collaborators.jobtitles.update', $jobtitle->id)
        : route('vacation_manager.collaborators.jobtitles.store');
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

                        <i class="bi bi-briefcase-fill me-2"></i>

                        {{ $isEdit ? 'Editar Cargo' : 'Novo Cargo' }}

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Cargo
                        </label>

                        <input type="text" name="name" class="form-control" value="{{ $jobtitle->name ?? '' }}"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Descrição
                        </label>

                        <textarea name="description" class="form-control" rows="3">{{ $jobtitle->description ?? '' }}</textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select" required>

                            <option value="active" @selected(($jobtitle->status ?? 'active') == 'active')>
                                Ativo
                            </option>

                            <option value="inactive" @selected(($jobtitle->status ?? '') == 'inactive')>
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

                        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar cargo' }}

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
