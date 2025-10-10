<?php

namespace App\Http\Controllers;

use App\Services\MkAuthService;
use Illuminate\Http\Request;

class MkAuthController extends Controller
{
    protected $mkAuth;

    public function __construct(MkAuthService $mkAuth)
    {
        $this->mkAuth = $mkAuth;
    }

    public function buscarNotas(Request $request)
    {
        $ano = $request->query('year');
        $mes = $request->query('month');
        $validate = $request->query('validate');

        $mkauthUrl = rtrim(config('custom.mkauth_url_site'), '/');
        $mkauthSearchLogin = ltrim(config('custom.mkauth_search_login'), '/');

        if (!$ano || !$mes || !preg_match('/^\d{4}$/', $ano) || !preg_match('/^\d{2}$/', $mes)) {
            return view('api.mk.nfe', [
                'notas' => [],
                'agrupado' => collect(),
                'mensagem' => 'Faça uma nova pesquisa clicando em Buscar.',
            ]);
        }

        $emissao = "{$ano}-{$mes}";

        try {
            // 🔹 1️⃣ Busca dados da API
            $dados = $this->mkAuth->get("gestorwf/show_nfe/{$emissao}");
            $encryptedPayload = $dados['payload'] ?? null;

            if (!$encryptedPayload) {
                throw new \Exception('Resposta inválida ou sem payload.');
            }

            // 🔹 2️⃣ Busca a chave do banco (keyapi)
            $keyapi = \DB::table('options')->where('reference', 'keyapi')->value('value');
            if (!$keyapi) {
                throw new \Exception('Chave de descriptografia (keyapi) não encontrada no banco.');
            }

            // 🔹 3️⃣ Descriptografa as chaves do .env usando a mesma IV fixa usada ao criptografar
            $encryptedKey = env('ENCRYPTED_API_KEY');
            $encryptedIv = env('ENCRYPTED_API_IV');

            // IV fixo usado quando criptografou
            $masterIv = env('MASTER_IV');

            $ciphertextKey = base64_decode($encryptedKey);

            $ciphertextIv = base64_decode($encryptedIv);

            $apiKey = openssl_decrypt(
                $ciphertextKey,        // dados binários
                'AES-256-CBC',      // método
                $keyapi,            // chave usada na criptografia
                OPENSSL_RAW_DATA,   // sinaliza que os dados são raw
                $masterIv           // IV usado na criptografia
            );
            $apiIv = openssl_decrypt(
                $ciphertextIv,
                'AES-256-CBC',
                $keyapi,
                OPENSSL_RAW_DATA,
                $masterIv
            );

            // 🔹 Garante que o IV final usado para dados também tenha 16 bytes
            $apiIvBin = substr($apiIv, 0, 16);

            if (!$apiKey || !$apiIvBin) {
                throw new \Exception('Falha ao descriptografar chaves do .env.');
            }

            // 🔹 4️⃣ Descriptografa o payload vindo da API
            $jsonDecrypted = openssl_decrypt(
                base64_decode($encryptedPayload),
                'AES-256-CBC',
                $apiKey,
                OPENSSL_RAW_DATA,
                $apiIvBin
            );


            $dados = json_decode($jsonDecrypted, true);

            if (!is_array($dados)) {
                throw new \Exception($apiKey . " " . $apiIv);
                //throw new \Exception('Falha ao descriptografar dados da API.');
            }

            // 🔹 5️⃣ Corrige encoding
            array_walk_recursive($dados, function (&$item) {
                if (is_string($item)) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'auto');
                }
            });

            $notas = $dados['notas'] ?? [];

            if (empty($notas)) {
                return view('api.mk.nfe', [
                    'notas' => [],
                    'agrupado' => collect(),
                    'mensagem' => 'Nenhuma nota encontrada para o período informado.',
                ]);
            }

            // 🔹 6️⃣ Agrupa as notas
            $agrupado = collect($notas)
                ->groupBy('cidade_ibge')
                ->map(function ($grupoCidade) {
                    return $grupoCidade
                        ->groupBy('cfop')
                        ->map(function ($grupoCfop) {
                            return $grupoCfop
                                ->groupBy(fn($item) => $item['veldown'])
                                ->map(function ($grupoVel) {
                                    return collect($grupoVel)
                                        ->groupBy('tipo')
                                        ->map(function ($grupoTipo) {
                                            $primeiro = $grupoTipo->first();
                                            return [
                                                'plano' => $primeiro['plano_nome'],
                                                'veldown' => $primeiro['veldown'] / 1000, // Mbps
                                                'quantidade' => $grupoTipo->count(),
                                                'cfop' => $primeiro['cfop'],
                                                'cidade_ibge' => $primeiro['cidade_ibge'],
                                                'mes' => \Carbon\Carbon::parse($primeiro['emissao'])->format('m'),
                                                'tecnologia' => $primeiro['tecnologia'] ?? '-',
                                                'tipo' => $primeiro['tipo'] ?? 'Padrão',
                                            ];
                                        })
                                        ->sortKeys();
                                })
                                ->sortKeysDesc();
                        })
                        ->sortKeys();
                })
                ->sortKeys();

            return view('api.mk.nfe', [
                'notas' => $notas,
                'agrupado' => $agrupado,
                'mkauthUrl' => $mkauthUrl,
                'mkauthSearchLogin' => $mkauthSearchLogin,
                'validate' => $validate,
                'mensagem' => null,
            ]);

        } catch (\Exception $e) {
            return view('api.mk.nfe', [
                'notas' => [],
                'agrupado' => collect(),
                'mensagem' => 'Erro de conexão ou descriptografia: ' . $e->getMessage(),
            ]);
        }
    }

}