<?php

namespace App\Http\Controllers\Client;

use App\Models\FundingType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\FundingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestFundingController extends Controller
{
    /**
     * Affiche la liste des demandes de financement
     */
    public function index()
    {
        // CORRECTION: Retiré 'payments' du with()
        $requests = FundingRequest::where('user_id', Auth::id())
            ->with('fundingType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.requests.index', compact('requests'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $types = FundingType::where('is_active', true)->get();
        return view('client.requests.create', compact('types'));
    }

    /**
     * Stocke une nouvelle demande
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $isPredefined = $request->has('funding_type_id') && $request->funding_type_id;
        $isCustom = $request->input('is_custom') == '1';

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'amount_requested' => 'required|numeric|min:1000',
            'duration' => 'required|integer|min:6|max:60',
        ];

        if ($isPredefined) {
            $rules['funding_type_id'] = 'required|exists:funding_types,id';
            $rules['kkiapay_transaction'] = 'required|string';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($isPredefined) {
                $kkiapayData = $request->input('kkiapay_transaction');
                $kkiapayTransaction = json_decode($kkiapayData, true);

                if (!$kkiapayTransaction || !isset($kkiapayTransaction['transactionId'])) {
                    return back()->with('error', 'Données de paiement invalides.')->withInput();
                }

                // Vérifier doublon Kkiapay
                $existing = FundingRequest::where('kkiapay_transaction_id', $kkiapayTransaction['transactionId'])->first();
                if ($existing) {
                    return back()->with('error', 'Cette transaction Kkiapay a déjà été utilisée.')->withInput();
                }

                $type = FundingType::find($validated['funding_type_id']);
                $fee = $type->registration_fee ?? 0;

                $fundingRequest = FundingRequest::create([
                    'user_id' => $user->id,
                    'request_number' => FundingRequest::generateRequestNumber(),
                    'funding_type_id' => $type->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'type' => $type->category ?? 'autre',
                    'is_predefined' => true,
                    'amount_requested' => $validated['amount_requested'],
                    'duration' => $validated['duration'],
                    'expected_payment' => $fee,
                    'status' => 'paid',
                    'local_committee_country' => $user->country,
                    'submitted_at' => now(),
                    'paid_at' => now(),
                    'project_location' => $user->city . ', ' . $user->country,
                    'expected_jobs' => 0,
                    'kkiapay_transaction_id' => $kkiapayTransaction['transactionId'],
                    'kkiapay_phone' => $this->formatPhoneNumber($kkiapayTransaction['phoneNumber'] ?? $user->phone),
                    'kkiapay_amount_paid' => $kkiapayTransaction['amount'] ?? $fee,
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'funding_request',
                    'title' => 'Demande confirmée',
                    'message' => "Votre demande #{$fundingRequest->request_number} a été confirmée. Transaction Kkiapay: {$kkiapayTransaction['transactionId']}",
                    'data' => ['funding_request_id' => $fundingRequest->id],
                ]);

                DB::commit();

                return redirect()->route('client.requests.show', $fundingRequest->id)
                    ->with('success', 'Demande créée et payée avec succès ! Référence: ' . $kkiapayTransaction['transactionId']);
            } else {
                // Demande personnalisée
                $fundingRequest = FundingRequest::create([
                    'user_id' => $user->id,
                    'request_number' => FundingRequest::generateRequestNumber(),
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'type' => 'custom',
                    'is_predefined' => false,
                    'amount_requested' => $validated['amount_requested'],
                    'duration' => $validated['duration'],
                    'status' => 'submitted',
                    'local_committee_country' => $user->country,
                    'submitted_at' => now(),
                    'project_location' => $user->city . ', ' . $user->country,
                    'expected_jobs' => 0,
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'funding_request',
                    'title' => 'Demande soumise',
                    'message' => "Votre demande #{$fundingRequest->request_number} est en cours d'examen.",
                    'data' => ['funding_request_id' => $fundingRequest->id],
                ]);

                DB::commit();

                return redirect()->route('client.requests.show', $fundingRequest->id)
                    ->with('success', 'Demande soumise avec succès. Examen sous 24-48h.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création demande: ' . $e->getMessage());
            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Affiche une demande spécifique
     */
    public function show($id)
    {
        // CORRECTION: Retiré 'payments' du with()
        $request = FundingRequest::where('user_id', Auth::id())
            ->with('fundingType')
            ->findOrFail($id);

        return view('client.requests.show', compact('request'));
    }

    /**
     * Page de paiement pour demandes personnalisées validées
     */
    public function paymentPage($id)
    {
        $user = Auth::user();

        // CORRECTION: Retiré 'payments' du with()
        $fundingRequest = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['validated', 'pending_payment'])
            ->findOrFail($id);

        if (!$fundingRequest->expected_payment || $fundingRequest->expected_payment <= 0) {
            return redirect()->route('client.requests.show', $id)
                ->with('error', 'Aucun paiement requis pour cette demande.');
        }

        // CORRECTION: Vérification via kkiapay_transaction_id au lieu de payments()
        if ($fundingRequest->kkiapay_transaction_id) {
            return redirect()->route('client.requests.show', $id)
                ->with('info', 'Déjà payée.');
        }

        return view('client.requests.payment_custom', compact('fundingRequest', 'user'));
    }
    /**
     * Annuler une demande
     */
    public function cancel($id)
    {
        $fundingRequest = FundingRequest::where('user_id', Auth::id())
            ->whereIn('status', ['draft', 'submitted', 'validated'])
            ->findOrFail($id);

        try {
            $fundingRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'type' => 'funding_cancelled',
                'title' => 'Demande annulée',
                'message' => "Votre demande #{$fundingRequest->request_number} a été annulée.",
                'data' => ['funding_request_id' => $fundingRequest->id],
            ]);

            return redirect()->route('client.requests.index')
                ->with('success', 'Demande annulée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur annulation demande: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'annulation.');
        }
    }
    /**
     * Traiter le paiement Kkiapay pour demande personnalisée
     */
    public function processCustomPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'kkiapay_transaction' => 'required|string',
        ]);

        $user = Auth::user();

        $fundingRequest = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['validated', 'pending_payment'])
            ->findOrFail($id);

        $kkiapayTransaction = json_decode($validated['kkiapay_transaction'], true);

        if (!$kkiapayTransaction || !isset($kkiapayTransaction['transactionId'])) {
            return back()->with('error', 'Données invalides.');
        }

        // Vérifier doublon
        $existing = FundingRequest::where('kkiapay_transaction_id', $kkiapayTransaction['transactionId'])->first();
        if ($existing) {
            return back()->with('error', 'Transaction déjà utilisée.');
        }

        DB::beginTransaction();
        try {
            $fundingRequest->update([
                'status' => 'paid',
                'paid_at' => now(),
                'kkiapay_transaction_id' => $kkiapayTransaction['transactionId'],
                'kkiapay_phone' => $this->formatPhoneNumber($kkiapayTransaction['phoneNumber'] ?? $user->phone),
                'kkiapay_amount_paid' => $kkiapayTransaction['amount'] ?? $fundingRequest->expected_payment,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'payment_success',
                'title' => 'Paiement confirmé',
                'message' => "Paiement reçu pour #{$fundingRequest->request_number}. Ref: {$kkiapayTransaction['transactionId']}",
                'data' => ['funding_request_id' => $fundingRequest->id],
            ]);

            DB::commit();

            return redirect()->route('client.requests.show', $id)
                ->with('success', 'Paiement effectué ! Ref: ' . $kkiapayTransaction['transactionId']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur paiement: ' . $e->getMessage());
            return back()->with('error', 'Erreur traitement paiement.');
        }
    }

    /**
     * Callback Kkiapay (webhook)
     */
    public function kkiapayCallback(Request $request)
    {
        Log::info('Kkiapay callback', $request->all());

        try {
            $transactionId = $request->input('transactionId');

            if ($request->input('status') === 'success' && $transactionId) {
                $fundingRequest = FundingRequest::where('kkiapay_transaction_id', $transactionId)
                    ->where('status', '!=', 'paid')
                    ->first();

                if ($fundingRequest) {
                    $fundingRequest->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }

            return response()->json(['status' => 'received']);
        } catch (\Exception $e) {
            Log::error('Erreur callback: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Formate le numéro de téléphone
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) return null;

        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (str_starts_with($phone, '+229')) return $phone;
        if (str_starts_with($phone, '00229')) return '+' . substr($phone, 2);
        if (preg_match('/^[967]\d{7}$/', $phone)) return '+229' . $phone;

        return $phone;
    }
}
