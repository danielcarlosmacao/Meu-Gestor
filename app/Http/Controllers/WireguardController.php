<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WireguardService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class WireguardController extends Controller
{
    public function index(WireguardService $wg)
    {
        $clients = $wg->listClients();

        return view('vpn.index', compact('clients'));
    }

    public function store(Request $request, WireguardService $wg)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);

        $wg->createClient($request->name);

        return redirect()->back()->with('success', 'VPN criada com sucesso');
    }

    public function destroy(Request $request, $id, WireguardService $wg)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('error', 'Senha incorreta.');
        }

        $wg->deleteClient($id);

        return redirect()->back()->with('success', 'VPN removida');
    }

    public function qrcode($id, WireguardService $wg)
    {
        $svg = $wg->getQrCode($id);

        return response($svg)
            ->header('Content-Type', 'image/svg+xml');
    }

    public function download($id, WireguardService $wg)
    {
        $config = $wg->getConfig($id);
        $idabrev = mb_strimwidth($id, 0, 7, "");

        return response($config)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="vpn-' . $idabrev . '.conf"');
    }
}