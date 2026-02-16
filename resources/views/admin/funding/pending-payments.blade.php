@extends('admin.layouts.app')

@section('title', 'Paiements en attente')
@section('page-title', 'Validation des paiements')

@section('content')
<div class="payments-validation-container">

    <!-- Debug Info (à supprimer en production) -->
    @if(app()->environment('local'))
    <div class="alert alert-secondary mb-3">
        <small>Debug: Total payments = {{ $payments->total() }} | Query: status=paid, kkiapay_transaction_id not null, validated_at null</small>
    </div>
    @endif

    <!-- Header Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center h-100">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Validation des paiements Kkiapay
                    </h2>
                    <p class="text-muted mb-0">Vérifiez et confirmez les transactions en attente de crédit</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card bg-warning bg-opacity-10 border-warning">
                <div class="stat-icon text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value text-warning">{{ $stats['total_pending'] ?? 0 }}</h3>
                    <span class="stat-label">En attente</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card bg-primary bg-opacity-10 border-primary">
                <div class="stat-icon text-primary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value text-primary">{{ number_format($stats['total_amount_pending'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></h3>
                    <span class="stat-label">Montant total</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start">
            <div class="alert-icon bg-info bg-opacity-25 text-info rounded-circle me-3">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Processus de vérification</h6>
                <p class="mb-0 text-muted">Les demandes ci-dessous ont été payées via Kkiapay. Cliquez sur "Vérifier" pour confirmer le paiement.</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.funding.pending-payments') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label text-uppercase text-muted fw-semibold small">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                               placeholder="N° demande, transaction, client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3">
                    <label class="form-label text-uppercase text-muted fw-semibold small">Date début</label>
                    <input type="date" name="date_from" class="form-control bg-light border-0" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-2 col-md-3">
                    <label class="form-label text-uppercase text-muted fw-semibold small">Date fin</label>
                    <input type="date" name="date_to" class="form-control bg-light border-0" value="{{ request('date_to') }}">
                </div>
                <div class="col-lg-4 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ route('admin.funding.pending-payments') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-dark">Transactions à valider</h5>
                <span class="badge bg-primary rounded-pill">{{ $payments->total() }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux demandes
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-uppercase text-muted small fw-bold">Demande</th>
                        <th class="text-uppercase text-muted small fw-bold">Client</th>
                        <th class="text-uppercase text-muted small fw-bold">Montants</th>
                        <th class="text-uppercase text-muted small fw-bold">Transaction Kkiapay</th>
                        <th class="text-uppercase text-muted small fw-bold">Date</th>
                        <th class="text-end pe-4 text-uppercase text-muted small fw-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-bottom">
                            <!-- Request Info -->
                            <td class="ps-4">
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary font-monospace w-fit-content">
                                        {{ $payment->request_number }}
                                    </span>
                                    <span class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $payment->title }}">
                                        {{ Str::limit($payment->title, 35) }}
                                    </span>
                                    <span class="badge {{ $payment->is_predefined ? 'bg-success bg-opacity-10 text-success' : 'bg-purple bg-opacity-10 text-purple' }} w-fit-content">
                                        <i class="fas {{ $payment->is_predefined ? 'fa-box' : 'fa-pen-nib' }} me-1 small"></i>
                                        {{ $payment->is_predefined ? 'Prédéfinie' : 'Personnalisée' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Client -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $email = $payment->user?->email ?? 'default@example.com';
                                        $hue = crc32($email) % 360;
                                    @endphp
                                    <div class="avatar-circle" style="background: hsl({{ $hue }}, 70%, 45%); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                        {{ strtoupper(substr($payment->user?->first_name ?? 'N', 0, 1) . substr($payment->user?->last_name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $payment->user?->full_name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $payment->user?->email ?? '' }}</small>
                                        <small class="text-muted">{{ $payment->user?->phone ?? '' }}</small>
                                    </div>
                                </div>
                            </td>

                            <!-- Amounts -->
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-muted">Approuvé:</span>
                                        <span class="fw-semibold text-primary font-monospace">{{ number_format($payment->amount_approved ?? $payment->amount_requested, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-muted">Attendu:</span>
                                        <span class="fw-semibold text-warning font-monospace">{{ number_format($payment->expected_payment ?? 0, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-success bg-opacity-10 rounded small border border-success border-opacity-25">
                                        <span class="text-success fw-semibold">Payé:</span>
                                        <span class="fw-bold text-success font-monospace">{{ number_format($payment->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    @if(($payment->kkiapay_amount_paid ?? 0) != ($payment->expected_payment ?? 0))
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-warning bg-opacity-10 rounded small border border-warning border-opacity-25">
                                            <span class="text-warning fw-semibold">Écart:</span>
                                            <span class="fw-bold text-warning font-monospace">
                                                {{ number_format(($payment->kkiapay_amount_paid ?? 0) - ($payment->expected_payment ?? 0), 0, ',', ' ') }} FCFA
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Transaction Details -->
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">ID:</span>
                                        <code class="bg-light px-2 py-1 rounded text-primary small font-monospace text-truncate" style="max-width: 120px;" title="{{ $payment->kkiapay_transaction_id }}">
                                            {{ Str::limit($payment->kkiapay_transaction_id, 16) }}
                                        </code>
                                        <button class="btn btn-link btn-sm p-0 text-muted" onclick="copyToClipboard('{{ $payment->kkiapay_transaction_id }}')" title="Copier">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="text-muted">Tél:</span>
                                        <span class="fw-medium">{{ $payment->kkiapay_phone ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            <i class="fas fa-check-circle me-1"></i>SUCCESS
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2 text-success">
                                        <i class="fas fa-calendar-check"></i>
                                        <span class="fw-semibold small">{{ $payment->paid_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                    </div>
                                    <small class="text-muted fst-italic">{{ $payment->paid_at?->diffForHumans() ?? '' }}</small>
                                    @if($payment->validated_at)
                                        <small class="text-info">Déjà validé le {{ $payment->validated_at->format('d/m/Y H:i') }}</small>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="pe-4">
                                <div class="d-flex flex-column gap-2 align-items-end">
                                    @if(!$payment->validated_at)
                                        <button class="btn btn-success btn-sm w-100" style="min-width: 140px;"
                                                onclick="openVerifyModal({{ $payment->id }}, '{{ $payment->request_number }}', '{{ $payment->user?->full_name ?? 'N/A' }}', '{{ number_format($payment->kkiapay_amount_paid, 0, ',', ' ') }} FCFA', '{{ $payment->kkiapay_transaction_id }}')">
                                            <i class="fas fa-check-circle me-2"></i>Vérifier Paiement
                                        </button>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info mb-2">
                                            <i class="fas fa-check-double me-1"></i>Déjà validé
                                        </span>
                                    @endif

                                    <div class="d-flex gap-2 w-100">
                                        <a href="{{ route('admin.funding.show-request', $payment->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                        @if($payment->kkiapay_transaction_id)
                                            <a href="https://app.kkiapay.me/dashboard/transactions/details/{{ $payment->kkiapay_transaction_id }}"
                                               target="_blank"
                                               class="btn btn-dark btn-sm flex-fill"
                                               title="Voir sur Kkiapay">
                                                <i class="fas fa-external-link-alt me-1"></i>Kkiapay
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon bg-success bg-opacity-10 text-success mb-3">
                                        <i class="fas fa-check-double fa-2x"></i>
                                    </div>
                                    <h5 class="text-dark mb-2">Aucun paiement en attente</h5>
                                    <p class="text-muted mb-3">Tous les paiements ont été vérifiés ou aucune demande n'a encore été payée.</p>
                                    <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Retour aux demandes
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de <strong>{{ $payments->firstItem() }}</strong> à <strong>{{ $payments->lastItem() }}</strong> sur <strong>{{ $payments->total() }}</strong> paiements
                </small>
                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-shield-alt fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Confirmer la vérification</h5>
                            <small class="text-muted">Vérifiez les détails avant de continuer</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex gap-3 mb-4">
                        <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                        <div>
                            <strong class="text-dark">Vérification requise</strong>
                            <p class="mb-0 small text-muted">Assurez-vous que la transaction existe dans votre dashboard Kkiapay avant confirmation.</p>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Demande</small>
                                    <strong class="text-dark" id="verifyRequestNumber">-</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Client</small>
                                    <strong class="text-dark" id="verifyClient">-</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Transaction ID</small>
                                    <code class="bg-white px-2 py-1 rounded text-primary font-monospace" id="verifyTransaction">-</code>
                                </div>
                                <div class="col-12 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Montant payé</small>
                                        <strong class="text-success fs-4 font-monospace" id="verifyAmount">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="verifyForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check p-3 bg-success bg-opacity-10 rounded border border-success border-opacity-25">
                                <input class="form-check-input border-success" type="checkbox" id="confirmVerify" name="confirm_verify" value="1" required>
                                <label class="form-check-label fw-semibold ms-2" for="confirmVerify" style="color: #065f46;">
                                    J'ai vérifié cette transaction sur Kkiapay
                                </label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold text-uppercase">Notes de vérification (optionnel)</label>
                            <textarea class="form-control bg-light border-0" id="verifyNotes" name="verification_notes" rows="2" placeholder="Commentaires..."></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">Annuler</button>
                    <button type="button" class="btn btn-success px-4" onclick="submitVerify()">
                        <i class="fas fa-check-circle me-2"></i>Confirmer et continuer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <!-- Toasts will be injected here -->
    </div>

</div>

<style>
.payments-validation-container {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Stat Cards */
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 0.75rem;
    border-left: 4px solid;
    height: 100%;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: currentColor;
    background-opacity: 0.1;
}

.stat-icon i {
    color: inherit;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0;
    line-height: 1.2;
    font-feature-settings: "tnum";
}

.stat-value small {
    font-size: 0.75rem;
    opacity: 0.8;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6c757d;
}

/* Alert Customization */
.alert-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* Table Enhancements */
.table th {
    font-size: 0.6875rem;
    letter-spacing: 0.025em;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.table td {
    padding-top: 1.25rem;
    padding-bottom: 1.25rem;
}

.avatar-circle {
    transition: transform 0.2s;
}

tr:hover .avatar-circle {
    transform: scale(1.05);
}

/* Badges Custom */
.badge {
    font-weight: 500;
}

.w-fit-content {
    width: fit-content;
}

/* Empty State */
.empty-state {
    padding: 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 1rem;
}

.modal-header {
    border-radius: 1rem 1rem 0 0;
}

.modal-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Form Check Custom */
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Modal Management
function openVerifyModal(id, requestNumber, client, amount, transactionId) {
    const modalEl = document.getElementById('verifyModal');
    const modal = new bootstrap.Modal(modalEl);

    document.getElementById('verifyRequestNumber').textContent = requestNumber;
    document.getElementById('verifyClient').textContent = client;
    document.getElementById('verifyTransaction').textContent = transactionId;
    document.getElementById('verifyAmount').textContent = amount;

    const form = document.getElementById('verifyForm');
    form.reset();
    // CORRECTION: URL alignée avec la route du contrôleur
    form.action = `{{ url('admin/funding') }}/${id}/verify-payment`;

    modal.show();
}

function closeModal() {
    const modalEl = document.getElementById('verifyModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
        modal.hide();
    }
}

function submitVerify() {
    const form = document.getElementById('verifyForm');
    const confirmCheck = document.getElementById('confirmVerify').checked;

    if (!confirmCheck) {
        showToast('error', 'Confirmation requise', 'Veuillez cocher la case de vérification');
        return;
    }

    form.submit();
}

// Clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Copié !', 'ID de transaction copié dans le presse-papiers');
    }).catch(err => {
        console.error('Erreur:', err);
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);
        showToast('success', 'Copié !', 'ID de transaction copié');
    });
}

// Toast System
function showToast(type, title, message) {
    const container = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();

    const bgColors = {
        success: 'bg-success',
        error: 'bg-danger',
        info: 'bg-info',
        warning: 'bg-warning'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgColors[type]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="fas ${icons[type]}"></i>
                    <div>
                        <strong>${title}</strong>
                        <div class="small opacity-75">${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();

    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Session Messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast('success', 'Succès', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showToast('error', 'Erreur', '{{ session('error') }}');
    @endif

    @if(session('warning'))
        showToast('warning', 'Attention', '{{ session('warning') }}');
    @endif
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
