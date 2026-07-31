@php
    $isEdit = !is_null($vacation);

    $modalId = $isEdit ? 'editVacationModal' . $vacation->id : 'addVacationModal';

    $action = $isEdit
        ? route('vacation_manager.vacations.update', $vacation->id)
        : route('vacation_manager.vacations.store');

    $deleteFormId = $isEdit ? 'deleteVacationForm' . $vacation->id : null;

    $startDate = $isEdit && $vacation->start_date ? \Carbon\Carbon::parse($vacation->start_date)->format('Y-m-d') : '';

    $endDate = $isEdit && $vacation->end_date ? \Carbon\Carbon::parse($vacation->end_date)->format('Y-m-d') : '';

    $vacationDays = '';

    if ($isEdit && $startDate && $endDate) {
        $vacationDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
    }
@endphp

<div class="modal fade vm-modal vacation-modal" id="{{ $modalId }}" tabindex="-1"
    aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ $action }}" method="POST" data-submit-lock class="vacation-form">
                @csrf

                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="modal-header">

                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        <i class="bi bi-calendar2-week-fill me-2"></i>

                        {{ $isEdit ? 'Editar férias' : 'Cadastrar férias' }}
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>

                </div>

                <div class="modal-body">

                    {{-- Colaborador --}}
                    <div class="mb-3">

                        <label for="{{ $modalId }}_collaborator" class="form-label">
                            Colaborador
                        </label>

                        <select id="{{ $modalId }}_collaborator" name="collaborator_id" class="form-select"
                            required>
                            <option value="">
                                Selecione um colaborador
                            </option>

                            @foreach ($collaborators as $collaboratorOption)
                                <option value="{{ $collaboratorOption->id }}" @selected(old('collaborator_id', $vacation->collaborator_id ?? '') == $collaboratorOption->id)>
                                    {{ $collaboratorOption->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    {{-- Quantidade de dias --}}
                    <div class="mb-3">

                        <label for="{{ $modalId }}_temp_vacations" class="form-label">
                            Quantidade de dias
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-calendar3"></i>
                            </span>

                            <input type="number" id="{{ $modalId }}_temp_vacations" name="temp_vacations"
                                class="form-control vacation-days" value="{{ old('temp_vacations', $vacationDays) }}"
                                step="1" min="1" max="30" placeholder="Ex.: 30">

                            <span class="input-group-text">
                                dias
                            </span>

                        </div>

                        <small class="text-muted">
                            A data final será calculada automaticamente.
                        </small>

                    </div>

                    <div class="row">

                        {{-- Data inicial --}}
                        <div class="col-md-6 mb-3">

                            <label for="{{ $modalId }}_start_date" class="form-label">
                                Data de início
                            </label>

                            <input type="date" id="{{ $modalId }}_start_date" name="start_date"
                                class="form-control vacation-start-date" value="{{ old('start_date', $startDate) }}"
                                required>

                        </div>

                        {{-- Data final --}}
                        <div class="col-md-6 mb-3">

                            <label for="{{ $modalId }}_end_date" class="form-label">
                                Data de fim
                            </label>

                            <input type="date" id="{{ $modalId }}_end_date" name="end_date"
                                class="form-control vacation-end-date" value="{{ old('end_date', $endDate) }}"
                                required>

                        </div>

                    </div>

                    {{-- Informações --}}
                    <div class="mb-0">

                        <label for="{{ $modalId }}_information" class="form-label">
                            Informações
                        </label>

                        <textarea id="{{ $modalId }}_information" name="information" class="form-control" rows="4"
                            placeholder="Digite observações sobre este período de férias">{{ old('information', $vacation->information ?? '') }}</textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    @can('vacations.delete')
                        @if ($isEdit)
                            <button type="submit" class="btn btn-outline-danger me-auto" form="{{ $deleteFormId }}"
                                data-confirm="Tem certeza que deseja excluir estas férias?">
                                <i class="bi bi-trash me-1"></i>
                                Excluir
                            </button>
                        @endif
                    @endcan

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">
                        <i class="bi bi-check-lg me-1"></i>

                        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar férias' }}
                    </button>

                </div>

            </form>

            @can('vacations.delete')
                @if ($isEdit)
                    <form id="{{ $deleteFormId }}" method="POST"
                        action="{{ route('vacation_manager.vacations.destroy', ['id' => $vacation->id]) }}"
                        class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            @endcan

        </div>

    </div>
</div>
