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

public function buscarNotas($emissao)
{
    if (!preg_match('/^\d{4}-\d{2}$/', $emissao)) {
        return response()->json([
            'status' => 'erro',
            'mensagem' => 'Formato inválido, use YYYY-MM'
        ], 422);
    }

    try {
        $dados = $this->mkAuth->get("gestor/show_nfe/{$emissao}");

        // Força UTF-8
        array_walk_recursive($dados, function (&$item) {
            if (is_string($item)) {
                $item = mb_convert_encoding($item, 'UTF-8', 'auto');
            }
        });

        $notas = $dados['notas'] ?? [];

        // ✅ Agrupamento IBGE > CFOP > VELDOWN > TIPO
        $agrupado = collect($notas)
            ->groupBy('cidade_ibge')
            ->map(function ($grupoCidade) {
                return $grupoCidade
                    ->groupBy('cfop')
                    ->map(function ($grupoCfop) {
                        return $grupoCfop
                            ->groupBy(function ($item) {
                                return $item['veldown']; // agrupa por velocidade bruta
                            })
                            ->map(function ($grupoVel) {
                                // Dentro de cada velocidade, agrupa por tipo
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
                                    ->sortKeys(); // ordena tipo em ordem alfabética
                            })
                            ->sortKeysDesc(); // ordena velocidade desc
                    })
                    ->sortKeys(); // ordena CFOP asc
            })
            ->sortKeys(); // ordena IBGE asc

        return view('api.nfe', [
            'notas' => $notas,
            'agrupado' => $agrupado,
            'mensagem' => $dados['mensagem'] ?? null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'erro',
            'mensagem' => $e->getMessage()
        ], 500);
    }
}



}
