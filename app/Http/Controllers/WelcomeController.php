<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Models\Collaborator;

class WelcomeController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $collaborator = Collaborator::where('user_id', auth()->id())->first();

        if ($collaborator) {
            return redirect()->route('vacation_manager.work_schedules.myscale', [
                'collaborator' => $collaborator->id,
                'date'         => now()->format('m-y'),
            ]);
        }

        // Fallback caso o usuário logado seja um administrador sem vínculo na tabela collaborators
        return redirect()->route('vacation_manager.work_schedules.index');
    }
}
