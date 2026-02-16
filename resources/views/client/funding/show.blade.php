@extends('layouts.client')

@section('title', 'Détails de la demande #' . $request->request_number)

@section('content')
<div class="client-request-detail">

    {{-- Header sticky avec dégradé --}}
    <header class="detail-header">
        <div class="container">
            <div class="detail-header__inner">
                <a href="{{ route('client.requests.index') }}" class="detail-header__back">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="detail-header__content">
                    <div class="detail-header__meta">Demande #{{ $request->request_number }}</div>
                    <h1 class="detail-header__title">Détails du financement</h1>
                </div>
                <div class="detail-header__amount">
                    <span class="detail-header__value">{{ number_format($request->amount_requested, 0, ',', ' ') }}</span>
                    <span class="detail-header__currency">FCFA</span>
                </div>
            </div>
        </div>
    </header>

    @php
    // Configuration centralisée des statuts
    $statusConfig = [
        'draft' => ['label' => 'Brouillon', 'class' => 'badge--neutral', 'icon' => 'fa-edit', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        'submitted' => ['label' => 'Soumise', 'class' => 'badge--info', 'icon' => 'fa-paper-plane', 'color' => '#3b82f6', 'bg' => '#dbeafe'],
        'under_review' => ['label' => 'En examen', 'class' => 'badge--warning', 'icon' => 'fa-search', 'color' => '#d97706', 'bg' => '#fef3c7'],
        'pending_committee' => ['label' => 'Comité', 'class' => 'badge--warning', 'icon' => 'fa-users', 'color' => '#d97706', 'bg' => '#fef3c7'],
        'validated' => ['label' => 'Validée', 'class' => 'badge--success', 'icon' => 'fa-check-circle', 'color' => '#059669', 'bg' => '#d1fae5'],
        'pending_payment' => ['label' => 'Paiement requis', 'class' => 'badge--warning', 'icon' => 'fa-credit-card', 'color' => '#d97706', 'bg' => '#fef3c7'],
        'paid' => ['label' => 'Payée', 'class' => 'badge--info', 'icon' => 'fa-clock', 'color' => '#3b82f6', 'bg' => '#dbeafe'],
        'approved' => ['label' => 'Approuvée', 'class' => 'badge--success', 'icon' => 'fa-award', 'color' => '#059669', 'bg' => '#d1fae5'],
        'funded' => ['label' => 'Financée', 'class' => 'badge--primary', 'icon' => 'fa-money-bill-wave', 'color' => '#1e40af', 'bg' => '#dbeafe'],
        'completed' => ['label' => 'Terminée', 'class' => 'badge--success', 'icon' => 'fa-trophy', 'color' => '#047857', 'bg' => '#d1fae5'],
        'rejected' => ['label' => 'Rejetée', 'class' => 'badge--danger', 'icon' => 'fa-times-circle', 'color' => '#dc2626', 'bg' => '#fee2e2'],
        'cancelled' => ['label' => 'Annulée', 'class' => 'badge--neutral', 'icon' => 'fa-ban', 'color' => '#64748b', 'bg' => '#f1f5f9'],
    ];

    $current = $statusConfig[$request->status] ?? $statusConfig['draft'];
    $isPredefined = $request->is_predefined;
    $needsPayment = in_array($request->status, ['validated', 'pending_payment']) && empty($request->kkiapay_transaction_id);
    $paymentPending = $request->status === 'paid' && !empty($request->kkiapay_transaction_id);
    $transferScheduled = !empty($request->transfer_scheduled_at) && empty($request->transfer_executed_at);
    $isRejected = $request->status === 'rejected';
    $isCancelled = $request->status === 'cancelled';

    // Configuration des étapes selon le type
    if ($isPredefined) {
        $steps = [
            ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt'],
            ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card'],
            ['id' => 'review', 'label' => 'Examen', 'icon' => 'fa-search'],
            ['id' => 'approved', 'label' => 'Approuvée', 'icon' => 'fa-check-circle'],
            ['id' => 'transfer', 'label' => 'Transfert', 'icon' => 'fa-calendar-check'],
            ['id' => 'funded', 'label' => 'Financée', 'icon' => 'fa-trophy'],
        ];
        $stepMap = ['draft' => 0, 'submitted' => 0, 'paid' => 1, 'under_review' => 2, 'pending_committee' => 2, 'approved' => 3, 'funded' => 5, 'completed' => 5];
    } else {
        $steps = [
            ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt'],
            ['id' => 'review', 'label' => 'Examen', 'icon' => 'fa-search'],
            ['id' => 'validated', 'label' => 'Validation', 'icon' => 'fa-clipboard-check'],
            ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card'],
            ['id' => 'verification', 'label' => 'Vérification', 'icon' => 'fa-user-check'],
            ['id' => 'transfer', 'label' => 'Transfert', 'icon' => 'fa-calendar-check'],
            ['id' => 'funded', 'label' => 'Financée', 'icon' => 'fa-trophy'],
        ];
        $stepMap = ['draft' => 0, 'submitted' => 0, 'under_review' => 1, 'pending_committee' => 1, 'validated' => 2, 'pending_payment' => 3, 'paid' => 4, 'approved' => 5, 'funded' => 6, 'completed' => 6];
    }

    $currentStep = $transferScheduled ? (count($steps) - 2) : ($stepMap[$request->status] ?? 0);
    if ($transferScheduled && $isPredefined) $currentStep = 4;
    @endphp

    <main class="container detail-main">

        {{-- Carte de progression --}}
        <section class="card progress-card animate-in">
            <div class="progress-card__header">
                <span class="badge {{ $isPredefined ? 'badge--purple' : 'badge--blue' }}">
                    {{ $isPredefined ? 'Prédéfinie' : 'Personnalisée' }}
                </span>
                <span class="progress-card__title">Progression</span>
            </div>

            <div class="progress-steps">
                @foreach($steps as $index => $step)
                    @php
                        $completed = $index < $currentStep && !$isRejected && !$isCancelled;
                        $current = $index === $currentStep && !$isRejected && !$isCancelled;
                    @endphp
                    <div class="progress-step {{ $completed ? 'progress-step--completed' : '' }} {{ $current ? 'progress-step--current' : '' }}">
                        <div class="progress-step__icon">
                            @if($completed)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $step['icon'] }}"></i>
                            @endif
                        </div>
                        <span class="progress-step__label">{{ $step['label'] }}</span>
                        @if($current)
                            <div class="progress-step__pulse"></div>
                        @endif
                    </div>
                    @if(!$loop->last)
                        <div class="progress-step__line {{ $completed ? 'progress-step__line--active' : '' }}"></div>
                    @endif
                @endforeach
            </div>

            @if(!$isRejected && !$isCancelled)
                <div class="progress-bar">
                    <div class="progress-bar__track">
                        <div class="progress-bar__fill" style="width: {{ (($currentStep + 1) / count($steps)) * 100 }}%"></div>
                    </div>
                    <span class="progress-bar__percent">{{ round((($currentStep + 1) / count($steps)) * 100) }}%</span>
                </div>
            @else
                <div class="progress-alert progress-alert--{{ $isRejected ? 'danger' : 'neutral' }}">
                    <i class="fas {{ $isRejected ? 'fa-times-circle' : 'fa-ban' }}"></i>
                    <span>Demande {{ $isRejected ? 'rejetée' : 'annulée' }}</span>
                </div>
            @endif

            {{-- Message contextuel --}}
            @if(!$isRejected && !$isCancelled)
                <div class="progress-message">
                    @if($needsPayment)
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Paiement requis pour continuer le traitement</span>
                    @elseif($paymentPending)
                        <i class="fas fa-clock"></i>
                        <span>Paiement en attente de vérification par l'administration</span>
                    @elseif($transferScheduled)
                        <i class="fas fa-calendar-check"></i>
                        <span>Transfert programmé pour le {{ \Carbon\Carbon::parse($request->transfer_scheduled_at)->format('d/m/Y') }}</span>
                    @elseif($request->status === 'funded')
                        <i class="fas fa-check-double"></i>
                        <span>Financement crédité sur votre wallet !</span>
                    @elseif($request->status === 'under_review')
                        <i class="fas fa-search"></i>
                        <span>Votre demande est en cours d'examen</span>
                    @else
                        <i class="fas fa-info-circle"></i>
                        <span>{{ $current['label'] }}</span>
                    @endif
                </div>
            @endif
        </section>

        {{-- Alertes de statut --}}
        @if($needsPayment && $request->expected_payment > 0)
        <section class="alert alert--warning animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert__content">
                <strong>Paiement requis</strong>
                <p>Montant à payer : {{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</p>
                @if($request->payment_motif)<small>{{ $request->payment_motif }}</small>@endif
            </div>
            <a href="{{ route('client.requests.payment', $request->id) }}" class="btn btn--warning btn--small">Payer</a>
        </section>
        @endif

        @if($paymentPending)
        <section class="alert alert--info animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-user-clock"></i></div>
            <div class="alert__content">
                <strong>Vérification en cours</strong>
                <p>Votre paiement est en cours de vérification par notre équipe.</p>
                <div class="alert__details">
                    <span>ID: <code>{{ $request->kkiapay_transaction_id }}</code></span>
                    <span>Montant: {{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </section>
        @endif

        @if($transferScheduled)
        <section class="alert alert--success animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-calendar-check"></i></div>
            <div class="alert__content">
                <strong>Transfert programmé</strong>
                <p>Votre financement est programmé pour transfert.</p>
                @if($request->monthly_repayment_amount)
                <div class="alert__details">
                    <span>Mensualité: {{ number_format($request->monthly_repayment_amount, 0, ',', ' ') }} FCFA</span>
                    <span>Durée: {{ $request->repayment_duration_months }} mois</span>
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Carte principale --}}
        <section class="card detail-card animate-in stagger-2">

            {{-- En-tête avec statut --}}
            <div class="detail-status" style="background: {{ $current['bg'] }}; border-color: {{ $current['color'] }}20;">
                <div class="detail-status__icon" style="background: {{ $current['color'] }}20; color: {{ $current['color'] }};">
                    <i class="fas {{ $current['icon'] }}"></i>
                </div>
                <div class="detail-status__info">
                    <span class="detail-status__label" style="color: {{ $current['color'] }};">{{ $current['label'] }}</span>
                    <span class="detail-status__date">Mis à jour le {{ $request->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            {{-- Informations générales --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-info-circle"></i> Informations</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-item__label">Numéro</span>
                        <span class="detail-item__value font-mono">#{{ $request->request_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item__label">Type</span>
                        <span class="detail-item__value">
                            <span class="badge {{ $isPredefined ? 'badge--purple' : 'badge--blue' }}">
                                {{ $isPredefined ? 'Prédéfini' : 'Personnalisé' }}
                            </span>
                            @if($request->fundingType)
                                <span class="detail-item__sub">{{ $request->fundingType->name }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item__label">Créée le</span>
                        <span class="detail-item__value">{{ $request->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($request->submitted_at)
                    <div class="detail-item">
                        <span class="detail-item__label">Soumise le</span>
                        <span class="detail-item__value">{{ $request->submitted_at->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Montants --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-coins"></i> Financement</h3>
                <div class="amount-display">
                    <div class="amount-display__main">
                        <span class="amount-display__value">{{ number_format($request->amount_requested, 0, ',', ' ') }}</span>
                        <span class="amount-display__currency">FCFA</span>
                    </div>
                    <span class="amount-display__label">Montant demandé</span>
                </div>

                @if($request->amount_approved && $request->amount_approved != $request->amount_requested)
                <div class="amount-approved">
                    <span>Montant approuvé</span>
                    <strong>{{ number_format($request->amount_approved, 0, ',', ' ') }} FCFA</strong>
                </div>
                @endif

                <div class="detail-grid detail-grid--2cols mt-4">
                    <div class="detail-item">
                        <span class="detail-item__label">Durée</span>
                        <span class="detail-item__value">{{ $request->duration }} mois</span>
                    </div>
                    @if($request->expected_jobs)
                    <div class="detail-item">
                        <span class="detail-item__label">Emplois</span>
                        <span class="detail-item__value">{{ $request->expected_jobs }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Description avec toggle intelligent --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-align-left"></i> Description</h3>
                <div class="description-wrapper" id="descWrapper">
                    <div class="description-content" id="descContent">
                        {{ $request->description }}
                    </div>
                    <div class="description-gradient" id="descGradient"></div>
                </div>
                <button type="button" class="btn-toggle" id="toggleDesc" onclick="toggleDescription()">
                    <span id="toggleText">Voir plus</span>
                    <i class="fas fa-chevron-down" id="toggleIcon"></i>
                </button>
                @if($request->project_location)
                <div class="detail-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $request->project_location }}</span>
                </div>
                @endif
            </div>

            {{-- Notes admin --}}
            @if($request->admin_validation_notes && in_array($request->status, ['validated', 'approved', 'rejected', 'paid', 'funded']))
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-comment-alt"></i> Notes</h3>
                <div class="admin-notes {{ $request->status === 'rejected' ? 'admin-notes--rejected' : '' }}">
                    {{ $request->admin_validation_notes }}
                </div>
            </div>
            @endif

            {{-- Plan de remboursement --}}
            @if($request->monthly_repayment_amount)
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-calendar-alt"></i> Remboursement</h3>
                <div class="repayment-card">
                    <div class="repayment-card__main">
                        <span class="repayment-card__label">Mensualité</span>
                        <span class="repayment-card__value">{{ number_format($request->monthly_repayment_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="repayment-card__details">
                        <div class="repayment-detail">
                            <span>Durée</span>
                            <strong>{{ $request->repayment_duration_months }} mois</strong>
                        </div>
                        @if($request->repayment_start_date)
                        <div class="repayment-detail">
                            <span>Début</span>
                            <strong>{{ \Carbon\Carbon::parse($request->repayment_start_date)->format('d/m/Y') }}</strong>
                        </div>
                        @endif
                        @if($request->total_repayment_amount)
                        <div class="repayment-detail">
                            <span>Total</span>
                            <strong>{{ number_format($request->total_repayment_amount, 0, ',', ' ') }} FCFA</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Historique simplifié --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-history"></i> Historique</h3>
                <div class="timeline">
                    @php $events = [
                        ['date' => $request->created_at, 'icon' => 'fa-file-alt', 'label' => 'Demande créée', 'color' => 'neutral'],
                        ['date' => $request->submitted_at, 'icon' => 'fa-paper-plane', 'label' => 'Soumise', 'color' => 'info'],
                        ['date' => $request->reviewed_at, 'icon' => 'fa-search', 'label' => 'En examen', 'color' => 'warning'],
                        ['date' => $request->validated_at, 'icon' => 'fa-check-circle', 'label' => 'Validée', 'color' => 'success'],
                        ['date' => $request->paid_at, 'icon' => 'fa-credit-card', 'label' => 'Payée', 'color' => 'info'],
                        ['date' => $request->approved_at, 'icon' => 'fa-award', 'label' => 'Approuvée', 'color' => 'success'],
                        ['date' => $request->transfer_scheduled_at, 'icon' => 'fa-calendar-check', 'label' => 'Transfert programmé', 'color' => 'primary'],
                        ['date' => $request->transfer_executed_at ?? $request->funded_at, 'icon' => 'fa-trophy', 'label' => 'Financée', 'color' => 'success'],
                    ]; @endphp

                    @foreach(array_filter($events, fn($e) => $e['date']) as $event)
                    <div class="timeline-item">
                        <div class="timeline-item__dot timeline-item__dot--{{ $event['color'] }}"></div>
                        <div class="timeline-item__content">
                            <span class="timeline-item__label">{{ $event['label'] }}</span>
                            <span class="timeline-item__date">{{ $event['date']->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @endforeach

                    @if($isRejected || $isCancelled)
                    <div class="timeline-item timeline-item--{{ $isRejected ? 'rejected' : 'cancelled' }}">
                        <div class="timeline-item__dot timeline-item__dot--{{ $isRejected ? 'danger' : 'neutral' }}"></div>
                        <div class="timeline-item__content">
                            <span class="timeline-item__label">{{ $isRejected ? 'Demande rejetée' : 'Demande annulée' }}</span>
                            @if($request->admin_validation_notes)
                                <span class="timeline-item__note">{{ $request->admin_validation_notes }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Actions --}}
        <div class="detail-actions animate-in stagger-3">
            @if($needsPayment)
                <a href="{{ route('client.requests.payment', $request->id) }}" class="btn btn--primary btn--large">
                    <i class="fas fa-credit-card"></i>
                    Effectuer le paiement
                </a>
            @endif

            @if($request->status === 'draft')
                <a href="{{ route('client.requests.edit', $request->id) }}" class="btn btn--secondary">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
            @endif

            @if(in_array($request->status, ['draft', 'submitted', 'validated', 'pending_payment']))
                <button type="button" class="btn btn--danger btn--outline" onclick="openCancelModal()">
                    <i class="fas fa-trash-alt"></i>
                    Annuler la demande
                </button>
            @endif

            <a href="{{ route('client.requests.index') }}" class="btn btn--ghost">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </main>

    {{-- Modal d'annulation --}}
    <div class="modal-overlay" id="cancelOverlay" onclick="closeCancelModal()"></div>
    <div class="modal" id="cancelModal">
        <div class="modal__header">
            <h3>Annuler la demande</h3>
            <button onclick="closeCancelModal()" class="modal__close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal__body">
            <div class="modal__icon modal__icon--warning"><i class="fas fa-exclamation-triangle"></i></div>
            <p class="modal__text">Êtes-vous sûr de vouloir annuler la demande <strong>#{{ $request->request_number }}</strong> ?</p>
            <p class="modal__warning">Cette action est irréversible.</p>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeCancelModal()">Non, garder</button>
            <form action="{{ route('client.requests.cancel', $request->id) }}" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--danger">
                    <i class="fas fa-trash-alt"></i>
                    Oui, annuler
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
/* ===== VARIABLES & RESET ===== */
.client-request-detail {
    --primary: #0f172a;
    --primary-light: #1e293b;
    --accent: #3b82f6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --neutral: #64748b;
    --purple: #8b5cf6;
    --bg: #f8fafc;
    --surface: #ffffff;
    --text: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --radius: 12px;
    --radius-lg: 16px;

    background: var(--bg);
    min-height: 100vh;
    padding-bottom: 100px;
}

.container { max-width: 800px; margin: 0 auto; padding: 0 16px; }

/* ===== HEADER ===== */
.detail-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.3);
}

.detail-header__inner {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
}

.detail-header__back {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    color: white;
    text-decoration: none;
    transition: all 0.2s;
    flex-shrink: 0;
}

.detail-header__back:hover { background: rgba(255,255,255,0.25); }

.detail-header__content { flex: 1; min-width: 0; }

.detail-header__meta {
    font-size: 0.75rem;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
}

.detail-header__title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.detail-header__amount {
    text-align: right;
    flex-shrink: 0;
}

.detail-header__value {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
}

.detail-header__currency {
    font-size: 0.75rem;
    opacity: 0.8;
    text-transform: uppercase;
}

/* ===== COMPONENTS ===== */
.card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 16px;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge--neutral { background: #f1f5f9; color: #475569; }
.badge--info { background: #dbeafe; color: #1d4ed8; }
.badge--success { background: #d1fae5; color: #047857; }
.badge--warning { background: #fef3c7; color: #b45309; }
.badge--danger { background: #fee2e2; color: #b91c1c; }
.badge--primary { background: #dbeafe; color: #1e40af; }
.badge--purple { background: #ede9fe; color: #6d28d9; }
.badge--blue { background: #dbeafe; color: #1d4ed8; }

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    width: 100%;
}

.btn:active { transform: scale(0.98); }

.btn--primary {
    background: linear-gradient(135deg, #1b5a8d 0%, #164a77 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25);
}

.btn--secondary { background: #f1f5f9; color: #475569; }
.btn--secondary:hover { background: #e2e8f0; }

.btn--danger { background: #fee2e2; color: #dc2626; }
.btn--danger:hover { background: #fecaca; }

.btn--danger.btn--outline {
    background: transparent;
    border: 2px solid #fecaca;
}

.btn--ghost {
    background: transparent;
    color: #64748b;
    border: 1px solid var(--border);
}

.btn--warning {
    background: #f59e0b;
    color: white;
}

.btn--small { padding: 8px 16px; font-size: 0.8125rem; }
.btn--large { padding: 16px 24px; font-size: 1rem; }

/* ===== PROGRESS CARD ===== */
.progress-card { padding: 20px; }

.progress-card__header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.progress-card__title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text);
}

.progress-steps {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
    overflow-x: auto;
    padding-bottom: 8px;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 60px;
    position: relative;
}

.progress-step__icon {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
    position: relative;
    z-index: 2;
}

.progress-step--completed .progress-step__icon {
    background: var(--success);
    color: white;
}

.progress-step--current .progress-step__icon {
    background: var(--warning);
    color: white;
    animation: pulse 2s infinite;
}

.progress-step__label {
    font-size: 0.625rem;
    font-weight: 600;
    color: #94a3b8;
    text-align: center;
    white-space: nowrap;
}

.progress-step--completed .progress-step__label,
.progress-step--current .progress-step__label {
    color: var(--text);
    font-weight: 700;
}

.progress-step__pulse {
    position: absolute;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--warning);
    opacity: 0.3;
    animation: ripple 2s infinite;
    z-index: 1;
}

.progress-step__line {
    flex: 1;
    height: 3px;
    background: #e2e8f0;
    margin-top: 16px;
    min-width: 10px;
    max-width: 30px;
}

.progress-step__line--active {
    background: linear-gradient(90deg, var(--success), #34d399);
}

.progress-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.progress-bar__track {
    flex: 1;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar__fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-bar__percent {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--primary);
    min-width: 45px;
    text-align: right;
}

.progress-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
}

.progress-alert--danger { background: #fee2e2; color: #991b1b; }
.progress-alert--neutral { background: #f1f5f9; color: #475569; }

.progress-message {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #f0f9ff;
    border-radius: 10px;
    font-size: 0.875rem;
    color: #0369a1;
}

.progress-message i { color: #0ea5e9; }

/* ===== ALERTS ===== */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
    border-radius: var(--radius);
    margin-bottom: 16px;
}

.alert--warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; }
.alert--info { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; }
.alert--success { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #6ee7b7; }

.alert__icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert--warning .alert__icon { background: #f59e0b20; color: #d97706; }
.alert--info .alert__icon { background: #3b82f620; color: #2563eb; }
.alert--success .alert__icon { background: #10b98120; color: #059669; }

.alert__content { flex: 1; min-width: 0; }
.alert__content strong { display: block; margin-bottom: 4px; color: var(--text); }
.alert__content p { margin: 0; font-size: 0.875rem; color: var(--text-muted); }
.alert__content small { display: block; margin-top: 4px; font-size: 0.75rem; opacity: 0.8; }

.alert__details {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(0,0,0,0.1);
    font-size: 0.8125rem;
}

.alert__details span { display: flex; align-items: center; gap: 6px; }
.alert__details code {
    background: rgba(0,0,0,0.05);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
}

/* ===== DETAIL CARD ===== */
.detail-card { margin-bottom: 16px; }

.detail-status {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    border-bottom: 1px solid;
}

.detail-status__icon {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.detail-status__info { min-width: 0; }

.detail-status__label {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.detail-status__date {
    font-size: 0.8125rem;
    opacity: 0.7;
}

.detail-section { padding: 20px; border-bottom: 1px solid var(--border); }
.detail-section:last-child { border-bottom: none; }

.detail-section__title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-section__title i { color: var(--text-muted); }

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.detail-grid--2cols { grid-template-columns: repeat(2, 1fr); }

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item__label {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
}

.detail-item__value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text);
    line-height: 1.4;
}

.detail-item__sub {
    display: block;
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 400;
    margin-top: 2px;
}

.font-mono { font-family: 'Courier New', monospace; }

/* Amount Display */
.amount-display {
    text-align: center;
    padding: 24px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: var(--radius);
    margin-bottom: 16px;
}

.amount-display__main { margin-bottom: 8px; }

.amount-display__value {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0369a1;
    line-height: 1;
}

.amount-display__currency {
    font-size: 1rem;
    color: #0284c7;
    font-weight: 600;
    margin-left: 8px;
}

.amount-display__label {
    font-size: 0.875rem;
    color: #0369a1;
    opacity: 0.8;
}

.amount-approved {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #ecfdf5;
    border-radius: 10px;
    font-size: 0.875rem;
}

.amount-approved strong { color: #047857; }

/* Description */
.description-wrapper {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
}

.description-content {
    background: #f8fafc;
    padding: 16px;
    font-size: 0.9375rem;
    line-height: 1.7;
    color: #374151;
    max-height: 120px;
    overflow: hidden;
    transition: max-height 0.4s ease;
}

.description-content.expanded { max-height: 2000px; }

.description-gradient {
    position: absolute;
    bottom: 0;
    left: 0; right: 0;
    height: 60px;
    background: linear-gradient(transparent, #f8fafc);
    pointer-events: none;
    transition: opacity 0.3s;
}

.description-content.expanded + .description-gradient { opacity: 0; }

.btn-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    background: transparent;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    color: var(--accent);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-toggle.show { display: flex; }
.btn-toggle:hover { background: #f8fafc; }
.btn-toggle i { transition: transform 0.3s; }
.btn-toggle.active i { transform: rotate(180deg); }

.detail-location {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 0.875rem;
}

/* Admin Notes */
.admin-notes {
    padding: 16px;
    background: #f0f9ff;
    border-radius: var(--radius);
    border-left: 4px solid var(--accent);
    font-size: 0.9375rem;
    line-height: 1.6;
    color: #1e40af;
}

.admin-notes--rejected {
    background: #fef2f2;
    border-left-color: var(--danger);
    color: #991b1b;
}

/* Repayment Card */
.repayment-card {
    background: #f8fafc;
    border-radius: var(--radius);
    padding: 20px;
    border: 1px solid var(--border);
}

.repayment-card__main {
    text-align: center;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 16px;
}

.repayment-card__label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.repayment-card__value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--success);
}

.repayment-card__details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.repayment-detail {
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-align: center;
}

.repayment-detail span {
    font-size: 0.6875rem;
    color: var(--text-muted);
    text-transform: uppercase;
}

.repayment-detail strong {
    font-size: 0.875rem;
    color: var(--text);
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 24px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: var(--border);
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child { padding-bottom: 0; }

.timeline-item__dot {
    position: absolute;
    left: -20px;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--border);
}

.timeline-item__dot--neutral { background: var(--neutral); box-shadow: 0 0 0 2px var(--neutral); }
.timeline-item__dot--info { background: var(--accent); box-shadow: 0 0 0 2px var(--accent); }
.timeline-item__dot--success { background: var(--success); box-shadow: 0 0 0 2px var(--success); }
.timeline-item__dot--warning { background: var(--warning); box-shadow: 0 0 0 2px var(--warning); }
.timeline-item__dot--primary { background: var(--primary); box-shadow: 0 0 0 2px var(--primary); }
.timeline-item__dot--danger { background: var(--danger); box-shadow: 0 0 0 2px var(--danger); }

.timeline-item--rejected .timeline-item__dot,
.timeline-item--cancelled .timeline-item__dot {
    background: var(--danger);
    box-shadow: 0 0 0 2px var(--danger);
}

.timeline-item__content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.timeline-item__label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text);
}

.timeline-item__date {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.timeline-item__note {
    margin-top: 8px;
    padding: 10px;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: #991b1b;
}

/* Actions */
.detail-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 24px;
}

/* Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.modal-overlay.show { opacity: 1; visibility: visible; }

.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.95);
    background: white;
    border-radius: var(--radius-lg);
    width: 90%;
    max-width: 400px;
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}

.modal.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) scale(1);
}

.modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--border);
}

.modal__header h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
}

.modal__close {
    width: 36px; height: 36px;
    border-radius: 50%;
    border: none;
    background: #f1f5f9;
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal__body { padding: 24px; text-align: center; }

.modal__icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 16px;
}

.modal__icon--warning { background: #fef3c7; color: #d97706; }

.modal__text {
    font-size: 1rem;
    color: var(--text);
    margin-bottom: 8px;
    line-height: 1.5;
}

.modal__warning {
    font-size: 0.875rem;
    color: var(--danger);
    font-weight: 500;
}

.modal__footer {
    display: flex;
    gap: 12px;
    padding: 0 20px 20px;
}

/* Animations */
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
}

@keyframes ripple {
    0% { transform: scale(1); opacity: 0.5; }
    100% { transform: scale(2); opacity: 0; }
}

.animate-in {
    opacity: 0;
    transform: translateY(20px);
    animation: slideUp 0.5s ease forwards;
}

.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }

@keyframes slideUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 380px) {
    .detail-grid { grid-template-columns: 1fr; }
    .repayment-card__details { grid-template-columns: 1fr; }
    .progress-step__label { font-size: 0.5625rem; }
    .amount-display__value { font-size: 2rem; }
}

@media (min-width: 640px) {
    .detail-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@push('scripts')
<script>
// Modal functions
function openCancelModal() {
    document.getElementById('cancelOverlay').classList.add('show');
    document.getElementById('cancelModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelOverlay').classList.remove('show');
    document.getElementById('cancelModal').classList.remove('show');
    document.body.style.overflow = '';
}

// Description toggle
function toggleDescription() {
    const content = document.getElementById('descContent');
    const btn = document.getElementById('toggleDesc');
    const text = document.getElementById('toggleText');
    const icon = document.getElementById('toggleIcon');

    const isExpanded = content.classList.contains('expanded');

    if (!isExpanded) {
        content.classList.add('expanded');
        content.style.maxHeight = content.scrollHeight + 'px';
        btn.classList.add('active');
        text.textContent = 'Voir moins';
        setTimeout(() => content.style.maxHeight = '2000px', 300);
    } else {
        content.classList.remove('expanded');
        content.style.maxHeight = '120px';
        btn.classList.remove('active');
        text.textContent = 'Voir plus';
        document.getElementById('descWrapper').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Init description manager
document.addEventListener('DOMContentLoaded', function() {
    const content = document.getElementById('descContent');
    const btn = document.getElementById('toggleDesc');

    if (content && content.scrollHeight > 150) {
        btn.classList.add('show');
    } else if (content) {
        content.style.maxHeight = 'none';
    }
});

// Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCancelModal();
});
</script>
@endpush
