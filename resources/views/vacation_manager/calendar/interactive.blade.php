@extends('layouts.header')
@section('title', 'Calendário Interativo de Férias')

@section('content')

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<div class="container my-4">
    <div id="calendar"></div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridYear,dayGridMonth'
            },
            editable: true,
            eventSources: [
                {
                    url: '{{ route('vacation_manager.events') }}',
                    method: 'GET',
                    failure: function () {
                        alert('Erro ao carregar eventos.');
                    }
                }
            ],
            eventDrop: function(info) {
                updateVacation(info.event);
            },
            eventResize: function(info) {
                updateVacation(info.event);
            },
            eventDidMount: function(info) {
                const tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });

        calendar.render();

        function updateVacation(event) {
            fetch('{{ route('vacation_manager.events.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: event.id,
                    start: event.startStr,
                    end: event.endStr || event.startStr
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao atualizar');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Férias atualizadas com sucesso!');
                }
            })
            .catch(error => {
                alert('Erro ao salvar: ' + error.message);
            });
        }
    });
</script>

@endsection
