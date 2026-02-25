<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Paramètres globaux (admin)
     */
    public function index()
    {
        // Récupérer ou créer les paramètres globaux (user_id = null)
        $settings = Setting::firstOrCreate(
            ['user_id' => null],
            [
                'notification_email' => true,
                'notification_sms' => false,
                'notification_push' => true,
                'language' => 'fr',
                'timezone' => 'Africa/Abidjan',
                'date_format' => 'd/m/Y',
                'currency' => 'XOF',
                'theme' => 'light',
                'email_notifications' => true,
                'sms_notifications' => false,
                'push_notifications' => true,
                'newsletter_subscribed' => true,
                'two_factor_auth' => false,
                'data_retention_period' => 365,
                'auto_logout_time' => 30,
                'default_view' => 'grid',
                'rows_per_page' => 25,
            ]
        );

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Mise à jour des paramètres globaux
     */
    public function update(Request $request)
    {
        $settings = Setting::firstOrCreate(
            ['user_id' => null],
            [
                'notification_email' => true,
                'notification_sms' => false,
                'notification_push' => true,
                'language' => 'fr',
                'timezone' => 'Africa/Abidjan',
                'date_format' => 'd/m/Y',
                'currency' => 'XOF',
                'theme' => 'light',
            ]
        );

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

        // Convertir les checkboxes
        $data['notification_email'] = $request->has('notification_email');
        $data['notification_sms'] = $request->has('notification_sms');
        $data['notification_push'] = $request->has('notification_push');

        $settings->update($data);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}