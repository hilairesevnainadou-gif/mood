<?php
// app/Http/Controllers/Admin/FundingValidationController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use App\Models\FundingPayment;
use App\Models\Notification;
use Illuminate\Http\Request;

class FundingValidationController extends Controller {

    // Liste des demandes personnalisées à valider (prix)
    public function pendingValidation() {
        $requests = FundingRequest::with('user')
            ->where('is_predefined', false)
            ->where('status', 'submitted')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.funding.pending-validation', compact('requests'));
    }

    // Définir le prix pour une demande personnalisée
    public function setPrice(Request $request, $id) {
        $request->validate([
            'approved_amount' => 'required|numeric|min:1000',
            'registration_fee' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:6',
            'comments' => 'nullable|string',
        ]);

        $fundingRequest = FundingRequest::findOrFail($id);

        if ($fundingRequest->is_predefined || $fundingRequest->status !== 'submitted') {
            return back()->with('error', 'Opération non autorisée.');
        }

        $fundingRequest->update([
            'status' => 'validated',
            'amount_approved' => $request->approved_amount,
            'expected_payment' => $request->registration_fee,
            'duration' => $request->duration,
            'admin_validation_notes' => $request->comments,
            'validated_at' => now(),
            'validated_by' => auth('admin')->id(),
        ]);

        // Notification au client
        Notification::create([
            'user_id' => $fundingRequest->user_id,
            'type' => 'request_validated',
            'title' => 'Votre demande est approuvée',
            'message' => "Montant approuvé: " . number_format($request->approved_amount, 0, ',', ' ') .
                        " FCFA. Frais d'inscription: " . number_format($request->registration_fee, 0, ',', ' ') .
                        " FCFA. Cliquez pour payer.",
            'data' => ['request_id' => $fundingRequest->id],
        ]);

        return back()->with('success', 'Demande validée et prix fixé. Le client a été notifié.');
    }

    // Liste des paiements à vérifier
    public function pendingPayments() {
        $payments = FundingPayment::with(['fundingRequest.user'])
            ->where('status', 'processing')
            ->orderBy('confirmed_by_user_at', 'asc')
            ->get();

        return view('admin.funding.pending-payments', compact('payments'));
    }

    // Valider ou rejeter un paiement
    public function verifyPayment(Request $request, $paymentId) {
        $request->validate([
            'action' => 'required|in:validate,reject',
            'admin_notes' => 'nullable|string',
        ]);

        $payment = FundingPayment::with('fundingRequest')->findOrFail($paymentId);

        if ($request->action === 'validate') {
            $payment->update([
                'status' => 'completed',
                'verified_by' => auth('admin')->id(),
                'verified_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Mettre à jour la demande
            $payment->fundingRequest->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Notifier client
            Notification::create([
                'user_id' => $payment->fundingRequest->user_id,
                'type' => 'payment_confirmed',
                'title' => 'Paiement confirmé - Documents requis',
                'message' => 'Votre paiement de ' . number_format($payment->amount, 0, ',', ' ') .
                            ' FCFA est confirmé. Veuillez maintenant télécharger les documents complémentaires.',
                'data' => ['request_id' => $payment->fundingRequest->id],
            ]);

            return back()->with('success', 'Paiement validé. Le client peut maintenant fournir les documents.');
        } else {
            $payment->update([
                'status' => 'failed',
                'admin_notes' => $request->admin_notes,
                'verified_by' => auth('admin')->id(),
                'verified_at' => now(),
            ]);

            // Remettre la demande en attente de paiement
            $payment->fundingRequest->update(['status' => 'pending_payment']);

            return back()->with('error', 'Paiement rejeté. Le client doit recommencer.');
        }
    }
}
