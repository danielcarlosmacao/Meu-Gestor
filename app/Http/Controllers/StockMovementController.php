<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;

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

        // Cria a movimentação
        $movement = StockMovement::create([
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'extra_items' => $validated['extra_items'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Processa apenas itens do estoque
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $stockItem = StockItem::find($item['id']);

                if ($validated['type'] === 'input') {
                    // Entrada: incrementa estoque e atualiza preço se informado
                    $stockItem->increment('current_stock', $item['quantity']);

                    $price = isset($item['price']) && $item['price'] !== '' ? $item['price'] : null;
                    if ($price !== null) {
                        $stockItem->price = $price;
                        $stockItem->save();
                    }
                } else {
                    // Saída: decrementa estoque
                    $stockItem->decrement('current_stock', $item['quantity']);

                    // Se preço não foi informado, usa preço atual do item
                    $price = isset($item['price']) && $item['price'] !== '' ? $item['price'] : $stockItem->price;
                }

                // Salva no pivot
                $movement->items()->attach($item['id'], [
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);
            }
        }

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


}
