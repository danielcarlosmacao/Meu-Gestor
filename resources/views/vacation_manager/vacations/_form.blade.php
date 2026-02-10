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
                        <label>Dias de ferias</label>
                        <input type="number" name="temp_vacations" id="temp_vacations" class="form-control" 
                            step="1" min="1" max="60">
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
                    action="{{ route('vacation_manager.vacations.destroy', ['id' => $vacation->id]) }}">
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

<!-- Toast de alerta para datas inválidas -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="invalidDateToast" class="toast align-items-center text-bg-warning border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                <!-- mensagem será injetada aqui -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Fechar"></button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dayNames = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira',
                'sexta-feira', 'sábado'
            ];

            const toastEl = document.getElementById('invalidDateToast');
            const toastMessage = document.getElementById('toastMessage');
            const bsToast = new bootstrap.Toast(toastEl, {
                delay: 4000
            }); // 4 segundos

            document.querySelectorAll('input[type="date"][name="start_date"]').forEach(function(input) {
                let lastWarnedDate = null;

                input.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    if (isNaN(selectedDate)) return;

                    const dayOfWeek = selectedDate.getDay();
                    const dateStr = this.value;

                    if (['5', '6', '4'].includes(String(dayOfWeek)) && lastWarnedDate !== dateStr) {
                        toastMessage.textContent =
                            `⚠️ Atenção: Você selecionou uma data de inicio de ferias que pode nao esta de acordo com as normas trabalhistas.`;
                        bsToast.show();
                        lastWarnedDate = dateStr;
                    }
                });
            });

            // Calcula ferias automatico
            document.querySelectorAll('.modal').forEach(function(modal) {

                const tempVacationsInput = modal.querySelector('input[name="temp_vacations"]');
                const startDateInput = modal.querySelector('input[name="start_date"]');
                const endDateInput = modal.querySelector('input[name="end_date"]');

                if (!tempVacationsInput || !startDateInput || !endDateInput) return;

                function calculateEndDate() {
                    const days = parseInt(tempVacationsInput.value);
                    const startDateValue = startDateInput.value;

                    if (!startDateValue || isNaN(days) || days <= 0) return;

                    const startDate = new Date(startDateValue);
                    startDate.setDate(startDate.getDate() + days);

                    const year = startDate.getFullYear();
                    const month = String(startDate.getMonth() + 1).padStart(2, '0');
                    const day = String(startDate.getDate()).padStart(2, '0');

                    endDateInput.value = `${year}-${month}-${day}`;
                }

                tempVacationsInput.addEventListener('input', calculateEndDate);
                startDateInput.addEventListener('change', calculateEndDate);
            });

        });
    </script>
@endpush
