@extends('layouts.header')
@section('title', 'Itens de Estoque')

@section('content')
    <div class="container mt-4">


        <div class="container mt-4" id="print-area">
            <div class="container mb-2 mb-md-5 mt-2 mt-md-5">
                <h2 class="text-center">Itens de Estoque
                    @can('stock.items.create')
                        <a href="{{ route('stock.items.create') }}" class="btn dcm-btn-primary mb-3"><i class="bi bi-plus"></i></a>
                    @endcan
                </h2>
            </div>
            @if (!is_null($totalStockValue))
                <div class="mb-3">
                    <span class="badge bgc-primary fs-5">
                        Total do Estoque: R$ {{ number_format($totalStockValue, 2, ',', '.') }}
                    </span>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead class="bgc-primary text-white">
                    <tr>
                        <th>Nome</th>
                        <th>Estoque Atual</th>
                        <th>Estoque Mínimo</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->current_stock }}</td>
                            <td>{{ $item->min_stock ?? '-' }}</td>
                            <td>{{ $item->price ? 'R$ ' . number_format($item->price, 2, ',', '.') : '-' }}</td>
                            <td>{{ __('status.' . $item->status) }}</td>
                            <td class="text-center align-middle p-1">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm dcm-btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i> Ações
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('stock.items.show', $item->id) }}" class="dropdown-item">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                        </li>
                                        <li>
                                            @can('stock.items.edit')
                                                <a href="{{ route('stock.items.edit', $item->id) }}" class="dropdown-item">
                                                    <i class="bi bi-pencil-square"></i> Editar
                                                </a>
                                            @endcan
                                        </li>
                                        <li>
                                            @can('stock.items.delete')
                                                <form action="{{ route('stock.items.destroy', $item->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item"
                                                        onclick="return confirm('Tem certeza que deseja deletar este item?')">
                                                        <i class="bi bi-trash"></i> Deletar
                                                    </button>
                                                </form>
                                            @endcan
                                        </li>
                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum item cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $items->links() }}
        </div>
    @endsection
