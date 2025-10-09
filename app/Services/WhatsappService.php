<?php

namespace App\Services;

use App\Models\Option;

use Illuminate\Support\Facades\Log;


class WhatsappService
{
    protected string $ip;
    protected string $user;
    protected string $token;
    protected string $method;

    public function __construct()
    {
        $this->ip     = Option::getValue('whatsapp_ip');
        $this->user   = Option::getValue('whatsapp_user');
        $this->token  = Option::getValue('whatsapp_token');
        $this->method = strtoupper(Option::getValue('whatsapp_method') ?? 'POST');

        if (!in_array($this->method, ['GET', 'POST'])) {
            throw new \InvalidArgumentException("Método inválido: {$this->method}. Use GET ou POST.");
        }
    }

    public function sendMessage(string $phone, string $message): string
    {
        Log::info('Whatsapp GET URL: ' . 'INICIO');
        if ($this->method === 'GET') {
            // Parâmetros para GET (modelo Evotrix)
            $data = http_build_query([
                'app' => 'webservices',
                'u'   => $this->user,
                'p'   => $this->token,
                'h'   => $this->token,
                'ta'  => 'pv',
                'op'  => 'pv',
                'to'  => $phone,
                'msg' => $message,
            ]);
             Log::info('Whatsapp GET URL: ' . 'MEIO');

            $url = rtrim($this->ip, '/') . '/index.php?' . $data;

             // Debug simples: salva a URL no arquivo whatsapp_debug_url.txt
    file_put_contents(storage_path('logs/whatsapp_debug_url.txt'), $url . PHP_EOL, FILE_APPEND);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        } else {
            // Parâmetros para POST (modelo antigo)
            $data = http_build_query([
                'u'    => $this->user,
                'to'   => '55' . $phone,
                'msg'  => $message,
                'pass' => $this->token,
                'auth' => 'true',
            ]);

            $url = "http://{$this->ip}/send-message";

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
