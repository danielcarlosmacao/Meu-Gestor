<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['roles', 'permissions'])->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }
    public function updatePermissions(Request $request, User $user)
    {

            $roles = $request->input('roles', []);
    
    // Verifica se o usuário está perdendo a role 'admin'
    $hadAdminRole = $user->hasRole('administrator');
    $willHaveAdminRole = in_array('administrator', $roles);
    
    if ($hadAdminRole && !$willHaveAdminRole) {
        // Está removendo a role admin deste usuário
        // Conta quantos usuários admin existem (exceto o atual)
        $adminCount = Role::findByName('administrator')
            ->users()
            ->where('id', '!=', $user->id)
            ->count();
        
        if ($adminCount === 0) {
            // Não pode remover porque seria o último admin
            return redirect()->route('admin.usuarios.index')->with('error', 'Não é permitido remover a última conta com permissão de administrador.');
        }
    }
    
        // Atualiza os papéis (roles)
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        } else {
            $user->syncRoles([]);
        }

        // Atualiza permissões individuais
        if ($request->has('permissions')) {
            $user->syncPermissions($request->input('permissions'));
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Permissões atualizadas com sucesso!');
    }
/*
    public function update(Request $request, User $user)
    {
        $user = auth()->user();

        // Validação básica
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);



        // Verifica se a senha atual está correta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        // Atualiza os dados
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
*/
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }
    public function toggleActive(User $user)
    {
        if (auth()->id() === $user->id) {
             return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode desativar a si mesmo.');
        }

        $user->active = !$user->active;
        $user->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
             return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode excluir a si mesmo.');
        }

        $user->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function resetPassword(User $user)
    {
        // Pode usar uma senha fixa ou gerar uma aleatória
        $newPassword = Str::random(8); // Ex: 'h8d92kLm'

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Você pode mostrar essa senha no frontend, ou enviar por e-mail
        return redirect()->back()->with('success', 'Senha redefinida para: ' . $newPassword);
    }

    public function usersOnline()
{
    $lifetime = config('session.lifetime');
    $limit = Carbon::now()->subMinutes($lifetime)->timestamp;

    $sessions = DB::table('sessions')
        ->where('last_activity', '>=', $limit)
        ->get();

    $users = [];

    foreach ($sessions as $session) {
        if (!empty($session->user_id)) {
            $user = User::find($session->user_id);
            if ($user) {
                $users[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            }
        }
    }

    return view('admin.users.usersOnline', compact('users'));
}
public function destroySession($userId)
{
    DB::table('sessions')->where('user_id', $userId)->delete();

    return redirect()->back()->with('success', 'Sessão encerrada com sucesso.');
}

}
