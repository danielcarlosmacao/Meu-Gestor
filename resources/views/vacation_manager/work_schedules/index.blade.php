@extends('layouts.header')

@section('title', 'Escala de Trabalho')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vacation-manager-module.css') }}">
    <style>
        .schedule-grid-container {
            overflow-x: auto;
            max-width: 100%;
        }

        .schedule-table th,
        .schedule-table td {
            text-align: center;
            vertical-align: middle;
            font-size: 0.75rem;
            padding: 10px 5px;
            /* Reduzido para economizar espaço horizontal */
        }

        /* Coluna do colaborador mais compacta */
        .sticky-col {
            position: sticky;
            left: 0;
            background-color: #fff;
            z-index: 2;
            min-width: 150px !important;
            max-width: 150px !important;
        }

        /* Células dos dias compactas para caberem na tela sem rolagem */
        .schedule-day-cell {
            min-width: 40px !important;
            width: 40px !important;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .schedule-day-cell:hover {
            background-color: #f1f5f9;
        }

        .badge-work {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .badge-day_off {
            background-color: #fef08a;
            color: #854d0e;
        }

        .badge-medical_leave {
            background-color: #fee2e2;
            color: #ac4242;
        }

        .badge-maternity_leave {
            background-color: #fee2e2;
            color: #1b995a;
        }

        .badge-vacation {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-holiday {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .badge-lack {
            background-color: #35393d;
            color: #ffffff;
            border: 1px solid #cbd5e1;
        }

        /* Estilização do Calendário de Folgas no Modal */
        .calendar-month-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
        }

        .calendar-weekday-header {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa;
            padding: 8px;
            font-size: 0.85rem;
            border-radius: 4px;
        }

        .calendar-day-box {
            min-height: 110px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
        }

        .calendar-day-box.other-month {
            background-color: #f8f9fa;
            opacity: 0.5;
        }

        .calendar-day-number {
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 4px;
            text-align: right;
        }

        .calendar-day-content {
            font-size: 0.75rem;
            overflow-y: auto;
            max-height: 90px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Cabeçalho com Controles -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Escala de Trabalho</h1>
                <p class="text-muted mb-0">Gerenciamento mensal de turnos e folgas da equipe (Semanas fechadas).</p>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Seletor de Mês e Ano -->
                <div class="d-flex align-items-center bg-white border rounded px-3 py-1 shadow-sm">
                    <label for="monthYearPicker" class="form-label mb-0 me-2 fw-semibold text-secondary">Mês/Ano:</label>
                    <input type="month" id="monthYearPicker"
                        value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                        class="form-control form-control-sm border-0 shadow-none">
                </div>

                <!-- Botão Folgas (Abre o Modal com o calendário de folgas) -->
                <button type="button" class="btn btn-outline-warning text-dark btn-sm fw-semibold" data-bs-toggle="modal"
                    data-bs-target="#folgasCalendarModal">
                    <i class="bi bi-calendar-check me-1"></i> Folgas
                </button>

                <!-- Botão Atualizar jornada -->
                @can('collaborators.workschedule.edit')
                    <button type="button" id="btnUpdateSchedule" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#updateScheduleModal">
                        <i class="bi bi-arrow-repeat me-1"></i> Config da escala
                    </button>
                @endcan
            </div>
        </div>

        <!-- Grade Calendário do Mês -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="schedule-grid-container">
                    <table class="table table-bordered table-hover mb-0 schedule-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start ps-3 sticky-col" style="z-index: 3;">Colaborador</th>
                                @foreach ($daysList as $dt)
                                    @php
                                        $isCurrentMonth = $dt->month == $startOfMonth->month;
                                        $isSunday = $dt->isSunday();
                                        $isWeekend = $dt->isWeekend();
                                    @endphp
                                    <th
                                        class="{{ !$isCurrentMonth ? 'text-muted bg-light' : ($isSunday ? 'text-danger fw-bold' : ($isWeekend ? 'text-secondary' : '')) }} schedule-day-cell">
                                        <div style="font-size: 0.6rem;" class="text-uppercase">
                                            {{ mb_substr($dt->translatedFormat('D'), 0, 3) }}</div>
                                        <div style="font-size: 0.75rem;">{{ $dt->format('d') }}</div>
                                    </th>
                                @endforeach
                                <th class="text-center bg-light align-middle" style="min-width: 60px;"
                                    title="Total de Folgas">F</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($colaboradores as $colaborador)
                                <tr>
                                    <td class="text-start ps-2 fw-medium text-dark sticky-col">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-truncate" style="max-width: 150px;"
                                                title="{{ $colaborador->name ?? $colaborador->nome }}">
                                                {{ $colaborador->name ?? $colaborador->nome }}
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <span class="badge bg-secondary text-light"
                                                    style="font-size: 0.55rem;">{{ $colaborador->work_scale }}</span>
                                                <a href="{{ url('/vacation_manager/work_schedules/' . $colaborador->id . '/' . $month . '-' . substr($year, -2)) }}"
                                                    target="_blank" class="text-secondary" title="Ver escala individual">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach ($daysList as $dt)
                                        @php
                                            $dateKey = $dt->format('Y-m-d');
                                            $schedule = $schedulesMap[$colaborador->id][$dateKey] ?? null;
                                            $type = $schedule ? $schedule->type : 'work';
                                            $isCurrentMonth = $dt->month == $startOfMonth->month;

                                            $label = match ($type) {
                                                'work' => 'T',
                                                'day_off' => 'F',
                                                'medical_leave' => 'A',
                                                'maternity_leave' => 'M',
                                                'vacation' => 'V',
                                                'holiday' => 'H',
                                                'lack' => 'L',
                                                default => '-',
                                            };

                                            $titleText = match ($type) {
                                                'work' => 'Trabalho',
                                                'day_off' => 'Folga',
                                                'medical_leave' => 'Licença Médica',
                                                'maternity_leave' => 'Licença Maternidade',
                                                'vacation' => 'Férias',
                                                'holiday' => 'Feriado',
                                                'lack' => 'Falta',
                                                default => 'Outro',
                                            };
                                        @endphp

                                        <td class="{{ !$isCurrentMonth ? 'bg-light opacity-50' : '' }} schedule-day-cell"
                                            data-collaborator="{{ $colaborador->id }}" data-date="{{ $dateKey }}">
                                            <span class="badge badge-{{ $type }} w-100 py-1 schedule-badge"
                                                style="font-size: 0.7rem;"
                                                title="{{ $titleText }} - {{ $dateKey }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    @endforeach

                                    <!-- Coluna com o Total de Folgas do Mês -->
                                    <td class="text-center fw-bold bg-light align-middle">
                                        @php
                                            $totalFolgas = 0;
                                            foreach ($daysList as $dt) {
                                                if ($dt->month == $startOfMonth->month) {
                                                    $dateKey = $dt->format('Y-m-d');
                                                    if (
                                                        isset($schedulesMap[$colaborador->id][$dateKey]) &&
                                                        $schedulesMap[$colaborador->id][$dateKey]->type === 'day_off'
                                                    ) {
                                                        $totalFolgas++;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-warning text-dark px-2 py-1"
                                            style="font-size: 0.75rem;">{{ $totalFolgas }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($daysList) + 2 }}" class="text-center py-4 text-muted">
                                        Nenhum colaborador ativo encontrado com escala 6x1 ou 5x2.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL GRANDE: Calendário de Folgas do Mês -->
    <div class="modal fade" id="folgasCalendarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" id="print-area">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-secondary">
                        <i class="bi bi-calendar-week me-2 text-warning"></i> Calendário de Folgas -
                        {{ $startOfMonth->translatedFormat('F / Y') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="bg-white p-3 rounded shadow-sm">
                        <!-- Cabeçalho dos Dias da Semana -->
                        <div class="calendar-month-grid mb-2">
                            <div class="calendar-weekday-header text-danger">Dom</div>
                            <div class="calendar-weekday-header">Seg</div>
                            <div class="calendar-weekday-header">Ter</div>
                            <div class="calendar-weekday-header">Qua</div>
                            <div class="calendar-weekday-header">Qui</div>
                            <div class="calendar-weekday-header">Sex</div>
                            <div class="calendar-weekday-header">Sáb</div>
                        </div>

                        <!-- Grid de Dias do Mês (Calendário Completo) -->
                        <div class="calendar-month-grid">
                            @php
                                $totalCollaboratorsCount = count($colaboradores);
                            @endphp

                            @foreach ($daysList as $dt)
                                @php
                                    $isCurrentMonth = $dt->month == $startOfMonth->month;
                                    $dateKey = $dt->format('Y-m-d');
                                @endphp
                                <div class="calendar-day-box {{ !$isCurrentMonth ? 'other-month' : '' }}">
                                    <div class="calendar-day-number text-muted">
                                        {{ $dt->format('d/m') }}
                                    </div>
                                    <div class="calendar-day-content">
                                        @php
                                            $folgasNoDia = [];
                                            foreach ($colaboradores as $colaborador) {
                                                $schedule = $schedulesMap[$colaborador->id][$dateKey] ?? null;
                                                if ($schedule && $schedule->type === 'day_off') {
                                                    $folgasNoDia[] = explode(
                                                        ' ',
                                                        trim($colaborador->name ?? $colaborador->nome),
                                                    )[0];
                                                }
                                            }
                                            $isEveryoneOff =
                                                $totalCollaboratorsCount > 0 &&
                                                count($folgasNoDia) === $totalCollaboratorsCount;
                                        @endphp

                                        @if ($isEveryoneOff)
                                            <span
                                                class="badge bg-danger text-white border border-danger px-1 py-1 w-100 text-center"
                                                style="font-size: 0.65rem;"
                                                title="Todos os colaboradores estão de folga neste dia">
                                                <i class="bi bi-people-fill me-1"></i>Todos de folga
                                            </span>
                                        @elseif(count($folgasNoDia) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($folgasNoDia as $nomeCollab)
                                                    <!-- Nomes mais estreitos e compactos -->
                                                    <span
                                                        class="badge bg-warning-subtle text-dark border border-warning px-1 py-0 text-truncate"
                                                        style="font-size: 0.65rem; max-width: 100%;"
                                                        title="{{ $nomeCollab }}">
                                                        {{ $nomeCollab }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic" style="font-size: 0.60rem;">Nenhuma
                                                folga</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Atualizar Jornada -->
    <div class="modal fade vm-modal" id="updateScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('vacation_manager.work_schedules.recalculate') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-arrow-repeat me-2"></i> Atualizar Jornada / Recalcular Mês
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <!-- Empresa abre aos domingos -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Domingos são dias de folga?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="open_sunday"
                                        id="open_sunday_yes" value="1">
                                    <label class="form-check-label" for="open_sunday_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="open_sunday"
                                        id="open_sunday_no" value="0" checked>
                                    <label class="form-check-label" for="open_sunday_no">Não</label>
                                </div>
                            </div>
                        </div>

                        <!-- Gerar todos os dias -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gerar todos os dias?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="generate_all_days"
                                        id="gen_all_yes" value="1">
                                    <label class="form-check-label" for="gen_all_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="generate_all_days"
                                        id="gen_all_no" value="0" checked>
                                    <label class="form-check-label" for="gen_all_no">Não</label>
                                </div>
                            </div>
                        </div>

                        <!-- Lançar férias -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lançar férias?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="apply_vacations"
                                        id="vacations_yes" value="1">
                                    <label class="form-check-label" for="vacations_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="apply_vacations"
                                        id="vacations_no" value="0" checked>
                                    <label class="form-check-label" for="vacations_no">Não</label>
                                </div>
                            </div>
                        </div>

                        <!-- Lançar feriados -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lançar feriados?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="apply_holidays"
                                        id="holidays_yes" value="1">
                                    <label class="form-check-label" for="holidays_yes">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="apply_holidays"
                                        id="holidays_no" value="0" checked>
                                    <label class="form-check-label" for="holidays_no">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Processar Jornada
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.canEditSchedule = @json(auth()->user()->can('collaborators.workschedule.edit'));
    </script>
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
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

                            window.location.href = `/vacation_manager/work_schedules/month/${month}-${yearShort}`;
                        });
                    }
                    // Clique na célula da grade para alternar folga/trabalho (AJAX)
                    document.querySelectorAll('.schedule-day-cell[data-collaborator]').forEach(cell => {
                        cell.addEventListener('click', function() {
                            const collaboratorId = this.dataset.collaborator;
                            const date = this.dataset.date;
                            const badge = this.querySelector('.schedule-badge');
                            const row = this.closest('tr');

                            fetch("{{ route('vacation_manager.work_schedules.toggle') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        collaborator_id: collaboratorId,
                                        date: date
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Guarda o tipo antigo antes de atualizar a classe
                                        const oldType = badge.className.match(/badge-([a-z_]+)/)?.[1];

                                        // Atualiza a badge do dia clicado instantaneamente
                                        badge.className =
                                            `badge badge-${data.type} w-100 py-1 schedule-badge`;
                                        badge.textContent = data.label;
                                        badge.title = data.type;

                                        // Atualiza automaticamente o contador de folgas da linha sem precisar dar F5
                                        const totalFolgasBadge = row.querySelector(
                                            'td:last-child .badge');
                                        if (totalFolgasBadge) {
                                            let currentFolgas = parseInt(totalFolgasBadge
                                                .textContent) || 0;

                                            if (data.type === 'day_off') {
                                                currentFolgas++; // Se virou folga, soma 1
                                            } else if (oldType === 'day_off') {
                                                currentFolgas = Math.max(0, currentFolgas -
                                                1); // Se deixou de ser folga, subtrai 1
                                            }

                                            totalFolgasBadge.textContent = currentFolgas;
                                        }
                                    } else {
                                        alert(data.message ||
                                        'Não foi possível alterar este registro.');
                                    }
                                })
                                .catch(error => console.error('Erro ao atualizar escala:', error));
                        });
                    });
    </script>
@endpush
