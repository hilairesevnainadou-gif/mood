<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * Les attributs pouvant être assignés en masse.
     *
     * @var array<string>
     */
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
        'rows_per_page',
    ];

    /**
     * Les attributs à caster.
     *
     * @var array<string, string>
     */
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
        'rows_per_page' => 'integer',
    ];

    /**
     * Les valeurs par défaut des attributs.
     *
     * @var array<string, mixed>
     */
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
        'rows_per_page' => 25,
    ];

    /**
     * Boot du modèle.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            // Invalider le cache lors de la sauvegarde
            Cache::forget('settings_global');
            if ($setting->user_id) {
                Cache::forget("settings_user_{$setting->user_id}");
            }
        });

        static::deleted(function ($setting) {
            Cache::forget('settings_global');
            if ($setting->user_id) {
                Cache::forget("settings_user_{$setting->user_id}");
            }
        });
    }

    // ============================================================================
    // RELATIONS
    // ============================================================================

    /**
     * Relation avec l'utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============================================================================
    // SCOPES
    // ============================================================================

    /**
     * Scope pour les paramètres globaux (admin).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope pour les paramètres utilisateur.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les notifications activées.
     */
    public function scopeWithNotificationsEnabled($query)
    {
        return $query->where('notification_email', true)
                     ->orWhere('notification_sms', true)
                     ->orWhere('notification_push', true);
    }

    // ============================================================================
    // ACCESSORS & MUTATORS
    // ============================================================================

    /**
     * Accessor pour les paramètres de notification.
     */
    public function getNotificationSettingsAttribute(): array
    {
        return [
            'email' => $this->notification_email,
            'sms' => $this->notification_sms,
            'push' => $this->notification_push,
        ];
    }

    /**
     * Accessor pour les paramètres d'affichage.
     */
    public function getDisplaySettingsAttribute(): array
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

    /**
     * Accessor pour les paramètres de sécurité.
     */
    public function getSecuritySettingsAttribute(): array
    {
        return [
            'two_factor_auth' => $this->two_factor_auth,
            'auto_logout_time' => $this->auto_logout_time,
        ];
    }

    /**
     * Accessor pour vérifier si c'est un paramètre global.
     */
    public function getIsGlobalAttribute(): bool
    {
        return is_null($this->user_id);
    }

    // ============================================================================
    // MÉTHODES DE VÉRIFICATION (BOOLEAN)
    // ============================================================================

    /**
     * Vérifie si les notifications email sont activées.
     */
    public function hasEmailNotifications(): bool
    {
        return $this->notification_email;
    }

    /**
     * Vérifie si les notifications SMS sont activées.
     */
    public function hasSmsNotifications(): bool
    {
        return $this->notification_sms;
    }

    /**
     * Vérifie si les notifications push sont activées.
     */
    public function hasPushNotifications(): bool
    {
        return $this->notification_push;
    }

    /**
     * Vérifie si l'utilisateur est abonné à la newsletter.
     */
    public function isSubscribedToNewsletter(): bool
    {
        return $this->newsletter_subscribed;
    }

    /**
     * Vérifie si l'authentification à deux facteurs est activée.
     */
    public function hasTwoFactorAuth(): bool
    {
        return $this->two_factor_auth;
    }

    /**
     * Vérifie si le thème sombre est activé.
     */
    public function isDarkTheme(): bool
    {
        return $this->theme === 'dark';
    }

    // ============================================================================
    // MÉTHODES DE MISE À JOUR
    // ============================================================================

    /**
     * Met à jour les paramètres.
     */
    public function updateSettings(array $settings): self
    {
        // Filtrer les champs autorisés
        $allowed = array_intersect_key($settings, array_flip($this->fillable));
        
        $this->update($allowed);
        
        return $this->fresh();
    }

    /**
     * Met à jour uniquement les paramètres de notification.
     */
    public function updateNotificationSettings(array $settings): self
    {
        $allowed = array_intersect_key($settings, [
            'notification_email' => true,
            'notification_sms' => true,
            'notification_push' => true,
            'email_notifications' => true,
            'sms_notifications' => true,
            'push_notifications' => true,
        ]);

        return $this->updateSettings($allowed);
    }

    /**
     * Met à jour uniquement les préférences d'affichage.
     */
    public function updateDisplaySettings(array $settings): self
    {
        $allowed = array_intersect_key($settings, [
            'language' => true,
            'timezone' => true,
            'date_format' => true,
            'currency' => true,
            'theme' => true,
            'default_view' => true,
            'rows_per_page' => true,
        ]);

        return $this->updateSettings($allowed);
    }

    /**
     * Réinitialise aux valeurs par défaut.
     */
    public function resetToDefaults(): self
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
            'rows_per_page' => 25,
        ];

        $this->update($defaults);

        return $this->fresh();
    }

    // ============================================================================
    // MÉTHODES STATIQUES (HELPERS)
    // ============================================================================

    /**
     * Récupère les paramètres globaux (admin).
     */
    public static function getGlobal(): ?self
    {
        return Cache::remember('settings_global', 300, function () {
            return self::global()->first();
        });
    }

    /**
     * Récupère ou crée les paramètres globaux.
     */
    public static function getOrCreateGlobal(): self
    {
        return self::firstOrCreate(
            ['user_id' => null],
            self::defaultValues()
        );
    }

    /**
     * Récupère les paramètres d'un utilisateur.
     */
    public static function getForUser(int $userId): ?self
    {
        return Cache::remember("settings_user_{$userId}", 300, function () use ($userId) {
            return self::forUser($userId)->first();
        });
    }

    /**
     * Récupère ou crée les paramètres d'un utilisateur.
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            array_merge(self::defaultValues(), ['user_id' => $userId])
        );
    }

    /**
     * Retourne les valeurs par défaut.
     */
    public static function defaultValues(): array
    {
        return [
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
        ];
    }

    /**
     * Fusionne les paramètres utilisateur avec les globaux.
     * Les paramètres utilisateur ont la priorité.
     */
    public static function getMergedForUser(int $userId): array
    {
        $global = self::getGlobal()?->toArray() ?? self::defaultValues();
        $user = self::getForUser($userId)?->toArray() ?? [];

        // Supprimer les clés techniques
        unset($global['id'], $global['user_id'], $global['created_at'], $global['updated_at']);
        unset($user['id'], $user['user_id'], $user['created_at'], $user['updated_at']);

        return array_merge($global, $user);
    }
}