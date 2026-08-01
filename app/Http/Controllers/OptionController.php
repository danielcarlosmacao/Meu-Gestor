<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OptionController extends Controller
{
    /**
     * Valores padrão das configurações gerais.
     */
    private const RESOURCE_DEFAULTS = [
        'hours_Generation' => 5,
        'hours_autonomy' => 48,
        'pagination' => 20,
        'whatsapp_method' => 'GET',
        'whatsapp_ip' => '',
        'whatsapp_user' => '',
        'whatsapp_token' => '',

        'themer' => 'header.css',
    ];

    /**
     * Referências das cores permitidas.
     */
    private const COLOR_REFERENCES = [
        'color-primary',
        'color-secondary',
        'color-text',
        'color-hover',
        'color-primary-login',
        'color-secondary-login',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cores e logo
    |--------------------------------------------------------------------------
    */

    public function editColors()
    {
        $options = Option::query()
            ->pluck('value', 'reference')
            ->toArray();

        $logo = $options['logo'] ?? null;

        return view('admin.option.colors', compact(
            'options',
            'logo'
        ));
    }

    public function updateColors(Request $request)
    {
        $validated = $request->validate([
            'color-primary' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],

            'color-secondary' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],

            'color-text' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],

            'color-hover' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],

            'color-primary-login' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],

            'color-secondary-login' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/',
            ],
        ], [
            'color-primary.required' => 'Informe a cor primária.',
            'color-secondary.required' => 'Informe a cor secundária.',
            'color-text.required' => 'Informe a cor do texto.',
            'color-hover.required' => 'Informe a cor de destaque.',
            'color-primary-login.required' => 'Informe a cor primária da tela de login.',
            'color-secondary-login.required' => 'Informe a cor secundária da tela de login.',

            'color-primary.regex' => 'A cor primária deve estar no formato hexadecimal, por exemplo: #0066CC.',
            'color-secondary.regex' => 'A cor secundária deve estar no formato hexadecimal, por exemplo: #0066CC.',
            'color-text.regex' => 'A cor do texto deve estar no formato hexadecimal, por exemplo: #FFFFFF.',
            'color-hover.regex' => 'A cor de destaque deve estar no formato hexadecimal, por exemplo: #0066CC.',
            'color-primary-login.regex' => 'A cor primária do login deve estar no formato hexadecimal.',
            'color-secondary-login.regex' => 'A cor secundária do login deve estar no formato hexadecimal.',
        ]);

        $changedOptions = DB::transaction(function () use ($validated) {
            $changed = 0;

            foreach (self::COLOR_REFERENCES as $reference) {
                $newValue = strtoupper($validated[$reference]);

                $option = Option::query()
                    ->where('reference', $reference)
                    ->lockForUpdate()
                    ->first();

                if (!$option) {
                    $option = Option::create([
                        'reference' => $reference,
                        'value' => $newValue,
                    ]);

                    $this->registerOptionActivity(
                        option: $option,
                        reference: $reference,
                        oldValue: null,
                        newValue: $newValue,
                        action: 'criada'
                    );

                    $changed++;

                    continue;
                }

                $oldValue = (string) $option->value;

                if ($oldValue === $newValue) {
                    continue;
                }

                $option->update([
                    'value' => $newValue,
                ]);

                $this->registerOptionActivity(
                    option: $option,
                    reference: $reference,
                    oldValue: $oldValue,
                    newValue: $newValue,
                    action: 'atualizada'
                );

                $changed++;
            }

            return $changed;
        });

        $this->refreshOptionsCache();

        if ($changedOptions === 0) {
            return back()->with(
                'info',
                'Nenhuma alteração nas cores foi identificada.'
            );
        }

        return back()->with(
            'success',
            $changedOptions === 1
                ? '1 cor foi atualizada com sucesso.'
                : "{$changedOptions} cores foram atualizadas com sucesso."
        );
    }

    public function updateLogo(Request $request)
    {
        $validated = $request->validate([
            'logo' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,svg',
                'max:2048',
            ],
        ], [
            'logo.required' => 'Selecione uma imagem para a logo.',
            'logo.image' => 'O arquivo selecionado deve ser uma imagem.',
            'logo.mimes' => 'A logo deve ser PNG, JPG, JPEG ou SVG.',
            'logo.max' => 'A logo deve ter no máximo 2 MB.',
        ]);

        $path = $request->file('logo')->store('logos', 'public');

        DB::transaction(function () use ($path) {
            $newValue = '/storage/' . $path;

            $option = Option::query()
                ->where('reference', 'logo')
                ->lockForUpdate()
                ->first();

            if (!$option) {
                $option = Option::create([
                    'reference' => 'logo',
                    'value' => $newValue,
                ]);

                $this->registerOptionActivity(
                    option: $option,
                    reference: 'logo',
                    oldValue: null,
                    newValue: $newValue,
                    action: 'criada'
                );

                return;
            }

            $oldValue = (string) $option->value;

            $option->update([
                'value' => $newValue,
            ]);

            $this->registerOptionActivity(
                option: $option,
                reference: 'logo',
                oldValue: $oldValue,
                newValue: $newValue,
                action: 'atualizada'
            );
        });

        $this->refreshOptionsCache();

        return redirect()
            ->route('options.colors.edit')
            ->with('success', 'Logo atualizada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | Recursos gerais do sistema
    |--------------------------------------------------------------------------
    */

    public function editResource()
    {
        $savedOptions = Option::query()
            ->whereIn(
                'reference',
                array_keys(self::RESOURCE_DEFAULTS)
            )
            ->pluck('value', 'reference')
            ->toArray();

        /*
         * Os valores salvos sobrescrevem os valores padrão.
         */
        $options = array_merge(
            self::RESOURCE_DEFAULTS,
            $savedOptions
        );

        /*
         * Lista de temas disponíveis cadastrados com:
         * reference = optionthemer
         */
        $themes = $this->availableThemes();

        /*
         * Tema atualmente selecionado.
         */
        $currentTheme = trim(
            (string) ($options['themer'] ?? self::RESOURCE_DEFAULTS['themer'])
        );

        /*
         * Caso o tema salvo não esteja mais na lista,
         * utiliza o primeiro tema disponível.
         */
        if (!in_array($currentTheme, $themes, true)) {
            $currentTheme = $themes[0];
            $options['themer'] = $currentTheme;
        }

        /*
         * Informa à view se já existe um token configurado.
         */
        $tokenConfigured = filled($options['whatsapp_token']);

        /*
         * O token salvo não será colocado no HTML.
         * Campo vazio significa manter o token atual.
         */
        $options['whatsapp_token'] = '';

        return view('admin.option.resource', compact(
            'options',
            'tokenConfigured',
            'themes',
            'currentTheme'
        ));
    }

    public function updateResource(Request $request)
    {
        $availableThemes = $this->availableThemes();

        $validated = $request->validate([
            'hours_Generation' => [
                'required',
                'integer',
                'min:1',
                'max:24',
            ],

            'hours_autonomy' => [
                'required',
                'integer',
                'min:1',
                'max:8760',
            ],

            'pagination' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'whatsapp_method' => [
                'required',
                'in:GET,POST',
            ],

            'whatsapp_ip' => [
                'nullable',
                'string',
                'max:255',
            ],

            'whatsapp_user' => [
                'nullable',
                'string',
                'max:255',
            ],

            'whatsapp_token' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'themer' => [
                'required',
                'string',
                'max:255',
                Rule::in($availableThemes),
            ],
        ], [
            'hours_Generation.required' => 'Informe as horas de geração.',
            'hours_Generation.integer' => 'As horas de geração devem ser um número inteiro.',
            'hours_Generation.min' => 'Informe pelo menos 1 hora de geração.',
            'hours_Generation.max' => 'As horas de geração não podem ultrapassar 24 horas.',

            'hours_autonomy.required' => 'Informe as horas de autonomia.',
            'hours_autonomy.integer' => 'As horas de autonomia devem ser um número inteiro.',
            'hours_autonomy.min' => 'Informe pelo menos 1 hora de autonomia.',
            'hours_autonomy.max' => 'As horas de autonomia não podem ultrapassar 8.760 horas.',

            'pagination.required' => 'Informe a quantidade de registros por página.',
            'pagination.integer' => 'A quantidade de registros deve ser um número inteiro.',
            'pagination.min' => 'A paginação deve ter pelo menos 1 registro.',
            'pagination.max' => 'A paginação deve ter no máximo 100 registros.',

            'whatsapp_method.required' => 'Selecione o método de envio.',
            'whatsapp_method.in' => 'O método de envio deve ser GET ou POST.',

            'whatsapp_ip.max' => 'O endereço da API deve ter no máximo 255 caracteres.',
            'whatsapp_user.max' => 'O usuário da API deve ter no máximo 255 caracteres.',
            'whatsapp_token.max' => 'O token deve ter no máximo 1.000 caracteres.',

            'themer.required' => 'Selecione um tema.',
            'themer.max' => 'O tema deve ter no máximo 255 caracteres.',
            'themer.in' => 'O tema selecionado não está cadastrado nas opções disponíveis.',
        ]);

        /*
     * Se o token estiver vazio, mantém o token atual.
     */
        if (blank($validated['whatsapp_token'] ?? null)) {
            unset($validated['whatsapp_token']);
        }

        $changedOptions = DB::transaction(function () use ($validated) {
            $changed = 0;

            foreach ($validated as $reference => $value) {
                $newValue = $this->normalizeOptionValue($value);

                $option = Option::query()
                    ->where('reference', $reference)
                    ->lockForUpdate()
                    ->first();

                if (!$option) {
                    $option = Option::create([
                        'reference' => $reference,
                        'value' => $newValue,
                    ]);

                    $this->registerOptionActivity(
                        option: $option,
                        reference: $reference,
                        oldValue: null,
                        newValue: $newValue,
                        action: 'criada'
                    );

                    $changed++;

                    continue;
                }

                $oldValue = (string) $option->value;

                if ($oldValue === $newValue) {
                    continue;
                }

                $option->update([
                    'value' => $newValue,
                ]);

                $this->registerOptionActivity(
                    option: $option,
                    reference: $reference,
                    oldValue: $oldValue,
                    newValue: $newValue,
                    action: 'atualizada'
                );

                $changed++;
            }

            return $changed;
        });

        $this->refreshOptionsCache();

        if ($changedOptions === 0) {
            return back()->with(
                'info',
                'Nenhuma alteração foi identificada.'
            );
        }

        return back()->with(
            'success',
            $changedOptions === 1
                ? '1 configuração foi atualizada com sucesso.'
                : "{$changedOptions} configurações foram atualizadas com sucesso."
        );
    }
    /*
    |--------------------------------------------------------------------------
    | Limpeza de caches do sistema
    |--------------------------------------------------------------------------
    */

    public function clearCache()
    {
        try {
            $commands = [
                'view:clear',
                'route:clear',
                'config:clear',
                'cache:clear',
                'optimize:clear',
            ];

            $outputs = [];

            foreach ($commands as $command) {
                Artisan::call($command);

                $outputs[$command] = trim(Artisan::output());
            }

            Log::info('Caches do sistema limpos manualmente.', [
                'user_id' => auth()->id(),
                'commands' => $commands,
                'outputs' => $outputs,
            ]);

            return back()->with(
                'success',
                'Caches do sistema limpos com sucesso.'
            );
        } catch (\Throwable $exception) {
            Log::error('Erro ao limpar os caches do sistema.', [
                'user_id' => auth()->id(),
                'erro' => $exception->getMessage(),
                'arquivo' => $exception->getFile(),
                'linha' => $exception->getLine(),
            ]);

            return back()->with(
                'error',
                'Não foi possível limpar os caches: '
                    . $exception->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Recursos administrativos do sistema
    |--------------------------------------------------------------------------
    */

    public function editSystemResource()
    {
        $options = Option::query()
            ->pluck('value', 'reference')
            ->toArray();

        return view(
            'admin.option.SystemResource',
            compact('options')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos auxiliares
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna os temas cadastrados na tabela options.
     *
     * Cada tema deve possuir:
     * reference = optionthemer
     * value = nome-do-arquivo.css
     */
    private function availableThemes(): array
    {
        $themes = Option::query()
            ->where('reference', 'optionthemer')
            ->whereNotNull('value')
            ->orderBy('value')
            ->pluck('value')
            ->map(fn($theme) => trim((string) $theme))
            ->filter(function (string $theme) {
                if ($theme === '') {
                    return false;
                }

                /*
                 * Permite somente o nome do arquivo CSS.
                 * Evita caminhos como ../ ou subdiretórios.
                 */
                return preg_match(
                    '/^[A-Za-z0-9._-]+\.css$/',
                    $theme
                ) === 1;
            })
            ->unique()
            ->values()
            ->all();

        if (empty($themes)) {
            return [
                self::RESOURCE_DEFAULTS['themer'],
            ];
        }

        return $themes;
    }

    /**
     * Normaliza um valor antes de salvar na tabela options.
     */
    private function normalizeOptionValue(mixed $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $value = trim((string) $value);

        if (strtolower($value) === 'null') {
            return '';
        }

        return $value;
    }

    /**
     * Atualiza o cache global das opções.
     */
    private function refreshOptionsCache(): void
    {
        Cache::forget('app.options');

        Cache::rememberForever('app.options', function () {
            return Option::query()
                ->pluck('value', 'reference')
                ->toArray();
        });
    }

    /**
     * Registra alterações das configurações no activity log.
     */
    private function registerOptionActivity(
        Option $option,
        string $reference,
        mixed $oldValue,
        mixed $newValue,
        string $action
    ): void {
        $isSensitive = $reference === 'whatsapp_token';

        /*
         * Nunca grava o conteúdo do token no log.
         */
        $loggedOldValue = $isSensitive && filled($oldValue)
            ? '[PROTEGIDO]'
            : $oldValue;

        $loggedNewValue = $isSensitive && filled($newValue)
            ? '[PROTEGIDO]'
            : $newValue;

        activity()
            ->causedBy(auth()->user())
            ->performedOn($option)
            ->withProperties([
                'reference' => $reference,

                'old' => [
                    'value' => $loggedOldValue,
                ],

                'new' => [
                    'value' => $loggedNewValue,
                ],
            ])
            ->log("Configuração '{$reference}' {$action}");
    }
}
