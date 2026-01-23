@extends('layouts.header')
@section('title', 'Calendário de Tarefas')
@section('content')

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/taskcalendar.css" type="text/css">

    @can('tasks.view')
        <div class="container mt-5">
            <div class="container mt-4">
                <div id="calendar"></div>
            </div>
        </div>
    @endcan
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                height: 'auto',
                aspectRatio: 1.6,
                dayMaxEventRows: true,
                fixedWeekCount: false,

                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: 'today prev,next'
                },


                events: "{{ route('tasks.events') }}",

                dateClick(info) {
                    window.location.href = `/tasks/day/${info.dateStr}`;
                }
            });

            calendar.render();
        });
    </script>

@endsection
