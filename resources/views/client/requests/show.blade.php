@extends('layouts.client')

@section('title', 'Détails de la demande')

@section('content')
<div class="pwa-request-detail-container">
    {{-- Header avec retour --}}
    <div class="pwa-detail-header">
        <a href="{{ route('client.requests.index') }}" class="pwa-back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Détails de la demande</h1>
        <div class="pwa-header-actions">
            {{-- Bouton de partage retiré --}}
        </div>
    </div>

    @php
        // Configuration des statuts
        $statusConfig = [
            'draft' => ['label' => 'Brouillon', 'class' => 'secondary', 'icon' => 'fa-edit', 'color' => '#6b7280'],
            'submitted' => ['label' => 'Soumise', 'class' => 'info', 'icon' => 'fa-paper-plane', 'color' => '#3b82f6'],
            'under_review' => ['label' => 'En examen', 'class' => 'info', 'icon' => 'fa-search', 'color' => '#3b82f6'],
            'pending_committee' => ['label' => 'Comité', 'class' => 'warning', 'icon' => 'fa-users', 'color' => '#f59e0b'],
            'validated' => ['label' => 'Validée', 'class' => 'success', 'icon' => 'fa-check-circle', 'color' => '#10b981'],
            'pending_payment' => ['label' => 'Paiement requis', 'class' => 'warning', 'icon' => 'fa-credit-card', 'color' => '#f59e0b'],
            'paid' => ['label' => 'Payée', 'class' => 'success', 'icon' => 'fa-check-double', 'color' => '#10b981'],
            'approved' => ['label' => 'Approuvée', 'class' => 'success', 'icon' => 'fa-award', 'color' => '#059669'],
            'funded' => ['label' => 'Financée', 'class' => 'primary', 'icon' => 'fa-money-bill-transfer', 'color' => '#1b5a8d'],
            'in_progress' => ['label' => 'En cours', 'class' => 'info', 'icon' => 'fa-spinner', 'color' => '#3b82f6'],
            'completed' => ['label' => 'Terminée', 'class' => 'success', 'icon' => 'fa-trophy', 'color' => '#047857'],
            'rejected' => ['label' => 'Rejetée', 'class' => 'danger', 'icon' => 'fa-times', 'color' => '#ef4444'],
            'cancelled' => ['label' => 'Annulée', 'class' => 'secondary', 'icon' => 'fa-ban', 'color' => '#6b7280'],
        ];
        $config = $statusConfig[$request->status] ?? ['label' => $request->status, 'class' => 'secondary', 'icon' => 'fa-circle', 'color' => '#6b7280'];

        // Logique de paiement
        $isPaid = !empty($request->kkiapay_transaction_id);
        $needsPayment = ($request->status === 'validated' || $request->status === 'pending_payment') && !$isPaid;

        // Déterminer le type de demande
        $isPredefined = $request->is_predefined;
    @endphp

    {{-- Barre de progression adaptative --}}
    <div class="progress-evolution-card">
        @php
            // Configuration des étapes selon le type de demande
            if ($isPredefined) {
                // PRÉDÉFINI : Soumission → Paiement → Examen → Validation
                $progressSteps = [
                    ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt'],
                    ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card'],
                    ['id' => 'review', 'label' => 'Examen', 'icon' => 'fa-search'],
                    ['id' => 'validated', 'label' => 'Validation', 'icon' => 'fa-check-circle'],
                ];

                // Mapping des statuts vers les étapes
                $statusToStep = [
                    'draft' => 0,
                    'submitted' => 0,
                    'pending_payment' => 1,
                    'paid' => 1,
                    'under_review' => 2,
                    'pending_committee' => 2,
                    'validated' => 3,
                    'approved' => 3,
                    'funded' => 3,
                    'completed' => 3,
                    'rejected' => -1,
                    'cancelled' => -1,
                ];
            } else {
                // PERSONNALISÉ : Soumission → Examen → Paiement → Validation
                $progressSteps = [
                    ['id' => 'submitted', 'label' => 'Soumission', 'icon' => 'fa-file-alt'],
                    ['id' => 'review', 'label' => 'Examen', 'icon' => 'fa-search'],
                    ['id' => 'payment', 'label' => 'Paiement', 'icon' => 'fa-credit-card'],
                    ['id' => 'validated', 'label' => 'Validation', 'icon' => 'fa-check-circle'],
                ];

                // Mapping des statuts vers les étapes
                $statusToStep = [
                    'draft' => 0,
                    'submitted' => 0,
                    'under_review' => 1,
                    'pending_committee' => 1,
                    'pending_payment' => 2,
                    'paid' => 2,
                    'validated' => 3,
                    'approved' => 3,
                    'funded' => 3,
                    'completed' => 3,
                    'rejected' => -1,
                    'cancelled' => -1,
                ];
            }

            $currentStepIndex = $statusToStep[$request->status] ?? 0;
            $isRejected = $request->status === 'rejected';
            $isCancelled = $request->status === 'cancelled';
            $totalSteps = count($progressSteps);

            // Calculer le pourcentage de progression
            if ($isRejected || $isCancelled) {
                $progressPercent = 0;
            } else {
                $progressPercent = (($currentStepIndex + 1) / $totalSteps) * 100;
            }
        @endphp

        <div class="progress-header-evolution">
            <div class="progress-type-badge {{ $isPredefined ? 'predefined' : 'custom' }}">
                {{ $isPredefined ? 'Prédéfinie' : 'Personnalisée' }}
            </div>
            <span class="progress-title-evolution">Évolution de la demande</span>
        </div>

        {{-- Étapes visuelles --}}
        <div class="progress-steps-container">
            @foreach($progressSteps as $index => $step)
                @php
                    $isActive = $index <= $currentStepIndex && !$isRejected && !$isCancelled;
                    $isCurrent = $index === $currentStepIndex && !$isRejected && !$isCancelled;
                @endphp
                <div class="progress-step-item {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}">
                    <div class="progress-step-circle">
                        @if($isActive)
                            <i class="fas {{ $isCurrent && !$isPaid && $step['id'] === 'payment' ? 'fa-hourglass-half' : $step['icon'] }}"></i>
                        @else
                            <span class="step-number">{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <span class="progress-step-label">{{ $step['label'] }}</span>
                    @if($isCurrent)
                        <div class="progress-step-indicator"></div>
                    @endif
                </div>
                @if(!$loop->last)
                    <div class="progress-step-line {{ $index < $currentStepIndex ? 'active' : '' }}"></div>
                @endif
            @endforeach
        </div>

        {{-- Barre de progression globale --}}
        <div class="progress-bar-global">
            <div class="progress-bar-track">
                <div class="progress-bar-fill {{ $isRejected ? 'rejected' : ($isCancelled ? 'cancelled' : '') }}"
                     style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="progress-percentage">
                @if($isRejected)
                    <span class="text-rejected">Rejetée</span>
                @elseif($isCancelled)
                    <span class="text-cancelled">Annulée</span>
                @else
                    <span>{{ round($progressPercent) }}%</span>
                @endif
            </div>
        </div>

        {{-- Message contextuel --}}
        @if($isCurrent && $currentStepIndex < $totalSteps - 1)
            <div class="progress-context-message">
                @if($progressSteps[$currentStepIndex]['id'] === 'payment')
                    <i class="fas fa-info-circle"></i>
                    <span>En attente de votre paiement pour continuer</span>
                @elseif($progressSteps[$currentStepIndex]['id'] === 'review')
                    <i class="fas fa-clock"></i>
                    <span>Votre demande est en cours d'examen par notre équipe</span>
                @elseif($progressSteps[$currentStepIndex]['id'] === 'submitted')
                    <i class="fas fa-clock"></i>
                    <span>Demande soumise, en attente de traitement</span>
                @endif
            </div>
        @elseif($currentStepIndex === $totalSteps - 1 && !$isRejected && !$isCancelled)
            <div class="progress-context-message success">
                <i class="fas fa-check-circle"></i>
                <span>Votre demande est validée et approuvée !</span>
            </div>
        @endif
    </div>

    {{-- Carte principale --}}
    <div class="pwa-detail-card">
        {{-- Status Banner --}}
        <div class="pwa-status-banner" style="background: {{ $config['color'] }}15; border-color: {{ $config['color'] }}30;">
            <div class="pwa-status-icon" style="background: {{ $config['color'] }}20; color: {{ $config['color'] }}">
                <i class="fas {{ $config['icon'] }}"></i>
            </div>
            <div class="pwa-status-info">
                <span class="pwa-status-label" style="color: {{ $config['color'] }}">{{ $config['label'] }}</span>
                <span class="pwa-status-date">Depuis le {{ $request->updated_at->format('d/m/Y à H:i') }}</span>
            </div>
        </div>

        {{-- Alert paiement si nécessaire --}}
        @if($needsPayment && $request->expected_payment > 0)
        <div class="pwa-payment-alert">
            <div class="pwa-alert-content">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Paiement requis</strong>
                    <p>Montant à payer : {{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</p>
                    @if($request->payment_motif)
                        <small>{{ $request->payment_motif }}</small>
                    @endif
                </div>
            </div>
            <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-btn-pay">
                Payer maintenant
            </a>
        </div>
        @endif

        {{-- Info Kkiapay si payé --}}
        @if($isPaid)
        <div class="pwa-kkiapay-info">
            <div class="pwa-kkiapay-header">
                <i class="fas fa-check-circle text-success"></i>
                <span>Paiement confirmé</span>
            </div>
            <div class="pwa-kkiapay-details">
                <div class="pwa-detail-row">
                    <span>Transaction ID:</span>
                    <code>{{ $request->kkiapay_transaction_id }}</code>
                </div>
                <div class="pwa-detail-row">
                    <span>Montant payé:</span>
                    <strong>{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</strong>
                </div>
                @if($request->kkiapay_phone)
                <div class="pwa-detail-row">
                    <span>Téléphone:</span>
                    <span>{{ $request->kkiapay_phone }}</span>
                </div>
                @endif
                <div class="pwa-detail-row">
                    <span>Date:</span>
                    <span>{{ $request->paid_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Détails de la demande --}}
        <div class="pwa-request-info">
            <div class="pwa-info-section">
                <h3>Informations générales</h3>

                <div class="pwa-info-grid">
                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Numéro de demande</span>
                        <span class="pwa-info-value font-mono">#{{ $request->request_number }}</span>
                    </div>

                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Type de demande</span>
                        <span class="pwa-info-value">
                            @if($request->is_predefined)
                                <span class="badge badge-predefined">Prédéfinie</span>
                                {{ $request->fundingType?->name ?? 'N/A' }}
                            @else
                                <span class="badge badge-custom">Personnalisée</span>
                            @endif
                        </span>
                    </div>

                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Date de création</span>
                        <span class="pwa-info-value">{{ $request->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    @if($request->submitted_at)
                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Date de soumission</span>
                        <span class="pwa-info-value">{{ $request->submitted_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="pwa-info-section">
                <h3>Financement demandé</h3>

                <div class="pwa-amount-display">
                    <span class="pwa-amount-value">{{ number_format($request->amount_requested, 0, ',', ' ') }}</span>
                    <span class="pwa-amount-currency">FCFA</span>
                </div>

                @if($request->amount_approved && $request->amount_approved != $request->amount_requested)
                <div class="pwa-amount-approved">
                    <span>Montant approuvé :</span>
                    <strong>{{ number_format($request->amount_approved, 0, ',', ' ') }} FCFA</strong>
                </div>
                @endif

                <div class="pwa-info-grid mt-3">
                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Durée</span>
                        <span class="pwa-info-value">{{ $request->duration }} mois</span>
                    </div>

                    @if($request->expected_jobs)
                    <div class="pwa-info-item">
                        <span class="pwa-info-label">Emplois attendus</span>
                        <span class="pwa-info-value">{{ $request->expected_jobs }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Section Description avec gestion de taille intelligente --}}
            <div class="pwa-info-section">
                <h3>Description du projet</h3>

                <div class="pwa-description-wrapper" id="descriptionWrapper">
                    <div class="pwa-description-box" id="descriptionBox">
                        {{ $request->description }}
                    </div>
                    <div class="pwa-description-gradient" id="descriptionGradient"></div>
                </div>

                {{-- Bouton Voir plus/moins (apparaît uniquement si nécessaire) --}}
                <button type="button" class="pwa-btn-toggle-description" id="toggleDescriptionBtn" onclick="toggleDescription()">
                    <span id="toggleText">Voir plus</span>
                    <i class="fas fa-chevron-down" id="toggleIcon"></i>
                </button>

                @if($request->project_location)
                <div class="pwa-location mt-3">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $request->project_location }}</span>
                </div>
                @endif
            </div>

            {{-- Notes admin si disponibles --}}
            @if($request->admin_validation_notes && in_array($request->status, ['validated', 'approved', 'rejected']))
            <div class="pwa-info-section">
                <h3>Notes de l'administration</h3>
                <div class="pwa-admin-notes {{ $request->status === 'rejected' ? 'notes-rejected' : '' }}">
                    {{ $request->admin_validation_notes }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="pwa-detail-actions">
        @if($needsPayment)
            <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-btn-primary btn-large">
                <i class="fas fa-credit-card"></i>
                Effectuer le paiement
            </a>
        @endif

        @if($request->status === 'draft')
            <a href="{{ route('client.requests.edit', $request->id) }}" class="pwa-btn-secondary">
                <i class="fas fa-edit"></i>
                Modifier la demande
            </a>
        @endif

        @if(in_array($request->status, ['draft', 'submitted', 'validated']))
            <button type="button" class="pwa-btn-danger" onclick="openCancelModal()">
                <i class="fas fa-trash"></i>
                Annuler la demande
            </button>
        @endif

        <a href="{{ route('client.requests.index') }}" class="pwa-btn-ghost">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
    </div>
</div>

{{-- Modal d'annulation --}}
<div class="pwa-modal-overlay" id="cancelModalOverlay" onclick="closeCancelModal()"></div>
<div class="pwa-modal" id="cancelModal">
    <div class="pwa-modal-header">
        <h3>Annuler la demande</h3>
        <button type="button" class="pwa-modal-close" onclick="closeCancelModal()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="pwa-modal-body">
        <div class="pwa-modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <p class="pwa-modal-text">Êtes-vous sûr de vouloir annuler la demande <strong>#{{ $request->request_number }}</strong> ?</p>
        <p class="pwa-modal-warning">Cette action est irréversible.</p>
    </div>
    <div class="pwa-modal-footer">
        <button type="button" class="pwa-btn-cancel" onclick="closeCancelModal()">Non, garder</button>
        <form action="{{ route('client.requests.cancel', $request->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="pwa-btn-confirm-delete">
                <i class="fas fa-trash"></i>
                Oui, annuler
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
.pwa-request-detail-container { padding-bottom: 2rem; }

.pwa-detail-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, var(--primary-600) 0%, #113a61 100%);
    color: white;
    margin: -1rem -1rem 1rem -1rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

.pwa-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255,255,255,0.2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    backdrop-filter: blur(10px);
}

.pwa-detail-header h1 {
    flex: 1;
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    font-family: 'Rajdhani', sans-serif;
}

.pwa-header-actions {
    display: flex;
    gap: 0.5rem;
    min-width: 40px;
}

/* ===== BARRE DE PROGRESSION ÉVOLUTION ===== */
.progress-evolution-card {
    background: white;
    border-radius: 16px;
    margin: 0 1rem 1rem;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.progress-header-evolution {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.progress-type-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-type-badge.predefined {
    background: #dbeafe;
    color: #1d4ed8;
}

.progress-type-badge.custom {
    background: #f3e8ff;
    color: #7c3aed;
}

.progress-title-evolution {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

/* Étapes visuelles */
.progress-steps-container {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    position: relative;
}

.progress-step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    position: relative;
    z-index: 2;
}

.progress-step-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #9ca3af;
    transition: all 0.3s ease;
}

.progress-step-item.active .progress-step-circle {
    background: #1b5a8d;
    color: white;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.3);
}

.progress-step-item.current .progress-step-circle {
    background: #d97706;
    color: white;
    animation: pulse-step 2s infinite;
    box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2);
}

.step-number {
    font-size: 0.9rem;
    font-weight: 700;
}

.progress-step-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #9ca3af;
    text-align: center;
    white-space: nowrap;
}

.progress-step-item.active .progress-step-label {
    color: #1b5a8d;
}

.progress-step-item.current .progress-step-label {
    color: #d97706;
    font-weight: 700;
}

.progress-step-indicator {
    position: absolute;
    bottom: -8px;
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: bounce 1s infinite;
}

.progress-step-line {
    flex: 1;
    height: 3px;
    background: #e5e7eb;
    margin-top: 20px;
    margin-left: -10px;
    margin-right: -10px;
    position: relative;
    z-index: 1;
    min-width: 20px;
}

.progress-step-line.active {
    background: linear-gradient(90deg, #1b5a8d 0%, #3b82f6 100%);
}

/* Barre de progression globale */
.progress-bar-global {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.progress-bar-track {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #1b5a8d 0%, #3b82f6 100%);
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-bar-fill.rejected {
    background: #ef4444;
}

.progress-bar-fill.cancelled {
    background: #6b7280;
}

.progress-percentage {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1b5a8d;
    min-width: 50px;
    text-align: right;
}

.progress-percentage .text-rejected {
    color: #ef4444;
}

.progress-percentage .text-cancelled {
    color: #6b7280;
}

/* Message contextuel */
.progress-context-message {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f0f9ff;
    border-radius: 10px;
    font-size: 0.85rem;
    color: #0369a1;
}

.progress-context-message i {
    color: #0ea5e9;
}

.progress-context-message.success {
    background: #f0fdf4;
    color: #15803d;
}

.progress-context-message.success i {
    color: #10b981;
}

@keyframes pulse-step {
    0%, 100% { box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.2); }
    50% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

/* ===== GESTION INTELLIGENTE DE LA DESCRIPTION ===== */

.pwa-description-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.pwa-description-box {
    background: #f8fafc;
    padding: 1rem;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #374151;

    /* Hauteur initiale limitée */
    max-height: 120px;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* État expanded */
.pwa-description-box.expanded {
    max-height: 2000px; /* Valeur suffisamment grande */
    overflow-y: auto;
}

/* Gradient pour l'effet de fondu */
.pwa-description-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50px;
    background: linear-gradient(transparent, #f8fafc);
    pointer-events: none;
    opacity: 1;
    transition: opacity 0.3s ease;
}

.pwa-description-box.expanded + .pwa-description-gradient {
    opacity: 0;
}

/* Bouton toggle */
.pwa-btn-toggle-description {
    display: none; /* Caché par défaut, affiché via JS si nécessaire */
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    margin-top: 0.5rem;
    background: transparent;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    color: #1b5a8d;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pwa-btn-toggle-description.show {
    display: flex;
}

.pwa-btn-toggle-description:active {
    background: #f1f5f9;
    transform: scale(0.98);
}

.pwa-btn-toggle-description i {
    transition: transform 0.3s ease;
}

.pwa-btn-toggle-description.active i {
    transform: rotate(180deg);
}

/* Variantes selon la longueur du texte */
.pwa-description-box.short {
    max-height: none; /* Pas de limite pour textes courts */
}

.pwa-description-box.medium {
    max-height: 120px;
}

.pwa-description-box.long {
    max-height: 200px;
}

/* ===== RESTE DES STYLES ===== */
.pwa-detail-card {
    background: white;
    border-radius: 16px;
    margin: 0 1rem 1rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.pwa-status-banner {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-bottom: 1px solid;
}

.pwa-status-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.pwa-status-info {
    display: flex;
    flex-direction: column;
}

.pwa-status-label {
    font-size: 1.1rem;
    font-weight: 700;
}

.pwa-status-date {
    font-size: 0.85rem;
    opacity: 0.8;
}

.pwa-payment-alert {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    padding: 1.25rem;
    border-bottom: 1px solid #f59e0b;
}

.pwa-alert-content {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.pwa-alert-content i {
    font-size: 1.5rem;
    color: #d97706;
}

.pwa-alert-content strong {
    display: block;
    color: #92400e;
    margin-bottom: 0.25rem;
}

.pwa-alert-content p {
    margin: 0;
    color: #78350f;
    font-weight: 600;
}

.pwa-alert-content small {
    color: #92400e;
    font-size: 0.85rem;
}

.pwa-btn-pay {
    display: block;
    width: 100%;
    padding: 0.875rem;
    background: #d97706;
    color: white;
    text-align: center;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
}

.pwa-kkiapay-info {
    background: #f0fdf4;
    border-bottom: 1px solid #86efac;
    padding: 1.25rem;
}

.pwa-kkiapay-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: #15803d;
    margin-bottom: 1rem;
}

.pwa-kkiapay-header i {
    font-size: 1.25rem;
}

.pwa-kkiapay-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.pwa-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
}

.pwa-detail-row span:first-child {
    color: #6b7280;
}

.pwa-detail-row code {
    background: rgba(0,0,0,0.05);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.pwa-request-info {
    padding: 1.25rem;
}

.pwa-info-section {
    margin-bottom: 1.5rem;
}

.pwa-info-section:last-child {
    margin-bottom: 0;
}

.pwa-info-section h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pwa-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.pwa-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.pwa-info-label {
    font-size: 0.8rem;
    color: #6b7280;
}

.pwa-info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
}

.font-mono {
    font-family: 'Courier New', monospace;
}

.badge {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 0.5rem;
}

.badge-predefined {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-custom {
    background: #f3e8ff;
    color: #7c3aed;
}

.pwa-amount-display {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
}

.pwa-amount-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0369a1;
    font-family: 'Rajdhani', sans-serif;
}

.pwa-amount-currency {
    font-size: 1rem;
    color: #0284c7;
    font-weight: 600;
}

.pwa-amount-approved {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #f0fdf4;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.pwa-amount-approved strong {
    color: #15803d;
}

.pwa-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.pwa-admin-notes {
    background: #f0f9ff;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #3b82f6;
    font-size: 0.95rem;
    line-height: 1.6;
}

.pwa-admin-notes.notes-rejected {
    background: #fef2f2;
    border-left-color: #ef4444;
}

.pwa-detail-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 0 1rem;
    margin-top: 1.5rem;
}

.pwa-btn-primary, .pwa-btn-secondary, .pwa-btn-danger, .pwa-btn-ghost {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
    width: 100%;
}

.pwa-btn-primary {
    background: linear-gradient(135deg, #1b5a8d 0%, #164a77 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25);
}

.pwa-btn-primary.btn-large {
    padding: 1.25rem;
    font-size: 1rem;
}

.pwa-btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.pwa-btn-danger {
    background: #fef2f2;
    color: #dc2626;
}

.pwa-btn-ghost {
    background: transparent;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

/* Modal styles */
.pwa-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.pwa-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.pwa-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 400px;
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.pwa-modal.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) scale(1);
}

.pwa-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    border-bottom: 1px solid #e5e7eb;
}

.pwa-modal-header h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.pwa-modal-close {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: #f3f4f6;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pwa-modal-body {
    padding: 1.5rem;
    text-align: center;
}

.pwa-modal-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #fef3c7;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin: 0 auto 1rem;
}

.pwa-modal-text {
    font-size: 1rem;
    color: #374151;
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.pwa-modal-warning {
    font-size: 0.875rem;
    color: #dc2626;
    font-weight: 500;
}

.pwa-modal-footer {
    display: flex;
    gap: 0.75rem;
    padding: 1rem 1.25rem 1.25rem;
    border-top: 1px solid #e5e7eb;
}

.pwa-modal-footer .pwa-btn-cancel,
.pwa-modal-footer .pwa-btn-confirm-delete {
    flex: 1;
    padding: 0.875rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    font-size: 0.95rem;
}

.pwa-modal-footer .pwa-btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.pwa-modal-footer .pwa-btn-confirm-delete {
    background: #dc2626;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.mt-3 {
    margin-top: 0.75rem;
}

/* Responsive */
@media (max-width: 380px) {
    .pwa-description-box {
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .pwa-description-box.medium {
        max-height: 100px;
    }

    .progress-step-label {
        font-size: 0.7rem;
    }

    .pwa-amount-value {
        font-size: 2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Gestion du modal d'annulation
function openCancelModal() {
    document.getElementById('cancelModalOverlay').classList.add('show');
    document.getElementById('cancelModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelModalOverlay').classList.remove('show');
    document.getElementById('cancelModal').classList.remove('show');
    document.body.style.overflow = '';
}

// Fermer avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
    }
});

// ===== GESTION INTELLIGENTE DE LA DESCRIPTION =====

document.addEventListener('DOMContentLoaded', function() {
    initDescriptionManager();
});

function initDescriptionManager() {
    const descriptionBox = document.getElementById('descriptionBox');
    const toggleBtn = document.getElementById('toggleDescriptionBtn');

    if (!descriptionBox) return;

    const text = descriptionBox.textContent.trim();
    const charCount = text.length;
    const lineCount = text.split('\n').length;

    // Déterminer la catégorie de taille
    let sizeCategory = 'short';

    if (charCount > 300 || lineCount > 5) {
        sizeCategory = 'long';
    } else if (charCount > 150 || lineCount > 3) {
        sizeCategory = 'medium';
    }

    // Appliquer la classe appropriée
    descriptionBox.classList.add(sizeCategory);

    // Afficher le bouton seulement si nécessaire
    if (sizeCategory !== 'short') {
        toggleBtn.classList.add('show');

        // Vérifier si le contenu déborde réellement
        setTimeout(() => {
            if (descriptionBox.scrollHeight <= descriptionBox.clientHeight + 10) {
                // Le contenu tient dans l'espace, masquer le bouton
                toggleBtn.classList.remove('show');
                descriptionBox.classList.remove('medium', 'long');
                descriptionBox.classList.add('short');
            }
        }, 100);
    }

    // Stocker l'état
    window.descriptionState = {
        isExpanded: false,
        sizeCategory: sizeCategory
    };
}

function toggleDescription() {
    const descriptionBox = document.getElementById('descriptionBox');
    const toggleBtn = document.getElementById('toggleDescriptionBtn');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');

    if (!window.descriptionState) return;

    window.descriptionState.isExpanded = !window.descriptionState.isExpanded;

    if (window.descriptionState.isExpanded) {
        // Expand
        descriptionBox.classList.add('expanded');
        descriptionBox.classList.remove('medium', 'long');
        toggleBtn.classList.add('active');
        toggleText.textContent = 'Voir moins';

        // Scroll smooth vers la description
        setTimeout(() => {
            descriptionBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    } else {
        // Collapse
        descriptionBox.classList.remove('expanded');
        descriptionBox.classList.add(window.descriptionState.sizeCategory);
        toggleBtn.classList.remove('active');
        toggleText.textContent = 'Voir plus';
    }
}

// Réinitialiser lors du redimensionnement
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initDescriptionManager, 250);
});
</script>
@endpush
@endsection
