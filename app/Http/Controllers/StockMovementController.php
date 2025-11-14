<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['items', 'user'])->latest()->paginate(15);
        return view('stock.movements.index', compact('movements'));
    }

    public function create()
    {
        $items = StockItem::active()->orderBy('name', 'ASC')->get();
        return view('stock.movements.create', compact('items'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|in:input,output',
        'description' => 'nullable|string',
        'extra_items' => 'nullable|string',
        'items' => 'nullable|array',
        'items.*.id' => 'required_with:items|exists:stock_items,id',
        'items.*.quantity' => 'required_with:items|integer|min:1',
        'items.*.price' => 'nullable|numeric|min:0',
    ]);

    // 1) Cria a movimentação
    $movement = StockMovement::create([
        'type' => $validated['type'],
        'description' => $validated['description'] ?? null,
        'extra_items' => $validated['extra_items'] ?? null,
        'user_id' => auth()->id(),
    ]);

    // 2) Coleta estoque ANTES (old) — usa id e nome
    $oldData = [];
    if (!empty($validated['items'])) {
        $ids = array_column($validated['items'], 'id');
        $stockItems = StockItem::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($validated['items'] as $it) {
            $si = $stockItems->get($it['id']);
            if (!$si) continue;
            $oldData[$si->id] = [
                'name' => $si->name,
                'stock' => $si->current_stock,
            ];
        }
    }

    // 3) Processa os itens (incrementa/decrementa e grava pivot)
    if (!empty($validated['items'])) {
        foreach ($validated['items'] as $item) {
            $stockItem = StockItem::find($item['id']);
            if (!$stockItem) continue;

            $quantity = (int) $item['quantity'];
            $price = isset($item['price']) && $item['price'] !== '' ? $item['price'] : null;

            if ($validated['type'] === 'input') {
                // Entrada
                $stockItem->increment('current_stock', $quantity);
                if ($price !== null) {
                    $stockItem->price = $price;
                    $stockItem->save();
                }
            } else {
                // Saída
                $stockItem->decrement('current_stock', $quantity);
                // se price não informado, mantemos o preço atual do item
                if ($price === null) {
                    $price = $stockItem->price;
                }
            }

            // Vincula no pivot (movement_items por exemplo)
            $movement->items()->attach($stockItem->id, [
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }
    }

    // 4) Coleta estoque DEPOIS (new)
    $newData = [];
    if (!empty($validated['items'])) {
        $ids = array_column($validated['items'], 'id');
        // refetch para garantir valores atualizados
        $stockItemsAfter = StockItem::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($stockItemsAfter as $id => $si) {
            $newData[$id] = [
                'name' => $si->name,
                'stock' => $si->current_stock,
            ];
        }
    }

    // 5) Monta old/new no formato desejado { "Item X": qty, ... }
    $oldFormatted = [];
    $newFormatted = [];

    foreach ($oldData as $id => $row) {
        $oldFormatted[$row['name']] = $row['stock'];
    }
    foreach ($newData as $id => $row) {
        $newFormatted[$row['name']] = $row['stock'];
    }

    // 6) Registra 1 log único para a movimentação
    activity()
        ->causedBy(auth()->user())
        ->performedOn($movement)
        ->withProperties([
            'movement_id' => $movement->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'old' => $oldFormatted,
            'new' => $newFormatted,
        ])
        ->log('Movimentação de estoque');

    return redirect()->route('stock.movements.index')
        ->with('success', 'Movimentação registrada com sucesso!');
}




    public function show($id)
    {
        $movement = StockMovement::with(['items', 'user'])->findOrFail($id);
        return view('stock.movements.show', compact('movement'));
    }

    public function updatePrices($id)
    {
        $movement = StockMovement::with('items')->findOrFail($id);

        foreach ($movement->items as $item) {
            // Busca o item no estoque pelo nome (ou por id, se preferir)
            $stockItem = StockItem::where('name', $item->name)->first();

            if ($stockItem && $stockItem->price) {
                // Atualiza o preço no PIVOT da movimentação
                $movement->items()->updateExistingPivot($item->id, [
                    'price' => $stockItem->price,
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('stock.movements.show', $id)
            ->with('success', 'Preços atualizados de acordo com o estoque!');
    }

    public function reportForm()
    {
        return view('stock.movements.report_form');
    }

    public function reportView(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'nullable|in:input,output,all',
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->endOfDay();

        $query = StockMovement::with(['items', 'user'])
            ->whereBetween('created_at', [$start, $end]);

        if ($validated['type'] !== 'all') {
            $query->where('type', $validated['type']);
        }

        $movements = $query->get();

        // Agrupar os dados resumidos
        $summary = [];
        $grandTotal = 0;

        foreach ($movements as $movement) {
            foreach ($movement->items as $item) {
                $totalItem = $item->pivot->quantity * $item->pivot->price;
                $grandTotal += $totalItem;

                if (!isset($summary[$item->name])) {
                    $summary[$item->name] = [
                        'name' => $item->name,
                        'total_qty' => 0,
                        'total_value' => 0,
                    ];
                }

                $summary[$item->name]['total_qty'] += $item->pivot->quantity;
                $summary[$item->name]['total_value'] += $totalItem;
            }
        }

        return view('stock.movements.report_view', [
            'movements' => $movements,
            'summary' => $summary,
            'grandTotal' => $grandTotal,
            'startDate' => $validated['start_date'],
            'endDate' => $validated['end_date'],
            'type' => $validated['type'],
        ]);
    }


}
