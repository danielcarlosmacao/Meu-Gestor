@php
    $isEdit = !is_null($vacation);
    $modalId = $isEdit ? 'editVacationModal' . $vacation->id : 'addVacationModal';
    $action = $isEdit
        ? route('vacation_manager.vacations.update', $vacation->id)
        : route('vacation_manager.vacations.store');
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Editar Férias' : 'Nova Férias' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Colaborador</label>
                        <select name="collaborator_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}"
                                    {{ isset($vacation) && $vacation->collaborator_id == $collaborator->id ? 'selected' : '' }}>
                                    {{ $collaborator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Data de Início</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ $vacation->start_date ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Data de Fim</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ $vacation->end_date ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Informações</label>
                        <textarea name="information" class="form-control">{{ $vacation->information ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn dcm-btn-primary">{{ $isEdit ? 'Salvar' : 'Cadastrar' }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </form>
        @can('vacations.delete')
        @if ($isEdit == 'Salvar')
            <form id="deleteVacationForm" method="POST"
                action="{{ route('vacation_manager.vacations.destroy', ['id' => '__ID__']) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Tem certeza que deseja excluir estas férias?')">
                    Excluir
                </button>
            </form>
    </div>
    @endif
    @endcan

</div>
</div>
</div>
