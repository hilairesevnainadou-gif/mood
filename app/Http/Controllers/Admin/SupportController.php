<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportTicketReplyMail;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'messages'])->findOrFail($id);

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::with('user')->findOrFail($id);

        $data = $request->validate([
            'message' => ['required', 'string', 'min:5'],
        ]);

        // Ajouter le message
        $message = $ticket->addMessage($data['message'], auth('admin')->id(), true);

        // Mettre à jour le statut du ticket
        $ticket->update([
            'status' => 'answered',
            'updated_at' => now()
        ]);

        // Créer une notification pour l'utilisateur
        Notification::create([
            'user_id' => $ticket->user_id,
            'type' => 'support_ticket_reply',
            'title' => 'Nouvelle réponse à votre ticket #' . $ticket->ticket_number,
            'message' => 'L\'administrateur a répondu à votre demande de support.',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'ticket_subject' => $ticket->subject,
                'message_preview' => substr($data['message'], 0, 100),
                'admin_name' => auth('admin')->user()->name ?? 'Administrateur'
            ],
            'is_read' => false
        ]);

        // Envoyer l'email à l'utilisateur
        try {
            Mail::to($ticket->user->email)
                ->send(new SupportTicketReplyMail($ticket, $data['message'], $ticket->user));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email support: ' . $e->getMessage());
            // On continue même si l'email échoue
        }

        return back()->with('success', 'Réponse envoyée au client avec notification.');
    }

    /**
     * Fermer un ticket
     */
    public function close($id)
    {
        $ticket = SupportTicket::with('user')->findOrFail($id);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth('admin')->id()
        ]);

        // Notification de fermeture
        Notification::create([
            'user_id' => $ticket->user_id,
            'type' => 'support_ticket_closed',
            'title' => 'Ticket #' . $ticket->ticket_number . ' fermé',
            'message' => 'Votre ticket de support a été fermé.',
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number
            ],
            'is_read' => false
        ]);

        return back()->with('success', 'Ticket fermé avec succès.');
    }

    /**
     * Rouvrir un ticket
     */
    public function reopen($id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null
        ]);

        return back()->with('success', 'Ticket rouvert avec succès.');
    }
}
