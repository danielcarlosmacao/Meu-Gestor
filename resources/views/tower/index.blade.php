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
                    <th scope="col">Data Inst. bateria</th>
                    <th scope="col">Tempo em Produção</th>
                    <th scope="col">Placa</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($towers as $tower)
                    <tr>
                        <th scope="row">
                            <a href="{{ route('tower.show', $tower->id) }}" class="text-decoration-none text-black">
                                {{ $tower->name }}</a>
                        </th>
                        <td>{{ $tower->voltage }}</td>
                        <td>{{ $tower->active_equipments_count }}</td>
                        <td>{{ $tower->activeBattery->battery->name ?? 'Sem bateria'  }}</td>
                        <td>{{ optional(optional($tower->activeBattery)->installation_date)->format('d/m/Y') ?? 'Sem bateria' }}</td>
                        <td>{{$tower->activeBattery->years_since_installation ?? 'Sem bateria' }}</td>
                        <td>{{round($tower->summary->watts_plate)}} W - {{round($tower->summary->amps_plate)}} A</td>
                        <td class="text-center align-middle p-1"> 
                            @can('towers.delete')
                            <button type="button" class="btn btn-danger btn-sm" onclick="deletar({{ $tower->id }})"><i class="bi bi-trash">
                                </i> Deletar
                            </button>
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
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-bgc-primary" id="addTowerLabel">Novo Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('tower.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>

                        <div class="mb-3">
                            <label for="voltage" class="form-label">Voltagem</label>
                            <input type="number" class="form-control" id="voltage" name="voltage" min="12"
                                max="1000" step="12" step="1" required>
                        </div>

                        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <script>
        const routeDestroy = "{{ route('tower.destroy', ['id' => ':id']) }}";
        const refDestroy = "esta torre";
    </script>
@endsection
