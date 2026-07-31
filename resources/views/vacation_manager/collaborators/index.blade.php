@extends('layouts.header')

@section('title', 'Gerenciar Colaboradores')

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
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div>
                        <h1 class="vm-title">
                            Colaboradores
                        </h1>

                        <p class="vm-subtitle">
                            Gerencie os colaboradores cadastrados no sistema
                        </p>
                    </div>

                </div>

                @can('collaborators.create')
                    <button type="button" class="btn vm-btn-add" data-bs-toggle="modal" data-bs-target="#addCollaboratorModal"
                        title="Adicionar colaborador">
                        <i class="bi bi-plus-lg"></i>

                        <span class="vm-btn-text">
                            Novo colaborador
                        </span>
                    </button>
                @endcan

            </div>

            {{-- Tabela --}}
            <div class="vm-table-wrapper">

                <table class="table vm-table">

                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Data de admissão</th>
                            <th>Cor</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($collaborators as $collaborator)
                            <tr>

                                {{-- Nome --}}
                                <td>
                                    <div class="vm-name">

                                        <span class="vm-avatar"
                                            style="background-color: {{ $collaborator->color ?? '#6c757d' }}">
                                            {{ mb_strtoupper(mb_substr($collaborator->name, 0, 1)) }}
                                        </span>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $collaborator->name }}
                                            </div>

                                            <small class="text-muted">
                                                Código #{{ $collaborator->id }}
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                {{-- Data de admissão --}}
                                <td class="vm-date">

                                    <i class="bi bi-calendar3 me-1 text-muted"></i>

                                    {{ \Carbon\Carbon::parse($collaborator->admission_date)->format('d/m/Y') }}

                                </td>

                                {{-- Cor --}}
                                <td>

                                    <span class="vm-color-swatch"
                                        style="background-color: {{ $collaborator->color ?? '#6c757d' }}"
                                        title="{{ $collaborator->color }}"></span>

                                </td>

                                {{-- Status --}}
                                <td>

                                    <span
                                        class="vm-badge vm-badge-status
                                            {{ $collaborator->status !== 'active' ? 'is-inactive' : '' }}">
                                        <i
                                            class="bi
                                                {{ $collaborator->status === 'active' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>

                                        {{ __('status.' . $collaborator->status) }}
                                    </span>

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="vm-actions justify-content-end">

                                        @can('collaborators.edit')
                                            <button type="button" class="btn btn-warning vm-action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editCollaboratorModal{{ $collaborator->id }}"
                                                title="Editar colaborador">
                                                <i class="bi bi-pencil-square"></i>

                                                <span>
                                                    Editar
                                                </span>
                                            </button>
                                        @endcan

                                    </div>

                                </td>

                            </tr>

                            {{-- Modal de edição --}}
                            @include('vacation_manager.collaborators._form', [
                                'collaborator' => $collaborator,
                            ])

                        @empty

                            <tr>
                                <td colspan="5" class="vm-empty">

                                    <i class="bi bi-people"></i>

                                    <strong>
                                        Nenhum colaborador cadastrado
                                    </strong>

                                    <div class="mt-1">
                                        Cadastre o primeiro colaborador para começar.
                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($collaborators->hasPages())
                <div class="vm-pagination">
                    {{ $collaborators->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de adição --}}
    @include('vacation_manager.collaborators._form', ['collaborator' => null])

@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
@endpush
