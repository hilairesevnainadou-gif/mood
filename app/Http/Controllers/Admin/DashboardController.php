<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\FundingRequest;
use App\Models\SupportTicket;
use App\Models\Training;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'transactions' => Transaction::count(),
            'funding_requests' => FundingRequest::count(),
            'documents_pending' => Document::where('status', 'pending')->count(),
            'trainings' => Training::count(),
            'support_tickets' => SupportTicket::where('status', 'open')->count(),
        ];

        $recentUsers = User::orderByDesc('created_at')->take(5)->get();
        $recentTransactions = Transaction::orderByDesc('created_at')->take(5)->get();
        $recentTickets = SupportTicket::orderByDesc('created_at')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTransactions', 'recentTickets'));
    }
}
