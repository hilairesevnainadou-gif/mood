<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_admin',
        'attachments',
        'read',
        'read_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'read' => 'boolean',
        'attachments' => 'array',
        'read_at' => 'datetime'
    ];

    protected $attributes = [
        'is_admin' => false,
        'read' => false
    ];

    // Dans SupportMessage.php, remplacez la relation ticket() par :
public function ticket()
{
    return $this->belongsTo(SupportTicket::class, 'ticket_id');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFormattedMessageAttribute()
    {
        return nl2br(e($this->message));
    }

    public function getAttachmentCountAttribute()
    {
        return $this->attachments ? count($this->attachments) : 0;
    }

    // Méthodes
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'read' => false,
            'read_at' => null
        ]);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function getAttachmentsList()
    {
        return $this->attachments ?? [];
    }

    public function addAttachment($attachment)
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = $attachment;

        $this->update(['attachments' => $attachments]);
    }

    public function removeAttachment($index)
    {
        $attachments = $this->attachments ?? [];

        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $attachments = array_values($attachments); // Réindexer le tableau

            $this->update(['attachments' => $attachments]);
            return true;
        }

        return false;
    }
}
