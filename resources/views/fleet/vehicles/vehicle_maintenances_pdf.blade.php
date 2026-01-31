<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Relatório de Manutenções</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2,
        h4 {
            margin: 0 0 10px 0;
        }

        .cards-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cards-table td {
            border: 1px solid #333;
            padding: 10px;
            vertical-align: top;
            width: 33%;
        }

        .card-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-info {
            margin: 2px 0;
            font-size: 12px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }

        table.data-table th {
            background-color: #ddd;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            background: #6c757d;
            color: white;
            border-radius: 4px;
            font-size: 10px;
            margin-right: 3px;
        }
    </style>
</head>

<body>

    <h2>
        Resumo de Manutenções -
        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
        até
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </h2>

    <table class="cards-table">
        <tr>
            @php
                $count = 0;
                $allCost = 0;
            @endphp
            @foreach ($vehicles as $vehicle)
                @php
                    $vehicleMaintenances = $maintenances->where('vehicle_id', $vehicle->id);
                    $totalMaintenances = $vehicleMaintenances->count();
                    $totalCost = $vehicleMaintenances->sum('cost');
                    $lastMileage = $maxMileages[$vehicle->id] ?? '-';
                    $kmWheeled = $kmWheeled[$vehicle->id] ?? '-';
                    $allCost = $allCost + $totalCost;
                
                @endphp

                @if ($totalMaintenances > 0)
                    <td>
                        <div class="card-title">{{ $vehicle->model }} {{ $vehicle->year }}</div>
                        <div class="card-info"><strong>Total Manutenções:</strong> {{ $totalMaintenances }}</div>
                        <div class="card-info"><strong>Última Km:</strong>
                            {{ is_numeric($lastMileage) ? number_format($lastMileage, 0, ',', '.') . ' km' : '-' }}
                        </div>
                        <div class="card-info"><strong>Km Rodado:</strong>
                        {{ is_numeric($kmWheeled) ? number_format($kmWheeled, 0, ',', '.') . ' km' : '-' }}
                        </div>
                        <div class="card-info"><strong>Valor Total:</strong> R$
                            {{ number_format($totalCost, 2, ',', '.') }}</div>
                    </td>

                    @php $count++; @endphp

                    @if ($count % 3 == 0)
        </tr>
        <tr>
            @endif
            @endif
            @endforeach

            {{-- Completar a linha se necessário --}}
            @for ($i = $count % 3; $i > 0 && $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
    </table>
    <h4>Custo total de R$ {{ $allCost }}</h4></br>

    <h4>Detalhamento das Manutenções</h4>

    <table class="data-table">
        <thead>
            <tr>
                <th>Veículo</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Quilometragem</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Oficina</th>
                <th>Serviços</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maintenances as $maintenance)
                <tr>
                    <td>
                        <div
                            style="width: 10px; height: 10px; background-color:{{ $maintenance->vehicle->color ?? '' }} ; border-radius: 1px;">
                        </div>
                        {{ $maintenance->vehicle->model ?? '-' }} {{ $maintenance->vehicle->year ?? '-' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst(__('typemaintenances.' . $maintenance->type)) }}</td>
                    <td>{{ number_format($maintenance->mileage, 0, ',', '.') }} km</td>
                    <td>R$ {{ number_format($maintenance->cost, 2, ',', '.') }}</td>
                    <td>{{ __('status.' . $maintenance->status) }}</td>
                    <td>{{ $maintenance->workshop }}</td>
                    <td>
                        @foreach ($maintenance->services as $service)
                            <span class="badge">{{ $service->name }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><small>Relatório gerado em {{ date('d/m/Y H:i') }}</small></p>

</body>

</html>
