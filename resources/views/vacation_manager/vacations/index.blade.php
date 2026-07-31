@extends('layouts.header')

@section('title', 'Férias dos Colaboradores')

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
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>

                    <div>
                        <h1 class="vm-title">
                            Férias
                        </h1>

                        <p class="vm-subtitle">
                            Gerencie os períodos de férias dos colaboradores
                        </p>
                    </div>

                </div>

                @can('vacations.create')
                    <button type="button" class="btn vm-btn-add" data-bs-toggle="modal" data-bs-target="#addVacationModal"
                        title="Adicionar férias">
                        <i class="bi bi-plus-lg"></i>

                        <span class="vm-btn-text">
                            Novas férias
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
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Duração</th>
                            <th>Informações</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($vacations as $vacation)
                            @php
                                $vacationStartDate = \Carbon\Carbon::parse($vacation->start_date);
                                $vacationEndDate = \Carbon\Carbon::parse($vacation->end_date);

                                $vacationInterval = $vacationStartDate->diffInDays($vacationEndDate) + 1;
                            @endphp

                            <tr>

                                {{-- Colaborador --}}
                                <td>

                                    <div class="vm-name">

                                        <span class="vm-avatar"
                                            style="background-color: {{ $vacation->collaborator->color ?? '#6c757d' }}">
                                            {{ mb_strtoupper(mb_substr($vacation->collaborator->name, 0, 1)) }}
                                        </span>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $vacation->collaborator->name }}
                                            </div>

                                            <small class="text-muted">
                                                Colaborador #{{ $vacation->collaborator->id }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                {{-- Início --}}
                                <td class="vm-date">

                                    <i class="bi bi-calendar-event me-1 text-muted"></i>

                                    {{ $vacationStartDate->format('d/m/Y') }}

                                </td>

                                {{-- Fim --}}
                                <td class="vm-date">

                                    <i class="bi bi-calendar-check me-1 text-muted"></i>

                                    {{ $vacationEndDate->format('d/m/Y') }}

                                </td>

                                {{-- Quantidade de dias --}}
                                <td>

                                    <span
                                        class="vm-badge vm-badge-days
                                            {{ $vacationInterval > 30 ? 'is-over-limit' : '' }}">
                                        <i class="bi bi-calendar3"></i>

                                        {{ $vacationInterval }}
                                        {{ $vacationInterval === 1 ? 'dia' : 'dias' }}
                                    </span>

                                </td>

                                {{-- Informações --}}
                                <td>

                                    @if (!empty($vacation->info))
                                        <span title="{{ $vacation->info }}">
                                            {{ \Illuminate\Support\Str::limit($vacation->info, 80) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            Sem informações
                                        </span>
                                    @endif

                                </td>

                                {{-- Ações --}}
                                <td>

                                    <div class="vm-actions justify-content-end">

                                        @can('vacations.edit')
                                            <button type="button" class="btn btn-warning vm-action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editVacationModal{{ $vacation->id }}" title="Editar férias">
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
                            @include('vacation_manager.vacations._form', [
                                'vacation' => $vacation,
                                'collaborators' => $collaborators,
                            ])

                        @empty

                            <tr>
                                <td colspan="6" class="vm-empty">

                                    <i class="bi bi-calendar2-week"></i>

                                    <strong>
                                        Nenhum período de férias cadastrado
                                    </strong>

                                    <div class="mt-1">
                                        Cadastre as férias dos colaboradores para começar.
                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginação --}}
            @if ($vacations->hasPages())
                <div class="vm-pagination">
                    {{ $vacations->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal de adição --}}
    @include('vacation_manager.vacations._form', [
        'vacation' => null,
        'collaborators' => $collaborators,
    ])

@endsection

@push('scripts')
    <script src="{{ asset('js/vacation-manager-module.js') }}"></script>
@endpush
