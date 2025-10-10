<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateEncryptedKeys extends Command
{
    protected $signature = 'generate:keys';
    protected $description = 'Gera chaves criptografadas para .env e banco';

    public function handle()
    {
        // 1️⃣ Chave principal para guardar no banco
        $keyapi = bin2hex(random_bytes(16)); // 32 caracteres hexadecimais
        $this->info("Salve no banco (options): keyapi = $keyapi");

        // 2️⃣ Valor real da API e IV
        $apiKey = "sua_key";
        $apiIv  = "sua IV";

        // 3️⃣ Vetor inicial fixo (seguro para o .env)
        $masterIv = "sua masterIv"; // 16 caracteres

        // 4️⃣ Criptografa ambos os valores
        $encryptedKey = base64_encode(openssl_encrypt($apiKey, 'AES-256-CBC', $keyapi, OPENSSL_RAW_DATA, $masterIv));
        $encryptedIv  = base64_encode(openssl_encrypt($apiIv,  'AES-256-CBC', $keyapi, OPENSSL_RAW_DATA, $masterIv));

        // 5️⃣ Exibe os valores para o .env
        $this->info("\nAdicione ao seu .env:");
        $this->line("ENCRYPTED_API_KEY={$encryptedKey}");
        $this->line("ENCRYPTED_API_IV={$encryptedIv}");
        $this->line("MASTER_IV={$masterIv}");
    }
}
