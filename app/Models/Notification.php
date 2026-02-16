<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIconAttribute()
    {
        $icons = [
            'request_submitted' => 'fas fa-file-alt',
            'request_approved' => 'fas fa-check-circle',
            'request_rejected' => 'fas fa-times-circle',
            'committee_review' => 'fas fa-users',
            'decision_made' => 'fas fa-gavel',
            'mission_1_completed' => 'fas fa-graduation-cap',
            'mission_2_completed' => 'fas fa-file-upload',
            'funding_received' => 'fas fa-money-bill-wave',
            'repayment_due' => 'fas fa-calendar-alt',
            'support_ticket_created' => 'fas fa-headset',
            'support_ticket_reply' => 'fas fa-reply',           // Nouveau
            'support_ticket_closed' => 'fas fa-lock',           // Nouveau
            'document_validated' => 'fas fa-file-check',
            'document_rejected' => 'fas fa-file-exclamation'
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    public function getColorAttribute()
    {
        $colors = [
            'request_submitted' => 'primary',
            'request_approved' => 'success',
            'request_rejected' => 'danger',
            'committee_review' => 'info',
            'decision_made' => 'warning',
            'mission_1_completed' => 'success',
            'mission_2_completed' => 'info',
            'funding_received' => 'success',
            'repayment_due' => 'warning',
            'support_ticket_created' => 'info',
            'support_ticket_reply' => 'success',                // Nouveau
            'support_ticket_closed' => 'secondary',             // Nouveau
            'document_validated' => 'success',
            'document_rejected' => 'danger'
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    // Méthodes
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}
