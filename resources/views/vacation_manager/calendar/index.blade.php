@extends('layouts.header')
@section('title', 'Calendário de Férias')

@section('content')

<style>
    .calendar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    .month {
        border: 1px solid #ccc;
        border-radius: 8px;
        overflow: visible;
    }
    .month-name {
        background: #007bff;
        color: #fff;
        text-align: center;
        padding: 5px;
        font-weight: bold;
    }
    .day-names, .days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
    }
    .day-names div {
        font-weight: bold;
        background: #f0f0f0;
        padding: 3px 0;
    }
    .day {
        border: 1px solid #eee;
        padding: 5px;
        min-height: 40px;
        font-size: 0.75rem;
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
        min-height: 12px;
        cursor: pointer;
    }
    .day-number {
        position: relative;
        z-index: 2;
        font-weight: bold;
    }
</style>

<div class="container my-4">
    <form method="GET" class="text-center mb-4">
        <label for="year">Ano:</label>
        <input type="number" name="year" id="year" value="{{ $year }}" class="form-control d-inline w-auto" min="2000" max="2100">
        <button class="btn btn-primary btn-sm">Atualizar</button>
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
                    <div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sab</div><div>Dom</div>
                </div>

                <div class="days">
                    @for ($i = 1; $i < $startDayOfWeek; $i++)
                        <div class="day"></div>
                    @endfor

                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        <div class="day">
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
