<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
       
    $request->validate([
        'hours_Generation' => 'required|integer',
        'hours_autonomy'   => 'required|integer',
        'pagination'       => 'required|integer',
        'whatsapp_ip'      => 'nullable|string|max:255',
        'whatsapp_method'  => 'nullable|string|in:get,post,GET,POST',
        'whatsapp_user'    => 'nullable|string|max:255',
        'whatsapp_token'   => 'nullable|string|max:255',
    ]);

    $keys = [
        'hours_Generation',
        'hours_autonomy',
        'pagination',
        'whatsapp_method',
        'whatsapp_ip',
        'whatsapp_user',
        'whatsapp_token'
    ];

    foreach ($keys as $ref) {
        $value = $request->input($ref);

        // Normaliza valores "null" ou realmente null
        if (is_null($value) || strtolower(trim((string) $value)) === 'null') {
            $value = '';
        }

        // Busca o registro existente
        $option = Option::where('reference', $ref)->first();
        $oldData = $option ? $option->toArray() : null;

        // Atualiza ou cria
        $option = Option::updateOrCreate(
            ['reference' => $ref],
            ['value' => $value]
        );

        // Log da alteração
        activity()
            ->causedBy(auth()->user())
            ->performedOn($option)
            ->withProperties([
                'reference' => $ref,
                'old' => $oldData,
                'new' => $option->toArray()
            ])
            ->log("Configuração '{$ref}' Atualizada");
    }
        return redirect()->back()->with('success', 'Recursos atualizados com sucesso!');
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
