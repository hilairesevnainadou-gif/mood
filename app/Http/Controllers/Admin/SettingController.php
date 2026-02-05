<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->firstOrCreate(['user_id' => null]);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Setting::query()->firstOrCreate(['user_id' => null]);

        $data = $request->validate([
            'notification_email' => ['nullable', 'boolean'],
            'notification_sms' => ['nullable', 'boolean'],
            'notification_push' => ['nullable', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'currency' => ['nullable', 'string', 'max:10'],
            'theme' => ['nullable', 'string', 'max:50'],
            'rows_per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $settings->update($data);

        return back()->with('success', 'Paramètres mis à jour.');
    }
}
