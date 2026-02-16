<?php
// app/Console/Commands/ProcessMonthlyRepayments.php

namespace App\Console\Commands;

use App\Models\FundingRepayment;
use App\Models\FundingRequest;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMonthlyRepayments extends Command
{
    protected $signature = 'repayments:process';
    protected $description = 'Traite les remboursements mensuels automatiques';

    public function handle()
    {
        $today = now()->startOfDay();

        // Récupérer les échéances d'aujourd'hui
        $dueRepayments = FundingRepayment::with(['fundingRequest.user.wallet'])
            ->whereDate('due_date', $today)
            ->where('status', 'pending')
            ->get();

        $this->info("{$dueRepayments->count()} échéances à traiter aujourd'hui.");

        foreach ($dueRepayments as $repayment) {
            try {
                DB::transaction(function () use ($repayment) {
                    $funding = $repayment->fundingRequest;
                    $user = $funding->user;
                    $wallet = $user->wallet;

                    if (!$wallet) {
                        throw new \Exception("Wallet non trouvé pour l'utilisateur {$user->id}");
                    }

                    $amount = $repayment->amount_due;

                    // Vérifier le solde
                    if ($wallet->balance < $amount) {
                        // Solde insuffisant - marquer comme retard
                        $repayment->update(['status' => 'late']);

                        Notification::create([
                            'user_id' => $user->id,
                            'type' => 'repayment',
                            'title' => 'Remboursement en retard',
                            'message' => "Votre mensualité de " . number_format($amount, 0, ',', ' ') .
                                        " FCFA n'a pas pu être prélevée. Solde insuffisant.",
                            'data' => ['repayment_id' => $repayment->id],
                        ]);

                        Log::warning("Solde insuffisant pour remboursement #{$repayment->id}");
                        return;
                    }

                    // Créer la transaction de débit
                    $transaction = Transaction::create([
                        'wallet_id' => $wallet->id,
                        'transaction_id' => (string) \Illuminate\Support\Str::uuid(),
                        'type' => 'debit',
                        'amount' => $amount,
                        'total_amount' => $amount,
                        'status' => 'completed',
                        'payment_method' => 'automatic_repayment',
                        'description' => 'Remboursement mensuel - ' . $funding->request_number,
                        'reference' => 'REP-' . $repayment->repayment_number,
                        'completed_at' => now(),
                    ]);

                    // Débiter le wallet
                    $wallet->decrement('balance', $amount);

                    // Marquer comme payé
                    $repayment->markAsPaid($amount, $transaction->id, 'automatic');

                    // Notification
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'repayment',
                        'title' => 'Remboursement effectué',
                        'message' => "Votre mensualité de " . number_format($amount, 0, ',', ' ') .
                                    " FCFA a été prélevée sur votre wallet.",
                        'data' => [
                            'repayment_id' => $repayment->id,
                            'transaction_id' => $transaction->id,
                        ],
                    ]);
                });

                $this->info("Remboursement #{$repayment->id} traité avec succès.");

            } catch (\Exception $e) {
                Log::error("Erreur traitement remboursement #{$repayment->id}: " . $e->getMessage());
                $this->error("Erreur remboursement #{$repayment->id}: " . $e->getMessage());
            }
        }

        // Vérifier les retards
        $lateRepayments = FundingRepayment::where('due_date', '<', $today)
            ->where('status', 'pending')
            ->get();

        foreach ($lateRepayments as $repayment) {
            $repayment->markAsLate();
        }

        $this->info("{$lateRepayments->count()} remboursements marqués en retard.");

        return Command::SUCCESS;
    }
}
