<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postit;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class PostitController extends Controller
{
    public function index()
    {
        $postits = Postit::where('user_id', Auth::id())->get();
        $users = User::where('active','=','1')->orderBy('name', 'asc')->get();
        
        return view('welcome', compact('postits','users'));
    }

    public function store(Request $request)
    {
      
        $postit = Postit::create([
            'user_id' => $request->input('user_id', Auth::id()),
            'content' => $request->input('content', ''),
            'color' => $request->input('color', '#fffa65'),
            'pos_x' => $request->input('pos_x', 100),
            'pos_y' => $request->input('pos_y', 100),
            'width' => $request->input('width', 200),
            'height' => $request->input('height', 200),
        ]);

        return response()->json(['status' => 'success', 'postit' => $postit]);
    }

    public function update(Request $request, $id)
{
    // Validação opcional
    $validated = $request->validate([
        'content' => 'sometimes|string|max:500',
        'pos_x' => 'sometimes|integer',
        'pos_y' => 'sometimes|integer',
        'width' => 'sometimes|integer|min:0',
        'height' => 'sometimes|integer|min:0',
    ]);

    // Busca o postit do usuário autenticado
    $postit = Postit::where('id', $id)
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

    // Atualiza apenas os campos presentes no request
    $updateData = array_filter($validated, fn($v) => !is_null($v));
    $postit->update($updateData);

    // 🔹 Log de alteração (opcional)
    activity()
        ->causedBy(Auth::user())
        ->performedOn($postit)
        ->withProperties([
            'updated_fields' => $updateData
        ])
        ->log('Postit Atualizado');

    return response()->json(['status' => 'updated']);
}

    public function destroy($id)
    {
        Postit::where('id', $id)->where('user_id', Auth::id())->delete();
        return response()->json(['status' => 'deleted']);
    }
}
