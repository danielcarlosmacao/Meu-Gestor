@extends('layouts.header')

@section('title', 'Cursos dos Colaboradores')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/vacation-manager-module.css') }}">
@endpush

@section('content')

    <div class="container vm-page">

        <div class="vm-card">

            {{-- Cabeçalho --}}
            <div class="vm-card-header">

                <div class="vm-title-group">

                    <div class="vm-title-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>

                    <div>
                        <h1 class="vm-title">
                            Cursos realizados
                        </h1>

                        <p class="vm-subtitle">
                            Controle de cursos, treinamentos, certificados e validades
                        </p>
                    </div>

                </div>

                @can('collaborators.courses.create')
                    <button type="button" class="btn vm-btn-add" data-bs-toggle="modal" data-bs-target="#addCourseModal"
                        title="Adicionar curso">
                        <i class="bi bi-plus-lg"></i>

                        <span class="vm-btn-text">
                            Novo curso
                        </span>
                    </button>
                @endcan

            </div>

            {{-- Tabela --}}
            <div class="vm-table-wrapper">

                <table class="table vm-table">

                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Colaborador</th>
                            <th>Validade</th>
                            <th>Anexo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($courses as $course)
                            <tr>

                                {{-- Curso --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $course->title }}
                                    </div>

                                    @if (!empty($course->description))
                                        <small class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($course->description, 70) }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Colaborador --}}
                                <td>
                                    <span class="vm-badge vm-collaborator-badge"
                                        style="background-color: {{ $course->collaborator->color ?? '#6c757d' }}">
                                        <i class="bi bi-person-fill"></i>

                                        {{ $course->collaborator->name }}
                                    </span>
                                </td>

                                {{-- Validade --}}
                                <td class="vm-date">

                                    @if ($course->valid_until)
                                        @php
                                            $validUntil = \Carbon\Carbon::parse($course->valid_until);
                                            $isExpired = $validUntil->isPast();
                                        @endphp

                                        <span
                                            class="vm-badge {{ $isExpired ? 'vm-badge-days is-over-limit' : 'vm-badge-status' }}">
                                            <i
                                                class="bi {{ $isExpired ? 'bi-exclamation-circle-fill' : 'bi-calendar-check-fill' }}"></i>

                                            {{ $validUntil->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-infinity me-1"></i>
                                            Sem validade
                                        </span>
                                    @endif

                                </td>

                                {{-- Anexo --}}
                                <td>

                                    <div class="vm-file-actions">

                                        @if ($course->token)
                                            @can('collaborators.courses.view.pdf')
                                                <a href="{{ route('vacation_manager.collaborator.courses.show', $course->token) }}"
                                                    class="btn btn-outline-primary vm-action-btn" target="_blank"
                                                    rel="noopener noreferrer" title="Visualizar PDF">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                    PDF
                                                </a>

                                                <a href="{{ route('vacation_manager.collaborator.courses.download', $course->token) }}"
                                                    class="btn btn-outline-secondary vm-action-btn" title="Baixar PDF">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endcan
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-paperclip me-1"></i>
                                                Sem anexo
                                            </span>
                                        @endif

                                    </div>

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="vm-actions justify-content-end">

                                        @can('collaborators.courses.edit')
                                            <button type="button" class="btn btn-warning vm-action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editCourseModal" data-token="{{ $course->token }}"
                                                data-title="{{ $course->title }}"
                                                data-collaborator="{{ $course->collaborator_id }}"
                                                data-validity="{{ $course->valid_until ? \Carbon\Carbon::parse($course->valid_until)->format('Y-m-d') : '' }}"
                                                data-update-url="{{ route('vacation_manager.collaborator.courses.update', $course->token) }}"
                                                title="Editar curso">
                                                <i class="bi bi-pencil-square"></i>
                                                Editar
                                            </button>

                                            <form
                                                action="{{ route('vacation_manager.collaborator.courses.destroy', $course->token) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger vm-action-btn"
                                                    data-confirm="Deseja excluir este curso?" title="Excluir curso">
                                                    <i class="bi bi-trash"></i>
                                                    Excluir
                                                </button>
                                            </form>
                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="vm-empty">

                                    <i class="bi bi-mortarboard"></i>

                                    <strong>
                                        Nenhum curso cadastrado
                                    </strong>

                                    <div class="mt-1">
                                        Cadastre o primeiro curso ou treinamento.
                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($courses->hasPages())
                <div class="vm-pagination">
                    {{ $courses->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- =====================================================
        MODAL: ADICIONAR CURSO
    ====================================================== --}}
    <div class="modal fade vm-modal" id="addCourseModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <form method="POST" action="{{ route('vacation_manager.collaborator.courses.store') }}"
                enctype="multipart/form-data" class="modal-content" data-submit-lock>

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-mortarboard-fill me-2"></i>
                        Adicionar curso
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Colaborador
                        </label>

                        <select name="collaborator_id" class="form-select" required>
                            <option value="">
                                Selecione...
                            </option>

                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}">
                                    {{ $collaborator->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Título
                        </label>

                        <input type="text" name="title" class="form-control"
                            placeholder="Ex.: NR-10, Trabalho em altura..." required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Descrição
                        </label>

                        <textarea name="description" class="form-control" rows="4" placeholder="Informações adicionais sobre o curso"></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Validade
                        </label>

                        <input type="date" name="valid_until" class="form-control">

                        <small class="text-muted">
                            Deixe em branco caso o curso não tenha validade.
                        </small>

                    </div>

                    <div class="mb-0">

                        <label class="form-label">
                            Arquivo do curso
                        </label>

                        <input type="file" name="file" class="form-control" accept="application/pdf"
                            data-file-preview="#addCourseFileName" required>

                        <span id="addCourseFileName" class="vm-file-name">
                            Nenhum arquivo selecionado.
                        </span>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar curso
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- =====================================================
        MODAL: EDITAR CURSO
    ====================================================== --}}
    <div class="modal fade vm-modal" id="editCourseModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <form id="editCourseForm" method="POST" class="modal-content" data-submit-lock>

                @csrf
                @method('PUT')

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>
                        Editar curso
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label for="edit_title" class="form-label">
                            Título
                        </label>

                        <input type="text" id="edit_title" name="title" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_collaborator" class="form-label">
                            Colaborador
                        </label>

                        <select id="edit_collaborator" name="collaborator_id" class="form-select" required>
                            @foreach ($collaborators as $collaborator)
                                <option value="{{ $collaborator->id }}">
                                    {{ $collaborator->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="mb-0">

                        <label for="edit_validity" class="form-label">
                            Validade
                        </label>

                        <input type="date" id="edit_validity" name="valid_until" class="form-control">

                        <small class="text-muted">
                            Deixe em branco caso o curso não tenha validade.
                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>

                    <button type="submit" class="btn dcm-btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar alterações
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
@endpush
