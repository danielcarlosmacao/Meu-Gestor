@extends('layouts.header')
@section('title', 'FTTH - BOX')

@section('content')

    @php
        $fiberColors = [
            1 => '#00a651',
            2 => '#ffff00',
            3 => '#ffffff',
            4 => '#0000ff',
            5 => '#ff0000',
            6 => '#8A2BE2',
            7 => '#8b4513',
            8 => '#9400d3',
            9 => '#00ffff',
            10 => '#ffa500',
            11 => '#ffc0cb',
            12 => '#808080',
        ];

        function getFiberNumber($name)
        {
            if (preg_match('/(\d+)$/', $name, $matches)) {
                return (int) $matches[1];
            }
            return null;
        }

        function getTextColor($bg)
        {
            return in_array($bg, ['#000000', '#0000ff', '#8b4513', '#9400d3']) ? '#fff' : '#000';
        }
    @endphp

    <style>
        .fiber-row:hover {
            filter: brightness(0.95);
        }
    </style>

    <div class="container mb-1 mb-md-4 mt-1 mt-md-4">
        <h2 class="text-center">
            {{ $box->info }}
            <a href="{{ route('fiberbox.index', ['pon' => $box->pon_id]) }}" class="btn dcm-btn-primary">
                <i class="bi bi-house"></i>
            </a>
        </h2>

    </div>
    <div class="container-fluid">

        <div class="row">

            {{-- ESQUERDA --}}
            <div class="col-md-3">

                {{-- Cabos --}}

                <div class="card mb-3 shadow-sm border-0">

                    <div class="card-header d-flex justify-content-between align-items-center bgc-primary">
                        <div class="fw-bold">
                            Cabos
                        </div>
                        @can('ftth.create')
                            <button class="btn btn-sm dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCable">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan
                    </div>

                    <div class="card-body p-0">

                        <table class="table table-hover table-sm align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Descrição</th>
                                    <th>Conexão</th>
                                    <th width="60" class="text-end"></th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($cables as $cable)
                                    <tr style="border-left: 4px solid {{ $cable->color }}">

                                        {{-- INFO --}}
                                        <td class="fw-semibold">
                                            <span style="color: {{ $cable->color }}">
                                                .
                                            </span>
                                            {{ $cable->info }}
                                        </td>

                                        {{-- CONEXÃO --}}
                                        <td>
                                            @if ($cable->input_fiber_box_id == $box->id)
                                                <span class="badge bg-secondary">
                                                    {{ $cable->outputFiberBox->info ?? '' }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ $cable->inputFiberBox->info ?? '' }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- AÇÕES --}}
                                        <td class="text-end">
                                            @can('ftth.delete')
                                                <form method="POST" action="{{ route('cable.destroy', $cable->id) }}"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este cabo?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Nenhum cabo cadastrado
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>



            </div>

            {{-- FIBRAS --}}
            <div class="col-md-5">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bgc-primary">
                        Bandeja de fibra
                        <button class="btn btn-sm dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalFiber">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>

                    <div class="card-body p-0">

                        <table class="table table-sm table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Fibra</th>
                                    <th>Fusão</th>
                                    <th>Status</th>
                                    <th>Sinal</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($fibers as $fiber)
                                    @php
                                        $num = getFiberNumber($fiber->fiber_identification);
                                        $bg = $fiberColors[$num] ?? '#f8f9fa';
                                        $text = getTextColor($bg);
                                    @endphp

                                    <tr class="fiber-row">

                                        {{-- FIBRA --}}
                                        <td
                                            style="background:{{ $bg }}; color:{{ $text }}; font-weight:600;">
                                            {{ $fiber->fiber_identification }}
                                        </td>

                                        {{-- FUSÃO --}}
                                        <td style="padding:0;">

                                            {{-- SPLITTER --}}
                                            @if ($fiber->splinter)
                                                <div class="p-2 border-bottom small">
                                                    <i class="bi bi-diagram-3"></i>
                                                    {{ $fiber->splinter->name }}
                                                    ({{ $fiber->splinter->loss->type }})
                                                </div>
                                            @endif

                                            {{-- FUSÕES --}}
                                            @foreach ($fiber->fusions1 as $fusion)
                                                @php
                                                    $numFusion = getFiberNumber($fusion->fiber2->fiber_identification);
                                                    $bgFusion = $fiberColors[$numFusion] ?? '#f8f9fa';
                                                    $textFusion = getTextColor($bgFusion);
                                                @endphp

                                                <div class="d-flex align-items-center justify-content-between"
                                                    style="
                                                            background: {{ $bgFusion }};
                                                            color: {{ $textFusion }};
                                                            width: 100%;
                                                            padding: 8px 10px;
                                                            border-left: 4px solid rgba(0,0,0,0.15);
                                                        ">

                                                    {{ $fusion->fiber2->fiber_identification }}

                                                </div>
                                            @endforeach

                                        </td>

                                        {{-- STATUS --}}
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ __('status.' . $fiber->status) }}
                                            </span>
                                        </td>

                                        {{-- SINAL --}}
                                        <td>
                                            {{ $fiber->optical_power }} dBm
                                        </td>

                                        {{-- AÇÕES --}}
                                        <td class="text-end">
                                            @if ($fiber->status == 'unused')
                                                <form method="POST" action="{{ route('fiber.destroy', $fiber->id) }}">
                                                    @csrf @method('DELETE')

                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Tem certeza que deseja excluir esta fibra?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>
                </div>

            </div>


            <div class="col-md-4">


                {{-- Splineters --}}
                <div class="card mb-3 shadow-sm border-0">

                    <div class="card-header d-flex justify-content-between align-items-center bgc-primary">
                        <div class="fw-bold">
                            Splinters
                        </div>
                        @can('ftth.create')
                            <button class="btn btn-sm dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalSplinter">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan
                    </div>

                    <div class="card-body p-0">

                        <table class="table table-hover table-sm align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Fibra</th>
                                    <th>Splinter</th>
                                    <th>Sinal</th>
                                    <th width="60" class="text-end"></th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($splinters as $spl)
                                    <tr>

                                        {{-- NOME --}}
                                        <td class="fw-semibold">
                                            {{ $spl->name }}
                                        </td>

                                        {{-- TIPO --}}
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ __('fiber.' . $spl->type) }}
                                            </span>
                                        </td>

                                        {{-- FIBRA --}}
                                        <td>
                                            <small class="text-muted">
                                                {{ $spl->inputCable->fiber_identification ?? '-' }}
                                            </small>
                                        </td>

                                        {{-- LOSS --}}
                                        <td>
                                            <span class="badge bg-dark">
                                                {{ $spl->loss->type }}
                                            </span>
                                        </td>

                                        {{-- SINAL --}}
                                        <td>
                                            @php
                                                $power = $spl->inputCable->optical_power ?? 0;
                                            @endphp

                                            @if ($spl->loss->splinter_type == 'balanced')
                                                {{ $power - $spl->loss->loss1 }} dBm
                                            @else
                                                {{ $power - $spl->loss->loss1 }} dBm
                                                {{ $power - $spl->loss->loss2 }} dBm
                                            @endif
                                        </td>

                                        {{-- AÇÕES --}}
                                        <td class="text-end">
                                            @can('ftth.delete')
                                                <form method="POST" action="{{ route('splinter.destroy', $spl->id) }}"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este splinter?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </form>
                                            @endcan

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Nenhum splinter cadastrado
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>

                {{-- FUSÕES --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bgc-primary">
                        Fusões
                        @can('ftth.create')
                            <button class="btn btn-sm dcm-btn-primary" data-bs-toggle="modal" data-bs-target="#modalFusion">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan
                    </div>

                    <div class="card-body p-0">

                        <table class="table table-sm table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Fibra 1</th>
                                    <th>Fibra 2</th>
                                    <th class="text-end" style="width: 90px;"></th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($fusions as $fusion)
                                    @php
                                        $num1 = getFiberNumber($fusion->fiber1->fiber_identification);
                                        $bg1 = $fiberColors[$num1] ?? '#f8f9fa';
                                        $text1 = getTextColor($bg1);

                                        $num2 = getFiberNumber($fusion->fiber2->fiber_identification);
                                        $bg2 = $fiberColors[$num2] ?? '#f8f9fa';
                                        $text2 = getTextColor($bg2);
                                    @endphp

                                    <tr>

                                        {{-- FIBRA 1 --}}
                                        <td
                                            style="background:{{ $bg1 }}; color:{{ $text1 }}; font-weight:600;">
                                            {{ $fusion->fiber1->fiber_identification }}
                                        </td>

                                        {{-- FIBRA 2 --}}
                                        <td
                                            style="background:{{ $bg2 }}; color:{{ $text2 }}; font-weight:600;">
                                            {{ $fusion->fiber2->fiber_identification }}
                                        </td>

                                        {{-- AÇÕES --}}
                                        <td class="text-end">

                                            <div class="d-inline-flex align-items-center gap-2">

                                                {{-- INFO (OLHO) --}}
                                                @if ($fusion->info)
                                                    <i class="bi bi-eye-fill text-primary"
                                                        style="cursor:pointer; font-size: 1.1rem;" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ $fusion->info }}">
                                                    </i>
                                                @endif

                                                @can('ftth.delete')
                                                    {{-- DELETE --}}
                                                    <form method="POST" action="{{ route('fusion.destroy', $fusion->id) }}"
                                                        class="m-0">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button class="btn btn-sm btn-outline-danger p-1 px-2"
                                                            onclick="return confirm('Tem certeza que deseja excluir esta fusão?')">

                                                            <i class="bi bi-trash"></i>

                                                        </button>

                                                    </form>
                                                @endcan

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Nenhuma fusão cadastrada
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- MODAIS --}}
    @include('ftth.modals.cable')
    @include('ftth.modals.fiber')
    @include('ftth.modals.splinter')
    @include('ftth.modals.fusion')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let cableSelect = document.getElementById("cable_select");
            let container = document.getElementById("fibers_container");

            let fibersAll = @json($allFibers);
            let currentBoxId = {{ $box->id }};

            if (cableSelect && container) {

                cableSelect.addEventListener("change", function() {

                    let selected = this.options[this.selectedIndex];

                    let fibers = parseInt(selected?.dataset?.fibers || 0);
                    let info = selected?.dataset?.info || 'CABO';

                    container.innerHTML = "";

                    if (!fibers) return;

                    for (let i = 1; i <= fibers; i++) {

                        let fiberName = info + "-F-" + String(i).padStart(2, '0');

                        // NÃO DUPLICAR NA BOX
                        let exists = fibersAll.find(f =>
                            f.fiber_identification === fiberName &&
                            f.fiber_box_id == currentBoxId &&
                            f.deleted_at === null
                        );

                        if (exists) continue;

                        // BUSCA SINAL EM OUTRA BOX
                        let mirror = fibersAll.find(f =>
                            f.fiber_identification === fiberName &&
                            f.fiber_box_id != currentBoxId &&
                            f.optical_power !== null
                        );

                        let power = mirror ? mirror.optical_power : '';

                        container.innerHTML += `
    <div class="row mb-2 align-items-center fiber-item">

        <div class="col-md-5">
            <input type="hidden" name="fibers[${i}][fiber_identification]" value="${fiberName}">
            <input class="form-control shadow-sm" value="${fiberName}" disabled>
        </div>

        <div class="col-md-5">
            <input type="number" step="0.01"
                name="fibers[${i}][optical_power]"
                class="form-control shadow-sm"
                value="${power}">
        </div>
        
<div class="col-md-1 d-flex align-items-center p-0">
<button type="button" 
        class="btn btn-sm btn-outline-danger remove-fiber"
        style="padding: 4px 8px;">
    <i class="bi bi-dash-lg"></i>
</button>
        </div>

    </div>
`;
                    }

                });

            }

        });

        document.addEventListener('click', function(e) {

            if (e.target.closest('.remove-fiber')) {
                let row = e.target.closest('.fiber-item');
                if (row) row.remove();
            }

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let fiberColors = @json($fiberColors);

            function getFiberNumber(name) {
                if (!name) return null;
                let match = name.match(/(\d+)$/);
                return match ? parseInt(match[1]) : null;
            }

            function paintSelect(selectEl) {

                if (!selectEl || selectEl.selectedIndex < 0) return;

                let option = selectEl.options[selectEl.selectedIndex];
                if (!option) return;

                let text = option.text;

                let num = getFiberNumber(text);
                let color = fiberColors[num] || '#ffffff';

                selectEl.style.backgroundColor = color;
                selectEl.style.color =
                    (color === '#ffffff' || color === '#ffff00') ? '#000' : '#fff';
            }

            let fiber1 = document.getElementById('fiber1');
            let fiber2 = document.getElementById('fiber2');

            // -----------------------------
            // FIBER 1
            // -----------------------------
            if (fiber1 && fiber2) {

                fiber1.addEventListener('change', function() {

                    let selected = this.value;

                    Array.from(fiber2.options).forEach(option => {
                        option.disabled = (option.value === selected);
                    });

                    paintSelect(this);
                });

                paintSelect(fiber1);
            }

            // -----------------------------
            // FIBER 2
            // -----------------------------
            if (fiber2) {

                fiber2.addEventListener('change', function() {
                    paintSelect(this);
                });

                paintSelect(fiber2);
            }

            // -----------------------------
            // TOOLTIP BOOTSTRAP
            // -----------------------------
            if (typeof bootstrap !== 'undefined') {

                let tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );

                tooltipTriggerList.map(function(el) {
                    return new bootstrap.Tooltip(el, {
                        boundary: 'window'
                    });
                });
            }

        });



        const colorPicker = document.getElementById('colorPicker');
        const colorHex = document.getElementById('colorHex');

        // Quando escolher no seletor
        colorPicker.addEventListener('input', () => {
            colorHex.value = colorPicker.value;
        });

        // Quando digitar manualmente
        colorHex.addEventListener('input', () => {
            if (/^#([0-9A-F]{3}){1,2}$/i.test(colorHex.value)) {
                colorPicker.value = colorHex.value;
            }
        });

        // Valor inicial sincronizado
        colorHex.value = colorPicker.value;
    </script>
@endsection
