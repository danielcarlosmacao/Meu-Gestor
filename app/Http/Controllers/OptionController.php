<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Option;

class OptionController extends Controller
{
    //Options Colors
    public function editColors()
    {
        $logoOption = Option::where('reference', 'logo')->first();
        $logo = $logoOption ? $logoOption->value : null;

        $options = Option::pluck('value', 'reference')->toArray();
        return view('admin.option.colors', compact('options', 'logo'));
    }

    public function updateColors(Request $request)
    {
        $request->validate([
            'color-primary' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'color-secondary' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'color-text' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'color-hover' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'color-primary-login' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'color-secondary-login' => ['required', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ]);

        $colors = ['color-primary', 'color-secondary', 'color-text', 'color-hover', 'color-primary-login', 'color-secondary-login'];

        foreach ($colors as $ref) {
            Option::updateOrCreate(
                ['reference' => $ref],
                ['value' => $request->input($ref)]
            );
        }


        Cache::forget('app.options');
        Cache::rememberForever('app.options', function () {
            return Option::pluck('value', 'reference')->toArray();
        });

        return redirect()->back()->with('success', 'Cores atualizadas com sucesso!');
    }

    //Optios towers
    public function editResource()
    {
        $options = Option::pluck('value', 'reference')->toArray();
        return view('admin.option.resource', compact('options'));
    }
    public function updateResource(Request $request)
    {
        $data = $request->validate([
            'hours_Generation' => 'required|integer|min:1',
            'hours_autonomy' => 'required|integer|min:1',
            'pagination' => 'required|integer|min:1|max:100',
            'whatsapp_method' => 'nullable|in:GET,POST',
            'whatsapp_ip' => 'nullable|string|max:255',
            'whatsapp_user' => 'nullable|string|max:255',
            'whatsapp_token' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {

            foreach ($data as $reference => $newValue) {

                // Normaliza valores
                $newValue = is_null($newValue) || trim((string) $newValue) === 'null'
                    ? ''
                    : (string) $newValue;

                $option = Option::where('reference', $reference)->first();

                // Se não existir ainda → cria e loga
                if (!$option) {
                    $option = Option::create([
                        'reference' => $reference,
                        'value' => $newValue,
                    ]);

                    activity()
                        ->causedBy(auth()->user())
                        ->performedOn($option)
                        ->withProperties([
                            'reference' => $reference,
                            'old' => null,
                            'new' => $newValue,
                        ])
                        ->log("Configuração '{$reference}' criada");

                    continue;
                }

                // Se valor não mudou → não faz NADA
                if ((string) $option->value === $newValue) {
                    continue;
                }

                // Guarda valor antigo
                $oldValue = $option->value;

                // Atualiza
                $option->update(['value' => $newValue]);

                // Loga somente se houve mudança
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($option)
                    ->withProperties([
                        'reference' => $reference,
                        'old' => [
                            'value' => $oldValue,
                        ],
                        'new' => [
                            'value' => $newValue,
                        ],
                    ])
                    ->log("Configuração '{$reference}' atualizada");

            }
        });

        return back()->with('success', 'Apenas as configurações alteradas foram salvas.');
    }

    public function editSystemResource()
    {
        $options = Option::pluck('value', 'reference')->toArray();
        return view('admin.option.SystemResource', compact('options'));
    }


    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');

            Option::updateOrCreate(
                ['reference' => 'logo'],
                ['value' => '/storage/' . $path]
            );
        }
        Cache::forget('app.options');
        Cache::rememberForever('app.options', function () {
            return Option::pluck('value', 'reference')->toArray();
        });
        return redirect()->route('options.colors.edit')->with('success', 'Logo atualizada com sucesso!');
    }

}
