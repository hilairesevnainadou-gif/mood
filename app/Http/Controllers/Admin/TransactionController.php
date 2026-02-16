<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * Affiche la liste des transactions avec filtres et statistiques
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = Transaction::with(['wallet.user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->orderByDesc('created_at')
                              ->paginate(15)
                              ->withQueryString();

        $stats = $this->getTransactionStats();

        return view('admin.transactions.index', array_merge(
            compact('transactions', 'search', 'type', 'status'),
            $stats
        ));
    }

    /**
     * Calcule les statistiques des transactions
     */
    private function getTransactionStats(): array
    {
        return [
            'total_count' => Transaction::count(),
            'pending_count' => Transaction::where('status', 'pending')->count(),
            'completed_count' => Transaction::where('status', 'completed')->count(),
            'total_amount' => Transaction::where('status', 'completed')->sum('amount'),
        ];
    }

    /**
     * Valide une transaction (retrait) - Débite définitivement le compte
     */
    public function validateTransaction(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $transaction = Transaction::with(['wallet.user'])->findOrFail($id);

        // Vérifier si c'est bien un retrait en attente
        if (!$transaction->isPending()) {
            return back()->with('error', 'Cette transaction ne peut pas être validée (statut: ' . $transaction->status . ')');
        }

        // Vérifier que c'est bien un débit/retrait
        if (!in_array($transaction->type, ['debit', 'withdrawal'])) {
            return back()->with('error', 'Seuls les retraits peuvent être validés ici.');
        }

        try {
            DB::beginTransaction();

            // La transaction passe en complétée (le solde a déjà été débité lors de la demande)
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'validated_by' => auth('admin')->id(),
                    'validated_at' => now()->toIso8601String(),
                    'admin_notes' => $request->input('admin_notes'),
                ]),
            ]);

            // Notification au client
            Notification::create([
                'user_id' => $transaction->wallet->user_id,
                'type' => 'transaction',
                'title' => 'Retrait validé',
                'message' => 'Votre demande de retrait de ' . $transaction->formatted_amount . ' a été validée et traitée.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => 'completed',
                    'amount' => $transaction->amount,
                ],
            ]);

            // Email au client
            $this->sendTransactionEmail(
                $transaction->wallet->user,
                $transaction,
                'validated',
                'Votre retrait a été validé'
            );

            DB::commit();

            return back()->with('success', 'Transaction validée avec succès. Le client a été notifié.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur validation transaction: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Rejette une transaction (retrait) - Rembourse le client
     */
    public function rejectTransaction(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        $transaction = Transaction::with(['wallet.user'])->findOrFail($id);

        // Vérifications
        if ($transaction->isCompleted()) {
            return back()->with('error', 'Impossible de rejeter une transaction déjà validée.');
        }

        if (in_array($transaction->status, ['cancelled', 'failed'])) {
            return back()->with('error', 'Cette transaction est déjà annulée ou échouée.');
        }

        // Vérifier que c'est bien un débit/retrait
        if (!in_array($transaction->type, ['debit', 'withdrawal'])) {
            return back()->with('error', 'Seuls les retraits peuvent être rejetés ici.');
        }

        try {
            DB::beginTransaction();

            $wallet = $transaction->wallet;
            $refundAmount = (float) $transaction->amount;
            $currentBalance = (float) $wallet->balance;

            // Rembourser le client (recréditer le solde)
            $wallet->balance = $currentBalance + $refundAmount;
            $wallet->save();

            // Mettre à jour la transaction comme échouée
            $transaction->update([
                'status' => 'failed',
                'completed_at' => now(),
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'rejected_by' => auth('admin')->id(),
                    'rejected_at' => now()->toIso8601String(),
                    'rejection_reason' => $request->input('rejection_reason'),
                    'refund_amount' => $refundAmount,
                    'refund_applied' => true,
                ]),
            ]);

            // Notification au client
            Notification::create([
                'user_id' => $wallet->user_id,
                'type' => 'transaction',
                'title' => 'Retrait rejeté',
                'message' => 'Votre demande de retrait de ' . $transaction->formatted_amount . ' a été rejetée. Le montant a été recrédité sur votre compte.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => 'failed',
                    'amount' => $transaction->amount,
                    'rejection_reason' => $request->input('rejection_reason'),
                    'refund_amount' => $refundAmount,
                    'new_balance' => $wallet->balance,
                ],
            ]);

            // Email au client avec la raison du rejet
            $this->sendTransactionEmail(
                $wallet->user,
                $transaction,
                'rejected',
                'Votre retrait a été rejeté',
                $request->input('rejection_reason')
            );

            DB::commit();

            return back()->with('success', 'Transaction rejetée. Le montant de ' . number_format($refundAmount, 0, ',', ' ') . ' FCFA a été recrédité au client.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet transaction: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email au client concernant sa transaction
     */
    private function sendTransactionEmail(User $user, Transaction $transaction, string $status, string $subject, ?string $reason = null)
    {
        try {
            $emailData = [
                'user' => $user,
                'transaction' => $transaction,
                'status' => $status,
                'subject' => $subject,
                'reason' => $reason,
            ];

            Mail::send('emails.transaction_status', $emailData, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                        ->subject($subject);
            });

            Log::info('Email transaction envoyé', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur envoi email transaction: ' . $e->getMessage());
            // Ne pas bloquer le processus si l'email échoue
        }
    }

    /**
     * Exporte les transactions en CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::with('wallet.user');

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transactions_' . now()->format('Y-m-d_H-i') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // BOM pour Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID',
                'Référence',
                'Type',
                'Montant',
                'Frais',
                'Total',
                'Statut',
                'Méthode',
                'Date création',
                'Date complétion',
                'Client',
                'Email client',
                'Wallet'
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->reference ?? $transaction->transaction_id,
                    $transaction->type_label,
                    $transaction->amount,
                    $transaction->fee ?? 0,
                    $transaction->total_amount,
                    $transaction->status_label,
                    $transaction->payment_method,
                    $transaction->created_at->format('d/m/Y H:i'),
                    $transaction->completed_at?->format('d/m/Y H:i') ?? '-',
                    $transaction->wallet?->user?->name ?? 'N/A',
                    $transaction->wallet?->user?->email ?? 'N/A',
                    $transaction->wallet?->wallet_number ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Affiche les détails d'une transaction
     */
    public function show($id)
    {
        $transaction = Transaction::with(['wallet.user'])->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Annuler une transaction en attente (alternative au rejet)
     */
    public function cancelTransaction(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:1000',
        ]);

        $transaction = Transaction::with(['wallet.user'])->findOrFail($id);

        if (!$transaction->isPending()) {
            return back()->with('error', 'Seules les transactions en attente peuvent être annulées.');
        }

        try {
            DB::beginTransaction();

            $wallet = $transaction->wallet;
            $refundAmount = (float) $transaction->amount;

            // Rembourser le client
            $wallet->balance += $refundAmount;
            $wallet->save();

            $transaction->update([
                'status' => 'cancelled',
                'completed_at' => now(),
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'cancelled_by' => auth('admin')->id(),
                    'cancelled_at' => now()->toIso8601String(),
                    'cancellation_reason' => $request->input('cancellation_reason'),
                    'refund_applied' => true,
                ]),
            ]);

            // Notification
            Notification::create([
                'user_id' => $wallet->user_id,
                'type' => 'transaction',
                'title' => 'Retrait annulé',
                'message' => 'Votre demande de retrait a été annulée par l\'administration.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => 'cancelled',
                    'reason' => $request->input('cancellation_reason'),
                    'refund_amount' => $refundAmount,
                ],
            ]);

            // Email
            $this->sendTransactionEmail(
                $wallet->user,
                $transaction,
                'cancelled',
                'Votre retrait a été annulé',
                $request->input('cancellation_reason')
            );

            DB::commit();

            return back()->with('success', 'Transaction annulée et remboursée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur annulation transaction: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation.');
        }
    }
}
