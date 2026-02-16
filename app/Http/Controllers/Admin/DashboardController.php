<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\FundingRequest;
use App\Models\SupportTicket;
use App\Models\Training;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Périodes de référence pour les tendances
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // ==================== STATISTIQUES GLOBALES ====================

        $stats = [
            // Utilisateurs
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'new_this_month' => User::where('created_at', '>=', $startOfMonth)->count(),
                'new_last_month' => User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count(),
                'particuliers' => User::where('member_type', 'particulier')->count(),
                'entreprises' => User::where('member_type', 'entreprise')->count(),
                'verified' => User::where('is_verified', true)->count(),
            ],

            // Transactions
            'transactions' => [
                'total' => Transaction::count(),
                'pending' => Transaction::where('status', 'pending')->count(),
                'completed_this_month' => Transaction::where('status', 'completed')
                    ->where('updated_at', '>=', $startOfMonth)
                    ->count(),
                'total_amount_this_month' => Transaction::where('status', 'completed')
                    ->where('updated_at', '>=', $startOfMonth)
                    ->sum('amount') ?? 0,
            ],

            // Demandes de financement (utilisation correcte des scopes du modèle)
            'funding_requests' => [
                'total' => FundingRequest::count(),
                'pending' => FundingRequest::pending()->count(), // Scope existant
                'needs_payment' => FundingRequest::needsPayment()->count(), // Scope existant
                'pending_transfer' => FundingRequest::pendingTransfer()->count(), // Scope existant
                'by_status' => FundingRequest::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
            ],

            // Documents - CORRECTION : utilisation du bon modèle et statuts
            'documents' => [
                'pending_validation' => Document::pending()->count(), // Scope existant
                'validated_this_month' => Document::validated()
                    ->where('validated_at', '>=', $startOfMonth)
                    ->count(),
                'rejected' => Document::rejected()->count(), // Scope existant
                'expired' => Document::expired()->count(), // Scope existant
                'profile_pending' => Document::profileDocuments()->pending()->count(),
                'funding_pending' => Document::fundingDocuments()->pending()->count(),
            ],

            // Formations
            'trainings' => [
                'total' => Training::count(),
                'active' => Training::where('is_active', true)->count(),
                'enrollments_this_month' => DB::table('training_user')
                    ->where('enrolled_at', '>=', $startOfMonth)
                    ->count(),
            ],

            // Tickets support
            'support_tickets' => [
                'open' => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved_this_month' => SupportTicket::where('status', 'resolved')
                    ->where('resolved_at', '>=', $startOfMonth)
                    ->count(),
                'unassigned' => SupportTicket::whereNull('assigned_to')
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count(),
            ],
        ];

        // ==================== DONNÉES RÉCENTES (avec eager loading) ====================

        $recentUsers = User::with(['wallet', 'fundingRequests' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'member_type' => $user->member_type,
                'member_type_label' => $user->member_type === 'particulier' ? 'Particulier' : 'Entreprise',
                'is_verified' => $user->is_verified,
                'created_at' => $user->created_at,
                'has_wallet' => $user->wallet !== null,
                'last_funding_request' => $user->fundingRequests->first()?->title,
            ]);

        $recentTransactions = Transaction::with(['wallet.user'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($tx) => [
                'id' => $tx->id,
                'amount' => $tx->amount,
                'amount_formatted' => number_format($tx->amount, 0, ',', ' ') . ' FCFA',
                'status' => $tx->status,
                'type' => $tx->type,
                'user_name' => $tx->wallet?->user?->full_name ?? 'N/A',
                'created_at' => $tx->created_at,
            ]);

        $recentTickets = SupportTicket::with(['user', 'assignee'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($ticket) => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'category_label' => $ticket->category_label,
                'priority' => $ticket->priority,
                'priority_badge' => $ticket->priority_badge,
                'status_badge' => $ticket->status_badge,
                'user_name' => $ticket->user?->full_name,
                'assigned_to_name' => $ticket->assignee?->full_name,
                'created_at' => $ticket->created_at,
                'can_be_replied' => $ticket->canBeReplied(),
            ]);

        // ==================== ALERTES ET ACTIONS REQUISES ====================

        $alerts = [
            'urgent_tickets' => SupportTicket::where('priority', 'urgent')
                ->where('status', '!=', 'closed')
                ->count(),
            'funding_ready_for_transfer' => FundingRequest::readyForTransfer()->count(),
            'documents_expiring_soon' => Document::whereNotNull('expiry_date')
                ->where('expiry_date', '<=', $now->copy()->addDays(30))
                ->where('expiry_date', '>=', $now)
                ->where('is_expired', false)
                ->count(),
            'users_incomplete_profile' => User::where(function($q) {
                    $q->whereNull('first_name')
                      ->orWhereNull('last_name')
                      ->orWhereNull('phone');
                })
                ->where('created_at', '<=', $now->copy()->subDays(7))
                ->count(),
        ];

        // ==================== DONNÉES POUR GRAPHIQUES ====================

        $chartData = [
            'funding_by_month' => FundingRequest::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count'),
                    DB::raw('sum(amount_requested) as total_amount')
                )
                ->where('created_at', '>=', $now->copy()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),

            'users_by_month' => User::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', $now->copy()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentTransactions',
            'recentTickets',
            'alerts',
            'chartData'
        ));
    }
}
