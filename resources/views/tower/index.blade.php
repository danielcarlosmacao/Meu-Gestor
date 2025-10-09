@extends('layouts.header')
@section('title', 'Torres')
@section('content')


    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">controle de torres
            @can('towers.create')
                <button type="button" class="btn dcm-btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTower">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

    </div>

    <div class="container table-responsive">
        <table class="table table-striped ">
            <thead class="bgc-primary text-white">
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Voltagem</th>
                    <th scope="col">Equipamentos</th>
                    <th scope="col">Bateria</th>
                    <th scope="col">%</th>
                    <th scope="col">Data Inst. bateria</th>
                    <th scope="col">Tempo em Produção</th>
                    <th scope="col">Placa</th>
                    <th scope="col">%</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($towers as $tower)
                    <tr>
                        @php
                            if ($tower->activeBattery === null || $tower->activeBattery === '') {
                                $production_percentage = '0';
                            } else {
                                $voltageRatio = $tower->voltage / 12;
                                $totalAmp =
                                    $voltageRatio > 0
                                        ? ($tower->activeBattery->amount * $tower->activeBattery->battery->amps) /
                                            $voltageRatio
                                        : 0;
                                $production_percentage =
                                    $totalAmp > 0 ? ($tower->summary->battery_required / $totalAmp) * 100 : 0;
                            }

                            $consumptionAhDay = $tower->summary->consumption_ah_day ?? 0;
                            $platerrequire = $hours_Generation > 0 ? $consumptionAhDay / $hours_Generation : 0;
                            $plater_percentage =
                                $tower->summary->amps_plate > 0
                                    ? number_format(($platerrequire / $tower->summary->amps_plate) * 100, 2) . '%'
                                    : '0%';

                        @endphp
                        <th scope="row">
                            <a href="{{ route('tower.show', $tower->id) }}" class="text-decoration-none text-black">
                                {{ $tower->name }}</a>
                        </th>
                        <td>{{ $tower->voltage }}</td>
                        <td>{{ $tower->active_equipments_count }}</td>
                        <td>{{ $tower->activeBattery->battery->name ?? 'Sem bateria' }}</td>
                        <td>{{ number_format($production_percentage, 2) . '%' }}</td>
                        <td>{{ optional(optional($tower->activeBattery)->installation_date)->format('d/m/Y') ?? 'Sem bateria' }}
                        </td>
                        <td>{{ $tower->activeBattery->years_since_installation ?? 'Sem bateria' }}</td>
                        <td>{{ round($tower->summary->watts_plate) }} W - {{ round($tower->summary->amps_plate) }} A</td>
                        <td>{{ $plater_percentage }}</td>
                        <td class="text-center align-middle p-1">
                            @can('towers.delete')
                                <form action="{{ route('tower.destroy', $tower->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Tem certeza que deseja deletar esta torre?')">
                                        <i class="bi bi-trash"></i> Deletar
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $towers->links() }}
        </div>



    </div>

    <div class="modal fade" id="addTower" tabindex="-1" aria-labelledby="addTowerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow rounded-4 border-0">

                <div class="modal-header bgc-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold" id="addTowerLabel">Novo Registro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <form action="{{ route('tower.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nome <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-pill" id="name" name="name" required
                                placeholder="Digite o nome da torre">
                            <div class="invalid-feedback">
                                Por favor, insira o nome da torre.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="voltage" class="form-label fw-semibold">Voltagem (V) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-pill" id="voltage" name="voltage"
                                min="12" max="1000" step="12" required placeholder="Ex: 12, 24, 36...">
                            <div class="invalid-feedback">
                                Informe uma voltagem válida entre 12 e 1000.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn dcm-btn-primary rounded-pill">
                            <i class="bi bi-save"></i> Salvar
                        </button>
                        <button type="button" class="btn btn-secondary rounded-pill"
                            data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>





    <script>
        const routeDestroy = "{{ route('tower.destroy', ['id' => ':id']) }}";
        const refDestroy = "esta torre";

        // Exemplo simples de validação Bootstrap 5 nativa
        (() => {
            'use strict';
            const forms = document.querySelectorAll('form');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

@endsection
