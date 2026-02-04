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
            case 'pending': show = ['submitted', 'pending_payment', 'payment_verification'].includes(status); break;
            case 'processing': show = ['paid', 'documents_pending', 'validated'].includes(status); break;
            case 'approved': show = ['approved', 'transfer_initiated', 'completed'].includes(status); break;
            case 'rejected': show = (status === 'rejected'); break;
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

    setTimeout(function() {
        window.closeCancel();
        if (window.toast) window.toast.success('Succès', 'Demande annulée avec succès');
        setTimeout(function() { window.location.reload(); }, 1000);
    }, 1500);
};

window.showStatusInfo = function(status, title) {
    const statusMessages = {
        'submitted': 'Votre demande a été soumise et est en attente de validation.',
        'pending_payment': 'Veuillez effectuer le paiement des frais d\'inscription pour poursuivre.',
        'payment_verification': 'Nous vérifions votre paiement, cela peut prendre 2 à 5 minutes.',
        'paid': 'Votre paiement a été confirmé. Veuillez télécharger les documents requis.',
        'documents_pending': 'Nous examinons vos documents.',
        'validated': 'Votre demande a été validée. Le transfert sera initié sous peu.',
        'approved': 'Félicitations ! Votre demande est approuvée.',
        'transfer_initiated': 'Le transfert est en cours. Disponible sous 24-48h.',
        'completed': 'Votre demande est terminée.',
        'rejected': 'Votre demande a été rejetée.'
    };

    var msgEl = document.getElementById('pwaStatusMessage');
    var titleEl = document.getElementById('pwaStatusTitle');
    if (msgEl) msgEl.textContent = statusMessages[status] || 'Statut en cours.';
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
                    $pendingCount = $requests->whereIn('status', ['submitted', 'pending_payment', 'payment_verification'])->count();
                    $paymentPendingCount = $requests->where('status', 'pending_payment')->count();
                @endphp
                @if($paymentPendingCount > 0)
                    <span class="pwa-badge-pending pulse-badge">
                        <i class="fas fa-exclamation-circle"></i> {{ $paymentPendingCount }} paiement(s) en attente
                    </span>
                @elseif($pendingCount > 0)
                    <span class="pwa-badge-pending">
                        <i class="fas fa-clock"></i> {{ $pendingCount }} en cours
                    </span>
                @else
                    <span class="pwa-badge-success">
                        <i class="fas fa-check-circle"></i> À jour
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="pwa-stats-scroll">
        <div class="pwa-stats-track">
            <div class="pwa-stat-pill total">
                <div class="pwa-stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $requests->count() }}</span>
                    <span class="pwa-stat-label">Total</span>
                </div>
            </div>
            <div class="pwa-stat-pill pending">
                <div class="pwa-stat-icon"><i class="fas fa-clock"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $pendingCount }}</span>
                    <span class="pwa-stat-label">En cours</span>
                </div>
            </div>
            <div class="pwa-stat-pill approved">
                <div class="pwa-stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $requests->whereIn('status', ['approved', 'completed'])->count() }}</span>
                    <span class="pwa-stat-label">Approuvées</span>
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
            <button class="pwa-filter-chip" data-filter="pending" onclick="filterRequests('pending')">
                <i class="fas fa-clock text-warning me-1"></i> En attente
            </button>
            <button class="pwa-filter-chip" data-filter="processing" onclick="filterRequests('processing')">
                <i class="fas fa-sync text-info me-1"></i> Traitement
            </button>
            <button class="pwa-filter-chip" data-filter="approved" onclick="filterRequests('approved')">
                <i class="fas fa-check-circle text-success me-1"></i> Approuvées
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
            $statusConfig = [
                'submitted' => ['label' => 'Soumise', 'class' => 'info', 'icon' => 'fa-paper-plane', 'color' => '#3b82f6'],
                'pending_payment' => ['label' => 'Paiement requis', 'class' => 'warning', 'icon' => 'fa-credit-card', 'color' => '#f59e0b'],
                'payment_verification' => ['label' => 'Vérification', 'class' => 'warning', 'icon' => 'fa-search', 'color' => '#f59e0b'],
                'paid' => ['label' => 'Payée', 'class' => 'success', 'icon' => 'fa-check', 'color' => '#10b981'],
                'documents_pending' => ['label' => 'Documents', 'class' => 'info', 'icon' => 'fa-file-upload', 'color' => '#3b82f6'],
                'validated' => ['label' => 'Validée', 'class' => 'success', 'icon' => 'fa-check-circle', 'color' => '#10b981'],
                'approved' => ['label' => 'Approuvée', 'class' => 'success', 'icon' => 'fa-award', 'color' => '#10b981'],
                'transfer_initiated' => ['label' => 'Transfert', 'class' => 'primary', 'icon' => 'fa-exchange-alt', 'color' => '#1b5a8d'],
                'completed' => ['label' => 'Terminée', 'class' => 'success', 'icon' => 'fa-trophy', 'color' => '#10b981'],
                'rejected' => ['label' => 'Rejetée', 'class' => 'danger', 'icon' => 'fa-times', 'color' => '#ef4444'],
            ];
            $config = $statusConfig[$request->status] ?? ['label' => $request->status, 'class' => 'secondary', 'icon' => 'fa-circle', 'color' => '#6b7280'];
        @endphp

        <div class="pwa-request-card {{ $request->status === 'pending_payment' ? 'payment-pending' : '' }}" data-status="{{ $request->status }}" data-id="{{ $request->id }}">

            {{-- Alert Paiement en haut de carte si applicable --}}
            @if($request->status === 'pending_payment')
            <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-payment-banner" onclick="event.stopPropagation();">
                <div class="pwa-payment-banner-content">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Paiement requis : {{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</span>
                </div>
                <span class="pwa-payment-banner-btn">Payer <i class="fas fa-arrow-right"></i></span>
            </a>
            @endif

            <div class="pwa-card-main" onclick="toggleRequestActions({{ $request->id }})">
                <div class="pwa-request-status-icon" style="background: {{ $config['color'] }}20; color: {{ $config['color'] }}">
                    <i class="fas {{ $config['icon'] }}"></i>
                </div>

                <div class="pwa-request-details">
                    <div class="pwa-request-header-row">
                        <h3>#{{ $request->request_number }}</h3>
                        <span class="pwa-request-amount">{{ number_format($request->amount_requested, 0, ',', ' ') }} F</span>
                    </div>

                    <p class="pwa-request-title">{{ Str::limit($request->title, 40) }}</p>

                    <div class="pwa-request-meta">
                        <span class="pwa-request-type {{ $request->is_predefined ? 'predefined' : 'custom' }}">
                            {{ $request->is_predefined ? 'Prédéfini' : 'Personnalisé' }}
                        </span>
                        <span class="pwa-request-date">{{ $request->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="pwa-request-status-row">
                        <span class="pwa-status-badge" style="background-color: {{ $config['color'] }}20; color: {{ $config['color'] }}; border: 1px solid {{ $config['color'] }}40;">
                            {{ $config['label'] }}
                        </span>
                        @if($request->payment_motif && $request->status === 'pending_payment')
                        <span class="pwa-motif-preview">Motif: {{ $request->payment_motif }}</span>
                        @endif
                    </div>
                </div>

                <div class="pwa-request-chevron">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pwa-card-actions">
                <div class="pwa-actions-grid">
                    <a href="{{ route('client.requests.show', $request->id) }}" class="pwa-action-btn view" onclick="event.stopPropagation();">
                        <i class="fas fa-eye"></i><span>Détails</span>
                    </a>

                    @if(in_array($request->status, ['pending_payment', 'validated']))
                    <a href="{{ route('client.requests.payment', $request->id) }}" class="pwa-action-btn pay pulse-action" onclick="event.stopPropagation();">
                        <i class="fas fa-credit-card"></i><span>Payer</span>
                    </a>
                    @endif

                    @if(in_array($request->status, ['paid', 'documents_pending']))
                    <a href="{{ route('client.requests.show', $request->id) }}" class="pwa-action-btn upload" onclick="event.stopPropagation();">
                        <i class="fas fa-upload"></i><span>Docs</span>
                    </a>
                    @endif

                    @if($request->status === 'approved')
                    <a href="{{ route('client.requests.show', $request->id) }}#transfer" class="pwa-action-btn transfer" onclick="event.stopPropagation();">
                        <i class="fas fa-exchange-alt"></i><span>Transférer</span>
                    </a>
                    @endif

                    <button class="pwa-action-btn info" onclick="event.stopPropagation(); showStatusInfo('{{ $request->status }}', 'Demande #{{ $request->request_number }}');">
                        <i class="fas fa-info-circle"></i><span>Info</span>
                    </button>

                    @if(in_array($request->status, ['submitted', 'pending_payment']))
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
            <p>Créez votre première demande de financement</p>
            <a href="{{ route('client.requests.create') }}" class="pwa-btn-primary">
                <i class="fas fa-plus me-2"></i> Nouvelle demande
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- FAB --}}
<a href="{{ route('client.requests.create') }}" class="pwa-fab">
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
            <p>Annuler la demande <strong id="pwaCancelNumber"></strong> ?</p>
            <p class="text-danger small">Cette action est irréversible.</p>
        </div>
        <div class="pwa-sheet-footer">
            <button class="pwa-btn-cancel" onclick="closeCancel()">Retour</button>
            <button class="pwa-btn-confirm-delete" id="pwaBtnCancel" onclick="executeCancel()">
                <span class="pwa-btn-text">Confirmer</span>
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
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Compris</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Variables CSS */
:root {
    --primary-500: #1b5a8d;
    --primary-600: #164a77;
    --primary-50: #e8f4fd;
    --secondary-50: #f8fafc;
    --secondary-100: #f3f4f6;
    --secondary-200: #e5e7eb;
    --secondary-300: #d1d5db;
    --secondary-400: #9ca3af;
    --secondary-500: #6b7280;
    --secondary-600: #6b7280;
    --secondary-700: #374151;
    --secondary-800: #1f2937;
    --success-50: #f0fdf4;
    --success-600: #16a34a;
    --warning-50: #fffbeb;
    --warning-600: #d97706;
    --error-500: #ef4444;
    --error-200: #fecaca;
}

.pwa-requests-container { padding: 0 0 2rem 0; }
.pwa-requests-header {
    background: linear-gradient(135deg, var(--primary-600) 0%, #113a61 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin: -1rem -1rem 1rem -1rem;
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
.pwa-header-text h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; font-family: 'Rajdhani', sans-serif; }
.pwa-header-text p { font-size: 0.85rem; opacity: 0.9; margin: 0 0 0.5rem 0; }
.pwa-badge-pending, .pwa-badge-success {
    display: inline-flex; align-items: center; gap: 0.375rem;
    padding: 0.25rem 0.625rem; border-radius: 50px;
    font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(10px);
}
.pwa-badge-pending { background: rgba(245, 158, 11, 0.3); border: 1px solid rgba(245, 158, 11, 0.5); color: #fffbeb; }
.pwa-badge-success { background: rgba(34, 197, 94, 0.3); border: 1px solid rgba(34, 197, 94, 0.5); }
.pulse-badge { animation: pulse-badge 2s infinite; }

@keyframes pulse-badge {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; background: rgba(245, 158, 11, 0.5); }
}

/* Banner Paiement en haut de carte */
.pwa-payment-banner {
    display: flex; justify-content: space-between; align-items: center;
    background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    color: white; padding: 0.75rem 1rem;
    text-decoration: none; font-weight: 600; font-size: 0.9rem;
}
.pwa-payment-banner-content {
    display: flex; align-items: center; gap: 0.5rem;
}
.pwa-payment-banner-btn {
    display: flex; align-items: center; gap: 0.25rem;
    background: white; color: #d97706;
    padding: 0.35rem 0.75rem; border-radius: 20px;
    font-size: 0.8rem; font-weight: 700;
}
.pwa-request-card.payment-pending {
    border: 2px solid #f59e0b;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
}

/* Stats et filtres identiques... */
.pwa-stats-scroll { margin: 0 -1rem 1.25rem -1rem; padding: 0 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
.pwa-stats-scroll::-webkit-scrollbar { display: none; }
.pwa-stats-track { display: flex; gap: 0.625rem; width: max-content; }
.pwa-stat-pill { display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1rem; background: white; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); border: 1px solid var(--secondary-200); min-width: 120px; }
.pwa-stat-pill.approved .pwa-stat-icon { background: var(--success-50); color: var(--success-600); }
.pwa-stat-pill.pending .pwa-stat-icon { background: var(--warning-50); color: var(--warning-600); }
.pwa-stat-pill.amount .pwa-stat-icon { background: var(--primary-50); color: var(--primary-600); }
.pwa-stat-pill.total .pwa-stat-icon { background: var(--secondary-100); color: var(--secondary-600); }
.pwa-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; }
.pwa-stat-num { font-size: 1.25rem; font-weight: 700; color: var(--secondary-800); line-height: 1; }
.pwa-stat-label { font-size: 0.75rem; color: var(--secondary-500); }

.pwa-new-request-wrap { padding: 0 1rem; margin-bottom: 1.25rem; }
.pwa-new-request-btn {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #86efac; border-radius: 14px;
    color: #15803d; text-decoration: none; font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.pwa-new-request-btn:active { transform: scale(0.98); }

.pwa-filters-wrap { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; padding: 0 1rem; }
.pwa-filters-scroll { flex: 1; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; display: flex; gap: 0.5rem; }
.pwa-filters-scroll::-webkit-scrollbar { display: none; }
.pwa-filter-chip { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid var(--secondary-200); border-radius: 50px; font-size: 0.875rem; font-weight: 500; color: var(--secondary-600); white-space: nowrap; transition: all 0.2s; cursor: pointer; border: none; }
.pwa-filter-chip:active { transform: scale(0.95); }
.pwa-filter-chip.active { background: var(--primary-500); color: white; border-color: var(--primary-500); box-shadow: 0 4px 10px rgba(27, 90, 141, 0.25); }
.pwa-filter-count { background: var(--secondary-100); color: var(--secondary-700); padding: 0.125rem 0.5rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.pwa-filter-chip.active .pwa-filter-count { background: rgba(255,255,255,0.3); color: white; }
.pwa-refresh-btn { width: 40px; height: 40px; border-radius: 10px; background: white; border: 1px solid var(--secondary-200); color: var(--secondary-600); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.pwa-refresh-btn.spinning i { animation: spin 0.5s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.pwa-requests-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; padding: 0 1rem; }
.pwa-request-card { background: white; border-radius: 14px; border: 1px solid var(--secondary-200); overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: all 0.2s; }
.pwa-request-card:active { transform: scale(0.98); }
.pwa-card-main { display: flex; align-items: center; gap: 0.875rem; padding: 1rem; cursor: pointer; }
.pwa-request-status-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.pwa-request-details { flex: 1; min-width: 0; }
.pwa-request-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem; }
.pwa-request-header-row h3 { font-size: 0.9rem; font-weight: 700; color: var(--secondary-800); margin: 0; font-family: 'Rajdhani', sans-serif; }
.pwa-request-amount { font-size: 0.9rem; font-weight: 700; color: var(--primary-600); }
.pwa-request-title { font-size: 0.85rem; color: var(--secondary-600); margin: 0 0 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pwa-request-meta { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
.pwa-request-type { font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 500; }
.pwa-request-type.predefined { background: #dbeafe; color: #1d4ed8; }
.pwa-request-type.custom { background: #f3e8ff; color: #7c3aed; }
.pwa-request-date { font-size: 0.7rem; color: var(--secondary-400); }
.pwa-request-status-row { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.pwa-status-badge { font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 50px; font-weight: 600; white-space: nowrap; }
.pwa-motif-preview { font-family: monospace; font-size: 0.75rem; background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px dashed #f59e0b; }
.pwa-request-chevron { color: var(--secondary-400); transition: transform 0.3s; font-size: 0.875rem; }
.pwa-request-card.expanded .pwa-request-chevron { transform: rotate(90deg); }

.pwa-card-actions { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background: var(--secondary-50); border-top: 1px solid var(--secondary-200); }
.pwa-request-card.expanded .pwa-card-actions { max-height: 200px; }
.pwa-actions-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; padding: 0.75rem; }
@media (min-width: 400px) {
    .pwa-actions-grid { grid-template-columns: repeat(4, 1fr); }
    .pwa-actions-grid:has(.pwa-action-btn:nth-child(5)) { grid-template-columns: repeat(5, 1fr); }
}
.pwa-action-btn { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 0.625rem 0.25rem; background: white; border: 1px solid var(--secondary-200); border-radius: 10px; font-size: 0.7rem; color: var(--secondary-700); transition: all 0.2s; cursor: pointer; text-decoration: none; }
.pwa-action-btn:active { transform: scale(0.95); }
.pwa-action-btn i { font-size: 1.125rem; margin-bottom: 0.125rem; }
.pwa-action-btn.view { color: #0369a1; border-color: #bfdbfe; }
.pwa-action-btn.pay { color: #d97706; border-color: #fde68a; background: #fffbeb; font-weight: 600; }
.pwa-action-btn.upload { color: #164a77; border-color: #d1e9fb; }
.pwa-action-btn.transfer { color: #16a34a; border-color: #bbf7d0; }
.pwa-action-btn.info { color: #6b7280; border-color: #d1d5db; }
.pwa-action-btn.delete { color: #dc2626; border-color: #fecaca; }

.pulse-action { animation: pulse-action 2s infinite; }
@keyframes pulse-action {
    0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
}

.pwa-empty-state { text-align: center; padding: 2.5rem 1rem; background: white; border-radius: 14px; border: 2px dashed var(--secondary-300); }
.pwa-empty-icon { width: 64px; height: 64px; margin: 0 auto 1rem; background: var(--secondary-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--secondary-400); }
.pwa-empty-state h3 { color: var(--secondary-800); font-size: 1rem; margin-bottom: 0.25rem; }
.pwa-empty-state p { color: var(--secondary-500); font-size: 0.875rem; margin-bottom: 1.25rem; }
.pwa-btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.25rem; background: var(--primary-500); color: white; border-radius: 10px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; width: 100%; max-width: 260px; }

.pwa-fab { position: fixed; bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px) + 60px); right: 1.25rem; width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(27, 90, 141, 0.35); z-index: 99; text-decoration: none; }
.pwa-fab:active { transform: scale(0.95); }

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
.pwa-btn-confirm-delete { background: var(--error-500); color: white; }
</style>
@endpush
