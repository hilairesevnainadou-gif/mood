@extends('layouts.client')

@section('title', 'Mes Demandes')

@section('content')
<script>
// Fonctions globales PWA
window.toggleRequestActions = function(requestId) {
    const card = document.querySelector('.pwa-request-card[data-id="' + requestId + '"]');
    if (!card) return;

    document.querySelectorAll('.pwa-request-card.expanded').forEach(function(c) {
        if (c !== card) c.classList.remove('expanded');
    });
    card.classList.toggle('expanded');
};

window.filterRequests = function(filter) {
    document.querySelectorAll('.pwa-filter-chip').forEach(function(chip) {
        chip.classList.remove('active');
        if (chip.getAttribute('data-filter') === filter) {
            chip.classList.add('active');
        }
    });

    document.querySelectorAll('.pwa-request-card').forEach(function(card) {
        var status = card.getAttribute('data-status');
        var show = false;

        switch(filter) {
            case 'all': show = true; break;
            case 'draft': show = (status === 'draft'); break;
            case 'pending': show = ['submitted', 'under_review', 'pending_committee'].includes(status); break;
            case 'payment': show = ['validated', 'pending_payment'].includes(status); break;
            case 'processing': show = ['paid', 'approved', 'documents_validated', 'transfer_pending', 'funded', 'in_progress'].includes(status); break;
            case 'completed': show = (status === 'completed'); break;
            case 'rejected': show = ['rejected', 'cancelled'].includes(status); break;
        }

        card.style.display = show ? 'flex' : 'none';
    });
};

window.pwaCancelId = null;

window.confirmCancel = function(id, number) {
    window.pwaCancelId = id;
    var numEl = document.getElementById('pwaCancelNumber');
    if (numEl) numEl.textContent = number;
    var sheet = document.getElementById('pwaCancelSheet');
    if (sheet) sheet.classList.add('show');
};

window.closeCancel = function() {
    var sheet = document.getElementById('pwaCancelSheet');
    if (sheet) sheet.classList.remove('show');
    window.pwaCancelId = null;
};

window.executeCancel = function() {
    if (!window.pwaCancelId) return;

    var btn = document.getElementById('pwaBtnCancel');
    var spinner = btn ? btn.querySelector('.spinner-border') : null;
    var text = btn ? btn.querySelector('.pwa-btn-text') : null;

    if (btn) btn.disabled = true;
    if (text) text.textContent = 'Annulation...';
    if (spinner) spinner.classList.remove('d-none');

    // TODO: Appel AJAX pour annuler la demande
    setTimeout(function() {
        window.closeCancel();
        if (window.toast) window.toast.success('Succès', 'Demande annulée avec succès');
        setTimeout(function() { window.location.reload(); }, 1000);
    }, 1500);
};

window.showStatusInfo = function(status, title) {
    const statusMessages = {
        'draft': 'Votre demande est en brouillon. Finalisez et soumettez-la quand vous êtes prêt.',
        'submitted': 'Votre demande a été soumise et est en attente d\'examen par notre équipe.',
        'under_review': 'Votre demande est en cours d\'examen approfondi par nos analystes.',
        'pending_committee': 'Votre demande est soumise au comité local pour décision finale.',
        'validated': 'Votre demande a été validée. Effectuez le paiement des frais pour continuer.',
        'pending_payment': 'Veuillez effectuer le paiement via Kkiapay pour finaliser votre dossier.',
        'paid': 'Votre paiement a été confirmé. Votre dossier est en attente de vérification des documents.',
        'approved': 'Votre demande est approuvée et en attente de programmation du transfert.',
        'documents_validated': 'Vos documents sont validés. Le transfert est programmé et sera exécuté prochainement.',
        'transfer_pending': 'Le transfert est programmé. Les fonds seront bientôt disponibles sur votre wallet.',
        'funded': 'Les fonds ont été transférés sur votre wallet. Vous pouvez les utiliser.',
        'in_progress': 'Votre projet est en cours d\'exécution. Suivez les étapes de réalisation.',
        'completed': 'Votre demande est terminée avec succès. Merci pour votre confiance !',
        'rejected': 'Votre demande a été rejetée. Contactez le support pour plus d\'informations.',
        'cancelled': 'Votre demande a été annulée à votre demande.'
    };

    var msgEl = document.getElementById('pwaStatusMessage');
    var titleEl = document.getElementById('pwaStatusTitle');
    if (msgEl) msgEl.textContent = statusMessages[status] || 'Statut en cours de traitement.';
    if (titleEl) titleEl.textContent = title || 'Information';

    var modalEl = document.getElementById('pwaStatusModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

// Refresh button
document.addEventListener('DOMContentLoaded', function() {
    var refreshBtn = document.getElementById('pwaRefreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('spinning');
            setTimeout(function() { window.location.reload(); }, 500);
        });
    }
});
</script>

<div class="pwa-requests-container">
    {{-- Header --}}
    <div class="pwa-requests-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <div class="pwa-requests-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="pwa-header-text">
                <h1>Mes Demandes</h1>
                <p>{{ $requests->count() }} demande(s) au total</p>
                @php
                    // Comptage précis selon les statuts réels avec nouveaux statuts
                    $draftCount = $requests->where('status', 'draft')->count();
                    $pendingReviewCount = $requests->whereIn('status', ['submitted', 'under_review', 'pending_committee'])->count();
                    $paymentPendingCount = $requests->whereIn('status', ['validated', 'pending_payment'])->whereNull('kkiapay_transaction_id')->count();
                    // NOUVEAU: Comptage des transferts programmés/en cours
                    $transferScheduledCount = $requests->whereIn('status', ['paid', 'approved', 'documents_validated', 'transfer_pending'])->count();
                    $fundedCount = $requests->whereIn('status', ['funded', 'in_progress'])->count();
                    $completedCount = $requests->where('status', 'completed')->count();
                    $rejectedCount = $requests->whereIn('status', ['rejected', 'cancelled'])->count();

                    // Badge prioritaire
                    $badgeType = 'success';
                    $badgeMessage = 'À jour';
                    $badgeIcon = 'fa-check-circle';

                    if ($paymentPendingCount > 0) {
                        $badgeType = 'urgent';
                        $badgeMessage = $paymentPendingCount . ' paiement(s) requis';
                        $badgeIcon = 'fa-exclamation-circle';
                    } elseif ($transferScheduledCount > 0) {
                        $badgeType = 'info';
                        $badgeMessage = $transferScheduledCount . ' en traitement';
                        $badgeIcon = 'fa-clock';
                    } elseif ($pendingReviewCount > 0) {
                        $badgeType = 'pending';
                        $badgeMessage = $pendingReviewCount . ' en examen';
                        $badgeIcon = 'fa-clock';
                    } elseif ($draftCount > 0) {
                        $badgeType = 'draft';
                        $badgeMessage = $draftCount . ' brouillon(s)';
                        $badgeIcon = 'fa-edit';
                    }
                @endphp

                <span class="pwa-badge-{{ $badgeType }} {{ $badgeType === 'urgent' ? 'pulse-badge' : '' }}">
                    <i class="fas {{ $badgeIcon }}"></i> {{ $badgeMessage }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Scroll --}}
    <div class="pwa-stats-scroll">
        <div class="pwa-stats-track">
            <div class="pwa-stat-pill total">
                <div class="pwa-stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $requests->count() }}</span>
                    <span class="pwa-stat-label">Total</span>
                </div>
            </div>
            @if($draftCount > 0)
            <div class="pwa-stat-pill draft">
                <div class="pwa-stat-icon"><i class="fas fa-edit"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $draftCount }}</span>
                    <span class="pwa-stat-label">Brouillons</span>
                </div>
            </div>
            @endif
            <div class="pwa-stat-pill pending">
                <div class="pwa-stat-icon"><i class="fas fa-clock"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $pendingReviewCount }}</span>
                    <span class="pwa-stat-label">En examen</span>
                </div>
            </div>
            @if($paymentPendingCount > 0)
            <div class="pwa-stat-pill urgent">
                <div class="pwa-stat-icon"><i class="fas fa-credit-card"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $paymentPendingCount }}</span>
                    <span class="pwa-stat-label">À payer</span>
                </div>
            </div>
            @endif
            @if($transferScheduledCount > 0)
            <div class="pwa-stat-pill info">
                <div class="pwa-stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $transferScheduledCount }}</span>
                    <span class="pwa-stat-label">Transferts</span>
                </div>
            </div>
            @endif
            @if($fundedCount > 0)
            <div class="pwa-stat-pill funded">
                <div class="pwa-stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $fundedCount }}</span>
                    <span class="pwa-stat-label">Financées</span>
                </div>
            </div>
            @endif
            <div class="pwa-stat-pill completed">
                <div class="pwa-stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $completedCount }}</span>
                    <span class="pwa-stat-label">Terminées</span>
                </div>
            </div>
            <div class="pwa-stat-pill amount">
                <div class="pwa-stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ number_format($requests->sum('amount_requested')/1000000, 1) }}M</span>
                    <span class="pwa-stat-label">FCFA</span>
                </div>
            </div>
        </div>
    </div>

    {{-- New Request Button --}}
    <div class="pwa-new-request-wrap">
        <a href="{{ route('client.requests.create') }}" class="pwa-new-request-btn">
            <i class="fas fa-plus-circle"></i>
            <span>Nouvelle demande</span>
            <i class="fas fa-arrow-right ml-auto"></i>
        </a>
    </div>

    {{-- Filters --}}
    <div class="pwa-filters-wrap">
        <div class="pwa-filters-scroll">
            <button class="pwa-filter-chip active" data-filter="all" onclick="filterRequests('all')">
                <span>Toutes</span>
                <span class="pwa-filter-count">{{ $requests->count() }}</span>
            </button>
            @if($draftCount > 0)
            <button class="pwa-filter-chip" data-filter="draft" onclick="filterRequests('draft')">
                <i class="fas fa-edit text-secondary me-1"></i> Brouillons
            </button>
            @endif
            <button class="pwa-filter-chip" data-filter="pending" onclick="filterRequests('pending')">
                <i class="fas fa-clock text-info me-1"></i> En examen
            </button>
            @if($paymentPendingCount > 0)
            <button class="pwa-filter-chip urgent-filter" data-filter="payment" onclick="filterRequests('payment')">
                <i class="fas fa-credit-card text-warning me-1"></i> Paiement
            </button>
            @endif
            @if($transferScheduledCount > 0)
            <button class="pwa-filter-chip info-filter" data-filter="processing" onclick="filterRequests('processing')">
                <i class="fas fa-calendar-check text-info me-1"></i> Transferts
            </button>
            @else
            <button class="pwa-filter-chip" data-filter="processing" onclick="filterRequests('processing')">
                <i class="fas fa-sync text-primary me-1"></i> Traitement
            </button>
            @endif
            <button class="pwa-filter-chip" data-filter="completed" onclick="filterRequests('completed')">
                <i class="fas fa-trophy text-success me-1"></i> Terminées
            </button>
            <button class="pwa-filter-chip" data-filter="rejected" onclick="filterRequests('rejected')">
                <i class="fas fa-times-circle text-danger me-1"></i> Rejetées
            </button>
        </div>
        <button class="pwa-refresh-btn" id="pwaRefreshBtn">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    {{-- Requests List --}}
    <div class="pwa-requests-list">
        @forelse($requests as $request)
        @php
            // Configuration complète de tous les statuts avec NOUVEAUX statuts
            $statusConfig = [
                // Phase 1: Création
                'draft' => [
                    'label' => 'Brouillon',
                    'class' => 'draft',
                    'icon' => 'fa-edit',
                    'color' => '#6b7280',
                    'bg' => '#f3f4f6',
                    'description' => 'En cours de rédaction'
                ],

                // Phase 2: Soumission et examen
                'submitted' => [
                    'label' => 'Soumise',
                    'class' => 'submitted',
                    'icon' => 'fa-paper-plane',
                    'color' => '#3b82f6',
                    'bg' => '#dbeafe',
                    'description' => 'En attente d\'examen'
                ],
                'under_review' => [
                    'label' => 'En étude',
                    'class' => 'review',
                    'icon' => 'fa-search',
                    'color' => '#6366f1',
                    'bg' => '#e0e7ff',
                    'description' => 'Examen en cours'
                ],
                'pending_committee' => [
                    'label' => 'Comité',
                    'class' => 'committee',
                    'icon' => 'fa-users',
                    'color' => '#8b5cf6',
                    'bg' => '#ede9fe',
                    'description' => 'Décision du comité'
                ],

                // Phase 3: Validation et paiement
                'validated' => [
                    'label' => 'Validée',
                    'class' => 'validated',
                    'icon' => 'fa-check-circle',
                    'color' => '#10b981',
                    'bg' => '#d1fae5',
                    'description' => 'En attente de paiement'
                ],
                'pending_payment' => [
                    'label' => 'Paiement requis',
                    'class' => 'payment',
                    'icon' => 'fa-credit-card',
                    'color' => '#f59e0b',
                    'bg' => '#fef3c7',
                    'description' => 'Paiement en attente'
                ],

                // Phase 4: Paiement effectué et traitement
                'paid' => [
                    'label' => 'Payée',
                    'class' => 'paid',
                    'icon' => 'fa-money-bill-wave',
                    'color' => '#059669',
                    'bg' => '#a7f3d0',
                    'description' => 'Vérification des documents'
                ],
                'approved' => [
                    'label' => 'Approuvée',
                    'class' => 'approved',
                    'icon' => 'fa-award',
                    'color' => '#047857',
                    'bg' => '#6ee7b7',
                    'description' => 'En attente de programmation'
                ],
                // NOUVEAUX STATUTS
                'documents_validated' => [
                    'label' => 'Docs validés',
                    'class' => 'docs-validated',
                    'icon' => 'fa-file-signature',
                    'color' => '#0ea5e9',
                    'bg' => '#e0f2fe',
                    'description' => 'Transfert programmé'
                ],
                'transfer_pending' => [
                    'label' => 'Transfert imminent',
                    'class' => 'transfer-pending',
                    'icon' => 'fa-clock',
                    'color' => '#8b5cf6',
                    'bg' => '#ede9fe',
                    'description' => 'Exécution prochaine'
                ],
                'funded' => [
                    'label' => 'Financée',
                    'class' => 'funded',
                    'icon' => 'fa-wallet',
                    'color' => '#1d4ed8',
                    'bg' => '#bfdbfe',
                    'description' => 'Fonds transférés'
                ],
                'in_progress' => [
                    'label' => 'En cours',
                    'class' => 'progress',
                    'icon' => 'fa-spinner fa-spin',
                    'color' => '#0284c7',
                    'bg' => '#bae6fd',
                    'description' => 'Projet en exécution'
                ],

                // Phase 5: Finalisation
                'completed' => [
                    'label' => 'Terminée',
                    'class' => 'completed',
                    'icon' => 'fa-trophy',
                    'color' => '#15803d',
                    'bg' => '#86efac',
                    'description' => 'Projet complété'
                ],

                // Phase 6: Rejet/Annulation
                'rejected' => [
                    'label' => 'Rejetée',
                    'class' => 'rejected',
                    'icon' => 'fa-times-circle',
                    'color' => '#dc2626',
                    'bg' => '#fecaca',
                    'description' => 'Demande rejetée'
                ],
                'cancelled' => [
                    'label' => 'Annulée',
                    'class' => 'cancelled',
                    'icon' => 'fa-ban',
                    'color' => '#9ca3af',
                    'bg' => '#e5e7eb',
                    'description' => 'Demande annulée'
                ],
            ];

            $config = $statusConfig[$request->status] ?? [
                'label' => $request->status,
                'class' => 'default',
                'icon' => 'fa-circle',
                'color' => '#6b7280',
                'bg' => '#f3f4f6',
                'description' => 'Statut: ' . $request->status
            ];

            // Déterminer les actions possibles avec NOUVELLES conditions
            $isPaid = !empty($request->kkiapay_transaction_id);
            $needsPayment = in_array($request->status, ['validated', 'pending_payment']) && !$isPaid;
            $canEdit = ($request->status === 'draft');
            $canCancel = in_array($request->status, ['draft', 'submitted', 'validated', 'pending_payment']);
            $showKkiapay = $isPaid;
            // NOUVEAU: Afficher wallet pour funded ET les nouveaux statuts de transfert
            $showWallet = in_array($request->status, ['funded', 'in_progress']);
            $showTransferInfo = in_array($request->status, ['documents_validated', 'transfer_pending']);
            $isCompleted = ($request->status === 'completed');
            $isRejected = in_array($request->status, ['rejected', 'cancelled']);

            // NOUVEAU: Badge spécial pour transfert programmé
            $isTransferScheduled = $request->transfer_scheduled_at && !$request->transfer_executed_at;
        @endphp

        <div class="pwa-request-card status-{{ $config['class'] }} {{ $needsPayment ? 'payment-urgent' : '' }} {{ $showTransferInfo ? 'transfer-scheduled' : '' }} {{ $isRejected ? 'status-rejected-card' : '' }}"
             data-status="{{ $request->status }}"
             data-id="{{ $request->id }}">

            {{-- Banner Paiement Requis --}}
            @if($needsPayment && $request->expected_payment > 0)
            <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-urgent-banner" onclick="event.stopPropagation();">
                <div class="pwa-urgent-content">
                    <i class="fas fa-exclamation-circle pulse-icon"></i>
                    <div class="pwa-urgent-text">
                        <strong>Paiement requis</strong>
                        <span>{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <span class="pwa-urgent-btn">Payer maintenant <i class="fas fa-arrow-right"></i></span>
            </a>
            @endif

            {{-- Banner Transfert Programmé (NOUVEAU) --}}
            @if($showTransferInfo)
            <div class="pwa-transfer-banner">
                <div class="pwa-transfer-content">
                    <i class="fas fa-calendar-check pulse-icon"></i>
                    <div class="pwa-transfer-text">
                        <strong>Transfert programmé</strong>
                        <span>
                            @if($request->transfer_scheduled_at)
                                Pour le {{ $request->transfer_scheduled_at->format('d/m/Y à H:i') }}
                            @else
                                En cours de programmation
                            @endif
                        </span>
                    </div>
                </div>
                @if($request->monthly_repayment_amount)
                <span class="pwa-transfer-amount">{{ number_format($request->monthly_repayment_amount, 0, ',', ' ') }} F/mois</span>
                @endif
            </div>
            @endif

            {{-- Banner Kkiapay Confirmé --}}
            @if($showKkiapay)
            <div class="pwa-success-banner">
                <div class="pwa-success-content">
                    <i class="fas fa-check-circle"></i>
                    <div class="pwa-success-text">
                        <strong>Payée via Kkiapay</strong>
                        <span class="pwa-kkiapay-id">{{ Str::limit($request->kkiapay_transaction_id, 12) }}</span>
                    </div>
                </div>
                <span class="pwa-success-amount">{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} F</span>
            </div>
            @endif

            {{-- Banner Complétée --}}
            @if($isCompleted)
            <div class="pwa-completed-banner">
                <div class="pwa-completed-content">
                    <i class="fas fa-trophy"></i>
                    <span>Projet terminé avec succès !</span>
                </div>
            </div>
            @endif

            {{-- Banner Rejetée --}}
            @if($isRejected)
            <div class="pwa-rejected-banner">
                <div class="pwa-rejected-content">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ $request->status === 'cancelled' ? 'Annulée par vous' : 'Demande rejetée' }}</span>
                </div>
            </div>
            @endif

            <div class="pwa-card-main" onclick="toggleRequestActions({{ $request->id }})">
                <div class="pwa-request-status-icon" style="background: {{ $config['bg'] }}; color: {{ $config['color'] }}">
                    <i class="fas {{ $config['icon'] }}"></i>
                </div>

                <div class="pwa-request-details">
                    <div class="pwa-request-header-row">
                        <h3>#{{ $request->request_number }}</h3>
                        <span class="pwa-request-amount">{{ number_format($request->amount_requested, 0, ',', ' ') }} F</span>
                    </div>

                    <p class="pwa-request-title">{{ Str::limit($request->title, 45) }}</p>

                    <div class="pwa-request-meta">
                        <span class="pwa-request-type {{ $request->is_predefined ? 'predefined' : 'custom' }}">
                            <i class="fas {{ $request->is_predefined ? 'fa-box' : 'fa-pencil-alt' }}"></i>
                            {{ $request->is_predefined ? 'Prédéfini' : 'Personnalisé' }}
                        </span>
                        <span class="pwa-request-date">
                            <i class="far fa-calendar"></i> {{ $request->created_at->format('d/m/Y') }}
                        </span>
                        @if($request->duration)
                        <span class="pwa-request-duration">
                            <i class="far fa-clock"></i> {{ $request->duration }} mois
                        </span>
                        @endif
                    </div>

                    <div class="pwa-request-status-row">
                        <span class="pwa-status-badge" style="background-color: {{ $config['bg'] }}; color: {{ $config['color'] }}; border: 1px solid {{ $config['color'] }}40;">
                            {{ $config['label'] }}
                        </span>
                        <span class="pwa-status-desc">{{ $config['description'] }}</span>
                    </div>

                    {{-- NOUVEAU: Info remboursement si programmé --}}
                    @if($request->total_repayment_amount && $request->repayment_duration_months)
                    <div class="pwa-repayment-preview">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Remboursement: {{ number_format($request->total_repayment_amount, 0, ',', ' ') }} F sur {{ $request->repayment_duration_months }} mois</span>
                    </div>
                    @endif
                </div>

                <div class="pwa-request-chevron">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pwa-card-actions">
                <div class="pwa-actions-grid">
                    {{-- Voir détails --}}
                    <a href="{{ route('client.requests.show', $request->id) }}" class="pwa-action-btn view" onclick="event.stopPropagation();">
                        <i class="fas fa-eye"></i><span>Détails</span>
                    </a>

                    {{-- Modifier (brouillon uniquement) --}}
                    @if($canEdit)
                    <a href="{{ route('client.requests.edit', $request->id) }}" class="pwa-action-btn edit" onclick="event.stopPropagation();">
                        <i class="fas fa-pen"></i><span>Modifier</span>
                    </a>
                    @endif

                    {{-- Payer --}}
                    @if($needsPayment)
                    <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-action-btn pay pulse-action" onclick="event.stopPropagation();">
                        <i class="fas fa-credit-card"></i><span>Payer</span>
                    </a>
                    @endif

                    {{-- Voir Kkiapay --}}
                    @if($showKkiapay)
                    <button class="pwa-action-btn kkiapay" onclick="event.stopPropagation(); showKkiapayDetails('{{ $request->kkiapay_transaction_id }}', {{ $request->kkiapay_amount_paid }});">
                        <i class="fas fa-receipt"></i><span>Kkiapay</span>
                    </button>
                    @endif

                    {{-- Wallet/Transfert --}}
                    @if($showWallet)
                    <a href="{{ route('client.wallet.index') }}" class="pwa-action-btn transfer" onclick="event.stopPropagation();">
                        <i class="fas fa-wallet"></i><span>Wallet</span>
                    </a>
                    @endif

                    {{-- NOUVEAU: Info transfert programmé --}}
                    @if($showTransferInfo)
                    <button class="pwa-action-btn schedule" onclick="event.stopPropagation(); showTransferDetails({{ $request->id }});">
                        <i class="fas fa-calendar-check"></i><span>Programmé</span>
                    </button>
                    @endif

                    {{-- Info statut --}}
                    <button class="pwa-action-btn info" onclick="event.stopPropagation(); showStatusInfo('{{ $request->status }}', 'Demande #{{ $request->request_number }}');">
                        <i class="fas fa-info-circle"></i><span>Info</span>
                    </button>

                    {{-- Annuler --}}
                    @if($canCancel)
                    <button class="pwa-action-btn delete" onclick="event.stopPropagation(); confirmCancel({{ $request->id }}, '{{ $request->request_number }}');">
                        <i class="fas fa-trash"></i><span>Annuler</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="pwa-empty-state">
            <div class="pwa-empty-icon"><i class="fas fa-file-contract"></i></div>
            <h3>Aucune demande</h3>
            <p>Créez votre première demande de financement pour démarrer</p>
            <a href="{{ route('client.requests.create') }}" class="pwa-btn-primary">
                <i class="fas fa-plus me-2"></i> Nouvelle demande
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- FAB --}}
<a href="{{ route('client.requests.create') }}" class="pwa-fab" title="Nouvelle demande">
    <i class="fas fa-plus"></i>
</a>

{{-- Bottom Sheet Cancel --}}
<div class="pwa-bottom-sheet" id="pwaCancelSheet">
    <div class="pwa-sheet-overlay" onclick="closeCancel()"></div>
    <div class="pwa-sheet-content">
        <div class="pwa-sheet-header">
            <div class="pwa-sheet-drag"></div>
            <h3>Annuler la demande</h3>
        </div>
        <div class="pwa-sheet-body">
            <p>Êtes-vous sûr de vouloir annuler la demande <strong id="pwaCancelNumber"></strong> ?</p>
            <p class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Cette action est irréversible.</p>
        </div>
        <div class="pwa-sheet-footer">
            <button class="pwa-btn-cancel" onclick="closeCancel()">Retour</button>
            <button class="pwa-btn-confirm-delete" id="pwaBtnCancel" onclick="executeCancel()">
                <span class="pwa-btn-text">Confirmer l'annulation</span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
    </div>
</div>

{{-- Status Info Modal --}}
<div class="modal fade" id="pwaStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="pwaStatusTitle">Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="pwaStatusMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i> Compris
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Transfer Details Modal (NOUVEAU) --}}
<div class="modal fade" id="transferDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-check me-2"></i> Détails du transfert</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="transfer-icon-lg">
                        <i class="fas fa-clock fa-3x text-info"></i>
                    </div>
                    <h4 class="mt-2">Transfert programmé</h4>
                </div>
                <div class="transfer-details bg-light p-3 rounded">
                    <div class="mb-2">
                        <small class="text-muted">Date programmée</small>
                        <p class="mb-0 fw-bold" id="transferDate">-</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Montant à transférer</small>
                        <p class="mb-0 fw-bold text-primary" id="transferAmount">-</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Mensualité de remboursement</small>
                        <p class="mb-0 fw-bold text-info" id="transferMonthly">-</p>
                    </div>
                    <div>
                        <small class="text-muted">Durée</small>
                        <p class="mb-0" id="transferDuration">-</p>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle"></i>
                    Vous recevrez une notification dès que les fonds seront crédités sur votre wallet.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Kkiapay Details Modal --}}
<div class="modal fade" id="kkiapayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i> Détails du paiement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="kkiapay-icon mb-3">
                    <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                </div>
                <h4 class="mb-2">Paiement confirmé</h4>
                <p class="text-muted mb-3">Transaction Kkiapay</p>
                <div class="kkiapay-details bg-light p-3 rounded">
                    <div class="mb-2">
                        <small class="text-muted">ID Transaction</small>
                        <p class="font-monospace mb-0" id="kkiapayId"></p>
                    </div>
                    <div>
                        <small class="text-muted">Montant payé</small>
                        <p class="h4 text-success mb-0" id="kkiapayAmount"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Variables CSS étendues avec NOUVELLES couleurs */
:root {
    --primary-500: #1b5a8d;
    --primary-600: #164a77;
    --primary-50: #e8f4fd;

    --draft-color: #6b7280;
    --draft-bg: #f3f4f6;
    --submitted-color: #3b82f6;
    --submitted-bg: #dbeafe;
    --review-color: #6366f1;
    --review-bg: #e0e7ff;
    --committee-color: #8b5cf6;
    --committee-bg: #ede9fe;
    --validated-color: #10b981;
    --validated-bg: #d1fae5;
    --payment-color: #f59e0b;
    --payment-bg: #fef3c7;
    --paid-color: #059669;
    --paid-bg: #a7f3d0;
    --approved-color: #047857;
    --approved-bg: #6ee7b7;
    --docs-validated-color: #0ea5e9;
    --docs-validated-bg: #e0f2fe;
    --transfer-pending-color: #8b5cf6;
    --transfer-pending-bg: #ede9fe;
    --funded-color: #1d4ed8;
    --funded-bg: #bfdbfe;
    --progress-color: #0284c7;
    --progress-bg: #bae6fd;
    --completed-color: #15803d;
    --completed-bg: #86efac;
    --rejected-color: #dc2626;
    --rejected-bg: #fecaca;
    --cancelled-color: #9ca3af;
    --cancelled-bg: #e5e7eb;

    --secondary-50: #f8fafc;
    --secondary-100: #f1f5f9;
    --secondary-200: #e2e8f0;
    --secondary-300: #cbd5e1;
    --secondary-400: #94a3b8;
    --secondary-500: #64748b;
    --secondary-600: #475569;
    --secondary-700: #334155;
    --secondary-800: #1e293b;

    --urgent-color: #f59e0b;
    --info-color: #06b6d4;
}

/* Base */
.pwa-requests-container { padding: 0 0 2rem 0; background: var(--secondary-50); min-height: 100vh; }

/* Header */
.pwa-requests-header {
    background: linear-gradient(135deg, var(--primary-600) 0%, #113a61 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin: 0 0 1rem 0;
    position: relative;
    overflow: hidden;
}
.pwa-header-bg { position: absolute; inset: 0; opacity: 0.1; background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px; }
.pwa-header-content { position: relative; display: flex; align-items: center; gap: 1rem; color: white; }
.pwa-requests-icon {
    width: 60px; height: 60px; border-radius: 16px;
    background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; flex-shrink: 0;
}
.pwa-header-text h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; }
.pwa-header-text p { font-size: 0.85rem; opacity: 0.9; margin: 0 0 0.5rem 0; }

/* Badges */
.pwa-badge-urgent, .pwa-badge-pending, .pwa-badge-success, .pwa-badge-draft, .pwa-badge-info {
    display: inline-flex; align-items: center; gap: 0.375rem;
    padding: 0.35rem 0.75rem; border-radius: 50px;
    font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(10px);
}
.pwa-badge-urgent { background: rgba(245, 158, 11, 0.3); border: 1px solid rgba(245, 158, 11, 0.5); color: #fffbeb; }
.pwa-badge-pending { background: rgba(59, 130, 246, 0.3); border: 1px solid rgba(59, 130, 246, 0.5); color: #dbeafe; }
.pwa-badge-success { background: rgba(34, 197, 94, 0.3); border: 1px solid rgba(34, 197, 94, 0.5); color: #dcfce7; }
.pwa-badge-draft { background: rgba(107, 114, 128, 0.3); border: 1px solid rgba(107, 114, 128, 0.5); color: #f3f4f6; }
.pwa-badge-info { background: rgba(6, 182, 212, 0.3); border: 1px solid rgba(6, 182, 212, 0.5); color: #ecfeff; }
.pulse-badge { animation: pulse-badge 2s infinite; }
@keyframes pulse-badge { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

/* Banners */
.pwa-urgent-banner {
    display: flex; justify-content: space-between; align-items: center;
    background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    color: white; padding: 0.875rem 1rem;
    text-decoration: none; font-weight: 600;
}
.pwa-urgent-content { display: flex; align-items: center; gap: 0.75rem; }
.pulse-icon { animation: pulse-icon 1.5s infinite; }
@keyframes pulse-icon { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
.pwa-urgent-text { display: flex; flex-direction: column; }
.pwa-urgent-text strong { font-size: 0.9rem; }
.pwa-urgent-text span { font-size: 0.8rem; opacity: 0.9; }
.pwa-urgent-btn {
    display: flex; align-items: center; gap: 0.25rem;
    background: white; color: #d97706;
    padding: 0.5rem 1rem; border-radius: 20px;
    font-size: 0.8rem; font-weight: 700;
}

/* NOUVEAU: Banner transfert programmé */
.pwa-transfer-banner {
    display: flex; justify-content: space-between; align-items: center;
    background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%);
    color: white; padding: 0.875rem 1rem;
    font-weight: 600;
}
.pwa-transfer-content { display: flex; align-items: center; gap: 0.75rem; }
.pwa-transfer-text { display: flex; flex-direction: column; }
.pwa-transfer-text strong { font-size: 0.9rem; }
.pwa-transfer-text span { font-size: 0.75rem; opacity: 0.9; }
.pwa-transfer-amount { font-weight: 700; font-family: monospace; font-size: 0.9rem; }

.pwa-success-banner {
    display: flex; justify-content: space-between; align-items: center;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    color: white; padding: 0.75rem 1rem; font-size: 0.85rem;
}
.pwa-success-content { display: flex; align-items: center; gap: 0.5rem; }
.pwa-success-text { display: flex; flex-direction: column; }
.pwa-success-text strong { font-size: 0.85rem; }
.pwa-kkiapay-id { font-family: monospace; font-size: 0.7rem; opacity: 0.9; background: rgba(255,255,255,0.2); padding: 0.1rem 0.4rem; border-radius: 4px; }
.pwa-success-amount { font-weight: 700; font-family: monospace; }

.pwa-completed-banner {
    background: linear-gradient(90deg, #15803d 0%, #166534 100%);
    color: white; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600;
    display: flex; align-items: center; gap: 0.5rem;
}

.pwa-rejected-banner {
    background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
    color: white; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600;
    display: flex; align-items: center; gap: 0.5rem;
}

/* Stats */
.pwa-stats-scroll { margin: 0 -1rem 1.25rem -1rem; padding: 0 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
.pwa-stats-scroll::-webkit-scrollbar { display: none; }
.pwa-stats-track { display: flex; gap: 0.625rem; width: max-content; }
.pwa-stat-pill { display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1rem; background: white; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); border: 1px solid var(--secondary-200); min-width: 110px; }
.pwa-stat-pill.urgent .pwa-stat-icon { background: #fef3c7; color: #d97706; }
.pwa-stat-pill.draft .pwa-stat-icon { background: #f3f4f6; color: #6b7280; }
.pwa-stat-pill.pending .pwa-stat-icon { background: #dbeafe; color: #2563eb; }
.pwa-stat-pill.info .pwa-stat-icon { background: #cffafe; color: #0891b2; }
.pwa-stat-pill.funded .pwa-stat-icon { background: #dbeafe; color: #1d4ed8; }
.pwa-stat-pill.processing .pwa-stat-icon { background: #e0e7ff; color: #4f46e5; }
.pwa-stat-pill.completed .pwa-stat-icon { background: #dcfce7; color: #16a34a; }
.pwa-stat-pill.amount .pwa-stat-icon { background: var(--primary-50); color: var(--primary-600); }
.pwa-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.pwa-stat-num { font-size: 1.125rem; font-weight: 700; color: var(--secondary-800); line-height: 1; }
.pwa-stat-label { font-size: 0.7rem; color: var(--secondary-500); }

/* New Request Button */
.pwa-new-request-wrap { padding: 0 1rem; margin-bottom: 1.25rem; }
.pwa-new-request-btn {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #86efac; border-radius: 14px;
    color: #15803d; text-decoration: none; font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.pwa-new-request-btn:active { transform: scale(0.98); }

/* Filters */
.pwa-filters-wrap { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; padding: 0 1rem; }
.pwa-filters-scroll { flex: 1; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; display: flex; gap: 0.5rem; }
.pwa-filters-scroll::-webkit-scrollbar { display: none; }
.pwa-filter-chip { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid var(--secondary-200); border-radius: 50px; font-size: 0.875rem; font-weight: 500; color: var(--secondary-600); white-space: nowrap; transition: all 0.2s; cursor: pointer; border: none; }
.pwa-filter-chip:active { transform: scale(0.95); }
.pwa-filter-chip.active { background: var(--primary-500); color: white; border-color: var(--primary-500); box-shadow: 0 4px 10px rgba(27, 90, 141, 0.25); }
.pwa-filter-chip.urgent-filter { border-color: #f59e0b; color: #d97706; }
.pwa-filter-chip.urgent-filter.active { background: #f59e0b; color: white; }
.pwa-filter-chip.info-filter { border-color: #06b6d4; color: #0891b2; }
.pwa-filter-chip.info-filter.active { background: #06b6d4; color: white; }
.pwa-filter-count { background: var(--secondary-100); color: var(--secondary-700); padding: 0.125rem 0.5rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.pwa-filter-chip.active .pwa-filter-count { background: rgba(255,255,255,0.3); color: white; }
.pwa-refresh-btn { width: 40px; height: 40px; border-radius: 10px; background: white; border: 1px solid var(--secondary-200); color: var(--secondary-600); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.pwa-refresh-btn.spinning i { animation: spin 0.5s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Cards */
.pwa-requests-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; padding: 0 1rem; }
.pwa-request-card { background: white; border-radius: 14px; border: 1px solid var(--secondary-200); overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: all 0.2s; }
.pwa-request-card:active { transform: scale(0.98); }
.pwa-request-card.payment-urgent { border: 2px solid #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15); }
.pwa-request-card.transfer-scheduled { border: 2px solid #0ea5e9; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15); }
.pwa-request-card.status-rejected-card { opacity: 0.8; }
.pwa-card-main { display: flex; align-items: center; gap: 0.875rem; padding: 1rem; cursor: pointer; }
.pwa-request-status-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.pwa-request-details { flex: 1; min-width: 0; }
.pwa-request-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem; }
.pwa-request-header-row h3 { font-size: 0.85rem; font-weight: 700; color: var(--secondary-800); margin: 0; font-family: monospace; }
.pwa-request-amount { font-size: 0.9rem; font-weight: 700; color: var(--primary-600); }
.pwa-request-title { font-size: 0.875rem; color: var(--secondary-700); margin: 0 0 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; }
.pwa-request-meta { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; flex-wrap: wrap; }
.pwa-request-type { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 600; display: flex; align-items: center; gap: 0.25rem; }
.pwa-request-type.predefined { background: #dbeafe; color: #1d4ed8; }
.pwa-request-type.custom { background: #f3e8ff; color: #7c3aed; }
.pwa-request-date, .pwa-request-duration { font-size: 0.7rem; color: var(--secondary-400); display: flex; align-items: center; gap: 0.25rem; }
.pwa-request-status-row { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.pwa-status-badge { font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 600; white-space: nowrap; }
.pwa-status-desc { font-size: 0.7rem; color: var(--secondary-400); }
.pwa-request-chevron { color: var(--secondary-400); transition: transform 0.3s; font-size: 0.875rem; }
.pwa-request-card.expanded .pwa-request-chevron { transform: rotate(90deg); }

/* NOUVEAU: Repayment preview */
.pwa-repayment-preview {
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: rgba(14, 165, 233, 0.1);
    border-radius: 8px;
    font-size: 0.75rem;
    color: #0369a1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Actions */
.pwa-card-actions { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background: var(--secondary-50); border-top: 1px solid var(--secondary-200); }
.pwa-request-card.expanded .pwa-card-actions { max-height: 300px; }
.pwa-actions-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; padding: 0.75rem; }
@media (min-width: 400px) { .pwa-actions-grid { grid-template-columns: repeat(4, 1fr); } }
.pwa-action-btn { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 0.625rem 0.25rem; background: white; border: 1px solid var(--secondary-200); border-radius: 10px; font-size: 0.7rem; color: var(--secondary-700); transition: all 0.2s; cursor: pointer; text-decoration: none; }
.pwa-action-btn:active { transform: scale(0.95); }
.pwa-action-btn i { font-size: 1rem; margin-bottom: 0.125rem; }
.pwa-action-btn.view { color: #0369a1; border-color: #bfdbfe; }
.pwa-action-btn.edit { color: #7c3aed; border-color: #ddd6fe; }
.pwa-action-btn.pay { color: #d97706; border-color: #fde68a; background: #fffbeb; font-weight: 600; }
.pwa-action-btn.kkiapay { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }
.pwa-action-btn.transfer { color: #1d4ed8; border-color: #bfdbfe; background: #eff6ff; }
.pwa-action-btn.schedule { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }
.pwa-action-btn.info { color: #6b7280; border-color: #e5e7eb; }
.pwa-action-btn.delete { color: #dc2626; border-color: #fecaca; }

.pulse-action { animation: pulse-action 2s infinite; }
@keyframes pulse-action { 0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); } 50% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); } }

/* Empty State */
.pwa-empty-state { text-align: center; padding: 3rem 1.5rem; background: white; border-radius: 14px; border: 2px dashed var(--secondary-300); margin: 0 1rem; }
.pwa-empty-icon { width: 80px; height: 80px; margin: 0 auto 1.5rem; background: var(--secondary-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--secondary-400); }
.pwa-empty-state h3 { color: var(--secondary-800); font-size: 1.1rem; margin-bottom: 0.5rem; font-weight: 700; }
.pwa-empty-state p { color: var(--secondary-500); font-size: 0.9rem; margin-bottom: 1.5rem; }
.pwa-btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 0.875rem 1.5rem; background: var(--primary-500); color: white; border-radius: 12px; font-weight: 600; font-size: 0.95rem; text-decoration: none; border: none; }

/* FAB */
.pwa-fab { position: fixed; bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px) + 60px); right: 1.25rem; width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 16px rgba(27, 90, 141, 0.4); z-index: 99; text-decoration: none; }
.pwa-fab:active { transform: scale(0.95); }

/* Bottom Sheet */
.pwa-bottom-sheet { position: fixed; inset: 0; z-index: 9999; visibility: hidden; opacity: 0; transition: opacity 0.3s, visibility 0.3s; }
.pwa-bottom-sheet.show { visibility: visible; opacity: 1; }
.pwa-sheet-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
.pwa-sheet-content { position: absolute; bottom: 0; left: 0; right: 0; background: white; border-radius: 20px 20px 0 0; padding: 1rem 1.25rem; padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px)); transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.pwa-bottom-sheet.show .pwa-sheet-content { transform: translateY(0); }
.pwa-sheet-header { text-align: center; margin-bottom: 1.25rem; }
.pwa-sheet-drag { width: 36px; height: 4px; background: var(--secondary-300); border-radius: 2px; margin: 0 auto 0.75rem auto; }
.pwa-sheet-header h3 { font-size: 1.1rem; font-weight: 700; color: var(--secondary-800); margin: 0; }
.pwa-sheet-body { margin-bottom: 1.25rem; }
.pwa-sheet-body p { color: var(--secondary-600); font-size: 0.95rem; margin-bottom: 0.5rem; }
.pwa-sheet-footer { display: flex; gap: 0.75rem; }
.pwa-sheet-footer button { flex: 1; padding: 0.875rem; border-radius: 12px; font-weight: 600; font-size: 0.95rem; border: none; cursor: pointer; transition: transform 0.2s; }
.pwa-sheet-footer button:active { transform: scale(0.98); }
.pwa-btn-cancel { background: var(--secondary-100); color: var(--secondary-700); }
.pwa-btn-confirm-delete { background: #dc2626; color: white; }

/* Kkiapay Modal */
.kkiapay-icon { width: 80px; height: 80px; margin: 0 auto; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.kkiapay-details { text-align: left; }

/* Transfer Details Modal */
.transfer-icon-lg { width: 80px; height: 80px; margin: 0 auto; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.transfer-details { text-align: left; }
</style>
@endpush

@push('scripts')
<script>
// Fonction pour afficher les détails Kkiapay
window.showKkiapayDetails = function(transactionId, amount) {
    document.getElementById('kkiapayId').textContent = transactionId;
    document.getElementById('kkiapayAmount').textContent = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';

    var modal = new bootstrap.Modal(document.getElementById('kkiapayModal'));
    modal.show();
};

// NOUVEAU: Fonction pour afficher les détails du transfert programmé
window.showTransferDetails = function(requestId) {
    // Trouver la carte de la demande pour récupérer les infos
    var card = document.querySelector('.pwa-request-card[data-id="' + requestId + '"]');
    if (!card) return;

    // Ici vous pouvez faire un appel AJAX pour récupérer les détails complets
    // Pour l'instant, on affiche juste le modal
    var modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'));
    modal.show();
};
</script>
@endpush
