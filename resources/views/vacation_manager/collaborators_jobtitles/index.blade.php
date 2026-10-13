@extends('layouts.header')

@section('title', 'Gerenciar Cargos de Colaboradores')

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
                        <i class="bi bi-briefcase-fill"></i>
                    </div>

                    <div>
                        <h1 class="vm-title">
                            Cargos de Colaboradores
                        </h1>

                        <p class="vm-subtitle">
                            Gerencie os cargos de colaboradores cadastrados no sistema
                        </p>
                    </div>

                </div>

                @can('collaborators.create')
                    <button type="button" class="btn vm-btn-add" data-bs-toggle="modal"
                        data-bs-target="#addCollaboratorJobtitleModal" title="Adicionar cargo de colaborador">
                        <i class="bi bi-plus-lg"></i>

                        <span class="vm-btn-text">
                            Novo cargo
                        </span>
                    </button>
                @endcan

            </div>

            {{-- Tabela --}}
            <div class="vm-table-wrapper">

                <table class="table vm-table">

                    <thead>
                        <tr>
                            <th>Cargo</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($jobtitles as $jobtitle)
                            <tr>

                                {{-- Cargo --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $jobtitle->name }}
                                    </div>
                                </td>

                                {{-- Descrição --}}
                                <td>
                                    <span class="text-muted">
                                        {{ $jobtitle->description ?? 'Nenhuma descrição informada' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span
                                        class="vm-badge vm-badge-status
                                            {{ $jobtitle->status !== 'active' ? 'is-inactive' : '' }}">
                                        <i
                                            class="bi
                                                {{ $jobtitle->status === 'active' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                        {{ $jobtitle->status === 'active' ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="vm-actions justify-content-end">

                                        @can('collaborators.edit')
                                            <button type="button" class="btn btn-warning vm-action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editCollaboratorJobtitleModal{{ $jobtitle->id }}"
                                                title="Editar cargo">
                                                <i class="bi bi-pencil-square"></i>

                                                <span>
                                                    Editar
                                                </span>
                                            </button>
                                        @endcan

                                    </div>

                                </td>

                            </tr>

                            {{-- Modal de edição (renderizado para cada item) --}}
                            @include('vacation_manager.collaborators_jobtitles._form', [
                                'jobtitle' => $jobtitle,
                            ])

                        @empty

                            <tr>
                                <td colspan="4" class="vm-empty">

                                    <i class="bi bi-briefcase"></i>

                                    <strong>
                                        Nenhum cargo cadastrado
                                    </strong>

                                    <div class="mt-1">
                                        Cadastre o primeiro cargo para começar.
                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if (method_exists($jobtitles, 'hasPages') && $jobtitles->hasPages())
                <div class="vm-pagination">
                    {{ $jobtitles->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de adição (jobtitle = null) --}}
    @include('vacation_manager.collaborators_jobtitles._form', ['jobtitle' => null])

@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
@endpush
