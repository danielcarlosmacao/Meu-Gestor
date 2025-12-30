@extends('layouts.header')
@section('title', 'Baterias')
@section('content')

<div class="container mb-4 mt-4">
    <h2 class="text-center">
        Baterias
        @can('towers.create')
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
                <th>Nome</th>
                <th>Marca</th>
                <th>Tipo</th>
                <th>Voltagem</th>
                <th>Ah</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batterys as $battery)
                <tr>
                    <td>
                        <a href="{{ route('batteryproduction.report', $battery->id) }}"
                            class="text-decoration-none text-black">
                            {{ $battery->name }}
                        </a>
                    </td>
                    <td>{{ $battery->mark }}</td>
                    <td>{{ $battery->type ? __('baterry.' . $battery->type) : '' }}</td>
                    <td>{{ $battery->voltage }}</td>
                    <td>{{ $battery->amps }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm dcm-btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i> Ações
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('towers.edit')
                                    <li>
                                        <button class="dropdown-item edit-battery-btn"
                                            data-id="{{ $battery->id }}"
                                            data-name="{{ $battery->name }}"
                                            data-mark="{{ $battery->mark }}"
                                            data-type="{{ $battery->type }}"
                                            data-voltage="{{ $battery->voltage }}"
                                            data-amps="{{ $battery->amps }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBatteryModal">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                    </li>
                                @endcan

                                @can('towers.delete')
                                    <li>
                                        <form action="{{ route('battery.destroy', $battery->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item"
                                                onclick="return confirm('Tem certeza que deseja deletar?')">
                                                <i class="bi bi-trash"></i> Deletar
                                            </button>
                                        </form>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $batterys->links() }}
    </div>
</div>

{{-- ================= MODAL ADICIONAR ================= --}}
<div class="modal fade" id="addBattery" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('battery.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Bateria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label>Marca</label>
                        <input type="text" class="form-control" name="mark" required>
                    </div>

                    <div class="mb-3">
                        <label>Tipo</label>
                        <select class="form-control" name="type" required>
                            <option value="">Selecione</option>
                            <option value="Automotive">Automotiva</option>
                            <option value="stationary">Estacionária</option>
                            <option value="LiFePO4">LiFePO4</option>
                            <option value="others">Outro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Voltagem</label>
                        <input type="number" class="form-control" name="voltage" min="12" step="12" required>
                    </div>

                    <div class="mb-3">
                        <label>Amperes</label>
                        <input type="number" class="form-control" name="amps" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn dcm-btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDITAR ================= --}}
<div class="modal fade" id="editBatteryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editBatteryForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Bateria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label>Marca</label>
                        <input type="text" class="form-control" id="edit_mark" name="mark" required>
                    </div>

                    <div class="mb-3">
                        <label>Tipo</label>
                        <select class="form-control" id="edit_type" name="type" required>
                            <option value="Automotive">Automotiva</option>
                            <option value="stationary">Estacionária</option>
                            <option value="LiFePO4">LiFePO4</option>
                            <option value="others">Outro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Voltagem</label>
                        <input type="number" class="form-control" id="edit_voltage" name="voltage" required min="12" step="12">
                    </div>

                    <div class="mb-3">
                        <label>Amperes</label>
                        <input type="number" class="form-control" id="edit_amps" name="amps" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn dcm-btn-primary">Salvar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= JS ================= --}}
<script>
    const routeUpdate = "{{ route('battery.update', ':id') }}";

    document.querySelectorAll('.edit-battery-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_mark').value = btn.dataset.mark;
            document.getElementById('edit_type').value = btn.dataset.type;
            document.getElementById('edit_voltage').value = btn.dataset.voltage;
            document.getElementById('edit_amps').value = btn.dataset.amps;

            document.getElementById('editBatteryForm').action =
                routeUpdate.replace(':id', btn.dataset.id);
        });
    });
</script>

@endsection
