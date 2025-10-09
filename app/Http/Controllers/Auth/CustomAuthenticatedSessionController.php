<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomAuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'], // pode ser email ou nome de usuário
            'password' => ['required', 'string'],
        ]);

        // Buscar o usuário por email ou nome
        $user = User::where('email', $request->email)
            ->orWhere('name', $request->email)
            ->where('active', 1)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('As credenciais estão incorretas ou o usuário está desativado.'),
            ]);
        }

        // Autenticar e regenerar a sessão
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        activity('auth')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Fez Login');

        return redirect()->intended(route('welcome', absolute: false));
    }
}
