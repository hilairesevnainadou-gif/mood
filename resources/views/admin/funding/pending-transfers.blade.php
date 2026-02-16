@extends('admin.layouts.app')

@section('title', 'Transferts en attente')
@section('page-title', 'Validation des transferts')

@section('content')
<div class="transfers-management-wrapper">

    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>
                <i class="fas fa-clock"></i>
                Transferts en attente de validation
            </h2>
            <p>Validez, programmez et exécutez les transferts de fonds vers les wallets clients</p>
        </div>
        <div class="header-stats">
            <div class="quick-stat warning">
                <span class="stat-label">À programmer</span>
                <span class="stat-value">{{ $stats['to_schedule'] ?? 0 }}</span>
            </div>
            <div class="quick-stat info">
                <span class="stat-label">Programmés</span>
                <span class="stat-value">{{ $stats['scheduled'] ?? 0 }}</span>
            </div>
            <div class="quick-stat">
                <span class="stat-label">Montant total</span>
                <span class="stat-value">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></span>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="info-banner">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <h4>Processus de transfert</h4>
            <p><strong>Étape 1 :</strong> Les demandes "Payées" attendent la vérification des documents et la programmation du remboursement.<br>
               <strong>Étape 2 :</strong> Les demandes "Programmées" sont prêtes pour l'exécution finale du transfert.</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-bar">
        <a href="{{ route('admin.funding.pending-payments') }}" class="quick-action-card warning">
            <div class="action-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="action-content">
                <span class="action-title">Paiements à vérifier</span>
                <span class="action-desc">Voir les paiements Kkiapay en attente</span>
            </div>
            <i class="fas fa-arrow-right action-arrow"></i>
        </a>
        <a href="{{ route('admin.funding.pending-validation') }}" class="quick-action-card">
            <div class="action-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="action-content">
                <span class="action-title">Toutes les demandes</span>
                <span class="action-desc">Retour à la liste complète</span>
            </div>
            <i class="fas fa-arrow-right action-arrow"></i>
        </a>
    </div>

    <!-- Filters -->
    <div class="control-panel">
        <form method="GET" action="{{ route('admin.funding.pending-transfers') }}" class="filters-form">
            <div class="filter-row">
                <div class="filter-field">
                    <label for="stage">Étape</label>
                    <div class="select-affix">
                        <select id="stage" name="stage" onchange="this.form.submit()">
                            <option value="">Tous les transferts</option>
                            <option value="to_schedule" {{ request('stage') == 'to_schedule' ? 'selected' : '' }}>À programmer (documents)</option>
                            <option value="scheduled" {{ request('stage') == 'scheduled' ? 'selected' : '' }}>Programmés (à exécuter)</option>
                        </select>
                        <i class="fas fa-chevron-down suffix"></i>
                    </div>
                </div>

                <div class="filter-field search-field">
                    <label for="search">Recherche</label>
                    <div class="input-affix">
                        <i class="fas fa-search prefix"></i>
                        <input type="text" id="search" name="search" placeholder="N° demande, client..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="filter-field">
                    <label for="date_from">Date (du)</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="filter-field">
                    <label for="date_to">Date (au)</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                        <span>Filtrer</span>
                    </button>
                    @if(request()->hasAny(['search', 'stage', 'date_from', 'date_to']))
                        <a href="{{ route('admin.funding.pending-transfers') }}" class="btn btn-ghost">
                            <i class="fas fa-undo"></i>
                            <span>Réinitialiser</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Transfers Table -->
    <div class="data-container">
        <div class="container-header">
            <div class="header-title">
                <i class="fas fa-list-alt"></i>
                <h3>
                    @if(request('stage') == 'to_schedule')
                        Demandes à programmer
                    @elseif(request('stage') == 'scheduled')
                        Transferts programmés
                    @else
                        Tous les transferts en attente
                    @endif
                </h3>
                <span class="badge-count {{ request('stage') == 'to_schedule' ? 'warning' : (request('stage') == 'scheduled' ? 'info' : '') }}">
                    {{ $transfers->total() }}
                </span>
            </div>
            <div class="header-tools">
                <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux demandes
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="transfers-table">
                <thead>
                    <tr>
                        <th class="th-info">Demande</th>
                        <th class="th-client">Client</th>
                        <th class="th-amount">Montant</th>
                        <th class="th-status">Statut</th>
                        <th class="th-repayment">Remboursement</th>
                        <th class="th-waiting">Attente</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        @php
                            $isScheduled = $transfer->transfer_scheduled_at && !$transfer->transfer_executed_at;
                            $canSchedule = in_array($transfer->status, ['paid', 'approved']) && !$isScheduled;
                        @endphp
                        <tr class="transfer-row {{ $isScheduled ? 'scheduled' : 'to-schedule' }}">
                            <td class="td-info">
                                <div class="request-block">
                                    <span class="request-badge">{{ $transfer->request_number }}</span>
                                    <span class="request-title" title="{{ $transfer->title }}">
                                        {{ Str::limit($transfer->title, 35) }}
                                    </span>
                                    <span class="request-type">
                                        <i class="fas {{ $transfer->is_predefined ? 'fa-box' : 'fa-pen-nib' }}"></i>
                                        {{ $transfer->is_predefined ? ($transfer->fundingType?->name ?? 'Prédéfinie') : 'Personnalisée' }}
                                    </span>
                                </div>
                            </td>
                            <td class="td-client">
                                <div class="client-block">
                                    @php
                                        $email = $transfer->user?->email ?? 'default@example.com';
                                        $hue = crc32($email) % 360;
                                    @endphp
                                    <div class="client-avatar" style="background: hsl({{ $hue }}, 70%, 45%)">
                                        {{ strtoupper(substr($transfer->user?->first_name ?? 'N', 0, 1) . substr($transfer->user?->last_name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="client-details">
                                        <span class="client-name">{{ $transfer->user?->full_name ?? 'N/A' }}</span>
                                        <span class="client-email">{{ $transfer->user?->email ?? '' }}</span>
                                        <span class="client-phone">{{ $transfer->user?->phone ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="td-amount">
                                <div class="amount-highlight">
                                    <span class="amount-value">{{ number_format($transfer->amount_approved ?? $transfer->amount_requested, 0, ',', ' ') }}</span>
                                    <span class="amount-currency">FCFA</span>
                                </div>
                                <div class="wallet-info">
                                    <i class="fas fa-wallet"></i>
                                    <span>Wallet: {{ $transfer->user?->wallet?->wallet_number ?? 'À créer' }}</span>
                                </div>
                                @if($transfer->total_repayment_amount)
                                    <div class="repayment-total mt-2">
                                        <span class="text-muted">Total à rembourser:</span>
                                        <strong class="text-info">{{ number_format($transfer->total_repayment_amount, 0, ',', ' ') }} FCFA</strong>
                                    </div>
                                @endif
                            </td>
                            <td class="td-status">
                                @if($isScheduled)
                                    <span class="status-pill scheduled">
                                        <i class="fas fa-clock"></i>
                                        Programmé
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        Depuis {{ $transfer->transfer_scheduled_at->diffForHumans() }}
                                    </small>
                                @elseif($canSchedule)
                                    <span class="status-pill to-schedule">
                                        <i class="fas fa-hourglass-half"></i>
                                        {{ $transfer->status === 'paid' ? 'Payé - À vérifier' : 'Approuvé - À programmer' }}
                                    </span>
                                    @if($transfer->status === 'paid' && $transfer->validated_at)
                                        <small class="text-success d-block mt-1">
                                            <i class="fas fa-check"></i> Paiement vérifié
                                        </small>
                                    @endif
                                @else
                                    <span class="status-pill pending">
                                        <i class="fas fa-question-circle"></i>
                                        {{ $transfer->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="td-repayment">
                                <div class="repayment-program">
                                    @if($transfer->monthly_repayment_amount && $transfer->repayment_duration_months)
                                        <div class="repayment-row">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                            <span><strong>{{ number_format($transfer->monthly_repayment_amount, 0, ',', ' ') }} FCFA</strong>/mois</span>
                                        </div>
                                        <div class="repayment-row">
                                            <i class="fas fa-clock text-warning"></i>
                                            <span>{{ $transfer->repayment_duration_months }} mois</span>
                                        </div>
                                        <div class="repayment-row">
                                            <i class="fas fa-play-circle text-success"></i>
                                            <span>Début: {{ $transfer->repayment_start_date?->format('d/m/Y') ?? 'N/A' }}</span>
                                        </div>
                                        <div class="repayment-dates">
                                            <small class="text-muted">Fin: {{ $transfer->repayment_end_date?->format('d/m/Y') ?? 'N/A' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Non programmé
                                        </span>
                                        @if($canSchedule)
                                            <small class="text-warning d-block mt-1">Action requise</small>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="td-waiting">
                                <div class="waiting-indicator">
                                    @if($isScheduled)
                                        <div class="waiting-time">
                                            <i class="fas fa-hourglass-half"></i>
                                            <span>Programmé {{ $transfer->transfer_scheduled_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="waiting-bar">
                                            @php
                                                $hoursSince = $transfer->transfer_scheduled_at->diffInHours(now());
                                                $progress = min(100, $hoursSince * 2);
                                            @endphp
                                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="waiting-since">
                                            Le {{ $transfer->transfer_scheduled_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="verified-by mt-2">
                                            <i class="fas fa-user-check text-success"></i>
                                            Par: {{ $transfer->documentsCheckedBy?->name ?? 'Admin' }}
                                        </div>
                                    @elseif($canSchedule)
                                        <div class="waiting-time text-warning">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span>En attente de programmation</span>
                                        </div>
                                        @if($transfer->paid_at)
                                            <div class="waiting-since">
                                                Payé le {{ $transfer->paid_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                        @if($transfer->approved_at)
                                            <div class="waiting-since">
                                                Approuvé le {{ $transfer->approved_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="td-actions">
                                <div class="action-buttons">
                                    @if($canSchedule)
                                        <button class="btn-schedule" onclick="openScheduleModal({{ $transfer->id }}, '{{ $transfer->request_number }}', '{{ $transfer->user?->full_name ?? 'N/A' }}', {{ $transfer->amount_approved ?? $transfer->amount_requested }})">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>Programmer le transfert</span>
                                        </button>
                                    @elseif($isScheduled)
                                        <button class="btn-execute" onclick="openExecuteModal({{ $transfer->id }}, '{{ $transfer->request_number }}', '{{ $transfer->user?->full_name ?? 'N/A' }}', '{{ number_format($transfer->amount_approved ?? $transfer->amount_requested, 0, ',', ' ') }} FCFA', '{{ number_format($transfer->monthly_repayment_amount ?? 0, 0, ',', ' ') }} FCFA', {{ $transfer->repayment_duration_months ?? 0 }})">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Exécuter le transfert</span>
                                        </button>
                                        <button class="btn-cancel" onclick="openCancelModal({{ $transfer->id }}, '{{ $transfer->request_number }}')">
                                            <i class="fas fa-ban"></i>
                                            <span>Annuler</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.funding.show-request', $transfer->id) }}" class="btn-view">
                                        <i class="fas fa-eye"></i>
                                        <span>Voir détails</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-illustration success">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <h4>Aucun transfert en attente</h4>
                                    <p>Tous les transferts ont été traités ou aucune demande n'a encore atteint ce stade.</p>
                                    <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-arrow-left"></i>
                                        Retour aux demandes
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="container-footer">
                <div class="pagination-meta">
                    Affichage de <strong>{{ $transfers->firstItem() }}</strong> à <strong>{{ $transfers->lastItem() }}</strong> sur <strong>{{ $transfers->total() }}</strong> transferts
                </div>
                <div class="pagination-nav">
                    {{ $transfers->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Schedule Transfer Modal (NOUVEAU) -->
    <div id="scheduleModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('scheduleModal')"></div>
        <div class="modal-box modal-lg">
            <div class="modal-header">
                <div class="header-icon info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4>Programmer le transfert</h4>
                <p>Définissez le programme de remboursement avant le transfert</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Vérification des documents</strong>
                        <p>Assurez-vous d'avoir vérifié tous les documents avant de programmer le transfert.</p>
                    </div>
                </div>

                <div class="transfer-summary">
                    <div class="summary-item">
                        <span class="label">N° Demande</span>
                        <span class="value" id="schedRequestNumber">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Client</span>
                        <span class="value" id="schedClient">-</span>
                    </div>
                    <div class="summary-item highlight">
                        <span class="label">Montant approuvé</span>
                        <span class="value text-success" id="schedAmount">-</span>
                    </div>
                </div>

                <form id="scheduleForm" method="POST">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-calculator"></i> Programme de remboursement</h5>
                        <div class="form-grid three-cols">
                            <div class="form-group">
                                <label for="totalRepayment">Montant total remboursement *</label>
                                <div class="input-unit">
                                    <input type="number" step="1000" id="totalRepayment" name="total_repayment_amount" required min="1000">
                                    <span class="unit">FCFA</span>
                                </div>
                                <span class="help">Doit inclure capital + intérêts</span>
                            </div>
                            <div class="form-group">
                                <label for="repaymentDuration">Durée remboursement *</label>
                                <div class="input-unit">
                                    <input type="number" id="repaymentDuration" name="repayment_duration_months" required min="1" max="60" value="12">
                                    <span class="unit">mois</span>
                                </div>
                                <span class="help">1 à 60 mois</span>
                            </div>
                            <div class="form-group">
                                <label for="repaymentStartDate">Date début remboursement *</label>
                                <input type="date" id="repaymentStartDate" name="repayment_start_date" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-tasks"></i> Vérification</h5>
                        <label class="checkbox-item">
                            <input type="checkbox" id="docsChecked" name="documents_checked" value="1" required>
                            <span class="checkmark"></span>
                            <span class="label">J'ai vérifié tous les documents fournis et ils sont conformes</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="schedNotes">Notes de programmation (optionnel)</label>
                        <textarea id="schedNotes" name="final_notes" rows="2" placeholder="Commentaires sur cette programmation..."></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('scheduleModal')">Annuler</button>
                <button type="button" class="btn btn-info btn-lg" onclick="submitSchedule()">
                    <i class="fas fa-calendar-check"></i>
                    Programmer le transfert
                </button>
            </div>
        </div>
    </div>

    <!-- Execute Transfer Modal -->
    <div id="executeModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('executeModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon success pulse">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <h4>Confirmer l'exécution du transfert</h4>
                <p>Les fonds seront immédiatement crédités sur le wallet du client</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Action irréversible</strong>
                        <p>Une fois confirmé, le transfert sera exécuté et le montant crédité sur le wallet.</p>
                    </div>
                </div>

                <div class="transfer-summary">
                    <div class="summary-item">
                        <span class="label">N° Demande</span>
                        <span class="value" id="execRequestNumber">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Client</span>
                        <span class="value" id="execClient">-</span>
                    </div>
                    <div class="summary-item highlight">
                        <span class="label">Montant à transférer</span>
                        <span class="value text-success" id="execAmount">-</span>
                    </div>
                </div>

                <div class="repayment-confirm-box">
                    <h6><i class="fas fa-calendar-check text-primary me-2"></i>Programme de remboursement</h6>
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted">Mensualité:</span>
                            <strong class="d-block text-primary" id="execMonthly">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Durée:</span>
                            <strong class="d-block" id="execDuration">- mois</strong>
                        </div>
                    </div>
                </div>

                <form id="executeForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="checkbox-item confirm-check">
                            <input type="checkbox" id="confirmTransfer" name="confirm_transfer" value="1" required>
                            <span class="checkmark"></span>
                            <span class="label">Je confirme avoir vérifié tous les documents et autorise le transfert des fonds</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="finalNotes">Notes de validation (optionnel)</label>
                        <textarea id="finalNotes" name="final_notes" rows="2" placeholder="Commentaires sur cette exécution..."></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('executeModal')">Annuler</button>
                <button type="button" class="btn btn-success btn-lg" onclick="submitExecute()">
                    <i class="fas fa-check-circle"></i>
                    Confirmer et transférer
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Transfer Modal -->
    <div id="cancelModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('cancelModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon warning">
                    <i class="fas fa-ban"></i>
                </div>
                <h4>Annuler le transfert programmé</h4>
                <p>Le transfert sera annulé et la demande retournée au statut précédent</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Le client sera notifié de l'annulation.</span>
                </div>

                <div class="transfer-summary">
                    <div class="summary-item">
                        <span class="label">N° Demande</span>
                        <span class="value" id="cancelRequestNumber">-</span>
                    </div>
                </div>

                <form id="cancelForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="cancelReason">Motif de l'annulation *</label>
                        <textarea id="cancelReason" name="cancellation_reason" rows="3" required minlength="5" placeholder="Expliquez pourquoi le transfert est annulé..."></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelModal')">Retour</button>
                <button type="button" class="btn btn-warning" onclick="submitCancel()">
                    <i class="fas fa-ban"></i>
                    Confirmer l'annulation
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-stack"></div>

</div>

<style>
/* [CSS inchangé - identique à votre version] */
.transfers-management-wrapper {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --success: #10b981;
    --success-dark: #059669;
    --warning: #f59e0b;
    --warning-dark: #d97706;
    --danger: #ef4444;
    --info: #06b6d4;

    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;

    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

    --radius-sm: 6px;
    --radius: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;

    max-width: 100%;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Quick Actions Bar */
.quick-actions-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary);
    transition: width 0.3s ease;
}

.quick-action-card.warning::before {
    background: var(--warning);
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.quick-action-card:hover::before {
    width: 6px;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.quick-action-card.warning .action-icon {
    background: #fef3c7;
    color: #d97706;
}

.action-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.action-title {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 1rem;
}

.action-desc {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.action-arrow {
    color: var(--gray-400);
    transition: transform 0.3s ease;
}

.quick-action-card:hover .action-arrow {
    transform: translateX(4px);
    color: var(--gray-600);
}

/* Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.header-content h2 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-content h2 i {
    color: var(--warning);
    font-size: 1.5rem;
}

.header-content p {
    color: var(--gray-500);
    font-size: 1rem;
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 1rem;
}

.quick-stat {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    text-align: center;
    min-width: 120px;
}

.quick-stat.warning {
    background: linear-gradient(135deg, #fffbeb 0%, white 100%);
    border-color: var(--warning);
}

.quick-stat.info {
    background: linear-gradient(135deg, #ecfeff 0%, white 100%);
    border-color: var(--info);
}

.quick-stat .stat-label {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.quick-stat .stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--gray-800);
    font-feature-settings: "tnum";
}

.quick-stat.warning .stat-value {
    color: var(--warning);
}

.quick-stat.info .stat-value {
    color: var(--info);
}

.quick-stat .stat-value small {
    font-size: 0.875rem;
    color: var(--gray-500);
}

/* Info Banner */
.info-banner {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: var(--radius-lg);
    margin-bottom: 1.5rem;
}

.info-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.info-content h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e40af;
    margin-bottom: 0.5rem;
}

.info-content p {
    color: #3b82f6;
    font-size: 0.9375rem;
    margin: 0;
    line-height: 1.6;
}

/* Control Panel */
.control-panel {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.filters-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.filter-row {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.filter-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-field label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.input-affix, .select-affix {
    position: relative;
    display: flex;
    align-items: center;
}

.input-affix input, .select-affix select, .filter-field input, .filter-field select {
    width: 100%;
    padding: 0.625rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 0.9375rem;
    background: white;
    transition: all 0.2s;
}

.input-affix input:focus, .select-affix select:focus, .filter-field input:focus, .filter-field select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-affix .prefix {
    position: absolute;
    left: 1rem;
    color: var(--gray-400);
}

.input-affix input {
    padding-left: 2.5rem;
}

.select-affix .suffix {
    position: absolute;
    right: 1rem;
    color: var(--gray-400);
    pointer-events: none;
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.btn-ghost {
    background: transparent;
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
}

.btn-ghost:hover {
    background: var(--gray-50);
    color: var(--gray-800);
}

.btn-info {
    background: var(--info);
    color: white;
}

.btn-info:hover {
    background: #0891b2;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
}

/* Data Container */
.data-container {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.container-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(to right, white, var(--gray-50));
}

.header-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-title i {
    color: var(--warning);
    font-size: 1.125rem;
}

.header-title h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
}

.badge-count {
    background: var(--gray-500);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
}

.badge-count.warning {
    background: var(--warning);
}

.badge-count.info {
    background: var(--info);
}

/* Table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.transfers-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 1200px;
}

.transfers-table th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    white-space: nowrap;
}

.transfers-table td {
    padding: 1.25rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: top;
}

.transfers-table tbody tr {
    transition: all 0.2s;
}

.transfers-table tbody tr:hover {
    background: #fffbeb;
}

.transfers-table tbody tr.scheduled {
    border-left: 3px solid var(--info);
}

.transfers-table tbody tr.to-schedule {
    border-left: 3px solid var(--warning);
}

/* Cells */
.td-info {
    width: 16%;
}

.request-block {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.request-badge {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 0.8125rem;
    color: var(--primary);
    background: rgba(59, 130, 246, 0.1);
    padding: 0.25rem 0.625rem;
    border-radius: var(--radius-sm);
    display: inline-flex;
    width: fit-content;
}

.request-title {
    font-size: 0.9375rem;
    color: var(--gray-800);
    font-weight: 600;
    line-height: 1.4;
}

.request-type {
    font-size: 0.75rem;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.td-client {
    width: 16%;
}

.client-block {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
}

.client-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    text-transform: uppercase;
}

.client-details {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    min-width: 0;
}

.client-name {
    font-weight: 600;
    color: var(--gray-800);
    font-size: 0.9375rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.client-email, .client-phone {
    font-size: 0.8125rem;
    color: var(--gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.td-amount {
    width: 14%;
}

.amount-highlight {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.amount-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--gray-900);
    font-family: 'Courier New', monospace;
}

.amount-currency {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 600;
}

.wallet-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: var(--gray-500);
    padding: 0.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-sm);
}

.wallet-info i {
    color: var(--primary);
}

.repayment-total {
    padding: 0.5rem;
    background: rgba(6, 182, 212, 0.1);
    border-radius: var(--radius-sm);
    font-size: 0.8125rem;
}

.td-status {
    width: 12%;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-pill.scheduled {
    background: #cffafe;
    color: #0891b2;
}

.status-pill.to-schedule {
    background: #fef3c7;
    color: #d97706;
}

.status-pill.pending {
    background: #f3f4f6;
    color: #374151;
}

.td-repayment {
    width: 18%;
}

.repayment-program {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.repayment-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.repayment-row i {
    font-size: 1rem;
    width: 20px;
}

.repayment-dates {
    font-size: 0.75rem;
    color: var(--gray-400);
    margin-top: 0.25rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--gray-200);
}

.td-waiting {
    width: 14%;
}

.waiting-indicator {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.waiting-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--warning);
}

.waiting-time.text-warning {
    color: var(--warning);
}

.waiting-time i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.waiting-bar {
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--warning) 0%, #fbbf24 100%);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.waiting-since {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.verified-by {
    font-size: 0.8125rem;
    color: var(--gray-500);
}

.verified-by i {
    margin-right: 0.25rem;
}

.td-actions {
    width: 20%;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.btn-schedule {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--warning);
    color: white;
    border: none;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-schedule:hover {
    background: var(--warning-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-execute {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--success);
    color: white;
    border: none;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-execute:hover {
    background: var(--success-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-cancel {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    color: var(--warning);
    border: 1px solid var(--warning);
    border-radius: var(--radius);
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-cancel:hover {
    background: #fffbeb;
}

.btn-view {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--gray-100);
    color: var(--gray-600);
    border: none;
    border-radius: var(--radius);
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-view:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-illustration {
    width: 96px;
    height: 96px;
    margin: 0 auto 1.5rem;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--gray-400);
}

.empty-illustration.success {
    background: #d1fae5;
    color: var(--success);
}

.empty-state h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--gray-500);
    font-size: 1rem;
    max-width: 400px;
    margin: 0 auto 1.5rem;
    line-height: 1.6;
}

/* Container Footer */
.container-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gray-50);
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-meta {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.pagination-meta strong {
    color: var(--gray-700);
    font-weight: 600;
}

/* Modals */
.modal-wrapper {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-wrapper.active {
    opacity: 1;
    visibility: visible;
}

.modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
}

.modal-box {
    position: relative;
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    display: flex;
    flex-direction: column;
    transform: scale(0.95) translateY(20px);
    transition: all 0.3s ease;
}

.modal-box.modal-lg {
    max-width: 720px;
}

.modal-wrapper.active .modal-box {
    transform: scale(1) translateY(0);
}

.modal-header {
    padding: 2rem 2rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid var(--gray-100);
}

.header-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.header-icon.success {
    background: #d1fae5;
    color: var(--success);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
}

.header-icon.success.pulse {
    animation: iconPulse 2s infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3); }
    50% { transform: scale(1.05); box-shadow: 0 6px 24px rgba(16, 185, 129, 0.4); }
}

.header-icon.info {
    background: #cffafe;
    color: var(--info);
    box-shadow: 0 4px 16px rgba(6, 182, 212, 0.3);
}

.header-icon.warning {
    background: #fef3c7;
    color: #d97706;
    box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3);
}

.modal-header h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.modal-header p {
    color: var(--gray-500);
    font-size: 0.9375rem;
    margin: 0;
}

.modal-body {
    padding: 1.5rem 2rem;
    overflow-y: auto;
}

.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.alert-warning strong {
    display: block;
    margin-bottom: 0.25rem;
}

.alert-warning p {
    margin: 0;
    font-size: 0.8125rem;
}

.alert-info {
    background: #cffafe;
    color: #155e75;
    border: 1px solid #a5f3fc;
}

.alert-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

.transfer-summary {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--gray-200);
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.summary-item {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.summary-item.highlight {
    background: white;
    margin: -1.25rem;
    padding: 1.25rem;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    border-left: 3px solid var(--success);
}

.summary-item .label {
    font-size: 0.8125rem;
    color: var(--gray-500);
    font-weight: 500;
}

.summary-item .value {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 0.9375rem;
}

.summary-item .value.text-success {
    color: var(--success);
    font-size: 1.125rem;
    font-family: 'Courier New', monospace;
}

.repayment-confirm-box {
    background: var(--gray-50);
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--gray-200);
}

.repayment-confirm-box h6 {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 0.75rem;
}

.row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.col-6 {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.text-muted {
    color: var(--gray-500);
    font-size: 0.8125rem;
}

.d-block {
    display: block;
}

.text-primary {
    color: var(--primary);
}

.form-section {
    margin-bottom: 1.5rem;
}

.form-section h5 {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h5 i {
    color: var(--primary);
}

.form-grid {
    display: grid;
    gap: 1rem;
}

.form-grid.three-cols {
    grid-template-columns: repeat(3, 1fr);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--gray-700);
}

.input-unit {
    position: relative;
    display: flex;
    align-items: center;
}

.input-unit input, .form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 0.9375rem;
    transition: all 0.2s;
    background: white;
}

.input-unit input:focus, .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-unit .unit {
    position: absolute;
    right: 1rem;
    color: var(--gray-400);
    font-size: 0.8125rem;
    font-weight: 600;
    pointer-events: none;
}

.form-group .help {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-size: 0.9375rem;
    color: var(--gray-700);
}

.checkbox-item input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--gray-300);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
    background: white;
}

.checkbox-item input[type="checkbox"]:checked + .checkmark {
    background: var(--success);
    border-color: var(--success);
}

.checkbox-item input[type="checkbox"]:checked + .checkmark::after {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    color: white;
    font-size: 0.75rem;
}

.checkbox-item.confirm-check {
    padding: 1rem;
    background: #f0fdf4;
    border-radius: var(--radius);
    border: 1px solid #86efac;
}

.checkbox-item.confirm-check .label {
    font-weight: 600;
    color: #166534;
    line-height: 1.4;
}

.modal-footer {
    padding: 1.25rem 2rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background: var(--gray-50);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: var(--success-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-success.btn-lg {
    padding: 0.875rem 1.5rem;
    font-size: 1rem;
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-warning:hover {
    background: var(--warning-dark);
    transform: translateY(-1px);
}

/* Toast */
.toast-stack {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-width: 400px;
}

.toast {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    box-shadow: var(--shadow-xl);
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    animation: toastSlide 0.3s ease;
    border: 1px solid var(--gray-200);
    border-left: 4px solid;
}

.toast.success { border-left-color: var(--success); }
.toast.error { border-left-color: var(--danger); }
.toast.warning { border-left-color: var(--warning); }

@keyframes toastSlide {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .filter-row {
        grid-template-columns: 1fr 1fr;
    }

    .form-grid.three-cols {
        grid-template-columns: 1fr;
    }

    .transfer-summary {
        grid-template-columns: 1fr;
    }

    .summary-item.highlight {
        margin: 0;
        border-radius: var(--radius-md);
        border-left: none;
        border-top: 3px solid var(--success);
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }

    .filter-row {
        grid-template-columns: 1fr;
    }

    .header-stats {
        width: 100%;
        justify-content: space-between;
    }

    .quick-stat {
        flex: 1;
    }

    .modal-footer {
        flex-direction: column-reverse;
    }

    .modal-footer .btn {
        width: 100%;
    }
}
</style>

<script>
// Modal functions
function openScheduleModal(id, requestNumber, client, amountApproved) {
    const modal = document.getElementById('scheduleModal');
    const form = document.getElementById('scheduleForm');

    document.getElementById('schedRequestNumber').textContent = requestNumber;
    document.getElementById('schedClient').textContent = client;
    document.getElementById('schedAmount').textContent = new Intl.NumberFormat('fr-FR').format(amountApproved) + ' FCFA';

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/verify-and-schedule`;

    // Pré-remplir avec des valeurs suggérées
    const suggestedRepayment = Math.round(amountApproved * 1.2 / 1000) * 1000; // +20%
    document.getElementById('totalRepayment').value = suggestedRepayment;

    // Date minimale = aujourd'hui
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('repaymentStartDate').min = today;
    document.getElementById('repaymentStartDate').value = today;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openExecuteModal(id, requestNumber, client, amount, monthly, duration) {
    const modal = document.getElementById('executeModal');
    const form = document.getElementById('executeForm');

    document.getElementById('execRequestNumber').textContent = requestNumber;
    document.getElementById('execClient').textContent = client;
    document.getElementById('execAmount').textContent = amount;
    document.getElementById('execMonthly').textContent = monthly;
    document.getElementById('execDuration').textContent = duration + ' mois';

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/execute-transfer`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openCancelModal(id, requestNumber) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');

    document.getElementById('cancelRequestNumber').textContent = requestNumber;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/cancel-transfer`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
}

function submitSchedule() {
    const form = document.getElementById('scheduleForm');

    const totalRepayment = document.getElementById('totalRepayment').value;
    const duration = document.getElementById('repaymentDuration').value;
    const startDate = document.getElementById('repaymentStartDate').value;
    const docsChecked = document.getElementById('docsChecked').checked;

    if (!totalRepayment || totalRepayment < 1000) {
        showToast('error', 'Erreur', 'Le montant total de remboursement est requis (minimum 1 000 FCFA)');
        return;
    }

    if (!duration || duration < 1 || duration > 60) {
        showToast('error', 'Erreur', 'La durée doit être comprise entre 1 et 60 mois');
        return;
    }

    if (!startDate) {
        showToast('error', 'Erreur', 'La date de début de remboursement est requise');
        return;
    }

    if (!docsChecked) {
        showToast('error', 'Erreur', 'Vous devez confirmer avoir vérifié les documents');
        return;
    }

    // Désactiver le bouton
    const btn = document.querySelector('#scheduleModal .btn-info');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Programmation...';

    form.submit();
}

function submitExecute() {
    const form = document.getElementById('executeForm');
    const confirmCheck = document.getElementById('confirmTransfer').checked;

    if (!confirmCheck) {
        showToast('error', 'Confirmation requise', 'Vous devez confirmer la validation du transfert');
        return;
    }

    // Désactiver le bouton
    const btn = document.querySelector('#executeModal .btn-success');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

    form.submit();
}

function submitCancel() {
    const form = document.getElementById('cancelForm');
    const reason = document.getElementById('cancelReason').value;

    if (!reason || reason.length < 5) {
        showToast('error', 'Erreur', 'Le motif d\'annulation doit contenir au moins 5 caractères');
        return;
    }

    // Désactiver le bouton
    const btn = document.querySelector('#cancelModal .btn-warning');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

    form.submit();
}

// Toast system
function showToast(type, title, message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle'
    };

    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b'
    };

    toast.innerHTML = `
        <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${colors[type]}20; color: ${colors[type]};">
            <i class="fas ${icons[type]}"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 700; color: #1e293b; font-size: 0.9375rem; margin-bottom: 0.25rem;">${title}</div>
            <div style="color: #64748b; font-size: 0.875rem;">${message}</div>
        </div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Event listeners
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal('scheduleModal');
        closeModal('executeModal');
        closeModal('cancelModal');
    }
});

// Session messages
@if(session('success'))
    showToast('success', 'Succès', '{{ session('success') }}');
@endif
@if(session('error'))
    showToast('error', 'Erreur', '{{ session('error') }}');
@endif
@if(session('warning'))
    showToast('warning', 'Attention', '{{ session('warning') }}');
@endif
</script>
@endsection
