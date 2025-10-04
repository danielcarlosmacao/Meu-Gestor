<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use Illuminate\Http\Request;

use App\Services\SettingService;

use App\Models\Equipment;
use App\Models\EquipmentProduction;

class StockItemController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();

        $items = StockItem::orderBy('name', 'asc')->paginate($perPage);
        return view('stock.items.index', compact('items'));
    }

    public function create()
    {
        return view('stock.items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_stock' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|integer|min:0',
        ]);

        $validated['current_stock'] = $validated['current_stock'] ?? 0;

        // Adiciona o status com base no checkbox (marcado = active, desmarcado = inactive)
        $validated['status'] = $request->has('status') ? 'active' : 'inactive';

        StockItem::create($validated);

        return redirect()->route('stock.items.index')->with('success', 'Item cadastrado com sucesso!');

    }

    public function edit($id)
    {
        $item = StockItem::findOrFail($id);
        return view('stock.items.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = StockItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_stock' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            // Não sobrescreve current_stock
        ]);

        // Atualiza o status: checked = active, unchecked = inactive
        $validated['status'] = $request->has('status') ? 'active' : 'inactive';

        $item->update($validated);

        return redirect()->route('stock.items.index')
            ->with('success', 'Item atualizado com sucesso!');
    }


    public function destroy($id)
    {
        $item = StockItem::findOrFail($id);
        $item->delete();

        return redirect()->route('stock.items.index')->with('success', 'Item removido com sucesso!');
    }

    public function show($id)
    {
        $item = StockItem::findOrFail($id);
        return view('stock.items.show', compact('item'));
    }

    public function showProduction()
    {
        // Busca todos os equipamentos
        $equipments = Equipment::orderBy('name')->get();

        $data = $equipments->map(function ($eq) {
            // Equipamento em produção
            $inProduction = EquipmentProduction::where('equipment_id', $eq->id)
                ->where('active', 'yes')
                ->count();

            // Equipamento no estoque (relaciona por nome)
            $stockItem = StockItem::where('name', $eq->name)->first();
//@dd($stockItem);
            return [
                'equipment_name' => $eq->name,
                'watts' => $eq->watts,
                'in_production' => $inProduction,
                'stock_qty' => $stockItem->current_stock ?? 0,
                'price' => $stockItem->price ?? null,
                'status' => $stockItem ? 'found' : 'not_found',
                'active' => $stockItem->status ?? '',           
            ];
        });

        return view('stock.items.production', compact('data'));
    }

}
