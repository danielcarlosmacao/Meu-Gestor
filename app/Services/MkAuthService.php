<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MkAuthService
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('MKAUTH_URL'), '/');
        $this->clientId = env('MKAUTH_CLIENT_ID');
        $this->clientSecret = env('MKAUTH_CLIENT_SECRET');
    }

    /**
     * Obtém o TokenJWT, armazenando em cache por 9 minutos (expira em 10min no MK-AUTH)
     */
    private function getToken()
    {
        return Cache::remember('mkauth_token', 540, function () {
            $basicAuth = base64_encode("{$this->clientId}:{$this->clientSecret}");

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $basicAuth,
            ])->get("{$this->baseUrl}/");

            if (!$response->successful()) {
                throw new \Exception("Erro ao autenticar no MK-AUTH: " . $response->body());
            }

            $token = trim($response->body());

            if (empty($token)) {
                throw new \Exception("Resposta inválida ao autenticar no MK-AUTH: corpo vazio");
            }

            return $token;
        });
    }

    /**
     * Faz uma requisição GET genérica para qualquer endpoint do MK-AUTH
     */
    public function get(string $endpoint, array $query = [])
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("{$this->baseUrl}/{$endpoint}", $query);

        if ($response->status() === 401) {
            // Token expirado -> limpa cache e tenta novamente
            Cache::forget('mkauth_token');
            return $this->get($endpoint, $query);
        }

        if (!$response->successful()) {
            throw new \Exception("Erro ao consultar endpoint [{$endpoint}]: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Faz uma requisição POST genérica para qualquer endpoint do MK-AUTH
     */
    public function post(string $endpoint, array $payload = [])
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/{$endpoint}", $payload);

        if ($response->status() === 401) {
            Cache::forget('mkauth_token');
            return $this->post($endpoint, $payload);
        }

        if (!$response->successful()) {
            throw new \Exception("Erro ao enviar POST para endpoint [{$endpoint}]: " . $response->body());
        }

        return $response->json();
    }
}
