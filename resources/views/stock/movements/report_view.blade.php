<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Relatório de Movimentações</title>
    <style>
        @page {
            margin: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #27313a;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 2px solid #263238;
        }

        .header h1 {
            margin: 0 0 5px;
            font-size: 18px;
        }

        .header p {
            margin: 0;
            color: #66717c;
        }

        .summary {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .summary td {
            width: 33.333%;
            padding: 9px;
            border: 1px solid #dde3e8;
            background: #f7f9fb;
        }

        .summary span {
            display: block;
            color: #6b747d;
            font-size: 8px;
            text-transform: uppercase;
        }

        .summary strong {
            display: block;
            margin-top: 3px;
            font-size: 13px;
        }

        h2 {
            margin: 18px 0 7px;
            font-size: 13px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.data th,
        table.data td {
            padding: 5px 6px;
            border: 1px solid #d8dde2;
            vertical-align: top;
        }

        table.data th {
            color: #fff;
            background: #394650;
            font-size: 8px;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background: #f8fafb;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            color: #fff;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }

        .entrada {
            background: #198754;
        }

        .saida {
            background: #dc3545;
        }

        .movimento {
            background: #0d6efd;
        }

        .semvalor {
            color: #b42318;
            font-weight: bold;
        }

        .footer {
            margin-top: 14px;
            padding-top: 7px;
            border-top: 1px solid #d8dde2;
            color: #6b747d;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
        $movementCount = $movements->count();
        $totalQuantity = collect($summary)->sum('total_qty');
    @endphp

    <div class="header">
        <h1>Relatório de Movimentações de Estoque</h1>
        <p>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até
            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @if ($type !== 'all')
                — {{ $type === 'input' ? 'Entradas' : 'Saídas' }}
            @endif
        </p>
    </div>

    <table class="summary">
        <tr>
            <td><span>Movimentações</span><strong>{{ $movementCount }}</strong></td>
            <td><span>Quantidade total</span><strong>{{ $totalQuantity }}</strong></td>
            <td><span>Valor geral</span><strong>R$ {{ number_format($grandTotal, 2, ',', '.') }}</strong></td>
        </tr>
    </table>

    <h2>Resumo por item</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Quantidade</th>
                <th class="right">Valor total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="right">{{ $item['total_qty'] }}</td>
                    <td class="right {{ $item['total_value'] == 0 ? 'semvalor' : '' }}">R$
                        {{ number_format($item['total_value'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Nenhum item encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Resumo das movimentações</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Itens extras</th>
                <th>Itens movimentados</th>
                <th class="right">Qtd.</th>
                <th class="right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $mov)
                @php
                    $totalQtd = 0;
                    $totalVal = 0;
                    foreach ($mov->items as $i) {
                        $totalQtd += $i->pivot->quantity;
                        $totalVal += $i->pivot->quantity * $i->pivot->price;
                    }
                    $badgeClass =
                        $mov->type === 'input' ? 'entrada' : ($mov->type === 'movement' ? 'movimento' : 'saida');
                    $typeName =
                        $mov->type === 'input' ? 'Entrada' : ($mov->type === 'movement' ? 'Movimento' : 'Saída');
                @endphp
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $typeName }}</span></td>
                    <td>{{ $mov->description ?? '-' }}</td>
                    <td>{{ $mov->extra_items ?? '-' }}</td>
                    <td>
                        @foreach ($mov->items as $i)
                            • {{ $i->name }} ({{ $i->pivot->quantity }} un.; R$
                            {{ number_format($i->pivot->price, 2, ',', '.') }})<br>
                        @endforeach
                    </td>
                    <td class="right">{{ $totalQtd }}</td>
                    <td class="right {{ $totalVal == 0 ? 'semvalor' : '' }}">R$
                        {{ number_format($totalVal, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Nenhuma movimentação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Detalhamento</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Item</th>
                <th class="right">Qtd.</th>
                <th class="right">Valor</th>
                <th class="right">Total</th>
                <th>Usuário</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $mov)
                @foreach ($mov->items as $item)
                    @php
                        $total = $item->pivot->quantity * $item->pivot->price;
                        $badgeClass =
                            $mov->type === 'input' ? 'entrada' : ($mov->type === 'movement' ? 'movimento' : 'saida');
                        $typeName =
                            $mov->type === 'input' ? 'Entrada' : ($mov->type === 'movement' ? 'Movimento' : 'Saída');
                    @endphp
                    <tr>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ $typeName }}</span></td>
                        <td>{{ $item->name }}</td>
                        <td class="right">{{ $item->pivot->quantity }}</td>
                        <td class="right {{ $item->pivot->price == 0 ? 'semvalor' : '' }}">R$
                            {{ number_format($item->pivot->price, 2, ',', '.') }}</td>
                        <td class="right {{ $total == 0 ? 'semvalor' : '' }}">R$
                            {{ number_format($total, 2, ',', '.') }}</td>
                        <td>{{ $mov->user->name ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="center">Nenhum detalhe encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Relatório gerado em {{ date('d/m/Y H:i') }}</div>
</body>

</html>
