<?php

namespace App\Http\Controllers\Client;

use App\Models\FundingType;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\FundingPayment;
use App\Models\FundingRequest;
use App\Models\FundingDocument;
use Illuminate\Support\Facades\DB;
use App\Models\MobilePaymentConfig;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestFundingController extends Controller
{
    public function index()
    {
        $requests = FundingRequest::where('user_id', Auth::id())
            ->with(['fundingType', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('client.requests.index', compact('requests'));
    }

    public function create()
    {
        $types = FundingType::where('is_active', true)->get();
        return view('client.requests.create', compact('types'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $isPredefined = $request->has('funding_type_id') && $request->funding_type_id;

        // Validation : amount et duration sont toujours requis maintenant
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'amount_requested' => 'required|numeric|min:1000',
            'duration' => 'required|integer|min:6|max:60',
        ];

        if ($isPredefined) {
            $rules['funding_type_id'] = 'required|exists:funding_types,id';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($isPredefined) {
                $type = FundingType::find($validated['funding_type_id']);
                $fee = $type->registration_fee ?? 0;
                $status = 'pending_payment';
                $isPredefinedBool = true;

                Log::info('Création demande prédéfinie', [
                    'type_id' => $type->id,
                    'amount' => $validated['amount_requested'],
                    'duration' => $validated['duration'],
                    'fee' => $fee
                ]);
            } else {
                $fee = null;
                $status = 'submitted';
                $isPredefinedBool = false;
            }

            $fundingRequest = FundingRequest::create([
                'user_id' => $user->id,
                'request_number' => $this->generateRequestNumber(),
                'funding_type_id' => $isPredefinedBool ? $type->id : null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $isPredefinedBool ? ($type->category ?? 'autre') : 'custom',
                'is_predefined' => $isPredefinedBool,
                'amount_requested' => $validated['amount_requested'],
                'duration' => $validated['duration'],
                'expected_payment' => $fee,
                'status' => $status,
                'local_committee_country' => $user->country,
                'submitted_at' => now(),
                'project_location' => $user->city . ', ' . $user->country,
                'expected_jobs' => 0,
            ]);

            // Création du paiement uniquement pour les prédéfinis
            if ($isPredefinedBool) {
                $payment = $this->createPaymentOrder($fundingRequest, $fee, $user);

                // Notification utilisateur
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'payment_required',
                    'title' => 'Paiement requis - Motif: ' . $payment->payment_motif,
                    'message' => "Frais d'inscription: " . number_format($fee, 0, ',', ' ') . " FCFA pour la demande #{$fundingRequest->request_number}",
                    'data' => [
                        'funding_request_id' => $fundingRequest->id,
                        'payment_id' => $payment->id,
                        'motif' => $payment->payment_motif,
                        'amount' => $fee
                    ],
                ]);

                DB::commit();

                return redirect()->route('client.requests.show', $fundingRequest->id)
                    ->with('payment_required', [
                        'message' => "Veuillez payer les frais d'inscription de " . number_format($fee, 0, ',', ' ') . " FCFA pour finaliser votre demande.",
                        'motif' => $payment->payment_motif,
                        'amount' => $fee,
                        'payment_url' => route('client.requests.payment', $fundingRequest->id)
                    ]);
            } else {
                // Demande personnalisée
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'funding_request',
                    'title' => 'Demande soumise',
                    'message' => "Votre demande personnalisée #{$fundingRequest->request_number} est en cours d'examen par nos équipes.",
                    'data' => ['funding_request_id' => $fundingRequest->id],
                ]);

                DB::commit();

                // ✅ Redirection vers show avec message de succès
                return redirect()->route('client.requests.show', $fundingRequest->id)
                    ->with('success', 'Demande soumise avec succès. Elle sera examinée par nos équipes sous 24-48h.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création demande financement: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())->withInput();
        }
    }
    private function generateRequestNumber()
    {
        $prefix = 'BHDM-' . date('Ymd');
        $last = FundingRequest::where('request_number', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
        $num = $last ? intval(substr($last->request_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function createPaymentOrder($fundingRequest, $amount, $user)
    {
        // Générer motif unique à 4 chiffres
        do {
            $motif = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (FundingPayment::where('payment_motif', $motif)->where('status', 'pending')->exists());

        // Mettre à jour la demande avec le motif
        $fundingRequest->update(['payment_motif' => $motif]);

        return FundingPayment::create([
            'funding_request_id' => $fundingRequest->id,
            'payment_number' => 'PAY-' . time(),
            'payment_motif' => $motif,
            'amount' => $amount,
            'type' => 'registration',
            'status' => 'pending',
            'phone_number' => $user->phone,
            'country' => $user->country,
            'payment_date' => now(), // ✅ AJOUTÉ : obligatoire selon votre migration
            'payment_method' => 'mobile_money',
        ]);
    }

    public function show($id)
    {
        $request = FundingRequest::where('user_id', Auth::id())
            ->with(['fundingType', 'payments', 'documents'])
            ->findOrFail($id);
        return view('client.requests.show', compact('request'));
    }

    /**
     * Affiche la page de paiement
     */
    public function paymentPage($id)
    {
        $user = Auth::user();

        // Récupérer la demande avec vérification de propriété
        $fundingRequest = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending_payment', 'validated'])
            ->with(['fundingType', 'payments' => function ($q) {
                $q->where('status', 'pending')->latest();
            }])
            ->findOrFail($id);

        // Si status validated (custom validé par admin) mais pas de paiement créé
        if ($fundingRequest->status === 'validated' && $fundingRequest->payments->isEmpty()) {
            // Créer le paiement pour les demandes custom validées
            $this->createPaymentOrder($fundingRequest, $fundingRequest->expected_payment, $user);
            $fundingRequest->refresh();
        }

        $payment = $fundingRequest->payments->first();

        if (!$payment) {
            return redirect()->route('client.requests.show', $id)
                ->with('error', 'Aucun paiement en attente pour cette demande.');
        }

        // Opérateurs mobiles disponibles selon le pays
        $operators = MobilePaymentConfig::where('country', $user->country)
            ->where('is_active', true)
            ->get();

        return view('client.requests.payment', compact(
            'fundingRequest',
            'payment',
            'operators',
            'user'
        ));
    }

    /**
     * Confirme le paiement (après saisie du numéro et opérateur)
     */
    public function confirmPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'phone_used' => 'required|string|regex:/^[0-9]{8,12}$/',
            'operator_id' => 'required|exists:mobile_payment_configs,id',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();

        $fundingRequest = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending_payment', 'validated'])
            ->findOrFail($id);

        $payment = $fundingRequest->payments()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return back()->with('error', 'Aucun paiement en attente trouvé.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le paiement
            $payment->update([
                'phone_number' => $validated['phone_used'],
                'mobile_operator' => $validated['operator_id'], // ou operator_id selon votre DB
                'transaction_id' => $validated['transaction_id'],
                'status' => 'processing',
                'confirmed_by_user_at' => now(),
            ]);

            // Mettre à jour la demande
            $fundingRequest->update([
                'status' => 'payment_verification'
            ]);

            // Notification admin
            Notification::create([
                'user_id' => 1, // Admin ID - adaptez selon votre config
                'type' => 'payment_to_verify',
                'title' => 'Paiement à vérifier - Motif: ' . $payment->payment_motif,
                'message' => "Demande #{$fundingRequest->request_number} - {$payment->amount} FCFA par {$user->name}",
                'data' => [
                    'funding_request_id' => $fundingRequest->id,
                    'payment_id' => $payment->id,
                    'motif' => $payment->payment_motif
                ]
            ]);

            DB::commit();

            return redirect()->route('client.requests.show', $id)
                ->with('success', 'Paiement signalé avec succès. Nous vérifions votre transaction sous 24h.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la confirmation: ' . $e->getMessage());
        }
    }

    public function uploadDocuments(Request $request, $id)
    {
        $fundingRequest = FundingRequest::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->findOrFail($id);

        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'document_types.*' => 'required|string',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $key => $file) {
                $path = $file->store('funding_documents/' . $fundingRequest->id, 'public');
                FundingDocument::create([
                    'funding_request_id' => $fundingRequest->id,
                    'document_type' => $request->document_types[$key],
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'status' => 'pending',
                ]);
            }
        }

        $fundingRequest->update(['status' => 'documents_pending']);

        return back()->with('success', 'Documents uploadés avec succès.');
    }

    public function initiateTransfer(Request $request, $id)
    {
        $fundingRequest = FundingRequest::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->findOrFail($id);

        $urgent = $request->has('urgent');
        $extraFee = $urgent ? 1000 : 0; // Frais urgent

        if ($extraFee > 0) {
            FundingPayment::create([
                'funding_request_id' => $fundingRequest->id,
                'payment_number' => 'FEE-' . time(),
                'amount' => $extraFee,
                'type' => 'urgent_fee',
                'status' => 'pending',
                'phone_number' => Auth::user()->phone,
            ]);

            return redirect()->route('client.requests.payment', $id)
                ->with('info', 'Veuillez payer les frais de transfert urgent.');
        }

        $fundingRequest->update([
            'status' => 'transfer_initiated',
            'transfer_initiated_at' => now(),
        ]);

        return back()->with('success', 'Transfert initié. Les fonds seront disponibles sous 24-48h.');
    }
}
