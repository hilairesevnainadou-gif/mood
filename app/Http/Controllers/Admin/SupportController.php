<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

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
        $ticket = SupportTicket::findOrFail($id);

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $ticket->addMessage($data['message'], auth('admin')->id(), true);

        return back()->with('success', 'Réponse envoyée au client.');
    }
}
