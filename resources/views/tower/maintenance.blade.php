@extends('layouts.header')
@section('title', 'Manutenções de Torres')
@section('content')


    <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
        <h2 class="text-center">Manutenções de Torres
            @can('towers.maintenance')
                <button type="button" class="btn dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenance">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan
            <button id="toggleFilterBtn" type="button" class="btn dcm-btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </h2>

    </div>
    <div class="container table-responsive">
        <div id="filterDiv" class="mb-3 px-2" style="display: none;">
            <form method="GET" action="{{ route('maintenance.index') }}"
                class="d-flex align-items-center gap-3 w-100 flex-wrap">
                <label for="status" class="form-label mb-0 fw-semibold me-2">Filtrar por Situação:</label>
                <select name="status" id="status" class="form-select form-select-sm" onchange="this.form.submit()"
                    style="max-width: 220px; min-width: 150px; border-radius: 0.375rem; box-shadow: 0 0 5px rgba(0,123,255,0.5); transition: box-shadow 0.3s ease;">
                    <option value="" {{ request('status') === null ? 'selected' : '' }}>Todas</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluídas</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivadas</option>
                </select>
            </form>
        </div>

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
                    @php
                        $hoje = \Carbon\Carbon::today();
                        $diasAntes = $hoje->copy()->addDays(5);
                        $destacarData = $m->status === 'completed' && $m->next_maintenance_date->lte($diasAntes);

                        $limiteAtraso = $hoje->copy()->addDays(5);
                        $destacarAtraso = $m->status === 'pending' && $m->maintenance_date->lte($limiteAtraso);
                    @endphp

                    <tr>
                        <td>{{ $m->tower->name ?? 'Torre não encontrada' }}</td>
                        <td>{{ $m->info }}</td>
                        <td class="{{ $destacarAtraso ? 'text-danger fw-bold' : '' }}">
                            {{ $m->maintenance_date->format('d/m/Y') }}</td>
                        <td class="{{ $destacarData ? 'text-danger fw-bold' : '' }}">
                            {{ $m->next_maintenance_date->format('d/m/Y') }}</td>
                        <td>{{ __('status.' . $m->status) }}</td>
                        <td class="text-center align-middle p-1">
                            @can('towers.maintenance')
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm dcm-btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i> Ações
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>

                                            <button type="button" class="dropdown-item  edit-maintenance-btn"
                                                data-id="{{ $m->id }}" data-tower_id="{{ $m->tower_id }}"
                                                data-info="{{ $m->info }}"
                                                data-maintenance_date="{{ $m->maintenance_date->format('Y-m-d') }}"
                                                data-next_maintenance_date="{{ $m->next_maintenance_date->format('Y-m-d') }}"
                                                data-status="{{ $m->status }}" data-bs-toggle="modal"
                                                data-bs-target="#editMaintenanceModal">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>

                                        </li>
                                        <li>
                                            <form action="{{ route('maintenance.destroy', $m->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item "
                                                    onclick="return confirm('Tem certeza que deseja deletar esta bateria?')">
                                                    <i class="bi bi-trash"></i> Deletar
                                                </button>
                                            </form>

                                        </li>
                                    </ul>
                                </div>
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
                                    name="next_maintenance_date" >
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


        document.getElementById('toggleFilterBtn').addEventListener('click', function() {
            const filterDiv = document.getElementById('filterDiv');
            if (filterDiv.style.display === 'none' || filterDiv.style.display === '') {
                filterDiv.style.display = 'block';
                this.textContent = 'Ocultar Filtros';
            } else {
                filterDiv.style.display = 'none';
                this.textContent = 'Mostrar Filtros';
            }
        });
    </script>

@endsection
