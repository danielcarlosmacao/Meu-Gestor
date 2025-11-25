@extends('layouts.header')
@section('title', 'Cursos dos Colaboradores')

@section('content')

    <div class="container mt-5">

        <h2 class="text-center">
            Cursos Realizados
            @can('collaborators.courses.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

        <table class="table table-striped mt-4">
            <thead class="bgc-primary">
                <tr>
                    <th>Título</th>
                    <th>Colaborador</th>
                    <th>Validade</th>
                    <th>Anexo</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td>{{ $course->title }}</td>

                        <td>
                            <span class="badge" style="background: {{ $course->collaborator->color }};">
                                {{ $course->collaborator->name }}
                            </span>
                        </td>

                        <td>
                            {{ $course->valid_until ? \Carbon\Carbon::parse($course->valid_until)->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            @if ($course->token)
                                @can('collaborators.courses.view.pdf')
                                    <a href="{{ route('vacation_manager.collaborator.courses.show', $course->token) }}"
                                        class="btn btn-sm dcm-btn-primary ms-1" target="_blank">
                                        PDF
                                    </a>

                                    <a href="{{ route('vacation_manager.collaborator.courses.download', $course->token) }}"
                                        class="btn btn-sm dcm-btn-primary ms-1">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endcan
                            @else
                                -
                            @endif
                        </td>


                        <td>
                            @can('collaborators.courses.edit')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal"
                                    data-token="{{ $course->token }}" data-title="{{ $course->title }}"
                                    data-collaborator="{{ $course->collaborator_id }}"
                                    data-validity="{{ $course->valid_until ? \Carbon\Carbon::parse($course->valid_until)->format('Y-m-d') : '' }}">
                                    Editar
                                </button>
                            @endcan
                            @can('collaborators.courses.edit')
                                <form action="{{ route('vacation_manager.collaborator.courses.destroy', $course->token) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Deseja excluir este curso?')">
                                        Excluir
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $courses->links() }}
        </div>

    </div>

    {{-- ========================
    MODAL: ADICIONAR CURSO
========================= --}}
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('vacation_manager.collaborator.courses.store') }}"
                enctype="multipart/form-data" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Colaborador</label>
                    <select name="collaborator_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        @foreach ($collaborators as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-3">Título</label>
                    <input type="text" name="title" class="form-control" required>

                    <label class="mt-3">Descrição</label>
                    <textarea name="description" class="form-control"></textarea>

                    <label class="mt-3">Validade</label>
                    <input type="date" name="valid_until" class="form-control">

                    <label class="mt-3">Arquivo do Curso (PDF)</label>
                    <input type="file" name="file" class="form-control" accept="application/pdf" required>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Salvar</button>
                </div>

            </form>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{--   MODAL PARA EDIÇÃO --}}
    {{-- ===================================================== --}}
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="editCourseForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Curso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Título</label>
                        <input type="text" id="edit_title" name="title" class="form-control mb-3" required>

                        <label class="form-label">Colaborador</label>
                        <select id="edit_collaborator" name="collaborator_id" class="form-control mb-3" required>
                            @foreach ($collaborators as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>

                        <label class="form-label">Validade</label>
                        <input type="date" id="edit_validity" name="valid_until" class="form-control mb-3" required>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            Salvar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection

{{-- ===================================================== --}}
{{--   SCRIPT MODAL DE EDIÇÃO --}}
{{-- ===================================================== --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {

        const modal = document.getElementById("editCourseModal");

        modal.addEventListener("show.bs.modal", (event) => {

            const button = event.relatedTarget;

            const token = button.getAttribute("data-token");
            const title = button.getAttribute("data-title");
            const collaborator = button.getAttribute("data-collaborator");
            const validity = button.getAttribute("data-validity");

            document.getElementById("edit_title").value = title;
            document.getElementById("edit_collaborator").value = collaborator;
            document.getElementById("edit_validity").value = validity;

            document.getElementById("editCourseForm").action =
                "/vacation_manager/collaborators/courses/" + token;
        });

    });
</script>
