<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Relatório de Movimentações</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2, h4 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #333; padding: 6px; vertical-align: top; }
        table th { background: #eee; }
        .right { text-align: right; }
        .badge { padding: 2px 5px; border-radius: 4px; color: #fff; font-size: 11px; }
        .entrada { background: #198754; }
        .saida { background: #dc3545; }
        .semvalor { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>
    Relatório de Movimentações de Estoque<br>
    <small>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
</h2>

@if($type !== 'all')
<p><strong>Tipo:</strong> {{ $type === 'input' ? 'Entradas' : 'Saídas' }}</p>
@endif

</br>

{{-- ======================================================
    RESUMO POR ITEM
====================================================== --}}
<h4 style="text-align: center;">Resumo por Item</h4>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th class="right">Quantidade</th>
            <th class="right">Valor Total (R$)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td class="right">{{ $item['total_qty'] }}</td>
            <td class="right {{ $item['total_value'] == 0 ? 'semvalor' : '' }}">
                {{ number_format($item['total_value'], 2, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4>Total Geral: R$ {{ number_format($grandTotal, 2, ',', '.') }}</h4>

</br>

{{-- ======================================================
    RESUMO DE TODAS AS MOVIMENTAÇÕES
====================================================== --}}
<h4 style="text-align: center;">Resumo de Todas as Movimentações</h4>

<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Descrição</th>
            <th>Itens Extras</th>
            <th>Itens Movimentados</th>
            <th class="right">Quantidade Total</th>
            <th class="right">Valor Total (R$)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movements as $mov)
            @php
                $totalQtd = 0;
                $totalVal = 0;
                foreach($mov->items as $i) {
                    $totalQtd += $i->pivot->quantity;
                    $totalVal += $i->pivot->quantity * $i->pivot->price;
                }
            @endphp
            <tr>
                <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge {{ $mov->type === 'input' ? 'entrada' : 'saida' }}">
                        {{ $mov->type === 'input' ? 'Entrada' : 'Saída' }}
                    </span>
                </td>
                <td>{{ $mov->description ?? '-' }}</td>
                <td>{{ $mov->extra_items ?? '-' }}</td>
                <td>
                    @foreach($mov->items as $i)
                        • {{ $i->name }} ({{ $i->pivot->quantity }} un, 
                        <span class="{{ $i->pivot->price == 0 ? 'semvalor' : '' }}">
                            R$ {{ number_format($i->pivot->price, 2, ',', '.') }}
                        </span>)<br>
                    @endforeach
                </td>
                <td class="right">{{ $totalQtd }}</td>
                <td class="right {{ $totalVal == 0 ? 'semvalor' : '' }}">
                    {{ number_format($totalVal, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</br>

{{-- ======================================================
    DETALHAMENTO DAS MOVIMENTAÇÕES
====================================================== --}}
<h4 style="text-align: center;">Detalhamento das Movimentações</h4>

<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Item</th>
            <th>Qtd</th>
            <th>Valor (R$)</th>
            <th>Total (R$)</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movements as $mov)
            @foreach($mov->items as $item)
                @php
                    $total = $item->pivot->quantity * $item->pivot->price;
                @endphp
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $mov->type === 'input' ? 'entrada' : 'saida' }}">
                            {{ $mov->type === 'input' ? 'Entrada' : 'Saída' }}
                        </span>
                    </td>
                    <td>{{ $item->name }}</td>
                    <td class="right">{{ $item->pivot->quantity }}</td>
                    <td class="right {{ $item->pivot->price == 0 ? 'semvalor' : '' }}">
                        {{ number_format($item->pivot->price, 2, ',', '.') }}
                    </td>
                    <td class="right {{ $total == 0 ? 'semvalor' : '' }}">
                        {{ number_format($total, 2, ',', '.') }}
                    </td>
                    <td>{{ $mov->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<hr>
<p><small>Relatório gerado em {{ date('d/m/Y H:i') }}</small></p>

</body>
</html>
