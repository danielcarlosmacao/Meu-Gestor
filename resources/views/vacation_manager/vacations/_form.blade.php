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
                            value="{{ isset($vacation->start_date) ? $vacation->start_date->format('Y-m-d') : '' }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Data de Fim</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ isset($vacation->end_date) ? $vacation->end_date->format('Y-m-d') : '' }}"
                            required>
                    </div>
                    @if (!$isEdit)
                        <div class="mb-3">
                            <label>Duração das férias</label>
                            <p class="days-difference form-control-plaintext text-primary">Selecione as datas.</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label>Informações</label>
                        <textarea name="information" class="form-control">{{ $vacation->information ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn dcm-btn-primary">{{ $isEdit ? 'Salvar' : 'Cadastrar' }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                    @can('vacations.delete')
                        @if ($isEdit)
                            <form id="deleteVacationForm" method="POST"
                                action="{{ route('vacation_manager.vacations.destroy', ['id' => $vacation->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Tem certeza que deseja excluir estas férias?')">
                                    Excluir
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast de alerta para datas inválidas -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="invalidDateToast" class="toast align-items-center text-bg-warning border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Fechar"></button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastEl = document.getElementById('invalidDateToast');
            const toastMessage = document.getElementById('toastMessage');
            const bsToast = new bootstrap.Toast(toastEl, {
                delay: 4000
            });

            function parseDate(value) {
                if (!value) return null;
                const parts = value.split('-');
                if (parts.length !== 3) return null;
                return new Date(parts[0], parts[1] - 1, parts[2]);
            }

            function calculateDays(form) {
                const startInput = form.querySelector('input[name="start_date"]');
                const endInput = form.querySelector('input[name="end_date"]');
                const output = form.querySelector('.days-difference');

                if (!startInput || !endInput || !output) return;

                const startDate = parseDate(startInput.value);
                const endDate = parseDate(endInput.value);

                if (!startDate || !endDate) {
                    output.textContent = 'Selecione as datas.';
                    output.classList.remove('text-danger');
                    output.classList.add('text-primary');
                    return;
                }

                const diffTime = endDate - startDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                if (diffDays > 0) {
                    output.textContent = `${diffDays} dia(s) de férias`;
                    output.classList.remove('text-danger');
                    output.classList.add('text-primary');
                } else {
                    output.textContent = '⚠️ A data final deve ser depois da inicial.';
                    output.classList.remove('text-primary');
                    output.classList.add('text-danger');
                }
            }

            // Reage às mudanças de datas
            document.addEventListener('change', function(e) {
                if (e.target.matches('input[name="start_date"], input[name="end_date"]')) {
                    calculateDays(e.target.closest('form'));
                }

                if (e.target.matches('input[name="start_date"]')) {
                    const selectedDate = parseDate(e.target.value);
                    if (selectedDate) {
                        const dayOfWeek = selectedDate.getDay();
                        if ([5, 6, 0].includes(dayOfWeek)) {
                            toastMessage.textContent =
                                `⚠️ Atenção: Você selecionou uma data de início de férias que pode não estar de acordo com as normas trabalhistas.`;
                            bsToast.show();
                        }
                    }
                }
            });

            // Calcula dias ao abrir modal
            document.addEventListener('shown.bs.modal', function(event) {
                const modal = event.target;
                if (modal.id === 'addVacationModal' || modal.id.startsWith('editVacationModal')) {
                    setTimeout(() => {
                        const form = modal.querySelector('form');
                        if (form) calculateDays(form);
                    }, 200);
                }
            });
        });
    </script>
@endpush
