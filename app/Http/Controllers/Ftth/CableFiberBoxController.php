<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FtthCableFiberBox;
use App\Models\FtthFiberCable;

class CableFiberBoxController extends Controller
{

    public function store(Request $request)
    {

        FtthCableFiberBox::create([

            'info' => $request->info,
            'color' => $request->color,
            'number_fiber' => $request->number_fiber,
            'input_fiber_box_id' => $request->input_fiber_box_id,
            'output_fiber_box_id' => $request->output_fiber_box_id

        ]);

        return redirect()->back();
    }


public function destroy($id)
{
    $cable = FtthCableFiberBox::findOrFail($id);

    // verifica se tem fibras
    $hasFibers = FtthFiberCable::where('cable_fiber_box_id', $id)->exists();

    if ($hasFibers) {
        return redirect()->back()
            ->with('error', 'Cabo possui fibras cadastradas');
    }

    $cable->delete();

    return redirect()->back()
        ->with('success', 'Cabo removido');
}

}