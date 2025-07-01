@extends('layouts.header')
@section('title', 'Férias dos Colaboradores')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center">Férias
            @can('vacations.create')
                <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addVacationModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

        <table class="table table-striped mt-4">
            <thead class="bgc-primary">
                <tr>
                    <th>Colaborador</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Dias de ferias</th>
                    <th>Informações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vacations as $vacation)
                    <tr>
                        @php
                        $vacationStart_date = new DateTime($vacation->start_date);
                        $vacationEnd_date = new DateTime($vacation->end_date);
                        $diff = $vacationStart_date->diff($vacationEnd_date) ;
                        $vacationInteval= $diff->days + 1;

                        @endphp
                        <td>{{ $vacation->collaborator->name }}</td>
                        <td>{{ $vacationStart_date->format('d/m/Y') }}</td>
                        <td>{{ $vacationEnd_date->format('d/m/Y') }}</td>
                        <td class="{{ $vacationInteval > 30 ? 'text-danger fw-bold' : '' }};">{{ $vacationInteval}}</td>
                        <td>{{ $vacation->info }}</td>
                        <td>
                            @can('vacations.edit')
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editVacationModal{{ $vacation->id }}">
                                    Editar
                                </button>
                            @endcan

                        </td>
                    </tr>

                    {{-- Modal de Edição --}}
                    @include('vacation_manager.vacations._form', [
                        'vacation' => $vacation,
                        'collaborators' => $collaborators,
                    ])
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $vacations->links() }}
        </div>
    </div>

    {{-- Modal de Adição --}}
    @include('vacation_manager.vacations._form', ['vacation' => null, 'collaborators' => $collaborators])
@endsection
