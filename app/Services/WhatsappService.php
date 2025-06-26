<?php

namespace App\Services;

use App\Models\Option;

class WhatsappService
{
    protected string $ip;
    protected string $token;

    public function __construct()
    {
        $this->ip = Option::getValue('whatsapp_ip');
        $this->token = Option::getValue('whatsapp_token');
    }

    public function sendMessage(string $phone, string $message): string
    {
        $data = http_build_query([
            'to' => '55' . $phone,
            'msg' => $message,
            'pass' => $this->token,
            'auth' => 'true',
        ]);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://{$this->ip}/send-message");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
