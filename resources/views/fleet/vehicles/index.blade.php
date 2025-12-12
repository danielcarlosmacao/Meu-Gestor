@extends('layouts.header')
@section('title', 'Controle de frota')
@section('content')

<div class="container mt-5">
    <h2 class="text-center">Controle de Frotas
        @can('fleets.create')
        <button class="btn dcm-btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="bi bi-plus-lg"></i>
        </button>
        @endcan
    </h2>

    <table class="table table-striped mt-4">
        <thead class="bgc-primary">
            <tr>
                <th></th>
                <th>Modelo</th>
                <th>Ano</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Tipo</th>
                <th>Combustível</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $vehicle)
            <tr>
                <td>
                    <div style="width: 20px; height: 20px; background-color: {{ $vehicle->color }}; border-radius: 4px;"></div>
                </td>
                <td>
                    <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}" class="btn btn-sm">
                        {{ $vehicle->model }}
                    </a>
                </td>
                <td>{{ $vehicle->year }}</td>
                <td>{{ $vehicle->license_plate }}</td>
                <td>{{ $vehicle->brand }}</td>
                <td> {{ __('vehicle_types.' . $vehicle->type) }}</td>
                <td>{{ $vehicle->fuel_type }}</td>
                <td>{{ __('status.' . $vehicle->status) }}</td>
                <td>@can('fleets.edit')
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editVehicleModal{{ $vehicle->id }}">
                        Editar
                    </button>
                    @endcan
                </td>
            </tr>
            @include('fleet.form.vehicles', ['vehicle' => $vehicle])
            @endforeach
        </tbody>
    </table>
              <div class="d-flex justify-content-center mt-4">
            {{ $vehicles->links() }}
        </div>
        <br>
</div>


@include('fleet.form.vehicles', ['vehicle' => null]) <!-- Modal de Adição -->


@endsection
