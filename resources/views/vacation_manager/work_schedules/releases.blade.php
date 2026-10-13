@extends('layouts.header')

@section('title', 'Gerenciamento de Afastamentos e Folgas')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Cabeçalho com Filtros e Botão Novo -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Afastamentos e Folgas</h1>
                <p class="text-muted mb-0">Controle de licenças médicas, maternidade e folgas avulsas do mês.</p>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Seletor de Mês e Ano -->
                <div class="d-flex align-items-center bg-white border rounded px-3 py-1 shadow-sm">
                    <label for="monthYearPicker" class="form-label mb-0 me-2 fw-semibold text-secondary">Mês/Ano:</label>
                    <input type="month" id="monthYearPicker" value="{{ $yearMonthValue }}"
                        class="form-control form-control-sm border-0 shadow-none">
                </div>

                <a href="{{ route('vacation_manager.holidays.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-calendar-event me-1"></i> Feriados
                </a>

                <!-- Botão Novo Lançamento -->
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#addReleaseModal">
                    <i class="bi bi-plus-lg me-1"></i> Novo Lançamento
                </button>
            </div>
        </div>


        <!-- Barra de Filtro (Checkbox de Exibir Folgas) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-2 px-3 d-flex align-items-center">
                <form method="GET" action="{{ route('vacation_manager.work_schedules.releases') }}" id="filterForm"
                    class="d-flex align-items-center">
                    <!-- Mantém a data atual no input oculto se houver troca de filtro -->
                    <input type="hidden" name="date" value="{{ $carbonDate->format('m-y') }}">

                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="show_day_off" name="show_day_off"
                            value="1" {{ $showDayOff ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit();">
                        <label class="form-check-label fw-semibold text-secondary ms-2" for="show_day_off">Exibir Folgas
                            (F)</label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Lançamentos -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Colaborador</th>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th class="text-end pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($releases as $release)
                                @php
                                    $typeName = match ($release->type) {
                                        'medical_leave' => 'Licença Médica',
                                        'maternity_leave' => 'Licença Maternidade',
                                        'day_off' => 'Folga',
                                        'lack' => 'Falta', // Tradução
                                        default => $release->type,
                                    };

                                    $badgeClass = match ($release->type) {
                                        'medical_leave' => 'bg-danger text-white',
                                        'maternity_leave' => 'bg-info text-dark',
                                        'day_off' => 'bg-warning text-dark',
                                        'lack' => 'bg-danger text-white', // Classe CSS personalizada
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-medium">
                                        {{ $release->collaborator->name ?? ($release->collaborator->nome ?? 'N/D') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($release->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">{{ $typeName }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        @can('collaborators.workschedule.edit')
                                            <form
                                                action="{{ route('vacation_manager.work_schedules.releases.destroy', $release->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir este lançamento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Nenhum registro encontrado para este mês.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($releases->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $releases->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Adição -->
    <div class="modal fade" id="addReleaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('vacation_manager.work_schedules.releases.store') }}" method="POST"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i> Adicionar Afastamento / Folga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Colaborador -->
                    <div class="mb-3">
                        <label for="collaborator_id" class="form-label fw-semibold">Colaborador</label>
                        <select name="collaborator_id" id="collaborator_id" class="form-select" required>
                            <option value="">Selecione o colaborador...</option>
                            @foreach ($collaborators as $collab)
                                <option value="{{ $collab->id }}">{{ $collab->name ?? $collab->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Tipo de Lançamento</label>
                        <select name="type" id="type" class="form-select" required>
                            @can('collaborators.medical_leave.edit')
                                <option value="medical_leave">Licença Médica</option>
                                <option value="maternity_leave">Licença Maternidade</option>
                            @endcan
                            @can('collaborators.workschedule.edit')
                                <option value="day_off">Folga</option>
                            @endcan
                            @can('collaborators.lack.edit')
                                <option value="lack">Falta</option>
                            @endcan
                        </select>
                    </div>

                    <!-- Data Inicial -->
                    <div class="mb-3">
                        <label for="start_date" class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Quantidade de Dias -->
                    <div class="mb-3">
                        <label for="days_count" class="form-label fw-semibold">Quantidade de Dias</label>
                        <input type="number" name="days_count" id="days_count" class="form-control" min="1"
                            value="1" required>
                        <div class="form-text">O sistema gerará os registros em sequência a partir da data inicial
                            informada.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Salvar
                        Lançamento</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seletor de mês/ano
            const picker = document.getElementById('monthYearPicker');
            if (picker) {
                picker.addEventListener('change', function() {
                    const value = this.value;
                    if (!value) return;

                    const [yearFull, month] = value.split('-');
                    const yearShort = yearFull.slice(-2);

                    const showDayOffParam = document.getElementById('show_day_off').checked ? '1' : '0';

                    window.location.href =
                        `/vacation_manager/work_schedules/releases?date=${month}-${yearShort}&show_day_off=${showDayOffParam}`;
                });
            }

            // Tratamento do Checkbox de Exibir Folgas para enviar 0 ou 1 corretamente
            const showDayOffCheckbox = document.getElementById('show_day_off');
            if (showDayOffCheckbox) {
                showDayOffCheckbox.addEventListener('change', function() {
                    const urlParams = new URLSearchParams(window.location.search);

                    // Pega o mês atual da URL se existir, senão deixa o padrão
                    const dateVal = urlParams.get('date') || '{{ $carbonDate->format('m-y') }}';
                    const showVal = this.checked ? '1' : '0';

                    window.location.href =
                        `/vacation_manager/work_schedules/releases?date=${dateVal}&show_day_off=${showVal}`;
                });
            }
        });
    </script>
@endpush
