@extends('layouts.header')
@section('title', 'Torres')
@section('content')
    @php
        $today = new DateTime();
    @endphp
    <div class="container-fluid">
        <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
            <h2 class="text-center"> {{ $tower->name }}
                @can('towers.edit')
                    <button class="btn dcm-btn-primary btn-sm " data-bs-toggle="modal"
                        data-bs-target="#editModal{{ $tower->id }}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                @endcan
            </h2>
        </div>
        <div class="row g-3">

            <!-- Bateria -->
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title text-center">Bateria
                            @can('towers.manage')
                                <button onclick="document.getElementById('batteryModal').style.display='block'"
                                    class="btn dcm-btn-primary">
                                    <i class="bi bi-plus-lg"></i> Adicionar
                                </button>
                            @endcan
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bgc-primary">
                                    <tr>
                                        <th>Informacao</th>
                                        <th>Nº</th>
                                        <th>Bateria</th>
                                        <th>Ah</th>
                                        <th>Data Inst.</th>
                                        <th>Data Rem.</th>
                                        <th>Ah total</th>
                                        <th>%</th>
                                        <th>Produção</th>
                                        <th>
                                            <a href="#"
                                                onclick="if(confirm('Deseja fixar o % da bateria nas baterias antigas?')){ window.location='{{ route('tower.recalcular.baterias', $tower->id) }}'; }"class="btn dcm-btn-primary">
                                                <i class="fa fa-refresh"></i>%
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tower->batteryProductions as $bp)
                                        @php

                                            $voltageRatio = $tower->voltage / $bp->battery->voltage;
                                            $totalAmp =
                                                $voltageRatio > 0
                                                    ? ($bp->amount * $bp->battery->amps) / $voltageRatio
                                                    : 0;

                                            // Verifica se o campo production_percentage está vazio
                                            if (
                                                $bp->production_percentage === null ||
                                                $bp->production_percentage === ''
                                            ) {
                                                $production_percentage =
                                                    $totalAmp > 0 ? ($summary->battery_required / $totalAmp) * 100 : 0;
                                            } else {
                                                $production_percentage = $bp->production_percentage;
                                            }

                                            $installationDate = new DateTime($bp->installation_date);
                                            $removalDate = new DateTime($bp->removal_date);

                                            if ($bp->removal_date == '' && $bp->active === 'yes') {
                                                $diff = $today->diff($installationDate); // OK!
                                                $years = $diff->y;
                                                $months = $diff->m;
                                                $tempProdution = $years . ' Ano e ' . $months . ' meses';
                                            } elseif ($bp->removal_date != '' || $bp->removal_date != null) {
                                                $diff = $installationDate->diff($removalDate); // OK!
                                                $years = $diff->y;
                                                $months = $diff->m;
                                                $tempProdution = $years . ' Ano e ' . $months . ' meses';
                                            } else {
                                                $tempProdution = '';
                                            }
                                        @endphp
                                        <tr class="{{ $bp->active === 'yes' ? 'fw-bold' : '' }}">
                                            <td>{{ $bp->info }}</td>
                                            <td>{{ $bp->amount }}</td>
                                            <td>{{ $bp->battery->mark }}</td>
                                            <td>{{ $bp->battery->amps }}</td>
                                            <td>{{ optional($bp->installation_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($bp->removal_date)->format('d/m/Y') }}</td>
                                            <td style="position: relative; padding-top: 4px; line-height: 1.1;">
    
                                                {{ number_format($totalAmp, 0) }} A

                                                @if ($bp->battery->voltage != 12)
                                                    <span class="voltbattery">
                                                        {{ $bp->battery->voltage }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($production_percentage, 2) }}%</td>
                                            <td>{{ $tempProdution }}</td>
                                            <td class="text-center align-middle p-1">
                                                @can('towers.manage')
                                                    <button class="edit-btn btn btn-warning btn-sm"
                                                        data-id="{{ $bp->id }}">Editar</button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title text-center">Resumo</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bgc-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>#</th>
                                        <th>#</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $wattsamps = $summary->watts_plate / $tower->voltage;

                                    @endphp
                                    <tr>
                                        <td>Horas Geração</td>
                                        <td>{{ $hours_Generation }}</td>
                                        <td>Horas autonomia</td>
                                        <td>{{ $hours_autonomy }}</td>
                                    </tr>
                                    <tr>
                                        <td>Consumo Ah Hora/Dia</td>
                                        <td>{{ $summary->time_ah_consumption }} &nbsp;
                                            {{ $summary->time_ah_consumption * 24 }}</td>
                                        <td>Bateria necessária</td>
                                        <td>{{ $summary->battery_required }}</td>
                                    </tr>
                                    <tr>
                                        <td>Consumo em Watts</td>
                                        <td>{{ $summary->consumption_ah_day }}</td>
                                        <td>Ah necessário gerar por 5h/dia</td>
                                        <td>{{ number_format($platerrequire, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Watts do painel</td>
                                        <td>{{ $summary->watts_plate }} </td>
                                        <td>Ah da placa</td>
                                        <td>
                                            {{ $summary->amps_plate }} A
                                            {{ $summary->amps_plate > 0 ? number_format(($platerrequire / $summary->amps_plate) * 100, 2) . '%' : '0%' }}

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Geração Watts em Ah por dia</td>
                                        <td>{{ number_format($wattsamps * $hours_Generation, 0) }} </td>
                                        <td>Geração Watts em Ah</td>
                                        <td>

                                            {{ number_format($wattsamps, 2) }} A
                                            {{ $wattsamps > 0 ? number_format(($platerrequire / $wattsamps) * 100, 2) . '%' : '0%' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Voltagem</td>
                                        <td>{{ $tower->voltage }} </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipamentos -->
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title text-center">Equipamentos
                            @can('towers.manage')
                                <button onclick="document.getElementById('equipmentModal').style.display='block'"
                                    class="btn dcm-btn-primary">
                                    <i class="bi bi-plus-lg"></i> Adicionar
                                </button>
                            @endcan
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bgc-primary">
                                    <tr>
                                        <th>Info</th>
                                        <th>Equipamento</th>
                                        <th>Watts</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tower->equipmentProductions as $ep)
                                        <tr>
                                            <td>{{ $ep->identification }}</td>
                                            <td>{{ $ep->equipment->name }}</td>
                                            <td>{{ $ep->equipment->watts }} W</td>
                                            <td>{{ $ep->active == 'yes' ? 'Ativo' : 'inativo' }}</td>
                                            <td class="text-center align-middle p-1">
                                                @can('towers.manage')
                                                    <button type="button" class="edit-equipment-btn btn btn-warning btn-sm"
                                                        data-id="{{ $ep->id }}">Editar</button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placa solar -->
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title text-center">Placa Solar
                            @can('towers.manage')
                                <button onclick="document.getElementById('plateModal').style.display='block'"
                                    class="btn dcm-btn-primary">

                                    <i class="bi bi-plus-lg"></i> Adicionar
                                </button>
                            @endcan
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bgc-primary">
                                    <tr>
                                        <th>Info</th>
                                        <th>Data instalação</th>
                                        <th>Ah</th>
                                        <th>Watts</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>
                                    @foreach ($tower->plateProductions as $pp)
                                        <tr>
                                            <td>{{ $pp->plate->name }}</td>
                                            <td>{{ $pp->plate->amps }} A</td>
                                            <td>{{ $pp->plate->watts }} W</td>
                                            <td>{{ optional($pp->installation_date)->format('d/m/Y') }}</td>
                                            <td class="text-center align-middle p-1">
                                                @can('towers.manage')
                                                    <button class="delete-plate-btn btn btn-danger"
                                                        data-id="{{ $pp->id }}">Excluir</button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                document.getElementById('batteryModal').style.display = 'block';
            });
        </script>
    @endif

    @extends('tower.form.addequipmenttower')
    @extends('tower.form.addbatterytower')
    @extends('tower.form.addplatetower')
    @extends('tower.form.editbatterytower')
    @extends('tower.form.editequipmenttower')
    @extends('tower.form.edittower')



    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // ---------- ROTAS ----------
                const routes = {
                    battery: {
                        edit: `{{ route('batteryproduction.edit', ['id' => '__ID__']) }}`,
                        update: `{{ route('batteryproduction.update', ['id' => '__ID__']) }}`,
                        destroy: `{{ route('batteryproduction.destroy', ['id' => '__ID__']) }}`
                    },
                    equipment: {
                        edit: `{{ route('equipmentproduction.edit', ['id' => '__ID__']) }}`,
                        update: `{{ route('equipmentproduction.update', ['id' => '__ID__']) }}`,
                        destroy: `{{ route('equipmentproduction.destroy', ['id' => '__ID__']) }}`
                    },
                    plate: {
                        destroy: `{{ route('plateproduction.destroy', ['id' => '__ID__']) }}`
                    }
                };

                const csrf = document.querySelector('input[name="_token"]').value;

                // ---------- DELETE PLATE ----------
                document.querySelectorAll('.delete-plate-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.id;
                        const url = routes.plate.destroy.replace('__ID__', id);

                        if (!confirm('Tem certeza que deseja excluir esta placa?')) return;

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-HTTP-Method-Override': 'DELETE',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            alert('Placa excluída com sucesso');
                            location.reload();
                        } else {
                            const data = await res.json();
                            alert(data.message || 'Erro ao excluir placa');
                        }
                    });
                });
                // ---------- BATERIA ----------
                // Função para garantir formato YYYY-MM-DD para input date
                function formatDateForInput(dateString) {
                    if (!dateString) return '';
                    return dateString.split('T')[0]; // Pega só o "YYYY-MM-DD"

                }


                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.id;
                        const url = routes.battery.edit.replace('__ID__', id);

                        const res = await fetch(url);
                        if (!res.ok) return alert("Erro ao buscar bateria");
                        const bp = await res.json();

                        document.getElementById('edit_id').value = bp.id;
                        document.getElementById('edit_battery_id').value = bp.battery_id;
                        document.getElementById('edit_info').value = bp.info;
                        document.getElementById('edit_amount').value = bp.amount ?? '';
                        document.getElementById('edit_installation_date').value =
                            formatDateForInput(bp.installation_date);
                        document.getElementById('edit_removal_date').value = formatDateForInput(bp
                            .removal_date);
                        document.getElementById('edit_active').value = bp.active;

                        document.getElementById('editModal').style.display = 'block';
                    });
                });


                const batteryForm = document.getElementById('editForm');
                if (batteryForm) {
                    batteryForm.addEventListener('submit', async e => {
                        e.preventDefault();
                        const id = document.getElementById('edit_id').value;
                        const url = routes.battery.update.replace('__ID__', id);
                        const formData = new FormData(batteryForm);

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-HTTP-Method-Override': 'PUT',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (res.ok) {
                            alert('Bateria atualizada com sucesso');
                            location.reload();
                        } else {
                            const data = await res.json();
                            alert(data.message || 'Erro ao atualizar bateria');
                        }
                    });

                    const deleteButton = document.getElementById('deleteButton');
                    if (deleteButton) {
                        deleteButton.addEventListener('click', async () => {
                            if (!confirm('Tem certeza que deseja excluir esta bateria?')) return;
                            const id = document.getElementById('edit_id').value;
                            const url = routes.battery.destroy.replace('__ID__', id);

                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-HTTP-Method-Override': 'DELETE',
                                    'Accept': 'application/json'
                                }
                            });

                            if (res.ok) {
                                alert('Bateria excluída com sucesso');
                                location.reload();
                            } else {
                                const data = await res.json();
                                alert(data.message || 'Erro ao excluir bateria');
                            }
                        });
                    }
                }

                // ---------- EQUIPAMENTO ----------
                document.querySelectorAll('.edit-equipment-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.id;
                        const url = routes.equipment.edit.replace('__ID__', id);

                        const res = await fetch(url);
                        if (!res.ok) return alert('Erro ao buscar equipamento');
                        const ep = await res.json();

                        document.getElementById('edit_equipment_id_hidden').value = ep.id;
                        document.getElementById('edit_equipment_id').value = ep.equipment_id;
                        document.getElementById('edit_identification').value = ep.identification;
                        document.getElementById('edit_equipment_active').value = ep.active;

                        document.getElementById('editEquipmentModal').style.display = 'block';
                    });
                });

                const equipmentForm = document.getElementById('editEquipmentForm');
                if (equipmentForm) {
                    equipmentForm.addEventListener('submit', async e => {
                        e.preventDefault();
                        const id = document.getElementById('edit_equipment_id_hidden').value;
                        const url = routes.equipment.update.replace('__ID__', id);
                        const formData = new FormData(equipmentForm);

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-HTTP-Method-Override': 'PUT',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (res.ok) {
                            alert('Equipamento atualizado com sucesso');
                            location.reload();
                        } else {
                            const data = await res.json();
                            alert(data.message || 'Erro ao atualizar equipamento');
                        }
                    });

                    const deleteEquipmentBtn = document.getElementById('deleteEquipmentBtn');
                    if (deleteEquipmentBtn) {
                        deleteEquipmentBtn.addEventListener('click', async () => {
                            if (!confirm('Deseja realmente excluir este equipamento?')) return;
                            const id = document.getElementById('edit_equipment_id_hidden').value;
                            const url = routes.equipment.destroy.replace('__ID__', id);

                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'X-HTTP-Method-Override': 'DELETE',
                                    'Accept': 'application/json'
                                }
                            });

                            if (res.ok) {
                                alert('Equipamento excluído com sucesso');
                                location.reload();
                            } else {
                                const data = await res.json();
                                alert(data.message || 'Erro ao excluir equipamento');
                            }
                        });
                    }
                }




            });
        </script>
    @endpush


@endsection
