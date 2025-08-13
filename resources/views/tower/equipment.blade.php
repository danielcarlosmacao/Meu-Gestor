@extends('layouts.header')
@section('title', 'Equipamentos')
@section('content')

    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">Equipamentos
            <!-- Botão que abre o modal -->
            @can('towers.create')
                <button type="button" class="btn btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm">
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
                    <th scope="col">Watts</th>
                    <th scope="col">Estoque</th>
                    <th scope="col">Em Produção</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipments as $equipment)
                    <tr>
                        <th scope="row">{{ $equipment->name }}</th>
                        <td>{{ $equipment->watts }}</td>
                        <td>{{ $equipment->stock > 0 ? $equipment->stock : '' }}</td>
                        <td>{{ $equipment->equipment_productions_count }}</td>
                        <td class="text-center align-middle p-1">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm dcm-btn-primary dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i> Ações
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        @can('towers.edit')
                                            <button type="button" class="dropdown-item edit-equipment-btn"
                                                data-id="{{ $equipment->id }}" data-name="{{ $equipment->name }}"
                                                data-watts="{{ $equipment->watts }}"data-stock="{{ $equipment->stock }}"
                                                data-bs-toggle="modal" data-bs-target="#editModal">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                        @endcan
                                    </li>
                                    <li>
                                        @can('towers.delete')
                                            <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item"
                                                    onclick="return confirm('Tem certeza que deseja deletar este equipamento ?')">
                                                    <i class="bi bi-trash"></i> Deletar
                                                </button>
                                            </form>
                                        @endcan
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $equipments->links() }}
        </div>
        <br>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Cabeçalho -->
                <div class="modal-header">
                    <h5 class="modal-title text-bgc-primary" id="modalFormLabel">Adicionar Equipamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do modal com formulário -->
                <div class="modal-body">
                    <form action="{{ route('equipment.store') }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="watts" class="form-label">Watts:</label>
                            <input type="number" class="form-control" id="watts" name="watts" min="0"
                                max="1000" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Estoque:</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0"
                                max="1000" step="1" required>
                        </div>

                        <!-- Botão de envio -->
                        <div class="text-end">
                            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de edição (fora do loop) -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-bgc-primary" id="editModalLabel">Editar Equipamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_watts" class="form-label">Watts</label>
                            <input type="number" class="form-control" id="edit_watts" name="watts" min="0"
                                max="1000" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_stock" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="edit_stock" name="stock" min="0"
                                max="1000" step="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const routeUpdate = "{{ route('equipment.update', ['id' => ':id']) }}";

        document.querySelectorAll('.edit-equipment-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const name = button.dataset.name;
                const watts = button.dataset.watts;
                const stock = button.dataset.stock;

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_watts').value = watts;
                document.getElementById('edit_stock').value = stock;

                const form = document.getElementById('editForm');
                form.action = routeUpdate.replace(':id', id);
            });
        });
    </script>
@endsection
