<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_email',
        'notification_sms',
        'notification_push',
        'language',
        'timezone',
        'date_format',
        'currency',
        'theme',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'newsletter_subscribed',
        'two_factor_auth',
        'privacy_settings',
        'data_retention_period',
        'auto_logout_time',
        'default_view',
        'rows_per_page'
    ];

    protected $casts = [
        'notification_email' => 'boolean',
        'notification_sms' => 'boolean',
        'notification_push' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'newsletter_subscribed' => 'boolean',
        'two_factor_auth' => 'boolean',
        'privacy_settings' => 'array',
        'data_retention_period' => 'integer',
        'auto_logout_time' => 'integer',
        'rows_per_page' => 'integer'
    ];

    protected $attributes = [
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
        'rows_per_page' => 25
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getNotificationSettingsAttribute()
    {
        return [
            'email' => $this->notification_email,
            'sms' => $this->notification_sms,
            'push' => $this->notification_push,
        ];
    }

    public function getDisplaySettingsAttribute()
    {
        return [
            'language' => $this->language,
            'timezone' => $this->timezone,
            'date_format' => $this->date_format,
            'currency' => $this->currency,
            'theme' => $this->theme,
            'default_view' => $this->default_view,
            'rows_per_page' => $this->rows_per_page,
        ];
    }

    public function getSecuritySettingsAttribute()
    {
        return [
            'two_factor_auth' => $this->two_factor_auth,
            'auto_logout_time' => $this->auto_logout_time,
        ];
    }

    // Méthodes
    public function hasEmailNotifications()
    {
        return $this->notification_email;
    }

    public function hasSmsNotifications()
    {
        return $this->notification_sms;
    }

    public function hasPushNotifications()
    {
        return $this->notification_push;
    }

    public function isSubscribedToNewsletter()
    {
        return $this->newsletter_subscribed;
    }

    public function hasTwoFactorAuth()
    {
        return $this->two_factor_auth;
    }

    // Mettre à jour les paramètres
    public function updateSettings(array $settings)
    {
        $this->update($settings);
        return $this;
    }

    // Réinitialiser aux valeurs par défaut
    public function resetToDefaults()
    {
        $defaults = [
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
            'rows_per_page' => 25
        ];

        $this->update($defaults);
        return $this;
    }
}