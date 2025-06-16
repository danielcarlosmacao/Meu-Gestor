@extends('layouts.header')
@section('title', 'Baterias')
@section('content')

    @if (session('success'))
        <script>
            window.onload = () => {
                alert("{{ session('success') }}");
            };
        </script>
    @endif


    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">Baterias
            @can('towers.create')
                <!-- Botão que abre o modal de adição -->
                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#addBattery">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>
    </div>

    <div class="container table-responsive">
        <table class="table table-striped">
            <thead class="bgc-primary text-white">
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Ah</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($batterys as $battery)
                    <tr>
                        <th scope="row">
                            <a href="{{ route('batteryproduction.report', $battery->id) }}"
                                class="text-decoration-none text-black">
                                {{ $battery->name }}
                            </a>
                        </th>
                        <td>{{ $battery->mark }}</td>
                        <td>{{ $battery->amps }}</td>
                        <td class="text-center align-middle p-1">
                            @can('towers.edit')
                                <button type="button" class="btn btn-warning btn-sm edit-battery-btn"
                                    data-id="{{ $battery->id }}" data-name="{{ $battery->name }}"
                                    data-mark="{{ $battery->mark }}" data-amps="{{ $battery->amps }}" data-bs-toggle="modal"
                                    data-bs-target="#editBatteryModal">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $batterys->links() }}
        </div>
        <br>
    </div>

    <!-- Modal de Adição -->
    <div class="modal fade" id="addBattery" tabindex="-1" aria-labelledby="addBatteryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-bgc-primary" id="addBatteryLabel">Nova Bateria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('battery.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="mark" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="mark" name="mark" required>
                        </div>
                        <div class="mb-3">
                            <label for="amps" class="form-label">Amperes</label>
                            <input type="number" class="form-control" id="amps" name="amps" min="0"
                                max="1000" step="1" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editBatteryModal" tabindex="-1" aria-labelledby="editBatteryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editBatteryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-bgc-primary" id="editBatteryModalLabel">Editar Bateria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_mark" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="edit_mark" name="mark" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_amps" class="form-label">Amperes</label>
                            <input type="number" class="form-control" id="edit_amps" name="amps" min="0"
                                max="1000" step="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                        @can('towers.delete')
                            <button type="button" class="btn btn-danger" id="deleteInModalBtn">Excluir</button>
                        @endcan
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const routeDestroy = "{{ route('battery.destroy', ['id' => ':id']) }}";
        const refDestroy = "esta bateria";
        const routeUpdate = "{{ route('battery.update', ['id' => ':id']) }}";

        // Preenche o modal de edição com os dados do botão clicado
        document.querySelectorAll('.edit-battery-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const name = button.dataset.name;
                const mark = button.dataset.mark;
                const amps = button.dataset.amps;

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_mark').value = mark;
                document.getElementById('edit_amps').value = amps;

                const form = document.getElementById('editBatteryForm');
                form.action = routeUpdate.replace(':id', id);

                // Configura botão de deletar dentro do modal
                document.getElementById('deleteInModalBtn').setAttribute('onclick', `deletar(${id})`);
            });
        });
    </script>

@endsection
