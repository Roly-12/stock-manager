<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        // On récupère les réglages sous forme de tableau clé => valeur
        $settings = Setting::pluck('value', 'key')->all();
        return Inertia::render('Settings/Index', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Paramètres mis à jour.');
    }
}