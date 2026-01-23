@extends('layouts.header')
@section('title', 'Tarefas do Dia')
@section('content')

    <div class="container mt-5">
         <h2 class="text-center d-flex justify-content-center align-items-center gap-2">
            Tarefas De {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}

            @can('tasks.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" id="btnCreateTask" data-date="{{ $date }}">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <a href="{{ route('tasks.calendar') }}" class="btn btn-outline-secondary btn-sm" title="Voltar ao calendário">
                    <i class="bi bi-calendar3"></i>
                </a>
            @endcan
        </h2>

        <ul class="list-group">
            @forelse($tasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center shadow-sm mb-2 rounded"
                    style="border-left: 6px solid {{ $task->color }}">

                    <div>
                        <strong>{{ $task->title }}</strong><br>

                        @if ($task->description)
                            <small class="text-muted">{{ $task->description }}</small><br>
                        @endif

                        <span class="badge mt-1" style="background-color: {{ $task->color }}">
                            {{ $task->status === 'completed' ? 'Concluída' : 'Pendente' }}
                        </span>
                    </div>

                    {{-- Ações --}}
                    @if (auth()->user()->canAny(['tasks.edit', 'tasks.delete']))
                        <div class="btn-group btn-group-sm">

                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i> Ações
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                @can('tasks.edit')
                                    <li>
                                        <form method="POST" action="{{ route('tasks.toggleStatus', $task) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="dropdown-item">
                                                {{ $task->status === 'completed' ? 'Reabrir' : 'Concluir' }}
                                            </button>
                                        </form>
                                    </li>

                                    <li>
                                        <button type="button" class="dropdown-item btn-edit-task"
                                            data-id="{{ $task->id }}" data-title="{{ $task->title }}"
                                            data-description="{{ $task->description }}" data-color="{{ $task->color }}">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                    </li>
                                @endcan

                                @can('tasks.delete')
                                    <li>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                            onsubmit="return confirm('Deseja excluir esta tarefa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    @endif

                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    Nenhuma tarefa para este dia.
                </li>
            @endforelse
        </ul>
    </div>

    {{-- ======================================================
MODAL ÚNICO (CRIAR + EDITAR)
====================================================== --}}
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow rounded-4 border-0">

                <div class="modal-header bgc-primary text-white">
                    <h5 class="modal-title" id="taskModalTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="taskForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="taskMethod">
                    <input type="hidden" name="task_date" id="taskDate">

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="title" id="taskTitle" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" id="taskDescription"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cor</label>
                            <input type="color" class="form-control form-control-color" name="color" id="taskColor">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ======================================================
JS (PRODUÇÃO)
====================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modalEl = document.getElementById('taskModal');
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('taskForm');

            const titleEl = document.getElementById('taskModalTitle');
            const methodEl = document.getElementById('taskMethod');
            const dateEl = document.getElementById('taskDate');

            // Criar
            const btnCreate = document.getElementById('btnCreateTask');
            if (btnCreate) {
                btnCreate.addEventListener('click', () => {

                    titleEl.innerText = 'Nova tarefa';
                    form.action = "{{ route('tasks.store') }}";
                    methodEl.value = '';
                    dateEl.value = btnCreate.dataset.date;

                    form.reset();
                    modal.show();
                });
            }

            // Editar
            document.querySelectorAll('.btn-edit-task').forEach(btn => {
                btn.addEventListener('click', () => {

                    titleEl.innerText = 'Editar tarefa';
                    form.action = `/tasks/${btn.dataset.id}`;
                    methodEl.value = 'PUT';
                    dateEl.value = "{{ $date }}";

                    document.getElementById('taskTitle').value = btn.dataset.title;
                    document.getElementById('taskDescription').value = btn.dataset.description;
                    document.getElementById('taskColor').value = btn.dataset.color;

                    modal.show();
                });
            });

        });
    </script>

@endsection
