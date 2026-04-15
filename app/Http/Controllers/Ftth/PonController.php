<?php

namespace App\Http\Controllers\Ftth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FtthPon;

class PonController extends Controller
{

    public function index()
    {

        $pons = FtthPon::orderBy('olt', 'ASC')->orderBy('info', 'ASC')->get();

        return view('ftth.pons', compact('pons'));
    }

    public function store(Request $request)
    {

        FtthPon::create([

            'olt' => $request->olt,
            'info' => $request->info,
            'signal' => $request->signal,
            'coordinates' => $request->coordinates

        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {

        $pon = FtthPon::findOrFail($id);

        $pon->delete();

        return redirect()->back();
    }

}