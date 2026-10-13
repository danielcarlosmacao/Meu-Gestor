@extends('layouts.header')

@section('title', 'Minha escala de trabalho')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vacation-manager-module.css') }}">
    <style>
        .calendar-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            padding: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .calendar-table th {
            text-align: center;
            text-transform: lowercase;
            font-weight: 600;
            color: #6c757d;
            padding: 15px;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.95rem;
        }

        .calendar-table td {
            height: 120px;
            /* Calendário maior e mais espaçoso */
            width: 14.28%;
            vertical-align: top;
            padding: 10px;
            border: 1px solid #e9ecef;
        }

        /* Dias de outros meses com visual apagado/suave */
        .calendar-table td.text-muted-cell {
            background-color: #fafbfc;
            color: #b0b7bd;
        }

        .day-number {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
            display: block;
            text-align: right;
        }

        .text-muted-cell .day-number {
            color: #adb5bd;
        }

        /* Estilos das badges de status */
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
            color: #991b1b;
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
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="calendar-card">
            <!-- Cabeçalho do Calendário -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0 text-dark fw-bold text-capitalize">
                    {{ $carbonDate->translatedFormat('F') }} De {{ $carbonDate->format('Y') }}
                </h2>

                <div class="d-flex align-items-center gap-2">
                    @php
                        $prevMonth = $carbonDate->copy()->subMonth()->format('m-y');
                        $nextMonth = $carbonDate->copy()->addMonth()->format('m-y');
                        $currentMonthKey = now()->format('m-y');
                    @endphp
                    <a href="{{ route('vacation_manager.work_schedules.myscale', ['collaborator' => $collaborator->id, 'date' => $currentMonthKey]) }}"
                        class="btn btn-outline-secondary btn-sm">Hoje</a>
                    <div class="btn-group" role="group">
                        <a href="{{ route('vacation_manager.work_schedules.myscale', ['collaborator' => $collaborator->id, 'date' => $prevMonth]) }}"
                            class="btn btn-outline-success btn-sm"><i class="bi bi-chevron-left"></i></a>
                        <a href="{{ route('vacation_manager.work_schedules.myscale', ['collaborator' => $collaborator->id, 'date' => $nextMonth]) }}"
                            class="btn btn-outline-success btn-sm"><i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="text-muted mb-3">
                Colaborador: <strong>{{ $collaborator->name ?? $collaborator->nome }}</strong> &bull; Escala: <span
                    class="badge bg-secondary">{{ $collaborator->work_scale }}</span>
            </div>

            <!-- Grade / Tabela do Calendário -->
            <div class="table-responsive">
                <table class="table calendar-table mb-0">
                    <thead>
                        <tr>
                            <th>dom.</th>
                            <th>seg.</th>
                            <th>ter.</th>
                            <th>qua.</th>
                            <th>qui.</th>
                            <th>sex.</th>
                            <th>sáb.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weeks as $week)
                            <tr>
                                @foreach ($week as $dt)
                                    @php
                                        $dateKey = $dt->format('Y-m-d');
                                        $isCurrentMonth = $dt->month == $startOfMonth->month;
                                        $schedule = $schedules[$dateKey] ?? null;

                                        // Se houver registro, usa o tipo dele (Férias e Feriados sobrescrevem folgas naturalmente se salvos assim). Se não, assume 'work' para o mês atual.
                                        $type = $schedule ? $schedule->type : ($isCurrentMonth ? '' : null);

                                        $shortLabel = match ($type) {
                                            'work' => 'Trabalho',
                                            'day_off' => 'Folga',
                                            'medical_leave' => 'L. Médica',
                                            'maternity_leave' => 'L. Maternidade',
                                            'vacation' => 'Férias',
                                            'holiday' => 'Feriado',
                                            'lack' => 'Falta',
                                            default => '',
                                        };
                                    @endphp
                                    <td class="{{ !$isCurrentMonth ? 'text-muted-cell' : '' }}">
                                        <span class="day-number">{{ $dt->format('d') }}</span>
                                        @if ($type)
                                            <div class="badge badge-{{ $type }} w-100 py-1 text-center mt-1 {{ !$isCurrentMonth ? 'opacity-75' : '' }}"
                                                style="font-size: 0.75rem;">
                                                {{ $shortLabel }}
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
@endpush
