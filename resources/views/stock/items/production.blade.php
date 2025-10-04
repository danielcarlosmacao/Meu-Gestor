@extends('layouts.header')
@section('title', 'Equipamentos em Produção e Estoque')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold">
                <i class="bi bi-cpu"></i> Equipamentos em Produção e Estoque
            </h2>
        </div>

        @if (!is_null($totalProductionValue))
            <div class="mb-3">
                <span class="badge bgc-primary fs-5">
                    Total em Produção: R$ {{ number_format($totalProductionValue, 2, ',', '.') }}
                </span>
            </div>
        @endif

        <table class="table table-bordered table-hover align-middle">
            <thead class="bgc-primary text-white">
                <tr>
                    <th>Equipamento</th>
                    <th>Watts</th>
                    <th>Produção Ativa</th>
                    <th>Estoque</th>
                    <th>Preço (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    @php
                        $price = is_numeric($item['price']) ? number_format((float) $item['price'], 2, ',', '.') : '-';
                    @endphp
                    <tr class="text-center">
                        <td class=" text-start fw-semibold">{{ $item['equipment_name'] }}</td>
                        <td>{{ $item['watts'] }} W</td>
                        <td>
                            @if ($item['in_production'] > 0)
                                <span class="badge bg-success">{{ $item['in_production'] }}</span>
                            @else
                                <span class="badge bg-secondary">0</span>
                            @endif
                        </td>
                        <td>
                            @if ($item['stock_qty'] > 0)
                                <span class="badge bg-info text-dark">{{ $item['stock_qty'] }}</span>
                            @elseif($item['status'] === 'not_found')
                                <span class="badge bg-secondary">Não Cadastrado</span>
                            @else
                                <span class="badge bg-warning text-dark">Sem Estoque</span>
                            @endif
                        </td>
                        <td>{{ $price !== '-' ? 'R$ ' . $price : '-' }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
