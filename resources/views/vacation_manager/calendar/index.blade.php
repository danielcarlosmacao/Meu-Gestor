@extends('layouts.header')
@section('title', 'Calendário de Férias')

@section('content')

<style>
    .calendar {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* Agora 4 meses por linha */
        gap: 0.75rem;
    }
    .month {
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow: visible;
        font-size: 0.7rem;
    }
    .month-name {
        background: var(--color-primary);
        color: #fff;
        text-align: center;
        padding: 4px;
        font-weight: bold;
        font-size: 0.75rem;
    }
    .day-names, .days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
    }
    .day-names div {
        font-weight: bold;
        background: #f0f0f0;
        padding: 2px 0;
        font-size: 0.65rem;
    }
    .day {
        border: 1px solid #eee;
        padding: 3px;
        min-height: 30px;
        font-size: 0.65rem;
        position: relative;
        overflow: visible;
    }
    .vacation-day-multi {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        z-index: 1;
        border-radius: 3px;
        opacity: 0.85;
    }
    .vacation-day-multi div {
        flex: 1;
        min-height: 10px;
        cursor: pointer;
    }
    .day-number {
        position: relative;
        z-index: 2;
        font-weight: bold;
        font-size: 0.7rem;
    }
    .sunday .day-number {
        color: red;
    }
</style>

<div class="container my-4">
    <form method="GET" class="text-center mb-4">
        <label for="year">Ano:</label>
        <input type="number" name="year" id="year" value="{{ $year }}" class="form-control d-inline w-auto" min="2000" max="2100">
        <button class="btn dcm-btn-primary btn-sm">Atualizar</button>
    </form>

    <div class="calendar">
        @for ($month = 1; $month <= 12; $month++)
            @php
                $date = \Carbon\Carbon::create($year, $month, 1);
                $startDayOfWeek = $date->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
                $daysInMonth = $date->daysInMonth;
                $vacationMap = [];

                foreach ($vacations as $vacation) {
                    $start = \Carbon\Carbon::parse($vacation->start_date);
                    $end = \Carbon\Carbon::parse($vacation->end_date);
                    $period = \Carbon\CarbonPeriod::create($start, $end);

                    foreach ($period as $day) {
                        if ($day->year == $year && $day->month == $month) {
                            $vacationMap[$day->day][] = [
                                'color' => $vacation->collaborator->color ?? '#ccc',
                                'name' => $vacation->collaborator->name ?? 'N/A'
                            ];
                        }
                    }
                }
            @endphp

            <div class="month">
                <div class="month-name">{{ $date->translatedFormat('F') }}</div>

                <div class="day-names">
                    <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
                </div>

                <div class="days">
                    @php
                        $adjustedStart = $startDayOfWeek % 7; // transform ISO start into week starting on Sunday
                    @endphp

                    @for ($i = 0; $i < $adjustedStart; $i++)
                        <div class="day"></div>
                    @endfor

                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDay = \Carbon\Carbon::create($year, $month, $day);
                            $isSunday = $currentDay->dayOfWeek === 0;
                        @endphp

                        <div class="day {{ $isSunday ? 'sunday' : '' }}">
                            @if(isset($vacationMap[$day]))
                                <div class="vacation-day-multi">
                                    @foreach($vacationMap[$day] as $info)
                                        <div style="background-color: {{ $info['color'] }};"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $info['name'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="day-number">{{ $day }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        @endfor
    </div>
</div>

<!-- Script para tooltips -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@endsection
