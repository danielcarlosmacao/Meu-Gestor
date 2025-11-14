@extends('layouts.header')

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
<div class="container">
    <div class="container  table-responsive">
        <table id="towersTable" class="table table-bordered table-striped table-hover" style="cursor:pointer;">
            <thead class="bgc-primary text-white">
                <tr>
                    <th data-col="name">Nome</th>
                    <th data-col="voltage">Voltagem</th>
                    <th data-col="equipments">Equipamentos</th>
                    <th data-col="battery_percentage">Bateria</th>
                    <th data-col="battery_percentage">% Bateria</th>

                    <th data-col="battery_install_ord">Data Inst. Bateria</th>

                    <th data-col="production_ord">Tempo Produção</th>

                    <th data-col="total_watts_placa">Total Watts Placa</th>
                    <th data-col="plate_percentage">% Placa</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>
                @foreach($towerData as $t)
                    <tr>

                        {{-- Nome com link --}}
                        <td>
                            <a href="{{ route('tower.show', $t['id']) }}" class="text-decoration-none text-black">
                                {{ $t['name'] }}
                            </a>
                        </td>

                        <td>{{ $t['voltage'] }}</td>
                        <td>{{ $t['equipments'] }}</td>

                        <td>{{ $t['battery'] }}</td>
                        <td>{{ $t['battery_percentage'] }}%</td>

                        {{-- Data instalação: mostra d/m/Y mas ordena por Y-m-d --}}
                        <td data-value="{{ $t['battery_install_ord'] }}">
                            {{ $t['battery_install_date'] }}
                        </td>

                        <td data-value="{{ $t['production_ord'] }}">
                            {{ $t['production_time'] }}
                        </td>

                        <td>{{ $t['total_watts_placa'] . " W  - " . $t['total_amps_placa'] ." A "}}</td>
                        <td>{{ $t['plate_percentage'] }}%</td>
                        <td class="text-center align-middle p-1">
                            @can('towers.delete')
                                <form action="{{ route('tower.destroy', $t['id']) }}" method="POST"
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
    </div>

    {{-- Paginação --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $pagination->links() }}
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
 

{{-- ===============================================
    SCRIPT DE ORDENAÇÃO (FUNCIONAL E OTIMIZADO)
=============================================== --}}
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

        
document.addEventListener("DOMContentLoaded", () => {

    const table = document.getElementById("towersTable");
    const headers = table.querySelectorAll("th");

    headers.forEach((th, idx) => {
        th.addEventListener("click", () => {

            // aumenta paginate quando ordenar
            const url = new URL(window.location.href);
            url.searchParams.set("perPage", "100");
            window.history.replaceState({}, "", url);

            const currentDir = th.getAttribute("data-sort") || "desc";
            const newDir = currentDir === "asc" ? "desc" : "asc";

            headers.forEach(h => h.removeAttribute("data-sort"));
            th.setAttribute("data-sort", newDir);

            sortTable(idx, newDir);
        });
    });

    function parseValue(value) {
        if (typeof value !== 'string') value = String(value ?? '');
        value = value.trim();

        // Se vier do data-value numérico, apenas retorna
        const num = parseFloat(value);
        if (!isNaN(num)) return num;

        // Se for data YYYY-MM-DD
        if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            return new Date(value).getTime();
        }

        // remove % e vírgulas
        const normalized = value.replace('%', '').replace(',', '.');

        const n = parseFloat(normalized);
        if (!isNaN(n)) return n;

        return normalized.toLowerCase();
    }

    function sortTable(colIndex, direction) {
        const rows = Array.from(table.querySelector("tbody").rows);

        rows.sort((rowA, rowB) => {

            const A = parseValue(rowA.cells[colIndex].dataset.value ?? rowA.cells[colIndex].innerText);
            const B = parseValue(rowB.cells[colIndex].dataset.value ?? rowB.cells[colIndex].innerText);

            if (A < B) return direction === "asc" ? -1 : 1;
            if (A > B) return direction === "asc" ? 1 : -1;
            return 0;
        });

        const tbody = table.querySelector("tbody");
        rows.forEach(r => tbody.appendChild(r));
    }
});
</script>

@endsection
