@extends('layouts.header')
@section('title', 'Manutenções do Veículo')
@section('content')

    <div class="container mt-5">
        <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
            <h3>
                Manutenções de: {{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                <button id="toggle-filter" class="btn mb-3">
                    <i class="bi bi-search"></i>
                </button>
            </h3>
            <div id="filter-form" style="display: none;">
                <form method="GET" class="row g-3 mt-4 mb-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Data Inicial</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Data Final</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn dcm-btn-primary">Filtrar</button>
                        <a href="{{ route('fleet.vehicle.maintenances', $vehicle->id) }}"
                            class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
            </div>

            @if ($maintenances->isEmpty())
                <div class="alert alert-warning mt-3">Nenhuma manutenção registrada para este veículo.</div>
            @else
        </div>
        <div class="alert alert-info" style="font-family: 'Roboto', sans-serif;">
            <strong>Total de custos:</strong> R$ {{ number_format($totalCost, 2, ',', '.') }}
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Quilometragem</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Serviços</th>
                    <th>info</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $maintenance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}</td>
                        <td>{{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}</td>
                        <td>{{ number_format($maintenance->mileage, 0, ',', '.') ?? '-' }} km</td>
                        <td>R$ {{ number_format($maintenance->cost, 2, ',', '.') }}</td>
                        <td>{{ $maintenance->status === 'pending' ? 'Pendente' : 'Concluído' }}</td>
                        <td>
                            @foreach ($maintenance->services as $service)
                                <span class="badge bg-secondary">{{ $service->name }}</span>
                            @endforeach
                        </td>
                        <td>{{ $maintenance->parts_used ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $maintenances->links() }}
        </div><br>

        @endif

        <a href="{{ route('fleet.vehicles.index') }}" class="btn btn-secondary mt-3">Voltar</a>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleButton = document.getElementById('toggle-filter');
                const filterForm = document.getElementById('filter-form');

                toggleButton.addEventListener('click', function() {
                    const isVisible = filterForm.style.display === 'block';
                    filterForm.style.display = isVisible ? 'none' : 'block';
                    toggleButton.textContent = isVisible ? 'Mostrar Filtro' : 'Esconder Filtro';
                });
            });
        </script>
    @endpush
@endsection
