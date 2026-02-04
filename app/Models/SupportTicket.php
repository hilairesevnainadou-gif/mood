<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'metadata'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array'
    ];

    protected $attributes = [
        'category' => 'general',
        'priority' => 'medium',
        'status' => 'open'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

   public function messages()
{
    return $this->hasMany(SupportMessage::class, 'ticket_id');
}

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'open' => '<span class="badge bg-primary">Ouvert</span>',
            'in_progress' => '<span class="badge bg-warning">En cours</span>',
            'resolved' => '<span class="badge bg-success">Résolu</span>',
            'closed' => '<span class="badge bg-secondary">Fermé</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => '<span class="badge bg-info">Basse</span>',
            'medium' => '<span class="badge bg-warning">Moyenne</span>',
            'high' => '<span class="badge bg-danger">Haute</span>',
            'urgent' => '<span class="badge bg-danger">Urgent</span>'
        ];

        return $badges[$this->priority] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getCategoryLabelAttribute()
    {
        $categories = [
            'general' => 'Général',
            'technical' => 'Technique',
            'billing' => 'Facturation',
            'account' => 'Compte',
            'training' => 'Formation',
            'funding' => 'Financement',
            'document' => 'Document',
            'other' => 'Autre'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    // Méthodes
    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function canBeReplied()
    {
        return !$this->isClosed() && !$this->isResolved();
    }

    public function addMessage($message, $userId, $isAdmin = false, $attachments = [])
    {
        return $this->messages()->create([
            'message' => $message,
            'user_id' => $userId,
            'is_admin' => $isAdmin,
            'attachments' => $attachments
        ]);
    }

    public function markAsInProgress($assigneeId = null)
    {
        $this->update([
            'status' => 'in_progress',
            'assigned_to' => $assigneeId
        ]);
    }

    public function markAsResolved()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
    }

    public function markAsClosed()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
    }

    public function reopen()
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null
        ]);
    }

    public function hasUnreadMessages($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_admin', true)
            ->where('read', false)
            ->exists();
    }

    public function markMessagesAsRead($userId)
    {
        $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_admin', true)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
    }


}
