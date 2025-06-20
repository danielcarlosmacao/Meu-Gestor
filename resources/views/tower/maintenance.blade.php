@extends('layouts.header')
@section('title', 'Manutenções de Torres')
@section('content')

    @if (session('success'))
        <script>
            window.onload = () => {
                alert("{{ session('success') }}");
            };
        </script>
    @endif

    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">Manutenções de Torres
            @can('towers.maintenance')
                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenance">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
        </h2>

    </div>
    <div class="container table-responsive">
        <table class="table table-striped">
            <thead class="bgc-primary text-white">
                <tr>
                    <th scope="col">Torre</th>
                    <th scope="col">Informação</th>
                    <th scope="col">Data Manutenção</th>
                    <th scope="col">Próxima Manutenção</th>
                    <th scope="col">Situação</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maintenances as $m)
                    <tr>
                        <td>{{ $m->tower->name ?? 'Torre não encontrada' }}</td>
                        <td>{{ $m->info }}</td>
                        <td>{{ $m->maintenance_date->format('d/m/Y') }}</td>
                        <td>{{ $m->next_maintenance_date->format('d/m/Y') }}</td>
                        <td>{{ __('status.' . $m->status) }}</td>
                        <td class="text-center align-middle p-1">
                            @can('towers.maintenance')
                                <button type="button" class="btn btn-warning btn-sm edit-maintenance-btn"
                                    data-id="{{ $m->id }}" data-tower_id="{{ $m->tower_id }}"
                                    data-info="{{ $m->info }}"
                                    data-maintenance_date="{{ $m->maintenance_date->format('Y-m-d') }}"
                                    data-next_maintenance_date="{{ $m->next_maintenance_date->format('Y-m-d') }}"
                                    data-status="{{ $m->status }}" data-bs-toggle="modal"
                                    data-bs-target="#editMaintenanceModal">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $maintenances->links() }}
        </div><br>

    </div>
    @can('towers.maintenance')
        <!-- Modal de Adição -->
        <div class="modal fade" id="addMaintenance" tabindex="-1" aria-labelledby="addMaintenanceLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMaintenanceLabel">Nova Manutenção</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('maintenance.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="tower_id" class="form-label">Torre</label>
                                <select class="form-control" id="tower_id" name="tower_id" required>
                                    <option value="">Selecione a Torre</option>
                                    @foreach ($towers as $tower)
                                        <option value="{{ $tower->id }}">{{ $tower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="info" class="form-label">Informação</label>
                                <textarea class="form-control" id="info" name="info" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="maintenance_date" class="form-label">Data Manutenção</label>
                                <input type="date" class="form-control" id="maintenance_date" name="maintenance_date"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="next_maintenance_date" class="form-label">Próxima Manutenção</label>
                                <input type="date" class="form-control" id="next_maintenance_date"
                                    name="next_maintenance_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Situação</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending">Pendente</option>
                                    <option value="completed">Concluída</option>
                                    <option value="archived">Arquivada</option>
                                </select>
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
        <div class="modal fade" id="editMaintenanceModal" tabindex="-1" aria-labelledby="editMaintenanceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="editMaintenanceForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMaintenanceModalLabel">Editar Manutenção</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id" name="id">
                            <div class="mb-3">
                                <label for="edit_tower_id" class="form-label">Torre</label>
                                <select class="form-control" id="edit_tower_id" name="tower_id" required>
                                    <option value="">Selecione a Torre</option>
                                    @foreach ($towers as $tower)
                                        <option value="{{ $tower->id }}">{{ $tower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_info" class="form-label">Informação</label>
                                <textarea class="form-control" id="edit_info" name="info" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_maintenance_date" class="form-label">Data Manutenção</label>
                                <input type="date" class="form-control" id="edit_maintenance_date"
                                    name="maintenance_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_next_maintenance_date" class="form-label">Próxima Manutenção</label>
                                <input type="date" class="form-control" id="edit_next_maintenance_date"
                                    name="next_maintenance_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Situação</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="pending">Pendente</option>
                                    <option value="completed">Concluída</option>
                                    <option value="archived">Arquivada</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn dcm-btn-primary">Salvar</button>
                            <button type="button" class="btn btn-danger" id="deleteInModalBtn">Excluir</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
    <!-- Scripts -->
    <script>
        const routeDestroy = "{{ route('maintenance.destroy', ':id') }}";
        const routeUpdate = "{{ route('maintenance.update', ':id') }}";

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-maintenance-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const tower_id = button.dataset.tower_id;
                    const info = button.dataset.info;
                    const maintenance_date = button.dataset.maintenance_date;
                    const next_maintenance_date = button.dataset.next_maintenance_date;
                    const status = button.dataset.status;

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_tower_id').value = tower_id;
                    document.getElementById('edit_info').value = info;
                    document.getElementById('edit_maintenance_date').value = maintenance_date;
                    document.getElementById('edit_next_maintenance_date').value =
                        next_maintenance_date;
                    document.getElementById('edit_status').value = status;

                    // Define a ação do formulário
                    document.getElementById('editMaintenanceForm').action = routeUpdate.replace(
                        ':id', id);

                    // Define a ação do botão de exclusão
                    document.getElementById('deleteInModalBtn').onclick = function() {
                        if (confirm('Tem certeza que deseja excluir esta manutenção?')) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = routeDestroy.replace(':id', id);

                            const token = document.createElement('input');
                            token.type = 'hidden';
                            token.name = '_token';
                            token.value = '{{ csrf_token() }}';

                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';

                            form.appendChild(token);
                            form.appendChild(method);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    };
                });
            });
        });
    </script>

@endsection
