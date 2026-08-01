@extends('layouts.header')

@section('title', 'Calendário de Férias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vacation-manager-module.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vacation-manager-calendar.css') }}">
@endpush

@section('content')

    @php
        /*
         * Feriados fixos e datas específicas do município.
         */
        $holidays = [
            "{$year}-01-01" => 'Confraternização Universal',
            "{$year}-02-13" => 'Aniversário da cidade',
            "{$year}-04-21" => 'Tiradentes',
            "{$year}-05-01" => 'Dia do Trabalho',
            "{$year}-09-07" => 'Independência do Brasil',
            "{$year}-10-12" => 'Nossa Senhora Aparecida',
            "{$year}-11-02" => 'Finados',
            "{$year}-11-15" => 'Proclamação da República',
            "{$year}-12-25" => 'Natal',
        ];

        /*
         * Monta todo o mapa de férias uma única vez.
         *
         * Estrutura:
         * $vacationMap['2026-07-01'] = [
         *     [
         *         'name'  => 'João',
         *         'color' => '#24b153',
         *     ]
         * ];
         */
        $vacationMap = [];

        foreach ($vacations as $vacation) {
            if (!$vacation->start_date || !$vacation->end_date) {
                continue;
            }

            $startDate = \Carbon\Carbon::parse($vacation->start_date);
            $endDate = \Carbon\Carbon::parse($vacation->end_date);

            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $periodDate) {
                if ((int) $periodDate->year !== (int) $year) {
                    continue;
                }

                $dateKey = $periodDate->format('Y-m-d');

                $vacationMap[$dateKey][] = [
                    'name' => $vacation->collaborator->name ?? 'Colaborador não informado',
                    'color' => $vacation->collaborator->color ?? '#adb5bd',
                ];
            }
        }

        /*
         * Colaboradores que aparecem no calendário.
         */
        $calendarCollaborators = $vacations
            ->filter(fn($vacation) => !is_null($vacation->collaborator))
            ->map(
                fn($vacation) => [
                    'id' => $vacation->collaborator->id,
                    'name' => $vacation->collaborator->name,
                    'color' => $vacation->collaborator->color ?? '#adb5bd',
                ],
            )
            ->unique('id')
            ->sortBy('name')
            ->values();
    @endphp

    <div class="container-fluid vm-page">

        <div class="vm-card">

            {{-- Cabeçalho --}}
            <div class="vm-card-header">

                <div class="vm-title-group">

                    <div class="vm-title-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <div>
                        <h1 class="vm-title">
                            Calendário de férias
                        </h1>

                        <p class="vm-subtitle">
                            Visualização anual dos períodos de férias dos colaboradores.
                        </p>
                    </div>

                </div>

                <form method="GET" action="{{ url()->current() }}" class="vacation-calendar-filter">
                    <div class="input-group input-group-sm">

                        <span class="input-group-text">
                            <i class="bi bi-calendar-event"></i>
                        </span>

                        <input type="number" name="year" id="calendarYear" value="{{ $year }}"
                            class="form-control" min="2000" max="2100" aria-label="Ano do calendário" required>

                        <button type="submit" class="btn dcm-btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Atualizar
                        </button>

                    </div>
                </form>

            </div>

            <div class="vm-card-body">

                {{-- Resumo --}}
                <div class="vacation-calendar-summary">

                    <div class="vacation-calendar-summary-item">

                        <div class="vacation-calendar-summary-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>

                        <div>
                            <span>Ano</span>
                            <strong>{{ $year }}</strong>
                        </div>

                    </div>

                    <div class="vacation-calendar-summary-item">

                        <div class="vacation-calendar-summary-icon">
                            <i class="bi bi-people"></i>
                        </div>

                        <div>
                            <span>Colaboradores</span>
                            <strong>
                                {{ $calendarCollaborators->count() }}
                            </strong>
                        </div>

                    </div>

                    <div class="vacation-calendar-summary-item">

                        <div class="vacation-calendar-summary-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>

                        <div>
                            <span>Períodos cadastrados</span>
                            <strong>
                                {{ $vacations->count() }}
                            </strong>
                        </div>

                    </div>

                    <div class="vacation-calendar-summary-item">

                        <div class="vacation-calendar-summary-icon">
                            <i class="bi bi-flag"></i>
                        </div>

                        <div>
                            <span>Feriados cadastrados</span>
                            <strong>
                                {{ count($holidays) }}
                            </strong>
                        </div>

                    </div>

                </div>

                {{-- Legenda 
                <div class="vacation-calendar-legend">

                    <div class="vacation-calendar-legend-title">
                        <i class="bi bi-info-circle me-1"></i>
                        Legenda
                    </div>

                    <div class="vacation-calendar-legend-items">

                        <div class="vacation-calendar-legend-item">
                            <span class="vacation-calendar-legend-sunday"></span>
                            Domingo
                        </div>

                        <div class="vacation-calendar-legend-item">
                            <span class="vacation-calendar-legend-holiday">
                                F
                            </span>
                            Feriado
                        </div>

                        @foreach ($calendarCollaborators as $calendarCollaborator)
                            <div class="vacation-calendar-legend-item">

                                <span class="vacation-calendar-legend-color"
                                    style="background-color: {{ $calendarCollaborator['color'] }}"></span>

                                {{ $calendarCollaborator['name'] }}

                            </div>
                        @endforeach

                    </div>

                </div>
--}}
                {{-- Calendário --}}
                <div class="vacation-calendar">

                    @for ($month = 1; $month <= 12; $month++)
                        @php
                            $monthDate = \Carbon\Carbon::create($year, $month, 1);

                            $daysInMonth = $monthDate->daysInMonth;

                            /*
                             * Carbon:
                             * 0 = domingo
                             * 1 = segunda
                             * ...
                             * 6 = sábado
                             */
                            $monthStartOffset = $monthDate->dayOfWeek;
                        @endphp

                        <section class="vacation-calendar-month">

                            <header class="vacation-calendar-month-header">

                                <span>
                                    {{ ucfirst($monthDate->translatedFormat('F')) }}
                                </span>

                                <small>
                                    {{ $monthDate->format('m/Y') }}
                                </small>

                            </header>

                            <div class="vacation-calendar-weekdays">

                                <div class="is-sunday">
                                    Dom
                                </div>

                                <div>
                                    Seg
                                </div>

                                <div>
                                    Ter
                                </div>

                                <div>
                                    Qua
                                </div>

                                <div>
                                    Qui
                                </div>

                                <div>
                                    Sex
                                </div>

                                <div>
                                    Sáb
                                </div>

                            </div>

                            <div class="vacation-calendar-days">

                                {{-- Espaços antes do primeiro dia --}}
                                @for ($emptyDay = 0; $emptyDay < $monthStartOffset; $emptyDay++)
                                    <div class="vacation-calendar-day is-empty" aria-hidden="true"></div>
                                @endfor

                                {{-- Dias do mês --}}
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $currentDate = \Carbon\Carbon::create($year, $month, $day);

                                        $dateKey = $currentDate->format('Y-m-d');

                                        $isSunday = $currentDate->isSunday();

                                        $isHoliday = isset($holidays[$dateKey]);

                                        $holidayName = $holidays[$dateKey] ?? null;

                                        $dayVacations = $vacationMap[$dateKey] ?? [];

                                        $hasVacation = count($dayVacations) > 0;

                                        $tooltipItems = [];

                                        foreach ($dayVacations as $vacationInfo) {
                                            $tooltipItems[] = $vacationInfo['name'];
                                        }

                                        if ($isHoliday) {
                                            $tooltipItems[] = $holidayName;
                                        }

                                        $tooltipText = implode(' • ', array_unique($tooltipItems));
                                    @endphp

                                    <div class="vacation-calendar-day
                                            {{ $isSunday ? 'is-sunday' : '' }}
                                            {{ $isHoliday ? 'is-holiday' : '' }}
                                            {{ $hasVacation ? 'has-vacation' : '' }}"
                                        @if ($tooltipText) data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="{{ $tooltipText }}" @endif>

                                        @if ($hasVacation)
                                            <div class="vacation-calendar-day-background">

                                                @foreach ($dayVacations as $vacationInfo)
                                                    <span style="background-color: {{ $vacationInfo['color'] }}"></span>
                                                @endforeach

                                            </div>
                                        @endif

                                        <span class="vacation-calendar-day-number">
                                            {{ $day }}
                                        </span>

                                        @if ($isHoliday)
                                            <span class="vacation-calendar-holiday-indicator" aria-label="Feriado">
                                                F
                                            </span>
                                        @endif

                                    </div>
                                @endfor

                            </div>

                        </section>
                    @endfor

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
    <script src="{{ asset('js/vacation-manager-calendar.js') }}"></script>
@endpush
