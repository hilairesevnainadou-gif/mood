@extends('layouts.client')

@section('title', 'Demande #' . $request->request_number)

@section('content')
<div class="request-detail-pro">
    {{-- Header --}}
    <div class="pro-header">
        <div class="pro-header-bg">
            <a href="{{ route('client.requests.index') }}" class="pro-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pro-header-content">
                <span class="pro-request-id">{{ $request->request_number }}</span>
                @php
                    $statusLabels = [
                        'submitted' => ['label' => 'Soumise', 'class' => 'badge-light'],
                        'pending_payment' => ['label' => 'Paiement', 'class' => 'badge-warning'],
                        'payment_verification' => ['label' => 'Vérification', 'class' => 'badge-info'],
                        'paid' => ['label' => 'Payée', 'class' => 'badge-success'],
                        'documents_pending' => ['label' => 'Documents', 'class' => 'badge-info'],
                        'validated' => ['label' => 'Validée', 'class' => 'badge-success'],
                        'approved' => ['label' => 'Approuvée', 'class' => 'badge-success'],
                        'transfer_initiated' => ['label' => 'Transfert', 'class' => 'badge-primary'],
                        'completed' => ['label' => 'Terminée', 'class' => 'badge-success'],
                        'rejected' => ['label' => 'Rejetée', 'class' => 'badge-danger'],
                    ];
                    $currentStatus = $statusLabels[$request->status] ?? ['label' => $request->status, 'class' => 'badge-secondary'];
                @endphp
                <span class="pro-status-badge {{ $currentStatus['class'] }}">{{ $currentStatus['label'] }}</span>
            </div>
        </div>
    </div>

    {{-- Étapes de progression --}}
    <div class="pro-steps-container">
        <div class="pro-steps-track">
            @php
                $steps = [
                    ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt'],
                    ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card'],
                    ['id' => 'documents', 'label' => 'Documents', 'icon' => 'fa-folder'],
                    ['id' => 'validation', 'label' => 'Validation', 'icon' => 'fa-check'],
                    ['id' => 'transfer', 'label' => 'Transfert', 'icon' => 'fa-exchange-alt'],
                ];

                $currentStepIndex = 0;
                if ($request->status === 'submitted') $currentStepIndex = 0;
                elseif (in_array($request->status, ['pending_payment', 'payment_verification'])) $currentStepIndex = 1;
                elseif (in_array($request->status, ['paid', 'documents_pending'])) $currentStepIndex = 2;
                elseif ($request->status === 'validated') $currentStepIndex = 3;
                elseif (in_array($request->status, ['approved', 'transfer_initiated', 'completed'])) $currentStepIndex = 4;
            @endphp

            @foreach($steps as $index => $step)
                @php
                    $isActive = $index <= $currentStepIndex;
                    $isCurrent = $index === $currentStepIndex;
                    $isRejected = $request->status === 'rejected' && $index === $currentStepIndex;
                @endphp
                <div class="pro-step {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }} {{ $isRejected ? 'rejected' : '' }}">
                    <div class="pro-step-icon">
                        <i class="fas {{ $isRejected ? 'fa-times' : $step['icon'] }}"></i>
                    </div>
                    <span class="pro-step-label">{{ $step['label'] }}</span>
                    @if($isCurrent && !$isRejected)
                        <div class="pro-step-indicator"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="pro-content">
        {{-- Message de session après création --}}
        @if(session('payment_required'))
        <div class="pro-alert pro-alert-urgent" id="paymentAlert">
            <div class="pro-alert-icon pulse">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="pro-alert-content">
                <h4>{{ session('payment_required')['message'] ?? 'Paiement requis' }}</h4>
                @if(isset(session('payment_required')['motif']))
                <div class="motif-display">
                    <span>Motif :</span>
                    <strong class="motif-code">{{ session('payment_required')['motif'] }}</strong>
                    <button onclick="copyMotif('{{ session('payment_required')['motif'] }}')" class="btn-copy">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                @endif
            </div>
            <a href="{{ session('payment_required')['payment_url'] ?? route('client.requests.payment', $request->id) }}" class="pro-btn-pay-now">
                <i class="fas fa-credit-card"></i> Payer maintenant
            </a>
        </div>
        @endif

        {{-- Carte Montant --}}
        <div class="pro-amount-card">
            <div class="pro-amount-label">Montant demandé</div>
            <div class="pro-amount-value">{{ number_format($request->amount_requested, 0, ',', ' ') }} FCFA</div>
            @if($request->expected_payment)
            <div class="pro-fee-row">
                <span>Frais d'inscription</span>
                <strong>{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</strong>
            </div>
            @endif
        </div>

        {{-- Section Informations --}}
        <div class="pro-section">
            <h3 class="pro-section-title">Informations</h3>
            <div class="pro-info-grid two-cols">
                <div class="pro-info-item">
                    <span class="pro-label">Type de demande</span>
                    <span class="pro-value">{{ $request->is_predefined ? 'Prédéfini' : 'Personnalisé' }}</span>
                </div>
                <div class="pro-info-item">
                    <span class="pro-label">Date de soumission</span>
                    <span class="pro-value">{{ $request->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="pro-info-item full">
                    <span class="pro-label">Objet du projet</span>
                    <span class="pro-value">{{ $request->title }}</span>
                </div>
            </div>
        </div>

        {{-- Alerte Paiement Global --}}
        @if(in_array($request->status, ['pending_payment', 'validated']))
        <div class="pro-alert pro-alert-payment">
            <div class="pro-alert-icon payment-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="pro-alert-content">
                <h4>Paiement requis</h4>
                <p>Montant à payer : <strong>{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</strong></p>
                @if($request->payment_motif)
                <div class="payment-motif-box">
                    <span>Motif :</span>
                    <code>{{ $request->payment_motif }}</code>
                </div>
                @endif
            </div>
            <a href="{{ route('client.requests.payment', $request->id) }}" class="pro-btn-action pulse-btn">
                <span>Effectuer</span>
                <span>le paiement</span>
            </a>
        </div>
        @endif

        {{-- Transfert Approuvé --}}
        @if($request->status === 'approved')
        <div class="pro-alert pro-alert-success">
            <div class="pro-alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="pro-alert-content">
                <h4>Demande approuvée !</h4>
                <p>Vos fonds sont prêts à être transférés</p>
            </div>
        </div>

        <div class="pro-transfer-options">
            <button onclick="showTransferNormal()" class="pro-btn-transfer">
                <span class="pro-transfer-title">Transfert standard</span>
                <span class="pro-transfer-subtitle">24-48h • Gratuit</span>
            </button>
            <button onclick="showTransferUrgent()" class="pro-btn-transfer urgent">
                <span class="pro-transfer-title"><i class="fas fa-bolt"></i> Transfert urgent</span>
                <span class="pro-transfer-subtitle">4h • +1 000 FCFA</span>
            </button>
        </div>
        @endif

        {{-- Documents --}}
        @if($request->requestDocuments && $request->requestDocuments->count() > 0)
        <div class="pro-section">
            <h3 class="pro-section-title">Documents fournis</h3>
            <div class="pro-docs-list">
                @foreach($request->requestDocuments as $doc)
                <div class="pro-doc-item">
                    <div class="pro-doc-icon {{ Str::contains($doc->original_name, ['.jpg', '.jpeg', '.png']) ? 'image' : 'pdf' }}">
                        <i class="fas {{ Str::contains($doc->original_name, ['.jpg', '.jpeg', '.png']) ? 'fa-file-image' : 'fa-file-pdf' }}"></i>
                    </div>
                    <div class="pro-doc-info">
                        <span class="pro-doc-name">{{ Str::limit($doc->original_name, 25) }}</span>
                        <span class="pro-doc-type">{{ $doc->document_type }}</span>
                    </div>
                    <span class="pro-doc-status status-{{ $doc->status }}"></span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- HISTORIQUE DES PAIEMENTS AVEC BOUTON PAYEMENT --}}
        @if($request->payments && $request->payments->count() > 0)
        <div class="pro-section">
            <h3 class="pro-section-title">Historique des paiements</h3>

            @foreach($request->payments as $payment)
                {{-- Si paiement en attente => carte cliquable entière --}}
                @if($payment->status === 'pending')
                <a href="{{ route('client.requests.payment', $request->id) }}" class="payment-link-wrapper">
                    <div class="pro-payment-history payment-pending">
                        <div class="payment-pending-badge">
                            <i class="fas fa-exclamation-circle"></i> En attente de paiement
                        </div>

                        <div class="pro-payment-main">
                            <div class="pro-payment-info">
                                <span class="pro-payment-ref">{{ $payment->payment_number }}</span>
                                @if($payment->payment_motif)
                                <div class="pro-payment-motif-box">
                                    <span>Motif requis :</span>
                                    <strong class="motif-highlight">{{ $payment->payment_motif }}</strong>
                                    <button type="button" class="btn-copy-motif" onclick="event.preventDefault(); copyMotif('{{ $payment->payment_motif }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <div class="pro-payment-amount-block">
                                <span class="pro-payment-amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                                <span class="pro-payment-cta">
                                    Payer <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>

                        <div class="pro-payment-footer">
                            <span class="pro-payment-hint">
                                <i class="fas fa-hand-pointer"></i> Appuyez pour procéder au paiement
                            </span>
                        </div>
                    </div>
                </a>
                @else
                {{-- Paiement déjà traité => affichage normal --}}
                <div class="pro-payment-history">
                    <div class="pro-payment-row">
                        <div class="pro-payment-info">
                            <span class="pro-payment-ref">{{ $payment->payment_number }}</span>
                            @if($payment->payment_motif)
                            <span class="pro-payment-motif">Motif: {{ $payment->payment_motif }}</span>
                            @endif
                        </div>
                        <span class="pro-payment-amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    <div class="pro-payment-row secondary">
                        <span class="pro-payment-status status-{{ $payment->status }}">
                            @if($payment->status === 'processing')
                                <i class="fas fa-spinner fa-spin"></i> En cours de vérification
                            @elseif($payment->status === 'completed')
                                <i class="fas fa-check-circle"></i> Payé
                            @elseif($payment->status === 'failed')
                                <i class="fas fa-times-circle"></i> Échoué
                            @else
                                {{ $payment->status }}
                            @endif
                        </span>
                        <span class="pro-payment-date">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        {{-- Retour --}}
        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('client.requests.index') }}" class="pro-back-link">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div style="height: 100px;"></div>
</div>

{{-- Modals --}}
<div class="pro-modal" id="modalTransferNormal">
    <div class="pro-modal-overlay" onclick="closeTransferNormal()"></div>
    <div class="pro-modal-content">
        <div class="pro-modal-header">
            <h3>Confirmer le transfert</h3>
            <button class="pro-modal-close" onclick="closeTransferNormal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pro-modal-body">
            <p class="pro-modal-text">Les fonds seront transférés sur votre compte sous <strong>24 à 48 heures</strong>.</p>
            <form action="{{ route('client.requests.transfer', $request->id) }}" method="POST">
                @csrf
                <button type="submit" class="pro-btn-primary">Confirmer le transfert</button>
            </form>
        </div>
    </div>
</div>

<div class="pro-modal" id="modalTransferUrgent">
    <div class="pro-modal-overlay" onclick="closeTransferUrgent()"></div>
    <div class="pro-modal-content">
        <div class="pro-modal-header urgent">
            <h3><i class="fas fa-bolt"></i> Transfert urgent</h3>
            <button class="pro-modal-close" onclick="closeTransferUrgent()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pro-modal-body">
            <div class="pro-urgent-info">
                <span class="pro-urgent-fee">+1 000 FCFA</span>
                <span class="pro-urgent-time">Traitement sous 4 heures</span>
            </div>
            <form action="{{ route('client.requests.transfer', $request->id) }}" method="POST">
                @csrf
                <input type="hidden" name="urgent" value="1">
                <button type="submit" class="pro-btn-primary urgent">Payer et confirmer</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.request-detail-pro { background: #f5f7fa; min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;}
.pro-header { background: linear-gradient(135deg, #1b5a8d 0%, #0f3a5c 100%); padding: 1.25rem; padding-top: calc(1.25rem + env(safe-area-inset-top, 0px)); position: relative; }
.pro-header-bg { display: flex; align-items: center; gap: 1rem; }
.pro-back { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; backdrop-filter: blur(10px); }
.pro-header-content { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
.pro-request-id { font-family: monospace; font-size: 1.4rem; font-weight: 700; color: white; letter-spacing: 1px; }
.pro-status-badge { display: inline-flex; align-items: center; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; width: fit-content; }
.badge-light { background: rgba(255,255,255,0.2); color: white; }
.badge-warning { background: #fbbf24; color: #78350f; }
.badge-info { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-danger { background: #ef4444; color: white; }

.pro-steps-container { background: white; padding: 1.5rem 1rem; border-bottom: 1px solid #e5e7eb; overflow-x: auto; }
.pro-steps-track { display: flex; justify-content: space-between; min-width: 400px; position: relative; }
.pro-steps-track::before { content: ''; position: absolute; top: 20px; left: 40px; right: 40px; height: 3px; background: #e5e7eb; z-index: 0; }
.pro-step { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; position: relative; z-index: 1; background: white; padding: 0 0.5rem; flex: 1; }
.pro-step-icon { width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; color: #9ca3af; border: 3px solid white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.pro-step.active .pro-step-icon { background: #1b5a8d; color: white; }
.pro-step.current .pro-step-icon { background: #d97706; color: white; box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2); animation: pulse-status 2s infinite; }
.pro-step.rejected .pro-step-icon { background: #ef4444; color: white; }
.pro-step-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; white-space: nowrap; }
.pro-step.active .pro-step-label { color: #1f2937; font-weight: 600; }
.pro-step-indicator { position: absolute; bottom: -6px; width: 6px; height: 6px; background: #10b981; border-radius: 50%; }

.pro-content { padding: 1.5rem 1rem; display: flex; flex-direction: column; gap: 1.5rem; max-width: 800px; margin: 0 auto; }

.pro-alert-urgent { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2); }
.pro-alert-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.pro-alert-urgent .pro-alert-icon { background: #f59e0b; color: white; }
.pro-alert-urgent .pro-alert-icon.pulse { animation: pulse-icon 2s infinite; }
.pro-alert-content { flex: 1; }
.pro-alert-content h4 { font-size: 1.1rem; font-weight: 700; color: #92400e; margin-bottom: 0.5rem; }
.motif-display { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; background: rgba(255,255,255,0.6); padding: 0.5rem; border-radius: 8px; }
.motif-code { font-family: monospace; font-size: 1.3rem; font-weight: 700; color: #92400e; letter-spacing: 2px; }
.btn-copy { background: white; border: none; width: 32px; height: 32px; border-radius: 6px; color: #92400e; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.pro-btn-pay-now { background: #1b5a8d; color: white; padding: 1rem 1.5rem; border-radius: 12px; font-weight: 700; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; white-space: nowrap; box-shadow: 0 4px 6px rgba(27,90,141,0.3); }

.pro-amount-card { background: linear-gradient(135deg, #1b5a8d 0%, #164a77 100%); color: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 25px rgba(27,90,141,0.3); text-align: center; }
.pro-amount-label { font-size: 0.95rem; opacity: 0.9; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px; }
.pro-amount-value { font-size: 2.25rem; font-weight: 700; font-family: 'Courier New', monospace; margin-bottom: 1rem; }
.pro-fee-row { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.9rem; opacity: 0.9; }

.pro-section { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.pro-section-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
.pro-section-title::before { content: ''; width: 4px; height: 20px; background: #1b5a8d; border-radius: 2px; }
.pro-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.pro-info-item { display: flex; flex-direction: column; gap: 0.25rem; }
.pro-info-item.full { grid-column: 1 / -1; }
.pro-label { font-size: 0.8rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
.pro-value { font-size: 0.95rem; font-weight: 600; color: #1f2937; line-height: 1.4; }

.pro-alert-payment { display: flex; align-items: center; gap: 1rem; padding: 1.25rem; border-radius: 16px; background: #fffbeb; border: 2px solid #f59e0b; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.pro-alert-payment .payment-icon { width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.pro-alert-content h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; color: #92400e; }
.pro-alert-content p { margin: 0; font-size: 0.95rem; color: #78350f; }
.payment-motif-box { margin-top: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.8); border-radius: 6px; display: inline-block; }
.payment-motif-box span { font-size: 0.8rem; color: #6b7280; display: block; margin-bottom: 0.25rem; }
.payment-motif-box code { font-family: monospace; font-size: 1.2rem; font-weight: 700; color: #1b5a8d; background: #e0f2fe; padding: 0.25rem 0.5rem; border-radius: 4px; letter-spacing: 1px; }
.pro-btn-action { padding: 0.75rem 1.5rem; background: #d97706; color: white; border-radius: 10px; font-weight: 700; text-decoration: none; white-space: nowrap; display: flex; flex-direction: column; align-items: center; line-height: 1.2; }
.pro-btn-action.pulse-btn { animation: pulse-pay 2s infinite; }

/* STYLES HISTORIQUE PAIEMENTS - CARTE CLIQUABLE */
.payment-link-wrapper { text-decoration: none; display: block; color: inherit; margin-bottom: 0.75rem; }
.pro-payment-history { padding: 1rem; background: #f9fafb; border-radius: 12px; border: 1px solid transparent; transition: all 0.2s; }
.pro-payment-history.payment-pending { background: #fffbeb; border: 2px solid #f59e0b; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.1); cursor: pointer; position: relative; overflow: hidden; }
.pro-payment-history.payment-pending:active { transform: scale(0.98); box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2); }

.payment-pending-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: #f59e0b; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.75rem; }
.payment-pending-badge i { font-size: 0.9rem; }

.pro-payment-main { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
.pro-payment-info { display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }
.pro-payment-ref { font-weight: 700; color: #1f2937; font-family: monospace; font-size: 1rem; }

.pro-payment-motif-box { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; background: rgba(255,255,255,0.6); padding: 0.5rem; border-radius: 8px; border: 1px dashed #fcd34d; }
.pro-payment-motif-box span { font-size: 0.75rem; color: #92400e; }
.motif-highlight { font-family: monospace; font-size: 1.2rem; font-weight: 700; color: #78350f; letter-spacing: 2px; }
.btn-copy-motif { background: white; border: 1px solid #e5e7eb; width: 28px; height: 28px; border-radius: 6px; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; margin-left: auto; }
.btn-copy-motif:hover { background: #f3f4f6; color: #1f2937; }

.pro-payment-amount-block { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; }
.pro-payment-amount { font-weight: 700; color: #1b5a8d; font-size: 1.1rem; white-space: nowrap; }
.pro-payment-cta { display: inline-flex; align-items: center; gap: 0.5rem; background: #d97706; color: white; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; margin-top: 0.25rem; box-shadow: 0 2px 4px rgba(217, 119, 6, 0.3); }
.pro-payment-cta i { font-size: 0.8rem; }

.pro-payment-footer { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed rgba(245, 158, 11, 0.3); display: flex; justify-content: center; }
.pro-payment-hint { font-size: 0.8rem; color: #92400e; display: flex; align-items: center; gap: 0.5rem; font-style: italic; }
.pro-payment-hint i { color: #f59e0b; }

/* Paiement non cliquable (traité) */
.pro-payment-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.pro-payment-row.secondary { margin-bottom: 0; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb; font-size: 0.85rem; }
.pro-payment-status { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-completed { background: #d1fae5; color: #065f46; }
.status-failed { background: #fee2e2; color: #991b1b; }
.pro-payment-date { color: #6b7280; font-size: 0.8rem; }
.pro-payment-motif { font-family: monospace; font-size: 0.8rem; background: #e5e7eb; color: #374151; padding: 0.2rem 0.5rem; border-radius: 4px; }

.pro-docs-list { display: flex; flex-direction: column; gap: 0.75rem; }
.pro-doc-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 12px; }
.pro-doc-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.pro-doc-icon.image { background: #dbeafe; color: #1d4ed8; }
.pro-doc-icon.pdf { background: #fee2e2; color: #dc2626; }
.pro-doc-info { flex: 1; display: flex; flex-direction: column; }
.pro-doc-name { font-weight: 600; color: #1f2937; font-size: 0.9rem; }
.pro-doc-type { font-size: 0.8rem; color: #6b7280; }
.pro-doc-status { width: 10px; height: 10px; border-radius: 50%; }
.status-validated { background: #10b981; }
.status-pending { background: #fbbf24; }

.pro-back-link { color: #6b7280; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; }

.pro-modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: flex-end; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s; }
.pro-modal.show { opacity: 1; visibility: visible; }
.pro-modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
.pro-modal-content { position: relative; background: white; width: 100%; max-width: 500px; border-radius: 24px 24px 0 0; padding: 1.5rem; transform: translateY(100%); transition: transform 0.3s; padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px)); }
.pro-modal.show .pro-modal-content { transform: translateY(0); }
.pro-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb; }
.pro-modal-header h3 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0; }
.pro-modal-header.urgent h3 { color: #d97706; }
.pro-modal-close { background: none; border: none; font-size: 1.25rem; color: #6b7280; padding: 0.5rem; cursor: pointer; }
.pro-modal-text { color: #4b5563; line-height: 1.6; margin-bottom: 1.5rem; text-align: center; }
.pro-urgent-info { text-align: center; margin-bottom: 1.5rem; }
.pro-urgent-fee { display: block; font-size: 2rem; font-weight: 700; color: #d97706; font-family: 'Courier New', monospace; }
.pro-urgent-time { color: #6b7280; font-size: 0.9rem; }
.pro-btn-primary { width: 100%; padding: 1rem; background: #1b5a8d; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; }
.pro-btn-primary.urgent { background: #f59e0b; color: white; }

.pro-transfer-options { display: flex; flex-direction: column; gap: 0.75rem; }
.pro-btn-transfer { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 1.25rem; background: white; border: 2px solid #e5e7eb; border-radius: 16px; width: 100%; transition: all 0.2s; cursor: pointer; }
.pro-btn-transfer:active { transform: scale(0.98); border-color: #1b5a8d; }
.pro-btn-transfer.urgent { border-color: #fbbf24; background: #fffbeb; }
.pro-transfer-title { font-weight: 700; color: #1f2937; font-size: 1rem; }
.pro-transfer-subtitle { font-size: 0.85rem; color: #6b7280; }

@keyframes pulse-status { 0%, 100% { box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2); } 50% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); } }
@keyframes pulse-icon { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
@keyframes pulse-pay { 0%, 100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4); } 50% { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); } }
</style>
@endpush

@push('scripts')
<script>
function showTransferNormal() {
    document.getElementById('modalTransferNormal').classList.add('show');
}
function closeTransferNormal() {
    document.getElementById('modalTransferNormal').classList.remove('show');
}
function showTransferUrgent() {
    document.getElementById('modalTransferUrgent').classList.add('show');
}
function closeTransferUrgent() {
    document.getElementById('modalTransferUrgent').classList.remove('show');
}

function copyMotif(motif) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(motif).then(function() {
            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.background = '#10b981';
            btn.style.color = 'white';
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    } else {
        const el = document.createElement('textarea');
        el.value = motif;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);

        // Feedback visuel simple
        const btn = event.currentTarget;
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => btn.innerHTML = original, 2000);
    }
}

// Scroll vers l'alerte de paiement si présente
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.getElementById('paymentAlert');
    if (alert) {
        setTimeout(function() {
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 500);
    }
});
</script>
@endpush
