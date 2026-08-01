<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Relatório de Manutenções</title>

    <style>
        @page {
            margin: 28px 24px 35px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #263238;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        /* ============================================================
           CABEÇALHO
        ============================================================ */

        .report-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border-bottom: 2px solid #34495e;
        }

        .report-header td {
            padding-bottom: 12px;
            vertical-align: middle;
        }

        .report-title {
            margin: 0;
            color: #263238;
            font-size: 19px;
            font-weight: bold;
        }

        .report-period {
            margin-top: 4px;
            color: #607d8b;
            font-size: 10px;
        }

        .report-date {
            width: 180px;
            color: #607d8b;
            font-size: 9px;
            text-align: right;
        }

        /* ============================================================
           TÍTULOS DAS SEÇÕES
        ============================================================ */

        .section-title {
            margin: 20px 0 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #cfd8dc;
            color: #37474f;
            font-size: 13px;
            font-weight: bold;
        }

        /* ============================================================
           RESUMO GERAL
        ============================================================ */

        .summary-table {
            width: 100%;
            margin-bottom: 18px;
            border-spacing: 7px;
            border-collapse: separate;
        }

        .summary-table td {
            width: 25%;
            padding: 11px;
            border: 1px solid #cfd8dc;
            border-radius: 5px;
            background-color: #f8fafb;
            vertical-align: top;
        }

        .summary-label {
            display: block;
            margin-bottom: 4px;
            color: #78909c;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .summary-value {
            color: #263238;
            font-size: 14px;
            font-weight: bold;
        }

        /* ============================================================
           CARDS DOS VEÍCULOS
        ============================================================ */

        .vehicles-table {
            width: 100%;
            border-spacing: 7px;
            border-collapse: separate;
            margin-bottom: 18px;
        }

        .vehicles-table td {
            width: 33.333%;
            padding: 10px;
            border: 1px solid #cfd8dc;
            border-radius: 5px;
            background-color: #ffffff;
            vertical-align: top;
        }

        .vehicle-card-title {
            padding-bottom: 6px;
            margin-bottom: 7px;
            border-bottom: 1px solid #eceff1;
            color: #263238;
            font-size: 11px;
            font-weight: bold;
        }

        .vehicle-color {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 5px;
            border: 1px solid #90a4ae;
            border-radius: 2px;
            vertical-align: middle;
        }

        .vehicle-plate {
            display: inline-block;
            margin-top: 3px;
            padding: 2px 5px;
            border: 1px solid #b0bec5;
            border-radius: 3px;
            color: #455a64;
            background-color: #eceff1;
            font-size: 8px;
            font-weight: bold;
        }

        .vehicle-card-info {
            width: 100%;
            margin-top: 3px;
            border-collapse: collapse;
        }

        .vehicle-card-info td {
            width: auto;
            padding: 2px 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            font-size: 9px;
        }

        .vehicle-card-info .label {
            color: #607d8b;
        }

        .vehicle-card-info .value {
            color: #263238;
            font-weight: bold;
            text-align: right;
        }

        /* ============================================================
           TABELA DE DETALHAMENTO
        ============================================================ */

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table th {
            padding: 6px 4px;
            border: 1px solid #78909c;
            background-color: #455a64;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
        }

        .data-table td {
            padding: 6px 4px;
            border: 1px solid #cfd8dc;
            color: #37474f;
            font-size: 8px;
            vertical-align: top;
            overflow-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .vehicle-name {
            font-weight: bold;
        }

        .vehicle-year {
            color: #78909c;
            font-size: 7px;
        }

        /* ============================================================
           BADGES
        ============================================================ */

        .badge {
            display: inline-block;
            margin: 1px 1px 1px 0;
            padding: 2px 4px;
            border-radius: 3px;
            background-color: #607d8b;
            color: #ffffff;
            font-size: 7px;
        }

        .badge-success {
            background-color: #2e7d32;
        }

        .badge-warning {
            background-color: #ef6c00;
        }

        .badge-danger {
            background-color: #c62828;
        }

        .badge-info {
            background-color: #0277bd;
        }

        .badge-secondary {
            background-color: #607d8b;
        }

        /* ============================================================
           MENSAGENS
        ============================================================ */

        .empty-state {
            padding: 20px;
            border: 1px solid #cfd8dc;
            background-color: #f8fafb;
            color: #607d8b;
            text-align: center;
        }

        /* ============================================================
           RODAPÉ
        ============================================================ */

        .report-footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #cfd8dc;
            color: #78909c;
            font-size: 8px;
            text-align: center;
        }
    </style>

</head>

<body>

    @php
        $allCost = $maintenances->sum('cost');
        $totalMaintenances = $maintenances->count();

        $vehiclesWithMaintenance = $vehicles->filter(function ($vehicle) use ($maintenances) {
            return $maintenances->where('vehicle_id', $vehicle->id)->isNotEmpty();
        });

        $totalVehicles = $vehiclesWithMaintenance->count();

        $averageCost = $totalMaintenances > 0 ? $allCost / $totalMaintenances : 0;
    @endphp

    {{-- ============================================================
        CABEÇALHO
    ============================================================= --}}

    <table class="report-header">

        <tr>

            <td>

                <h1 class="report-title">
                    Relatório de Manutenções
                </h1>

                <div class="report-period">

                    Período:

                    <strong>
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                    </strong>

                    até

                    <strong>
                        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </strong>

                </div>

            </td>

            <td class="report-date">

                Gerado em:

                <strong>
                    {{ now()->format('d/m/Y H:i') }}
                </strong>

            </td>

        </tr>

    </table>

    {{-- ============================================================
        RESUMO GERAL
    ============================================================= --}}

    <div class="section-title">
        Resumo geral
    </div>

    <table class="summary-table">

        <tr>

            <td>

                <span class="summary-label">
                    Manutenções
                </span>

                <span class="summary-value">
                    {{ $totalMaintenances }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Veículos atendidos
                </span>

                <span class="summary-value">
                    {{ $totalVehicles }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Custo total
                </span>

                <span class="summary-value">
                    R$ {{ number_format($allCost, 2, ',', '.') }}
                </span>

            </td>

            <td>

                <span class="summary-label">
                    Custo médio
                </span>

                <span class="summary-value">
                    R$ {{ number_format($averageCost, 2, ',', '.') }}
                </span>

            </td>

        </tr>

    </table>

    {{-- ============================================================
        RESUMO POR VEÍCULO
    ============================================================= --}}

    <div class="section-title">
        Resumo por veículo
    </div>

    @if ($vehiclesWithMaintenance->count())

        <table class="vehicles-table">

            <tr>

                @php
                    $vehicleCardCount = 0;
                @endphp

                @foreach ($vehiclesWithMaintenance as $vehicle)
                    @php
                        $vehicleMaintenances = $maintenances->where('vehicle_id', $vehicle->id);

                        $vehicleMaintenanceCount = $vehicleMaintenances->count();

                        $vehicleTotalCost = $vehicleMaintenances->sum('cost');

                        $lastMileage = $maxMileages[$vehicle->id] ?? null;

                        $vehicleKmWheeled = $kmWheeled[$vehicle->id] ?? null;
                    @endphp

                    <td>

                        <div class="vehicle-card-title">

                            <span class="vehicle-color"
                                style="background-color:
                                    {{ $vehicle->color ?: '#dee2e6' }};">
                            </span>

                            {{ $vehicle->model ?? 'Sem modelo' }}

                            @if ($vehicle->year)
                                {{ $vehicle->year }}
                            @endif

                            <br>

                            @if ($vehicle->license_plate)
                                <span class="vehicle-plate">
                                    {{ strtoupper($vehicle->license_plate) }}
                                </span>
                            @endif

                        </div>

                        <table class="vehicle-card-info">

                            <tr>

                                <td class="label">
                                    Manutenções
                                </td>

                                <td class="value">
                                    {{ $vehicleMaintenanceCount }}
                                </td>

                            </tr>

                            <tr>

                                <td class="label">
                                    Última quilometragem
                                </td>

                                <td class="value">

                                    @if (is_numeric($lastMileage))
                                        {{ number_format($lastMileage, 0, ',', '.') }}
                                        km
                                    @else
                                        —
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <td class="label">
                                    Quilômetros rodados
                                </td>

                                <td class="value">

                                    @if (is_numeric($vehicleKmWheeled) && $vehicleKmWheeled > 0)
                                        {{ number_format($vehicleKmWheeled, 0, ',', '.') }}
                                        km
                                    @else
                                        —
                                    @endif

                                </td>

                            </tr>

                            <tr>

                                <td class="label">
                                    Custo total
                                </td>

                                <td class="value">
                                    R$
                                    {{ number_format($vehicleTotalCost, 2, ',', '.') }}
                                </td>

                            </tr>

                        </table>

                    </td>

                    @php
                        $vehicleCardCount++;
                    @endphp

                    @if ($vehicleCardCount % 3 === 0 && !$loop->last)
            </tr>
            <tr>
    @endif
    @endforeach

    @php
        $remainingColumns = $vehicleCardCount % 3;
    @endphp

    @if ($remainingColumns > 0)

        @for ($i = $remainingColumns; $i < 3; $i++)
            <td style="border: 0; background: transparent;"></td>
        @endfor

    @endif

    </tr>

    </table>
@else
    <div class="empty-state">
        Nenhum veículo possui manutenção no período selecionado.
    </div>

    @endif

    {{-- ============================================================
        DETALHAMENTO
    ============================================================= --}}

    <div class="section-title">
        Detalhamento das manutenções
    </div>

    @if ($maintenances->count())

        <table class="data-table">

            <thead>

                <tr>

                    <th style="width: 15%;">
                        Veículo
                    </th>

                    <th style="width: 9%;">
                        Data
                    </th>

                    <th style="width: 10%;">
                        Tipo
                    </th>

                    <th style="width: 11%;">
                        Quilometragem
                    </th>

                    <th style="width: 10%;">
                        Valor
                    </th>

                    <th style="width: 9%;">
                        Status
                    </th>

                    <th style="width: 15%;">
                        Oficina
                    </th>

                    <th style="width: 21%;">
                        Serviços
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($maintenances as $maintenance)
                    @php
                        $status = strtolower($maintenance->status ?? '');

                        $statusClass = match ($status) {
                            'completed', 'concluded', 'finished' => 'badge-success',

                            'pending', 'scheduled' => 'badge-warning',

                            'canceled', 'cancelled' => 'badge-danger',

                            default => 'badge-secondary',
                        };

                        $workshopName = is_object($maintenance->workshop)
                            ? $maintenance->workshop->name
                            : $maintenance->workshop;
                    @endphp

                    <tr>

                        <td>

                            <span class="vehicle-color"
                                style="background-color:
                                    {{ $maintenance->vehicle->color ?? '#dee2e6' }};">
                            </span>

                            <span class="vehicle-name">

                                {{ $maintenance->vehicle->model ?? '-' }}

                            </span>

                            @if ($maintenance->vehicle?->year)
                                <span class="vehicle-year">
                                    {{ $maintenance->vehicle->year }}
                                </span>
                            @endif

                            @if ($maintenance->vehicle?->license_plate)
                                <br>

                                <span class="vehicle-plate">
                                    {{ strtoupper($maintenance->vehicle->license_plate) }}
                                </span>
                            @endif

                        </td>

                        <td class="nowrap">

                            @if ($maintenance->maintenance_date)
                                {{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}
                            @else
                                —
                            @endif

                        </td>

                        <td>

                            {{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}

                        </td>

                        <td class="nowrap">

                            {{ number_format($maintenance->mileage ?? 0, 0, ',', '.') }}
                            km

                        </td>

                        <td class="nowrap">

                            R$
                            {{ number_format($maintenance->cost ?? 0, 2, ',', '.') }}

                        </td>

                        <td>

                            <span class="badge {{ $statusClass }}">

                                {{ __('status.' . $maintenance->status) }}

                            </span>

                        </td>

                        <td>

                            {{ $workshopName ?: 'Não informada' }}

                        </td>

                        <td>

                            @forelse ($maintenance->services as $service)
                                <span class="badge badge-secondary">
                                    {{ $service->name }}
                                </span>

                            @empty

                                —
                            @endforelse

                        </td>

                    </tr>
                @endforeach

            </tbody>

        </table>
    @else
        <div class="empty-state">
            Nenhuma manutenção encontrada no período selecionado.
        </div>

    @endif

    {{-- ============================================================
        RODAPÉ
    ============================================================= --}}

    <div class="report-footer">

        Relatório de manutenções de veículos

        ·

        Período de
        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
        até
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}

        ·

        Gerado em {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>
