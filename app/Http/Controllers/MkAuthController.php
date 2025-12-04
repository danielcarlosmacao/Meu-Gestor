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
        $dados = $this->mkAuth->get("gestorwf/show_nfe/{$emissao}");

        // ✅ Só aplica array_walk_recursive se $dados for um array
        if (is_array($dados)) {
            array_walk_recursive($dados, function (&$item) {
                if (is_string($item)) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'auto');
                }
            });
        }

        $notas = $dados['notas'] ?? [];

        // Se não houver notas, apenas renderiza view vazia
        if (empty($notas)) {
            return view('api.mk.nfe', [
                'notas' => [],
                'agrupado' => collect(),
                'mensagem' => 'Nenhuma nota encontrada para o período informado.',
            ]);
        }

        // ✅ Agrupamento IBGE > CFOP > VELDOWN > TIPO
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
            'mensagem' => 'Erro de conexão com o servidor MKAuth erro:  ' . $e->getMessage(),
        ]);
    }
}



}