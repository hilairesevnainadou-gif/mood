<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs avec filtres et statistiques
     */
    public function index(Request $request)
    {
        // Récupération des paramètres de recherche
        $search = $request->input('search');
        $status = $request->input('status');
        $sort = $request->input('sort', 'recent');

        // Construction de la requête
        $query = User::query();

        // Recherche textuelle
        if ($search) {
            $searchTerm = '%' . trim($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", [$searchTerm]);
            });
        }

        // Filtre par statut
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Tri
        switch ($sort) {
            case 'name':
                $query->orderBy('last_name')->orderBy('first_name');
                break;
            case 'name_desc':
                $query->orderByDesc('last_name')->orderByDesc('first_name');
                break;
            case 'recent':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        // Pagination
        $users = $query->paginate(15)->withQueryString();

        // Statistiques pour la barre de stats
        $stats = $this->getUserStats();

        return view('admin.users.index', array_merge(compact('users', 'search', 'status', 'sort'), $stats));
    }

    /**
     * Calcule les statistiques des utilisateurs
     */
    private function getUserStats(): array
    {
        return [
            'activeCount' => User::where('is_active', true)->count(),
            'inactiveCount' => User::where('is_active', false)->count(),
            'newThisMonth' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Affiche les détails d'un utilisateur
     */
public function show($id)
{
    $user = User::with([
        'wallet',
        'documents' => function ($query) {
            $query->latest()->limit(10);
        },
        'transactions' => function ($query) {
            $query->latest()->limit(10);
        },
        'supportTickets' => function ($query) {
            $query->latest()->limit(10);
        },
        'fundingRequests' => function ($query) {
            $query->latest()->limit(10);
        }
    ])->findOrFail($id);

    // Statistiques additionnelles pour la vue détail
    $stats = [
        'wallet_balance' => $user->wallet ? $user->wallet->balance : 0,
        'wallet_currency' => $user->wallet ? $user->wallet->currency : 'XOF',
        'wallet_status' => $user->wallet ? $user->wallet->status : 'inactive',
        
        // CORRECTION : Préfixer 'status' avec 'transactions.'
        'total_transactions_count' => $user->transactions()->count(),
        'completed_transactions_count' => $user->transactions()->where('transactions.status', 'completed')->count(),
        'pending_transactions_count' => $user->transactions()->where('transactions.status', 'pending')->count(),
        'failed_transactions_count' => $user->transactions()->where('transactions.status', 'failed')->count(),
        'cancelled_transactions_count' => $user->transactions()->where('transactions.status', 'cancelled')->count(),
        
        // CORRECTION : Préfixer 'type' avec 'transactions.' (si ambigu)
        'deposits_count' => $user->transactions()->where('transactions.type', 'credit')->count(),
        'withdrawals_count' => $user->transactions()->where('transactions.type', 'debit')->count(),
        'transfers_count' => $user->transactions()->where('transactions.type', 'transfer')->count(),
        'payments_count' => $user->transactions()->where('transactions.type', 'payment')->count(),
        
        // Montants totaux
        'total_transactions_amount' => $user->transactions()->sum('amount'),
        'total_completed_amount' => $user->transactions()
            ->where('transactions.status', 'completed')
            ->sum('amount'),
        
        // Documents
        'documents_validated' => $user->documents()->where('status', 'validated')->count(),
        'documents_pending' => $user->documents()->where('status', 'pending')->count(),
        
        // Tickets et financements
        'open_tickets' => $user->supportTickets()->where('status', 'open')->count(),
        'funding_count' => $user->fundingRequests()->count(),
    ];

    return view('admin.users.show', compact('user', 'stats'));
}

    /**
     * Met à jour les informations d'un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:30'],
            'member_status' => ['nullable', 'string', 'max:100'],
            'member_type' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_verified' => ['boolean'],
        ]);

        // Gestion du nom complet si nécessaire
        if (isset($data['first_name']) && isset($data['last_name'])) {
            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        }

        $user->update($data);

        return back()->with('success', 'Profil utilisateur mis à jour avec succès.');
    }

    /**
     * Active un utilisateur
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            return back()->with('info', 'Cet utilisateur est déjà actif.');
        }

        $user->update(['is_active' => true]);

        // Optionnel : Envoyer un email de notification
        // $user->notify(new AccountActivatedNotification());

        return back()->with('success', 'Utilisateur activé avec succès.');
    }

    /**
     * Désactive un utilisateur
     */
    // Dans UserController::deactivate()
    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return back()->with('info', 'Cet utilisateur est déjà inactif.');
        }

        // Utilisation de la nouvelle méthode
        if ($user->hasPendingOperations()) {
            return back()->with('warning', 'Impossible de désactiver : opérations en cours.');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'Utilisateur désactivé avec succès.');
    }

    /**
     * Vérifie si l'utilisateur a des opérations en cours
     */
    private function hasPendingOperations(User $user): bool
    {
        return $user->fundingRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->exists() ||
            $user->transactions()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Exporte la liste des utilisateurs (CSV/Excel)
     */
    public function export(Request $request)
    {
        $users = User::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, ['ID', 'Nom', 'Email', 'Téléphone', 'Statut', 'Date d\'inscription']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->full_name,
                    $user->email,
                    $user->phone,
                    $user->is_active ? 'Actif' : 'Inactif',
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Supprime un utilisateur (avec vérifications)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Empêcher la suppression si opérations existantes
        if ($user->transactions()->exists() || $user->fundingRequests()->exists()) {
            return back()->with('error', 'Impossible de supprimer : historique présent.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé définitivement.');
    }

    /**
     * Méthode helper pour la vue (génère les initiales)
     */
    public function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: 'U';
    }
}
