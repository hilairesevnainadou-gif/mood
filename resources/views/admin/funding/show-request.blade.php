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
    // Configuration des statuts avec libellés clairs et couleurs distinctes
    $statusConfig = [
        'draft' => [
            'label' => 'Brouillon',
            'class' => 'badge--neutral',
            'icon' => 'fa-edit',
            'color' => '#64748b',
            'bg' => '#f1f5f9',
            'description' => 'Votre demande est en cours de rédaction'
        ],
        'submitted' => [
            'label' => 'Soumise',
            'class' => 'badge--info',
            'icon' => 'fa-paper-plane',
            'color' => '#3b82f6',
            'bg' => '#dbeafe',
            'description' => 'Votre demande a été soumise et sera traitée prochainement'
        ],
        'under_review' => [
            'label' => 'En étude',
            'class' => 'badge--warning',
            'icon' => 'fa-search',
            'color' => '#f59e0b',
            'bg' => '#fef3c7',
            'description' => 'Votre demande est en cours d\'analyse par notre équipe'
        ],
        'pending_committee' => [
            'label' => 'En comité',
            'class' => 'badge--committee',
            'icon' => 'fa-users',
            'color' => '#f97316',
            'bg' => '#ffedd5',
            'description' => 'Votre demande est soumise au comité d\'approbation'
        ],
        'validated' => [
            'label' => 'Validée - En attente de paiement',
            'class' => 'badge--validated',
            'icon' => 'fa-check-circle',
            'color' => '#10b981',
            'bg' => '#d1fae5',
            'description' => 'Votre demande est validée. Veuillez effectuer le paiement pour continuer'
        ],
        'pending_payment' => [
            'label' => 'Paiement en attente',
            'class' => 'badge--payment-required',
            'icon' => 'fa-credit-card',
            'color' => '#8b5cf6',
            'bg' => '#ede9fe',
            'description' => 'Le paiement des frais est requis pour poursuivre le traitement'
        ],
        'paid' => [
            'label' => 'Payée - Vérification en cours',
            'class' => 'badge--paid',
            'icon' => 'fa-clock',
            'color' => '#06b6d4',
            'bg' => '#cffafe',
            'description' => 'Votre paiement est en cours de vérification par l\'administration'
        ],
        'approved' => [
            'label' => 'Approuvée - Transfert à venir',
            'class' => 'badge--approved',
            'icon' => 'fa-award',
            'color' => '#059669',
            'bg' => '#d1fae5',
            'description' => 'Votre demande est approuvée. Le transfert sera programmé prochainement'
        ],
        'funded' => [
            'label' => 'Financée - Fonds reçus',
            'class' => 'badge--funded',
            'icon' => 'fa-money-bill-wave',
            'color' => '#1e40af',
            'bg' => '#dbeafe',
            'description' => 'Les fonds ont été transférés sur votre wallet'
        ],
        'completed' => [
            'label' => 'Terminée',
            'class' => 'badge--completed',
            'icon' => 'fa-trophy',
            'color' => '#047857',
            'bg' => '#d1fae5',
            'description' => 'Votre demande est terminée avec succès'
        ],
        'rejected' => [
            'label' => 'Rejetée',
            'class' => 'badge--danger',
            'icon' => 'fa-times-circle',
            'color' => '#dc2626',
            'bg' => '#fee2e2',
            'description' => 'Votre demande a été rejetée. Consultez les notes pour plus de détails'
        ],
        'cancelled' => [
            'label' => 'Annulée',
            'class' => 'badge--neutral',
            'icon' => 'fa-ban',
            'color' => '#64748b',
            'bg' => '#f1f5f9',
            'description' => 'Votre demande a été annulée'
        ],
    ];

    $current = $statusConfig[$request->status] ?? $statusConfig['draft'];
    $isPredefined = $request->is_predefined;

    // Détection des états spécifiques
    $needsPayment = in_array($request->status, ['validated', 'pending_payment']) && empty($request->kkiapay_transaction_id);
    $paymentPending = $request->status === 'paid' && !empty($request->kkiapay_transaction_id);
    $paymentProcessed = !empty($request->kkiapay_transaction_id) && in_array($request->status, ['approved', 'funded', 'completed']);
    $transferScheduled = !empty($request->transfer_scheduled_at) && empty($request->transfer_executed_at);
    $transferExecuted = !empty($request->transfer_executed_at);
    $isRejected = $request->status === 'rejected';
    $isCancelled = $request->status === 'cancelled';
    $isFunded = $request->status === 'funded' || $request->status === 'completed';

    // Configuration des étapes avec couleurs DISTINCTES pour chaque phase
    if ($isPredefined) {
        // PREDEFINI: Soumission (Bleu) → Paiement (Violet) → Examen (Orange) → Validation (Vert) → Transfert programmé (Orange foncé) → Financé (Bleu foncé)
        $steps = [
            ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'description' => 'Demande envoyée'],
            ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card', 'color' => '#8b5cf6', 'bg' => '#ede9fe', 'description' => 'Frais de dossier'],
            ['id' => 'review', 'label' => 'Étude', 'icon' => 'fa-search', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'description' => 'Analyse en cours'],
            ['id' => 'approved', 'label' => 'Approuvée', 'icon' => 'fa-check-circle', 'color' => '#10b981', 'bg' => '#d1fae5', 'description' => 'Demande acceptée'],
            ['id' => 'transfer', 'label' => 'Programmation', 'icon' => 'fa-calendar-check', 'color' => '#f97316', 'bg' => '#ffedd5', 'description' => 'Transfert planifié'],
            ['id' => 'funded', 'label' => 'Financée', 'icon' => 'fa-money-bill-wave', 'color' => '#1e40af', 'bg' => '#dbeafe', 'description' => 'Fonds reçus'],
        ];
        $stepMap = [
            'draft' => 0,
            'submitted' => 0,
            'paid' => 1,
            'under_review' => 2,
            'pending_committee' => 2,
            'approved' => 3,
            'funded' => 5,
            'completed' => 5
        ];
    } else {
        // PERSONNALISE: Soumission (Bleu) → Examen (Orange) → Validation (Vert) → Paiement (Violet) → Vérification (Cyan) → Transfert (Orange foncé) → Financé (Bleu foncé)
        $steps = [
            ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt', 'color' => '#3b82f6', 'bg' => '#dbeafe', 'description' => 'Demande envoyée'],
            ['id' => 'review', 'label' => 'Étude', 'icon' => 'fa-search', 'color' => '#f59e0b', 'bg' => '#fef3c7', 'description' => 'Analyse en cours'],
            ['id' => 'validated', 'label' => 'Validation', 'icon' => 'fa-clipboard-check', 'color' => '#10b981', 'bg' => '#d1fae5', 'description' => 'Demande validée'],
            ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card', 'color' => '#8b5cf6', 'bg' => '#ede9fe', 'description' => 'Frais de dossier'],
            ['id' => 'verification', 'label' => 'Vérification', 'icon' => 'fa-user-check', 'color' => '#06b6d4', 'bg' => '#cffafe', 'description' => 'Confirmation paiement'],
            ['id' => 'transfer', 'label' => 'Programmation', 'icon' => 'fa-calendar-check', 'color' => '#f97316', 'bg' => '#ffedd5', 'description' => 'Transfert planifié'],
            ['id' => 'funded', 'label' => 'Financée', 'icon' => 'fa-money-bill-wave', 'color' => '#1e40af', 'bg' => '#dbeafe', 'description' => 'Fonds reçus'],
        ];
        $stepMap = [
            'draft' => 0,
            'submitted' => 0,
            'under_review' => 1,
            'pending_committee' => 1,
            'validated' => 2,
            'pending_payment' => 3,
            'paid' => 4,
            'approved' => 5,
            'funded' => 6,
            'completed' => 6
        ];
    }

    $currentStep = $stepMap[$request->status] ?? 0;
    if ($transferScheduled) $currentStep = count($steps) - 2;
    if ($transferExecuted) $currentStep = count($steps) - 1;
    @endphp

    <main class="container detail-main">

        {{-- Carte de progression avec couleurs par étape --}}
        <section class="card progress-card animate-in">
            <div class="progress-card__header">
                <span class="badge {{ $isPredefined ? 'badge--purple' : 'badge--blue' }}">
                    {{ $isPredefined ? 'Prédéfinie' : 'Personnalisée' }}
                </span>
                <span class="progress-card__title">Étapes de ma demande</span>
            </div>

            <div class="progress-steps">
                @foreach($steps as $index => $step)
                    @php
                        $completed = $index < $currentStep && !$isRejected && !$isCancelled;
                        $current = $index === $currentStep && !$isRejected && !$isCancelled;
                        $stepColor = $step['color'];
                        $stepBg = $step['bg'];
                    @endphp
                    <div class="progress-step {{ $completed ? 'progress-step--completed' : '' }} {{ $current ? 'progress-step--current' : '' }}"
                         data-step="{{ $step['id'] }}">
                        <div class="progress-step__icon"
                             style="{{ $completed ? 'background: ' . $stepColor . '; color: white; box-shadow: 0 4px 12px ' . $stepColor . '40;' : ($current ? 'background: ' . $stepColor . '; color: white; box-shadow: 0 0 0 4px ' . $stepBg . ', 0 4px 12px ' . $stepColor . '40;' : 'background: ' . $stepBg . '; color: ' . $stepColor . ';') }}">
                            @if($completed)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $step['icon'] }}"></i>
                            @endif
                        </div>
                        <span class="progress-step__label"
                              style="{{ $completed || $current ? 'color: ' . $stepColor . '; font-weight: 700;' : 'color: #94a3b8;' }}">
                            {{ $step['label'] }}
                        </span>
                        @if($current)
                            <div class="progress-step__pulse" style="background: {{ $stepColor }}"></div>
                        @endif
                    </div>
                    @if(!$loop->last)
                        <div class="progress-step__line {{ $completed ? 'progress-step__line--active' : '' }}"
                             style="{{ $completed ? 'background: linear-gradient(90deg, ' . $steps[$index]['color'] . ', ' . $steps[$index + 1]['color'] . '); height: 4px;' : '' }}"></div>
                    @endif
                @endforeach
            </div>

            @if(!$isRejected && !$isCancelled)
                <div class="progress-bar">
                    <div class="progress-bar__track">
                        @php
                            $gradientColors = [];
                            for($i = 0; $i <= $currentStep && $i < count($steps); $i++) {
                                $gradientColors[] = $steps[$i]['color'];
                            }
                            $gradient = implode(', ', $gradientColors);
                        @endphp
                        <div class="progress-bar__fill"
                             style="width: {{ (($currentStep + 1) / count($steps)) * 100 }}%;
                                    background: linear-gradient(90deg, {{ $gradient }});"></div>
                    </div>
                    <span class="progress-bar__percent" style="color: {{ $steps[$currentStep]['color'] ?? '#1e40af' }}">
                        {{ round((($currentStep + 1) / count($steps)) * 100) }}%
                    </span>
                </div>
            @else
                <div class="progress-alert progress-alert--{{ $isRejected ? 'danger' : 'neutral' }}">
                    <i class="fas {{ $isRejected ? 'fa-times-circle' : 'fa-ban' }}"></i>
                    <span>Demande {{ $isRejected ? 'rejetée' : 'annulée' }}</span>
                </div>
            @endif

            {{-- Message contextuel avec la bonne couleur --}}
            @if(!$isRejected && !$isCancelled)
                <div class="progress-message"
                     style="background: {{ $steps[$currentStep]['bg'] }};
                            border-left: 4px solid {{ $steps[$currentStep]['color'] }};
                            color: {{ $steps[$currentStep]['color'] }};">
                    <i class="fas {{ $steps[$currentStep]['icon'] }}" style="color: {{ $steps[$currentStep]['color'] }};"></i>
                    <div class="progress-message__content">
                        <strong>{{ $steps[$currentStep]['label'] }}</strong>
                        <span>{{ $steps[$currentStep]['description'] }}</span>
                    </div>
                </div>
            @endif
        </section>

        {{-- Alerte Paiement Requis - VIOLET --}}
        @if($needsPayment && $request->expected_payment > 0)
        <section class="alert alert--payment animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-credit-card"></i></div>
            <div class="alert__content">
                <strong>Paiement requis</strong>
                <p>Le paiement des frais de dossier est nécessaire pour continuer le traitement de votre demande.</p>
                <div class="alert__amount">
                    <span>Montant à payer :</span>
                    <strong>{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</strong>
                </div>
                @if($request->payment_motif)<small>{{ $request->payment_motif }}</small>@endif
            </div>
            <a href="{{ route('client.requests.payment', $request->id) }}" class="btn btn--payment">Payer maintenant</a>
        </section>
        @endif

        {{-- Alerte Paiement en Vérification - CYAN --}}
        @if($paymentPending)
        <section class="alert alert--verification animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-user-clock"></i></div>
            <div class="alert__content">
                <strong>Vérification du paiement</strong>
                <p>Votre paiement a été reçu et est en cours de vérification par notre équipe administrative.</p>
                <div class="alert__details">
                    <div class="detail-row">
                        <span>Transaction ID :</span>
                        <code>{{ $request->kkiapay_transaction_id }}</code>
                    </div>
                    <div class="detail-row">
                        <span>Montant payé :</span>
                        <strong>{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="detail-row">
                        <span>Date :</span>
                        <span>{{ $request->paid_at?->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- Alerte Paiement Confirmé - VIOLET FONCÉ --}}
        @if($paymentProcessed)
        <section class="alert alert--processed animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-check-double"></i></div>
            <div class="alert__content">
                <strong>Paiement confirmé</strong>
                <p>Votre paiement a été vérifié et confirmé avec succès.</p>
                <div class="alert__details">
                    <div class="detail-row">
                        <span>Transaction ID :</span>
                        <code>{{ $request->kkiapay_transaction_id }}</code>
                    </div>
                    <div class="detail-row">
                        <span>Montant :</span>
                        <strong>{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="detail-row">
                        <span>Date de paiement :</span>
                        <span>{{ $request->paid_at?->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- Alerte Transfert Programmé - ORANGE FONCÉ --}}
        @if($transferScheduled)
        <section class="alert alert--transfer animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-calendar-check"></i></div>
            <div class="alert__content">
                <strong>Transfert programmé</strong>
                <p>Votre financement est programmé et sera transféré à la date indiquée.</p>
                <div class="alert__details">
                    <div class="detail-row highlight">
                        <span>Date de transfert :</span>
                        <strong>{{ \Carbon\Carbon::parse($request->transfer_scheduled_at)->format('d/m/Y') }}</strong>
                    </div>
                    @if($request->monthly_repayment_amount)
                    <div class="detail-row">
                        <span>Mensualité :</span>
                        <strong>{{ number_format($request->monthly_repayment_amount, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="detail-row">
                        <span>Durée :</span>
                        <strong>{{ $request->repayment_duration_months }} mois</strong>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        @endif

        {{-- Alerte Financement Exécuté - BLEU INDIGO --}}
        @if($transferExecuted || $isFunded)
        <section class="alert alert--funded animate-in stagger-1">
            <div class="alert__icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="alert__content">
                <strong>Financements reçus !</strong>
                <p>Les fonds ont été transférés avec succès sur votre wallet BHDM.</p>
                <div class="alert__details">
                    <div class="detail-row highlight">
                        <span>Montant reçu :</span>
                        <strong class="amount-highlight">{{ number_format($request->amount_approved ?? $request->amount_requested, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    @if($request->transfer_executed_at)
                    <div class="detail-row">
                        <span>Date de transfert :</span>
                        <span>{{ $request->transfer_executed_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        @endif

        {{-- Carte principale --}}
        <section class="card detail-card animate-in stagger-2">

            {{-- En-tête avec statut --}}
            <div class="detail-status" style="background: {{ $current['bg'] }}; border-bottom: 2px solid {{ $current['color'] }};">
                <div class="detail-status__icon" style="background: {{ $current['color'] }}20; color: {{ $current['color'] }};">
                    <i class="fas {{ $current['icon'] }}"></i>
                </div>
                <div class="detail-status__info">
                    <span class="detail-status__label" style="color: {{ $current['color'] }};">{{ $current['label'] }}</span>
                    <span class="detail-status__description">{{ $current['description'] }}</span>
                    <span class="detail-status__date">Mis à jour le {{ $request->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            {{-- Informations générales --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-info-circle"></i> Informations générales</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-item__label">Numéro de demande</span>
                        <span class="detail-item__value font-mono">#{{ $request->request_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-item__label">Type de financement</span>
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
                        <span class="detail-item__label">Date de création</span>
                        <span class="detail-item__value">{{ $request->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($request->submitted_at)
                    <div class="detail-item">
                        <span class="detail-item__label">Date de soumission</span>
                        <span class="detail-item__value">{{ $request->submitted_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Montants --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-coins"></i> Montant du financement</h3>
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

                {{-- Montant transféré si exécuté --}}
                @if($transferExecuted)
                <div class="amount-transferred">
                    <div class="amount-transferred__header">
                        <i class="fas fa-check-circle"></i>
                        <span>Montant transféré sur votre wallet</span>
                    </div>
                    <div class="amount-transferred__amount">
                        {{ number_format($request->amount_approved ?? $request->amount_requested, 0, ',', ' ') }} FCFA
                    </div>
                    <div class="amount-transferred__date">
                        Transfert effectué le {{ $request->transfer_executed_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
                @endif

                <div class="detail-grid detail-grid--2cols mt-4">
                    <div class="detail-item">
                        <span class="detail-item__label">Durée du financement</span>
                        <span class="detail-item__value">{{ $request->duration }} mois</span>
                    </div>
                    @if($request->expected_jobs)
                    <div class="detail-item">
                        <span class="detail-item__label">Emplois attendus</span>
                        <span class="detail-item__value">{{ $request->expected_jobs }} postes</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Description avec toggle intelligent --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-align-left"></i> Description du projet</h3>
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
                <h3 class="detail-section__title"><i class="fas fa-comment-alt"></i> Notes de l'administration</h3>
                <div class="admin-notes {{ $request->status === 'rejected' ? 'admin-notes--rejected' : '' }}">
                    {{ $request->admin_validation_notes }}
                </div>
            </div>
            @endif

            {{-- Plan de remboursement --}}
            @if($request->monthly_repayment_amount)
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-calendar-alt"></i> Plan de remboursement</h3>
                <div class="repayment-card">
                    <div class="repayment-card__main">
                        <span class="repayment-card__label">Mensualité</span>
                        <span class="repayment-card__value">{{ number_format($request->monthly_repayment_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="repayment-card__details">
                        <div class="repayment-detail">
                            <span>Durée totale</span>
                            <strong>{{ $request->repayment_duration_months }} mois</strong>
                        </div>
                        @if($request->repayment_start_date)
                        <div class="repayment-detail">
                            <span>Premier prélèvement</span>
                            <strong>{{ \Carbon\Carbon::parse($request->repayment_start_date)->format('d/m/Y') }}</strong>
                        </div>
                        @endif
                        @if($request->total_repayment_amount)
                        <div class="repayment-detail">
                            <span>Montant total</span>
                            <strong>{{ number_format($request->total_repayment_amount, 0, ',', ' ') }} FCFA</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Historique détaillé avec couleurs --}}
            <div class="detail-section">
                <h3 class="detail-section__title"><i class="fas fa-history"></i> Historique de la demande</h3>
                <div class="timeline">
                    @php
                    $eventColors = [
                        'created' => ['color' => '#64748b', 'bg' => '#f1f5f9', 'label' => 'Création'],
                        'submitted' => ['color' => '#3b82f6', 'bg' => '#dbeafe', 'label' => 'Soumission'],
                        'review' => ['color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => 'Étude'],
                        'committee' => ['color' => '#f97316', 'bg' => '#ffedd5', 'label' => 'Comité'],
                        'validated' => ['color' => '#10b981', 'bg' => '#d1fae5', 'label' => 'Validation'],
                        'payment' => ['color' => '#8b5cf6', 'bg' => '#ede9fe', 'label' => 'Paiement'],
                        'verification' => ['color' => '#06b6d4', 'bg' => '#cffafe', 'label' => 'Vérification'],
                        'approved' => ['color' => '#059669', 'bg' => '#d1fae5', 'label' => 'Approbation'],
                        'transfer_scheduled' => ['color' => '#f97316', 'bg' => '#ffedd5', 'label' => 'Programmation'],
                        'funded' => ['color' => '#1e40af', 'bg' => '#dbeafe', 'label' => 'Financement'],
                    ];

                    $events = [];

                    // Toujours afficher la création
                    $events[] = ['date' => $request->created_at, 'icon' => 'fa-file-alt', 'label' => 'Demande créée', 'type' => 'created'];

                    // Soumission
                    if($request->submitted_at) {
                        $events[] = ['date' => $request->submitted_at, 'icon' => 'fa-paper-plane', 'label' => 'Demande soumise', 'type' => 'submitted'];
                    }

                    // Mise en étude
                    if($request->reviewed_at) {
                        $events[] = ['date' => $request->reviewed_at, 'icon' => 'fa-search', 'label' => 'Mise en étude', 'type' => 'review'];
                    }

                    // Validation (pour personnalisé)
                    if($request->validated_at && !$isPredefined) {
                        $events[] = ['date' => $request->validated_at, 'icon' => 'fa-clipboard-check', 'label' => 'Demande validée', 'type' => 'validated'];
                    }

                    // Paiement
                    if($request->paid_at) {
                        $events[] = ['date' => $request->paid_at, 'icon' => 'fa-credit-card', 'label' => 'Paiement effectué', 'type' => 'payment'];
                    }

                    // Vérification du paiement
                    if($request->paid_at && in_array($request->status, ['approved', 'funded', 'completed'])) {
                        $events[] = ['date' => $request->paid_at, 'icon' => 'fa-user-check', 'label' => 'Paiement vérifié', 'type' => 'verification'];
                    }

                    // Approbation finale
                    if($request->approved_at) {
                        $events[] = ['date' => $request->approved_at, 'icon' => 'fa-award', 'label' => 'Demande approuvée', 'type' => 'approved'];
                    }

                    // Programmation du transfert
                    if($request->transfer_scheduled_at) {
                        $events[] = ['date' => $request->transfer_scheduled_at, 'icon' => 'fa-calendar-check', 'label' => 'Transfert programmé', 'type' => 'transfer_scheduled'];
                    }

                    // Transfert exécuté
                    if($request->transfer_executed_at) {
                        $events[] = ['date' => $request->transfer_executed_at, 'icon' => 'fa-money-bill-wave', 'label' => 'Financements reçus', 'type' => 'funded'];
                    }
                    @endphp

                    @foreach($events as $event)
                    @php $colors = $eventColors[$event['type']] ?? $eventColors['created']; @endphp
                    <div class="timeline-item">
                        <div class="timeline-item__dot" style="background: {{ $colors['color'] }}; box-shadow: 0 0 0 4px {{ $colors['bg'] }};"></div>
                        <div class="timeline-item__content">
                            <span class="timeline-item__label" style="color: {{ $colors['color'] }};">{{ $event['label'] }}</span>
                            <span class="timeline-item__date">{{ $event['date']->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>
                    @endforeach

                    @if($isRejected || $isCancelled)
                    <div class="timeline-item timeline-item--{{ $isRejected ? 'rejected' : 'cancelled' }}">
                        <div class="timeline-item__dot" style="background: #dc2626; box-shadow: 0 0 0 4px #fee2e2;"></div>
                        <div class="timeline-item__content">
                            <span class="timeline-item__label" style="color: #dc2626;">{{ $isRejected ? 'Demande rejetée' : 'Demande annulée' }}</span>
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
                <a href="{{ route('client.requests.payment', $request->id) }}" class="btn btn--payment btn--large">
                    <i class="fas fa-credit-card"></i>
                    Effectuer le paiement
                </a>
            @endif

            @if($request->status === 'draft')
                <a href="{{ route('client.requests.edit', $request->id) }}" class="btn btn--secondary">
                    <i class="fas fa-edit"></i>
                    Modifier ma demande
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
                Retour à mes demandes
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
            <p class="modal__warning">Cette action est irréversible et supprimera définitivement votre demande.</p>
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
    --cyan: #06b6d4;
    --orange: #f97316;
    --indigo: #1e40af;
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

/* BADGES SPÉCIFIQUES */
.badge--committee { background: #ffedd5; color: #c2410c; }
.badge--validated { background: #d1fae5; color: #047857; }
.badge--payment-required { background: #ede9fe; color: #7c3aed; }
.badge--paid { background: #cffafe; color: #0e7490; }
.badge--approved { background: #d1fae5; color: #047857; }
.badge--funded { background: #dbeafe; color: #1e40af; }
.badge--completed { background: #d1fae5; color: #065f46; }

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

/* BOUTON PAIEMENT - VIOLET */
.btn--payment {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.btn--payment:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
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
    gap: 4px;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 70px;
    position: relative;
}

.progress-step__icon {
    width: 40px; height: 40px;
    border-radius: 50%;
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
    background: var(--success) !important;
    color: white !important;
}

.progress-step__label {
    font-size: 0.625rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.progress-step__pulse {
    position: absolute;
    width: 40px; height: 40px;
    border-radius: 50%;
    opacity: 0.3;
    animation: ripple 2s infinite;
    z-index: 1;
}

.progress-step__line {
    flex: 1;
    height: 3px;
    background: #e2e8f0;
    margin-top: 18px;
    min-width: 8px;
    max-width: 20px;
    transition: all 0.3s;
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
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-bar__percent {
    font-size: 0.875rem;
    font-weight: 700;
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
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border-radius: 10px;
    font-size: 0.875rem;
}

.progress-message i {
    margin-top: 2px;
    font-size: 1.125rem;
}

.progress-message__content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.progress-message__content strong {
    font-weight: 700;
    font-size: 0.9375rem;
}

.progress-message__content span {
    opacity: 0.9;
    font-size: 0.8125rem;
}

/* ===== ALERTS AVEC COULEURS DISTINCTES ===== */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
    border-radius: var(--radius-lg);
    margin-bottom: 16px;
    border: 2px solid transparent;
}

/* PAIEMENT REQUIS - VIOLET */
.alert--payment {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border-color: #ddd6fe;
}
.alert--payment .alert__icon { background: #8b5cf620; color: #7c3aed; }

/* VÉRIFICATION EN COURS - CYAN */
.alert--verification {
    background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
    border-color: #a5f3fc;
}
.alert--verification .alert__icon { background: #06b6d420; color: #0891b2; }

/* PAIEMENT TRAITÉ - VIOLET FONCÉ */
.alert--processed {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    border-color: #e9d5ff;
}
.alert--processed .alert__icon { background: #a855f720; color: #7e22ce; }

/* TRANSFERT PROGRAMMÉ - ORANGE FONCÉ */
.alert--transfer {
    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    border-color: #fed7aa;
}
.alert--transfer .alert__icon { background: #f9731620; color: #ea580c; }

/* FINANCEMENT EXÉCUTÉ - BLEU INDIGO */
.alert--funded {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #bfdbfe;
}
.alert--funded .alert__icon { background: #1e40af20; color: #1e40af; }

.alert__icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert__content { flex: 1; min-width: 0; }
.alert__content strong { display: block; margin-bottom: 6px; color: var(--text); font-size: 1rem; }
.alert__content p { margin: 0 0 12px 0; font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; }
.alert__content small { display: block; margin-top: 8px; font-size: 0.75rem; color: var(--text-muted); font-style: italic; }

.alert__amount {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(255,255,255,0.6);
    border-radius: 8px;
    margin-top: 12px;
}

.alert__amount strong {
    font-size: 1.125rem;
    color: #7c3aed;
}

.alert__details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(0,0,0,0.1);
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    gap: 12px;
}

.detail-row span:first-child { color: var(--text-muted); flex-shrink: 0; }
.detail-row strong { color: var(--text); }
.detail-row.highlight { padding: 8px 12px; background: rgba(255,255,255,0.6); border-radius: 6px; }
.detail-row.highlight strong { color: #ea580c; font-size: 1rem; }

.alert--funded .detail-row.highlight strong.amount-highlight {
    color: #1e40af;
    font-size: 1.25rem;
}

.alert__details code {
    background: rgba(0,0,0,0.05);
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    word-break: break-all;
}

/* ===== DETAIL CARD ===== */
.detail-card { margin-bottom: 16px; }

.detail-status {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
}

.detail-status__icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.detail-status__info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-status__label {
    font-size: 1.125rem;
    font-weight: 700;
    line-height: 1.2;
}

.detail-status__description {
    font-size: 0.875rem;
    color: var(--text-muted);
    line-height: 1.4;
}

.detail-status__date {
    font-size: 0.75rem;
    color: var(--text-muted);
    opacity: 0.8;
    margin-top: 4px;
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
    margin-bottom: 12px;
}

.amount-approved strong { color: #047857; }

/* Montant transféré */
.amount-transferred {
    padding: 20px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 12px;
    border: 2px solid #bfdbfe;
    margin-bottom: 16px;
    text-align: center;
}

.amount-transferred__header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #1e40af;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.amount-transferred__header i {
    font-size: 1.25rem;
}

.amount-transferred__amount {
    font-size: 2rem;
    font-weight: 800;
    color: #1e40af;
    margin-bottom: 8px;
}

.amount-transferred__date {
    font-size: 0.8125rem;
    color: #3b82f6;
}

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

/* Timeline avec couleurs distinctes */
.timeline {
    position: relative;
    padding-left: 28px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: var(--border);
}

.timeline-item {
    position: relative;
    padding-bottom: 24px;
}

.timeline-item:last-child { padding-bottom: 0; }

.timeline-item__dot {
    position: absolute;
    left: -24px;
    top: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-item__content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.timeline-item__label {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text);
}

.timeline-item__date {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.timeline-item__note {
    margin-top: 8px;
    padding: 12px;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #991b1b;
    line-height: 1.5;
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
    .progress-step__label { font-size: 0.5625rem; max-width: 60px; }
    .amount-display__value { font-size: 2rem; }
    .progress-step__icon { width: 36px; height: 36px; }
    .detail-status__label { font-size: 1rem; }
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
