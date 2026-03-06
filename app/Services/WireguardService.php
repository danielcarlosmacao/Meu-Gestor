<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WireguardService
{
    protected $baseUrl;
    protected $password;
    protected $cookie;

    public function __construct()
    {
        $this->baseUrl = config('services.wireguard.url');
        $this->password = config('services.wireguard.password');

        $this->login();
    }

    private function login()
    {
        $response = Http::post($this->baseUrl . '/api/session', [
            'password' => $this->password
        ]);

        $this->cookie = $response->cookies()->toArray()[0]['Value'] ?? null;
    }

    private function http()
    {
        return Http::withHeaders([
            'Cookie' => 'connect.sid=' . $this->cookie,
            'Accept' => 'application/json'
        ]);
    }

    public function listClients()
    {
        $response = $this->http()->get($this->baseUrl . "/api/wireguard/client");

        if (!$response->successful()) {
            dd($response->body());
        }

        return $response->json();
    }

    public function createClient($name)
    {
        $response = $this->http()->post($this->baseUrl . "/api/wireguard/client", [
            'name' => $name
        ]);

        return $response->json();
    }

    public function deleteClient($id)
    {
        return $this->http()->delete($this->baseUrl . "/api/wireguard/client/$id");
    }

    public function getQrCode($id)
    {
        $response = $this->http()->get($this->baseUrl . "/api/wireguard/client/$id/qrcode.svg");

        return $response->body();
    }

    public function qrcodeClient($id)
    {
        return $this->baseUrl . "/api/wireguard/client/$id/qrcode.svg";
    }

    public function getConfig($id)
    {
        $response = $this->http()->get($this->baseUrl . "/api/wireguard/client/$id/configuration");

        return $response->body();
    }
}