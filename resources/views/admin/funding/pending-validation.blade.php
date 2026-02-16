@extends('admin.layouts.app')

@section('title', 'Gestion des demandes de financement')
@section('page-title', 'Demandes clients')

@section('content')
<div class="funding-management-wrapper">

    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <h2>
                <i class="fas fa-hand-holding-usd"></i>
                Demandes de financement clients
            </h2>
            <p>Gérez toutes les demandes déposées (personnalisées et prédéfinies)</p>
        </div>
        <div class="header-stats">
            <div class="quick-stat">
                <span class="stat-label">Total demandes</span>
                <span class="stat-value">{{ ($stats['total_pending'] ?? 0) + ($stats['pending_payment'] ?? 0) + ($stats['paid_awaiting_validation'] ?? 0) }}</span>
            </div>
            <div class="quick-stat highlight">
                <span class="stat-label">En attente</span>
                <span class="stat-value text-warning">{{ $stats['total_pending'] ?? 0 }}</span>
            </div>
            <div class="quick-stat">
                <span class="stat-label">Paiements à vérifier</span>
                <span class="stat-value">{{ $stats['paid_awaiting_validation'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-bar">
        <a href="{{ route('admin.funding.pending-transfers') }}" class="quick-action-card warning">
            <div class="action-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="action-content">
                <span class="action-title">Transferts en attente</span>
                <span class="action-desc">Voir les transferts programmés</span>
            </div>
            <i class="fas fa-arrow-right action-arrow"></i>
        </a>

        <a href="{{ route('admin.funding.pending-payments') }}" class="quick-action-card info">
            <div class="action-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="action-content">
                <span class="action-title">Paiements à vérifier</span>
                <span class="action-desc">{{ $stats['paid_awaiting_validation'] ?? 0 }} en attente</span>
            </div>
            <i class="fas fa-arrow-right action-arrow"></i>
        </a>
    </div>

    <!-- Stats Dashboard -->
    <div class="stats-grid">
        <!-- Demandes Personnalisées -->
        <div class="stat-category custom {{ request('type') == 'predefined' ? 'dimmed' : '' }}">
            <div class="category-header">
                <i class="fas fa-pencil-alt"></i>
                <span>Personnalisées</span>
                <span class="category-total">{{ $fund->where('is_predefined', false)->count() }}</span>
            </div>
            <div class="category-stats">
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', false)->where('status', 'submitted')->count() }}</span>
                    <span class="label">Soumises</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', false)->where('status', 'under_review')->count() }}</span>
                    <span class="label">En étude</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', false)->where('status', 'validated')->count() }}</span>
                    <span class="label">Validées</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', false)->where('status', 'paid')->count() }}</span>
                    <span class="label">Payées</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', false)->where('status', 'approved')->count() }}</span>
                    <span class="label">Approuvées</span>
                </div>
                <div class="mini-stat highlight">
                    <span class="number">{{ number_format($fund->where('is_predefined', false)->sum('amount_approved') / 1000000, 1) }}M</span>
                    <span class="label">FCFA</span>
                </div>
            </div>
        </div>

        <!-- Demandes Prédéfinies -->
        <div class="stat-category predefined {{ request('type') == 'custom' ? 'dimmed' : '' }}">
            <div class="category-header">
                <i class="fas fa-box-open"></i>
                <span>Prédéfinies</span>
                <span class="category-total">{{ $fund->where('is_predefined', true)->count() }}</span>
            </div>
            <div class="category-stats">
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', true)->where('status', 'submitted')->count() }}</span>
                    <span class="label">Soumises</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', true)->where('status', 'paid')->count() }}</span>
                    <span class="label">Payées</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', true)->where('status', 'documents_validated')->count() }}</span>
                    <span class="label">Docs validés</span>
                </div>
                <div class="mini-stat warning">
                    <span class="number">{{ $fund->where('is_predefined', true)->where('transfer_status', 'scheduled')->count() }}</span>
                    <span class="label">Transfert attente</span>
                </div>
                <div class="mini-stat">
                    <span class="number">{{ $fund->where('is_predefined', true)->where('status', 'funded')->count() }}</span>
                    <span class="label">Financées</span>
                </div>
                <div class="mini-stat highlight">
                    <span class="number">{{ number_format($fund->where('is_predefined', true)->sum('amount_approved') / 1000000, 1) }}M</span>
                    <span class="label">FCFA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="control-panel">
        <form method="GET" action="{{ route('admin.funding.pending-validation') }}" class="filters-form" id="filterForm">
            <div class="filter-row">
                <div class="filter-field">
                    <label for="type">Type de demande</label>
                    <div class="select-affix">
                        <select id="type" name="type" onchange="this.form.submit()">
                            <option value="">Toutes les demandes</option>
                            <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Personnalisées uniquement</option>
                            <option value="predefined" {{ request('type') == 'predefined' ? 'selected' : '' }}>Prédéfinies uniquement</option>
                        </select>
                        <i class="fas fa-chevron-down suffix"></i>
                    </div>
                </div>

                <div class="filter-field search-field">
                    <label for="search">Recherche</label>
                    <div class="input-affix">
                        <i class="fas fa-search prefix"></i>
                        <input type="text" id="search" name="search" placeholder="N°, client, titre..." value="{{ request('search') }}">
                        @if(request('search'))
                            <button type="button" class="suffix clear-btn" onclick="clearField('search')">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="filter-field">
                    <label for="status">Statut</label>
                    <div class="select-affix">
                        <select id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Soumise</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>En étude</option>
                            <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validée (attente paiement)</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payée</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvée</option>
                            <option value="documents_validated" {{ request('status') == 'documents_validated' ? 'selected' : '' }}>Docs validés (transfert attente)</option>
                            <option value="transfer_pending" {{ request('status') == 'transfer_pending' ? 'selected' : '' }}>Transfert programmé</option>
                            <option value="funded" {{ request('status') == 'funded' ? 'selected' : '' }}>Financée</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complétée</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                        </select>
                        <i class="fas fa-chevron-down suffix"></i>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="date_from">Du</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="filter-field">
                    <label for="date_to">Au</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    <span>Filtrer</span>
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-ghost">
                        <i class="fas fa-undo"></i>
                        <span>Réinitialiser</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-container">
        <div class="container-header">
            <div class="header-title">
                <i class="fas fa-list-alt"></i>
                <h3>Liste des demandes</h3>
                <span class="badge-count">{{ $fund->count() }}</span>
            </div>
            <div class="header-tools">
                <button class="tool-btn" onclick="refreshData()" title="Rafraîchir">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <a href="{{ route('admin.funding.pending-transfers') }}" class="tool-btn warning" title="Voir transferts en attente">
                    <i class="fas fa-clock"></i>
                </a>
                <a href="{{ route('admin.funding.pending-payments') }}" class="tool-btn" title="Voir paiements en attente">
                    <i class="fas fa-wallet"></i>
                    @if(($stats['paid_awaiting_validation'] ?? 0) > 0)
                        <span class="tool-badge">{{ $stats['paid_awaiting_validation'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th class="th-type">Type</th>
                        <th class="th-request">Demande</th>
                        <th class="th-client">Client</th>
                        <th class="th-amount">Montants</th>
                        <th class="th-status">Statut</th>
                        <th class="th-transfer">Transfert</th>
                        <th class="th-date">Dates</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fund as $request)
                        <tr class="request-row type-{{ $request->is_predefined ? 'predefined' : 'custom' }} {{ $request->transfer_status == 'scheduled' ? 'transfer-pending' : '' }}">
                            <td class="td-type">
                                @if($request->is_predefined)
                                    <span class="type-badge predefined">
                                        <i class="fas fa-box"></i>
                                        Prédéfinie
                                    </span>
                                    <span class="type-detail" title="{{ $request->fundingType?->name }}">
                                        {{ Str::limit($request->fundingType?->name ?? 'Type inconnu', 20) }}
                                    </span>
                                    <span class="type-category">{{ $request->fundingType?->category ?? 'N/A' }}</span>
                                @else
                                    <span class="type-badge custom">
                                        <i class="fas fa-pencil-alt"></i>
                                        Personnalisée
                                    </span>
                                    <span class="type-detail">Sur mesure</span>
                                    <span class="type-category">{{ $request->type ?? 'custom' }}</span>
                                @endif
                            </td>
                            <td class="td-request">
                                <div class="request-block">
                                    <span class="request-badge">{{ $request->request_number }}</span>
                                    <span class="request-title" title="{{ $request->title }}">
                                        {{ Str::limit($request->title, 40) }}
                                    </span>
                                    @if($request->description)
                                        <span class="request-desc">{{ Str::limit($request->description, 60) }}</span>
                                    @endif
                                    @if($request->project_location)
                                        <span class="request-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $request->project_location }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="td-client">
                                <div class="client-block">
                                    @php
                                        $email = $request->user?->email ?? 'default@example.com';
                                        $hue = crc32($email) % 360;
                                    @endphp
                                    <div class="client-avatar" style="background: hsl({{ $hue }}, 70%, 45%)">
                                        {{ strtoupper(substr($request->user?->first_name ?? 'N', 0, 1) . substr($request->user?->last_name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="client-details">
                                        <span class="client-name">{{ $request->user?->full_name ?? 'N/A' }}</span>
                                        <span class="client-email">{{ $request->user?->email ?? '' }}</span>
                                        <span class="client-phone">{{ $request->user?->phone ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="td-amount">
                                <div class="amount-block">
                                    <div class="amount-row">
                                        <span class="amount-label">Demandé:</span>
                                        <span class="amount-value">{{ number_format($request->amount_requested, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    @if($request->amount_approved)
                                        <div class="amount-row approved">
                                            <span class="amount-label">Approuvé:</span>
                                            <span class="amount-value">{{ number_format($request->amount_approved, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    @endif
                                    @if($request->expected_payment)
                                        <div class="amount-row fee">
                                            <span class="amount-label">Frais:</span>
                                            <span class="amount-value">{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    @endif
                                    @if($request->kkiapay_amount_paid)
                                        <div class="amount-row paid">
                                            <span class="amount-label">Payé:</span>
                                            <span class="amount-value">{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="td-status">
                                @php
                                    $statusConfig = [
                                        'draft' => ['class' => 'status-draft', 'icon' => 'fa-edit', 'label' => 'Brouillon'],
                                        'submitted' => ['class' => 'status-submitted', 'icon' => 'fa-paper-plane', 'label' => 'Soumise'],
                                        'under_review' => ['class' => 'status-review', 'icon' => 'fa-search', 'label' => 'En étude'],
                                        'validated' => ['class' => 'status-validated', 'icon' => 'fa-check-circle', 'label' => 'Validée'],
                                        'paid' => ['class' => 'status-paid', 'icon' => 'fa-money-bill-wave', 'label' => 'Payée'],
                                        'approved' => ['class' => 'status-approved', 'icon' => 'fa-check-double', 'label' => 'Approuvée'],
                                        'documents_validated' => ['class' => 'status-docs-validated', 'icon' => 'fa-file-signature', 'label' => 'Docs validés'],
                                        'transfer_pending' => ['class' => 'status-transfer-pending', 'icon' => 'fa-clock', 'label' => 'Transfert attente'],
                                        'funded' => ['class' => 'status-completed', 'icon' => 'fa-trophy', 'label' => 'Financée'],
                                        'rejected' => ['class' => 'status-rejected', 'icon' => 'fa-times-circle', 'label' => 'Rejetée'],
                                        'completed' => ['class' => 'status-completed', 'icon' => 'fa-trophy', 'label' => 'Complétée'],
                                    ];
                                    $currentStatus = $statusConfig[$request->status] ?? ['class' => 'status-pending', 'icon' => 'fa-question', 'label' => $request->status];
                                @endphp
                                <span class="status-pill {{ $currentStatus['class'] }}">
                                    <i class="fas {{ $currentStatus['icon'] }}"></i>
                                    {{ $currentStatus['label'] }}
                                </span>
                                @if($request->duration)
                                    <span class="status-meta">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $request->duration }} mois
                                    </span>
                                @endif
                            </td>
                            <td class="td-transfer">
                                @if($request->is_predefined)
                                    @if($request->transfer_status == 'scheduled')
                                        <div class="transfer-info pending">
                                            <i class="fas fa-hourglass-half"></i>
                                            <div class="transfer-details">
                                                <span class="transfer-status">Programmé</span>
                                                <span class="transfer-date">{{ $request->transfer_scheduled_at?->diffForHumans() ?? 'En attente' }}</span>
                                                <button class="btn-execute-transfer" onclick="openExecuteTransferModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ number_format($request->amount_approved ?? $request->amount_requested, 0, ',', ' ') }} FCFA')">
                                                    <i class="fas fa-check-circle"></i>
                                                    Exécuter transfert
                                                </button>
                                            </div>
                                        </div>
                                    @elseif($request->transfer_status == 'completed')
                                        <div class="transfer-info completed">
                                            <i class="fas fa-check-double"></i>
                                            <div class="transfer-details">
                                                <span class="transfer-status">Transféré</span>
                                                <span class="transfer-date">{{ $request->transfer_executed_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    @elseif($request->transfer_status == 'processing')
                                        <div class="transfer-info processing">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <div class="transfer-details">
                                                <span class="transfer-status">En cours</span>
                                                <span class="transfer-date">Traitement...</span>
                                            </div>
                                        </div>
                                    @elseif($request->transfer_status == 'cancelled')
                                        <div class="transfer-info" style="background: #fee2e2; border-color: #fecaca;">
                                            <i class="fas fa-ban" style="color: #dc2626;"></i>
                                            <div class="transfer-details">
                                                <span class="transfer-status" style="color: #dc2626;">Annulé</span>
                                                <span class="transfer-date">Transfert annulé</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="transfer-none">-</span>
                                    @endif
                                @else
                                    <span class="transfer-na">N/A</span>
                                @endif
                            </td>
                            <td class="td-date">
                                <div class="date-block">
                                    <div class="date-row">
                                        <span class="date-label">Créée:</span>
                                        <span class="date-value">{{ $request->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="date-row">
                                        <span class="date-label">Il y a:</span>
                                        <span class="date-relative">{{ $request->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($request->submitted_at)
                                        <div class="date-row">
                                            <span class="date-label">Soumise:</span>
                                            <span class="date-value">{{ $request->submitted_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($request->documents_checked_at)
                                        <div class="date-row highlight">
                                            <span class="date-label">Docs vérifiés:</span>
                                            <span class="date-value">{{ $request->documents_checked_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($request->paid_at)
                                        <div class="date-row success">
                                            <span class="date-label">Payée:</span>
                                            <span class="date-value">{{ $request->paid_at->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="td-actions">
                                <div class="action-dropdown">
                                    <button class="action-toggle" onclick="toggleDropdown(this)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="action-menu">
                                        <a href="{{ route('admin.funding.show-request', $request->id) }}" class="action-item">
                                            <i class="fas fa-eye"></i>
                                            Voir détails complets
                                        </a>

                                        @if(!$request->is_predefined)
                                            {{-- Actions pour demandes PERSONNALISÉES --}}
                                            @if(in_array($request->status, ['submitted']))
                                                <form action="{{ route('admin.funding.under-review', $request->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="action-item text-info" onclick="return confirm('Mettre cette demande en étude ?')">
                                                        <i class="fas fa-search"></i>
                                                        Mettre en étude
                                                    </button>
                                                </form>
                                            @endif

                                            @if(in_array($request->status, ['submitted', 'under_review']))
                                                <button class="action-item text-success" onclick="openValidationModal({{ $request->id }}, '{{ number_format($request->amount_requested, 0, ',', ' ') }} FCFA', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ $request->request_number }}')">
                                                    <i class="fas fa-check-circle"></i>
                                                    Valider & définir prix
                                                </button>

                                                <button class="action-item text-danger" onclick="openRejectModal({{ $request->id }}, '{{ $request->request_number }}', false)">
                                                    <i class="fas fa-times-circle"></i>
                                                    Rejeter la demande
                                                </button>
                                            @endif

                                            @if($request->status === 'validated')
                                                <span class="action-item disabled">
                                                    <i class="fas fa-clock"></i>
                                                    En attente paiement client
                                                </span>
                                            @endif

                                            @if($request->status === 'paid' && $request->kkiapay_transaction_id)
                                                <button class="action-item text-success" onclick="openVerifyPaymentModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ number_format($request->kkiapay_amount_paid, 0, ',', ' ') }} FCFA', '{{ number_format($request->expected_payment, 0, ',', ' ') }} FCFA')">
                                                    <i class="fas fa-wallet"></i>
                                                    Vérifier paiement
                                                </button>
                                            @endif
                                        @else
                                            {{-- Actions pour demandes PRÉDÉFINIES --}}
                                            @if($request->status === 'submitted')
                                                <button class="action-item text-success" onclick="openApprovePredefinedModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ number_format($request->amount_requested, 0, ',', ' ') }} FCFA')">
                                                    <i class="fas fa-check-circle"></i>
                                                    Approuver la demande
                                                </button>

                                                <button class="action-item text-danger" onclick="openRejectModal({{ $request->id }}, '{{ $request->request_number }}', true)">
                                                    <i class="fas fa-times-circle"></i>
                                                    Rejeter la demande
                                                </button>
                                            @endif

                                            @if(in_array($request->status, ['paid', 'approved']))
                                                <button class="action-item text-info" onclick="openCheckDocumentsModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ $request->user?->email ?? '' }}', {{ $request->amount_approved ?? $request->amount_requested }})">
                                                    <i class="fas fa-folder-open"></i>
                                                    Vérifier documents & programmer
                                                </button>
                                            @endif

                                            @if($request->status === 'documents_validated' && $request->transfer_status === 'scheduled')
                                                <button class="action-item text-success highlight" onclick="openExecuteTransferModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}', '{{ number_format($request->amount_approved ?? $request->amount_requested, 0, ',', ' ') }} FCFA')">
                                                    <i class="fas fa-money-check-alt"></i>
                                                    <strong>Exécuter le transfert</strong>
                                                </button>

                                                <button class="action-item text-warning" onclick="openCancelTransferModal({{ $request->id }}, '{{ $request->request_number }}')">
                                                    <i class="fas fa-ban"></i>
                                                    Annuler le transfert
                                                </button>
                                            @endif

                                            @if($request->status === 'funded')
                                                <button class="action-item text-primary" onclick="openCompleteModal({{ $request->id }}, '{{ $request->request_number }}', '{{ $request->user?->full_name ?? 'N/A' }}')">
                                                    <i class="fas fa-trophy"></i>
                                                    Finaliser (Complétée)
                                                </button>
                                            @endif
                                        @endif

                                        <div class="action-divider"></div>

                                        <a href="mailto:{{ $request->user?->email }}" class="action-item">
                                            <i class="fas fa-envelope"></i>
                                            Contacter le client
                                        </a>

                                        @if($request->user?->phone)
                                            <a href="tel:{{ $request->user->phone }}" class="action-item">
                                                <i class="fas fa-phone"></i>
                                                Appeler le client
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-illustration">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4>Aucune demande trouvée</h4>
                                    <p>Aucune demande ne correspond aux critères de recherche actuels.</p>
                                    <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-undo"></i>
                                        Réinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Execute Transfer Modal -->
    <div id="executeTransferModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('executeTransferModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon success pulse">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <h4>Valider et exécuter le transfert</h4>
                <p>Confirmez le crédit des fonds sur le wallet client</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Cette action est irréversible. Les fonds seront immédiatement crédités sur le wallet du client.</span>
                </div>

                <div class="request-summary-card highlight">
                    <div class="summary-grid">
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
                </div>

                <form id="executeTransferForm" method="POST" class="validation-form">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-comment-alt"></i> Notes de validation finale</h5>
                        <div class="form-group">
                            <textarea id="execNotes" name="final_notes" rows="3" placeholder="Commentaires sur cette validation finale (optionnel)..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="checkbox-item confirm-check">
                            <input type="checkbox" id="confirmTransfer" name="confirm_transfer" value="1" required>
                            <span class="checkmark"></span>
                            <span class="label">Je confirme avoir vérifié tous les documents et autorise le transfert des fonds</span>
                        </label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('executeTransferModal')">Annuler</button>
                <button type="button" class="btn btn-success btn-lg" onclick="submitExecuteTransfer()">
                    <i class="fas fa-check-circle"></i>
                    Confirmer et transférer
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Transfer Modal -->
    <div id="cancelTransferModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('cancelTransferModal')"></div>
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

                <div class="request-summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">N° Demande</span>
                            <span class="value" id="cancelRequestNumber">-</span>
                        </div>
                    </div>
                </div>

                <form id="cancelTransferForm" method="POST" class="validation-form">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-comment-alt"></i> Motif de l'annulation *</h5>
                        <div class="form-group">
                            <textarea id="cancelReason" name="cancellation_reason" rows="3" required minlength="5" placeholder="Expliquez pourquoi le transfert est annulé..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelTransferModal')">Retour</button>
                <button type="button" class="btn btn-warning" onclick="submitCancelTransfer()">
                    <i class="fas fa-ban"></i>
                    Confirmer l'annulation
                </button>
            </div>
        </div>
    </div>

    <!-- Validation Modal (personnalisées) -->
    <div id="validationModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('validationModal')"></div>
        <div class="modal-box modal-lg">
            <div class="modal-header">
                <div class="header-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>Valider la demande personnalisée</h4>
                <p>Définissez les montants approuvés pour cette demande</p>
            </div>

            <div class="modal-body">
                <div class="request-summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">N° Demande</span>
                            <span class="value" id="valRequestNumber">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Client</span>
                            <span class="value" id="valClient">-</span>
                        </div>
                        <div class="summary-item highlight">
                            <span class="label">Montant demandé</span>
                            <span class="value" id="valAmount">-</span>
                        </div>
                    </div>
                </div>

                <form id="validationForm" method="POST" class="validation-form">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-calculator"></i> Montants et détails</h5>
                        <div class="form-grid three-cols">
                            <div class="form-group">
                                <label for="valApprovedAmount">Montant approuvé *</label>
                                <div class="input-unit">
                                    <input type="number" step="1000" id="valApprovedAmount" name="amount_approved" required min="1000">
                                    <span class="unit">FCFA</span>
                                </div>
                                <span class="help">Minimum 1 000 FCFA</span>
                            </div>
                            <div class="form-group">
                                <label for="valExpectedPayment">Montant à facturer (frais) *</label>
                                <div class="input-unit">
                                    <input type="number" step="1000" id="valExpectedPayment" name="expected_payment" required min="0">
                                    <span class="unit">FCFA</span>
                                </div>
                                <span class="help">Frais d'inscription/adhésion</span>
                            </div>
                            <div class="form-group">
                                <label for="valDuration">Durée *</label>
                                <div class="input-unit">
                                    <input type="number" id="valDuration" name="duration" required min="6" max="120" value="12">
                                    <span class="unit">mois</span>
                                </div>
                                <span class="help">6 à 120 mois</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-comment-alt"></i> Motif du paiement</h5>
                        <div class="form-group">
                            <textarea id="valPaymentMotif" name="payment_motif" rows="2" placeholder="Ex: Frais d'adhésion au programme de financement..." required></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Notes administratives (interne)</h5>
                        <div class="form-group">
                            <textarea id="valComments" name="admin_notes" rows="2" placeholder="Commentaires internes sur cette validation..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('validationModal')">Annuler</button>
                <button type="button" class="btn btn-success" onclick="submitValidation()">
                    <i class="fas fa-check"></i>
                    Confirmer la validation
                </button>
            </div>
        </div>
    </div>

    <!-- Approve Predefined Modal -->
    <div id="approvePredefinedModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('approvePredefinedModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>Approuver la demande prédéfinie</h4>
                <p>Confirmez l'approbation de cette demande de financement prédéfinie</p>
            </div>

            <div class="modal-body">
                <div class="request-summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">N° Demande</span>
                            <span class="value" id="preRequestNumber">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Client</span>
                            <span class="value" id="preClient">-</span>
                        </div>
                        <div class="summary-item highlight">
                            <span class="label">Montant</span>
                            <span class="value" id="preAmount">-</span>
                        </div>
                    </div>
                </div>

                <form id="approvePredefinedForm" method="POST" class="validation-form">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-calculator"></i> Montant approuvé</h5>
                        <div class="form-group">
                            <label for="preApprovedAmount">Montant final approuvé *</label>
                            <div class="input-unit">
                                <input type="number" step="1000" id="preApprovedAmount" name="amount_approved" required min="1000">
                                <span class="unit">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Notes d'approbation</h5>
                        <div class="form-group">
                            <textarea id="preNotes" name="admin_notes" rows="3" placeholder="Commentaires sur cette approbation..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('approvePredefinedModal')">Annuler</button>
                <button type="button" class="btn btn-success" onclick="submitPredefinedApproval()">
                    <i class="fas fa-check"></i>
                    Confirmer l'approbation
                </button>
            </div>
        </div>
    </div>

    <!-- Check Documents Modal -->
    <div id="checkDocumentsModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('checkDocumentsModal')"></div>
        <div class="modal-box modal-lg">
            <div class="modal-header">
                <div class="header-icon info">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4>Vérification des documents et programmation</h4>
                <p>Vérifiez les documents et programmez le transfert</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Demande <strong id="docRequestNumber">-</strong> - Client: <strong id="docClient">-</strong></span>
                </div>

                <form id="checkDocumentsForm" method="POST" class="validation-form">
                    @csrf

                    <div class="form-section">
                        <h5><i class="fas fa-calculator"></i> Programmation du transfert</h5>
                        <div class="form-grid three-cols">
                            <div class="form-group">
                                <label for="totalRepayment">Montant total remboursement *</label>
                                <div class="input-unit">
                                    <input type="number" step="1000" id="totalRepayment" name="total_repayment_amount" required min="1000">
                                    <span class="unit">FCFA</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="repaymentDuration">Durée remboursement *</label>
                                <div class="input-unit">
                                    <input type="number" id="repaymentDuration" name="repayment_duration_months" required min="1" max="60" value="12">
                                    <span class="unit">mois</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="repaymentStartDate">Date début remboursement *</label>
                                <input type="date" id="repaymentStartDate" name="repayment_start_date" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-tasks"></i> Vérification des documents</h5>
                        <div class="checkbox-group">
                            <label class="checkbox-item">
                                <input type="checkbox" id="docChecked" name="documents_checked" value="1" required>
                                <span class="checkmark"></span>
                                <span class="label">J'ai vérifié tous les documents fournis</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-comment-alt"></i> Notes finales</h5>
                        <div class="form-group">
                            <textarea id="docNotes" name="final_notes" rows="3" placeholder="Commentaires sur cette vérification..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5><i class="fas fa-envelope"></i> Notification</h5>
                        <label class="checkbox-item">
                            <input type="checkbox" name="notify_client" value="1" checked>
                            <span class="checkmark"></span>
                            <span class="label">Envoyer une notification au client par email</span>
                        </label>
                        <div class="client-email-info">
                            <i class="fas fa-envelope"></i>
                            Email du client: <strong id="docClientEmail">-</strong>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('checkDocumentsModal')">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitDocumentCheck()">
                    <i class="fas fa-paper-plane"></i>
                    Programmer le transfert
                </button>
            </div>
        </div>
    </div>

    <!-- Verify Payment Modal -->
    <div id="verifyPaymentModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('verifyPaymentModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon success">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4>Vérifier le paiement Kkiapay</h4>
                <p>Confirmez la réception du paiement et passez à la vérification des documents</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Vérifiez que le montant payé correspond au montant attendu avant de confirmer.</span>
                </div>

                <div class="request-summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">N° Demande</span>
                            <span class="value" id="verifyRequestNumber">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Client</span>
                            <span class="value" id="verifyClient">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Montant payé</span>
                            <span class="value text-success" id="verifyPaidAmount">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Montant attendu</span>
                            <span class="value" id="verifyExpectedAmount">-</span>
                        </div>
                    </div>
                </div>

                <form id="verifyPaymentForm" method="POST" class="validation-form">
                    @csrf
                    <input type="hidden" name="confirm_verify" value="1">

                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Notes de vérification</h5>
                        <div class="form-group">
                            <textarea id="verifyNotes" name="verification_notes" rows="3" placeholder="Commentaires sur cette vérification de paiement..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="checkbox-item confirm-check">
                            <input type="checkbox" id="confirmVerify" name="confirm_verify_check" value="1" required>
                            <span class="checkmark"></span>
                            <span class="label">Je confirme avoir vérifié ce paiement Kkiapay</span>
                        </label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('verifyPaymentModal')">Annuler</button>
                <button type="button" class="btn btn-success" onclick="submitVerifyPayment()">
                    <i class="fas fa-check-circle"></i>
                    Confirmer la vérification
                </button>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div id="completeModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('completeModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon success">
                    <i class="fas fa-trophy"></i>
                </div>
                <h4>Marquer comme complétée</h4>
                <p>Finalisez cette demande de financement</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Cette action marquera la demande comme terminée. Le client sera notifié.</span>
                </div>

                <div class="request-summary-card">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">N° Demande</span>
                            <span class="value" id="compRequestNumber">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Client</span>
                            <span class="value" id="compClient">-</span>
                        </div>
                    </div>
                </div>

                <form id="completeForm" method="POST" class="validation-form">
                    @csrf
                    <div class="form-section">
                        <h5><i class="fas fa-comment-alt"></i> Commentaires de finalisation</h5>
                        <div class="form-group">
                            <textarea id="compNotes" name="completion_notes" rows="3" placeholder="Commentaires sur la finalisation de cette demande..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')">Annuler</button>
                <button type="button" class="btn btn-success" onclick="submitCompletion()">
                    <i class="fas fa-check-double"></i>
                    Confirmer la finalisation
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal (Universal) -->
    <div id="rejectModal" class="modal-wrapper">
        <div class="modal-overlay" onclick="closeModal('rejectModal')"></div>
        <div class="modal-box">
            <div class="modal-header">
                <div class="header-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h4>Rejeter la demande</h4>
                <p id="rejectSubtitle">Le motif sera communiqué au client par notification</p>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Cette action est irréversible. Le client sera notifié.</span>
                </div>

                <form id="rejectForm" method="POST" class="reject-form">
                    @csrf
                    <div class="form-group">
                        <label for="rejReason">Motif du rejet *</label>
                        <textarea id="rejReason" name="rejection_reason" rows="4" required minlength="10" placeholder="Expliquez clairement pourquoi cette demande est rejetée..."></textarea>
                        <span class="help">Minimum 10 caractères. Ce message sera visible par le client.</span>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-times"></i>
                    Confirmer le rejet
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-stack"></div>

</div>

<style>
/* [Le CSS reste identique - conservé tel quel] */
.funding-management-wrapper {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --success: #10b981;
    --success-dark: #059669;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --purple: #8b5cf6;

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
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
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

.quick-action-card.info::before {
    background: var(--info);
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

.quick-action-card.info .action-icon {
    background: #cffafe;
    color: #0891b2;
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
    margin-bottom: 2rem;
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
    color: var(--primary);
    font-size: 1.5rem;
}

.header-content p {
    color: var(--gray-500);
    font-size: 1rem;
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 1.5rem;
}

.quick-stat {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    text-align: center;
    transition: all 0.3s ease;
}

.quick-stat.highlight {
    border-color: var(--warning);
    background: linear-gradient(135deg, #fffbeb 0%, white 100%);
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
    color: var(--primary);
    font-feature-settings: "tnum";
}

.quick-stat .stat-value.text-warning {
    color: var(--warning);
}

.quick-stat .stat-value small {
    font-size: 0.875rem;
    color: var(--gray-500);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-category {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.stat-category.dimmed {
    opacity: 0.6;
}

.stat-category.custom {
    border-top: 4px solid var(--purple);
}

.stat-category.predefined {
    border-top: 4px solid var(--success);
}

.category-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-100);
}

.category-header i {
    width: 40px;
    height: 40px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.stat-category.custom .category-header i {
    background: #f3e8ff;
    color: var(--purple);
}

.stat-category.predefined .category-header i {
    background: #d1fae5;
    color: var(--success);
}

.category-header span {
    font-weight: 700;
    color: var(--gray-800);
    font-size: 1.125rem;
}

.category-total {
    margin-left: auto;
    background: var(--gray-100);
    color: var(--gray-600);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 700;
}

.category-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.mini-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.25rem;
    padding: 0.75rem;
    border-radius: var(--radius);
    transition: all 0.2s;
}

.mini-stat:hover {
    background: var(--gray-50);
}

.mini-stat.highlight {
    background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
    border: 1px solid var(--gray-200);
}

.mini-stat.warning {
    background: #fef3c7;
    border: 1px solid #fde68a;
}

.mini-stat.warning .number {
    color: #d97706;
}

.mini-stat.highlight .number {
    color: var(--primary);
    font-size: 1.25rem;
}

.mini-stat .number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--gray-900);
    font-feature-settings: "tnum";
}

.mini-stat .label {
    font-size: 0.75rem;
    color: var(--gray-500);
    font-weight: 500;
}

/* Control Panel */
.control-panel {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
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
    grid-template-columns: repeat(5, 1fr);
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

.input-affix .suffix, .select-affix .suffix {
    position: absolute;
    right: 1rem;
    color: var(--gray-400);
    pointer-events: none;
}

.clear-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--gray-400);
    transition: color 0.2s;
}

.clear-btn:hover {
    color: var(--danger);
}

.currency {
    font-weight: 600;
    color: var(--gray-500);
    font-size: 0.8125rem;
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-100);
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

.btn-ghost {
    background: transparent;
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
}

.btn-ghost:hover {
    background: var(--gray-50);
    color: var(--gray-800);
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
    color: var(--primary);
    font-size: 1.125rem;
}

.header-title h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
}

.badge-count {
    background: var(--primary);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
}

.header-tools {
    display: flex;
    gap: 0.75rem;
}

.tool-btn {
    position: relative;
    width: 36px;
    height: 36px;
    border-radius: var(--radius);
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.tool-btn:hover {
    background: var(--gray-50);
    color: var(--gray-700);
}

.tool-btn.warning {
    background: #fef3c7;
    border-color: #fde68a;
    color: #d97706;
}

.tool-btn.warning:hover {
    background: #fde68a;
}

.tool-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--danger);
    color: white;
    font-size: 0.625rem;
    font-weight: 700;
    padding: 0.125rem 0.375rem;
    border-radius: 9999px;
    border: 2px solid white;
}

/* Table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.requests-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 1600px;
}

.requests-table th {
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

.requests-table td {
    padding: 1.25rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: top;
}

.requests-table tbody tr {
    transition: all 0.2s;
}

.requests-table tbody tr:hover {
    background: var(--gray-50);
}

.requests-table tbody tr.type-predefined {
    border-left: 3px solid var(--success);
}

.requests-table tbody tr.type-custom {
    border-left: 3px solid var(--purple);
}

.requests-table tbody tr.transfer-pending {
    background: #fffbeb;
}

/* Type Cell */
.td-type {
    width: 10%;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius-sm);
    font-size: 0.8125rem;
    font-weight: 600;
    margin-bottom: 0.375rem;
}

.type-badge.predefined {
    background: #d1fae5;
    color: #065f46;
}

.type-badge.custom {
    background: #f3e8ff;
    color: #6b21a8;
}

.type-detail, .type-category {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.type-category {
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-weight: 600;
}

/* Request Cell */
.td-request {
    width: 18%;
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

.request-desc {
    font-size: 0.8125rem;
    color: var(--gray-500);
    line-height: 1.5;
}

.request-location {
    font-size: 0.75rem;
    color: var(--gray-400);
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* Client Cell */
.td-client {
    width: 14%;
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

/* Amount Cell */
.td-amount {
    width: 12%;
}

.amount-block {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.amount-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8125rem;
}

.amount-row .amount-label {
    color: var(--gray-500);
    font-weight: 500;
}

.amount-row .amount-value {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: var(--gray-800);
}

.amount-row.approved .amount-value {
    color: var(--success);
}

.amount-row.fee .amount-value {
    color: var(--warning);
}

.amount-row.paid .amount-value {
    color: var(--primary);
}

/* Status Cell */
.td-status {
    width: 10%;
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
    margin-bottom: 0.5rem;
}

.status-pill i {
    font-size: 0.625rem;
}

.status-draft { background: #f3f4f6; color: #374151; }
.status-submitted { background: #e0e7ff; color: #3730a3; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-review { background: #dbeafe; color: #1e40af; }
.status-validated { background: #fce7f3; color: #9d174d; }
.status-paid { background: #d1fae5; color: #065f46; }
.status-approved { background: #c7f2ff; color: #0066cc; }
.status-docs-validated { background: #e0e7ff; color: #3730a3; }
.status-transfer-pending { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.status-completed { background: #c7f2ff; color: #0066cc; }

.status-meta {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    color: var(--gray-400);
}

/* Transfer Cell */
.td-transfer {
    width: 12%;
}

.transfer-info {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
}

.transfer-info.pending {
    background: #fef3c7;
    border: 1px solid #fde68a;
}

.transfer-info.pending i {
    color: #d97706;
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.transfer-info.completed {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
}

.transfer-info.completed i {
    color: #059669;
}

.transfer-info.processing {
    background: #dbeafe;
    border: 1px solid #bfdbfe;
}

.transfer-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
}

.transfer-status {
    font-weight: 700;
    color: var(--gray-800);
}

.transfer-date {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.btn-execute-transfer {
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--success);
    color: white;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.2s;
}

.btn-execute-transfer:hover {
    background: var(--success-dark);
    transform: translateY(-1px);
}

.transfer-none, .transfer-na {
    color: var(--gray-400);
    font-size: 0.875rem;
}

/* Date Cell */
.td-date {
    width: 12%;
}

.date-block {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.date-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8125rem;
}

.date-row .date-label {
    color: var(--gray-500);
    font-weight: 500;
}

.date-row .date-value {
    color: var(--gray-700);
    font-weight: 600;
}

.date-row.highlight .date-value {
    color: var(--purple);
}

.date-row.success .date-value {
    color: var(--success);
}

.date-relative {
    color: var(--gray-400);
    font-size: 0.75rem;
}

/* Actions Cell */
.td-actions {
    width: 8%;
    text-align: right;
}

.action-dropdown {
    position: relative;
    display: inline-block;
}

.action-toggle {
    width: 36px;
    height: 36px;
    border-radius: var(--radius);
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
}

.action-toggle:hover {
    background: var(--gray-50);
    color: var(--gray-900);
    border-color: var(--gray-400);
}

.action-menu {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 0.5rem;
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--gray-200);
    min-width: 260px;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s;
}

.action-dropdown.active .action-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    background: none;
    width: 100%;
    cursor: pointer;
    text-align: left;
}

.action-item:hover {
    background: var(--gray-50);
    color: var(--gray-900);
}

.action-item.text-info {
    color: var(--info);
}

.action-item.text-info:hover {
    background: #ecfeff;
}

.action-item.text-success {
    color: var(--success);
}

.action-item.text-success:hover {
    background: #f0fdf4;
}

.action-item.text-success.highlight {
    background: #d1fae5;
    font-weight: 700;
}

.action-item.text-success.highlight:hover {
    background: #a7f3d0;
}

.action-item.text-danger {
    color: var(--danger);
}

.action-item.text-danger:hover {
    background: #fef2f2;
}

.action-item.text-warning {
    color: #d97706;
}

.action-item.text-warning:hover {
    background: #fef3c7;
}

.action-item.text-primary {
    color: var(--primary);
}

.action-item.text-primary:hover {
    background: #eff6ff;
}

.action-item.disabled {
    color: var(--gray-400);
    cursor: not-allowed;
}

.action-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 0.5rem 0;
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
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.header-icon.danger {
    background: #fee2e2;
    color: var(--danger);
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
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

.request-summary-card {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--gray-200);
}

.request-summary-card.highlight {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-color: #86efac;
}

.summary-grid {
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
}

.summary-item.highlight .value {
    color: var(--success);
    font-size: 1.125rem;
    font-family: 'Courier New', monospace;
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

.form-grid.two-cols {
    grid-template-columns: repeat(2, 1fr);
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

.alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.alert-info {
    background: #cffafe;
    color: #155e75;
    border: 1px solid #a5f3fc;
}

.modal-footer {
    padding: 1.25rem 2rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background: var(--gray-50);
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

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

/* Checkbox Group */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
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
}

.checkbox-item input[type="checkbox"]:checked + .checkmark {
    background: var(--primary);
    border-color: var(--primary);
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
}

/* Missing Documents List */
.missing-docs-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
}

.missing-docs-list .checkbox-item {
    padding: 0.5rem;
    border-radius: var(--radius);
    transition: background 0.2s;
}

.missing-docs-list .checkbox-item:hover {
    background: var(--gray-50);
}

.client-email-info {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background: var(--gray-100);
    border-radius: var(--radius);
    font-size: 0.875rem;
}

.client-email-info i {
    color: var(--primary);
    margin-right: 0.5rem;
}

.mt-3 {
    margin-top: 1rem;
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

.d-inline {
    display: inline;
}

/* Responsive */
@media (max-width: 1400px) {
    .filter-row {
        grid-template-columns: repeat(3, 1fr);
    }

    .category-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .filter-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .header-stats {
        flex-direction: column;
        gap: 0.75rem;
    }

    .quick-actions-bar {
        grid-template-columns: 1fr;
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

    .form-grid.two-cols,
    .form-grid.three-cols {
        grid-template-columns: 1fr;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .summary-item.highlight {
        margin: 0;
        border-radius: var(--radius-md);
        border-left: none;
        border-top: 3px solid var(--success);
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
// Dropdown toggle
function toggleDropdown(btn) {
    const dropdown = btn.closest('.action-dropdown');
    const isActive = dropdown.classList.contains('active');

    document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('active'));

    if (!isActive) {
        dropdown.classList.add('active');
    }
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.action-dropdown')) {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.remove('active'));
    }
});

// Modal functions - Execute Transfer
function openExecuteTransferModal(id, requestNumber, client, amount) {
    const modal = document.getElementById('executeTransferModal');
    const form = document.getElementById('executeTransferForm');

    document.getElementById('execRequestNumber').textContent = requestNumber;
    document.getElementById('execClient').textContent = client;
    document.getElementById('execAmount').textContent = amount;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/execute-transfer`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function submitExecuteTransfer() {
    const form = document.getElementById('executeTransferForm');
    const confirmCheck = document.getElementById('confirmTransfer').checked;

    if (!confirmCheck) {
        showToast('error', 'Erreur', 'Vous devez confirmer la validation du transfert');
        return;
    }

    form.submit();
}

// Modal functions - Cancel Transfer
function openCancelTransferModal(id, requestNumber) {
    const modal = document.getElementById('cancelTransferModal');
    const form = document.getElementById('cancelTransferForm');

    document.getElementById('cancelRequestNumber').textContent = requestNumber;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/cancel-transfer`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function submitCancelTransfer() {
    const form = document.getElementById('cancelTransferForm');
    const reason = document.getElementById('cancelReason').value;

    if (!reason || reason.length < 5) {
        showToast('error', 'Erreur', 'Le motif d\'annulation doit contenir au moins 5 caractères');
        return;
    }

    form.submit();
}

// Modal functions - Custom Validation
function openValidationModal(id, amount, client, requestNumber) {
    const modal = document.getElementById('validationModal');
    const form = document.getElementById('validationForm');

    document.getElementById('valRequestNumber').textContent = requestNumber;
    document.getElementById('valClient').textContent = client;
    document.getElementById('valAmount').textContent = amount;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/set-price`;

    const numericAmount = parseFloat(amount.replace(/[^\d]/g, ''));
    if (!isNaN(numericAmount)) {
        document.getElementById('valApprovedAmount').value = numericAmount;
        const suggestedFee = Math.max(5000, Math.round(numericAmount * 0.05 / 1000) * 1000);
        document.getElementById('valExpectedPayment').value = suggestedFee;
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Modal functions - Predefined Approval
function openApprovePredefinedModal(id, requestNumber, client, amount) {
    const modal = document.getElementById('approvePredefinedModal');
    const form = document.getElementById('approvePredefinedForm');

    document.getElementById('preRequestNumber').textContent = requestNumber;
    document.getElementById('preClient').textContent = client;
    document.getElementById('preAmount').textContent = amount;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/approve-predefined`;

    const numericAmount = parseFloat(amount.replace(/[^\d]/g, ''));
    if (!isNaN(numericAmount)) {
        document.getElementById('preApprovedAmount').value = numericAmount;
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openCheckDocumentsModal(id, requestNumber, client, clientEmail, amountApproved) {
    const modal = document.getElementById('checkDocumentsModal');
    const form = document.getElementById('checkDocumentsForm');

    document.getElementById('docRequestNumber').textContent = requestNumber;
    document.getElementById('docClient').textContent = client;
    document.getElementById('docClientEmail').textContent = clientEmail || 'Non disponible';

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/verify-and-schedule`;

    // Pré-remplir le montant de remboursement avec le montant approuvé + 20% par défaut
    if (amountApproved) {
        const suggestedRepayment = Math.round(amountApproved * 1.2 / 1000) * 1000;
        document.getElementById('totalRepayment').value = suggestedRepayment;
    }

    // Définir la date minimale à aujourd'hui
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('repaymentStartDate').min = today;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openVerifyPaymentModal(id, requestNumber, client, paidAmount, expectedAmount) {
    const modal = document.getElementById('verifyPaymentModal');
    const form = document.getElementById('verifyPaymentForm');

    document.getElementById('verifyRequestNumber').textContent = requestNumber;
    document.getElementById('verifyClient').textContent = client;
    document.getElementById('verifyPaidAmount').textContent = paidAmount;
    document.getElementById('verifyExpectedAmount').textContent = expectedAmount;

    form.reset();
    form.action = `{{ url('admin/funding') }}/payments/${id}/verify`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openCompleteModal(id, requestNumber, client) {
    const modal = document.getElementById('completeModal');
    const form = document.getElementById('completeForm');

    document.getElementById('compRequestNumber').textContent = requestNumber;
    document.getElementById('compClient').textContent = client;

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/complete`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Universal Reject Modal
let isPredefinedReject = false;

function openRejectModal(id, requestNumber, predefined = false) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const subtitle = document.getElementById('rejectSubtitle');

    isPredefinedReject = predefined;
    subtitle.textContent = predefined
        ? 'Le motif sera communiqué au client pour cette demande prédéfinie'
        : 'Le motif sera communiqué au client par notification';

    form.reset();
    form.action = `{{ url('admin/funding') }}/${id}/reject`;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
}

// Submit functions
function submitValidation() {
    const form = document.getElementById('validationForm');
    const amount = document.getElementById('valApprovedAmount').value;
    const fee = document.getElementById('valExpectedPayment').value;
    const duration = document.getElementById('valDuration').value;
    const motif = document.getElementById('valPaymentMotif').value;

    if (!amount || amount < 1000) {
        showToast('error', 'Erreur', 'Le montant approuvé doit être d\'au moins 1 000 FCFA');
        return;
    }
    if (!fee || fee < 0) {
        showToast('error', 'Erreur', 'Le montant à facturer est invalide');
        return;
    }
    if (!duration || duration < 6 || duration > 120) {
        showToast('error', 'Erreur', 'La durée doit être comprise entre 6 et 120 mois');
        return;
    }
    if (!motif || motif.length < 5) {
        showToast('error', 'Erreur', 'Le motif du paiement est requis');
        return;
    }

    form.submit();
}

function submitPredefinedApproval() {
    const form = document.getElementById('approvePredefinedForm');
    const amount = document.getElementById('preApprovedAmount').value;

    if (!amount || amount < 1000) {
        showToast('error', 'Erreur', 'Le montant approuvé doit être d\'au moins 1 000 FCFA');
        return;
    }

    form.submit();
}

function submitDocumentCheck() {
    const form = document.getElementById('checkDocumentsForm');
    const checked = document.getElementById('docChecked').checked;
    const totalRepayment = document.getElementById('totalRepayment').value;
    const duration = document.getElementById('repaymentDuration').value;
    const startDate = document.getElementById('repaymentStartDate').value;

    if (!checked) {
        showToast('error', 'Erreur', 'Vous devez confirmer avoir vérifié les documents');
        return;
    }

    if (!totalRepayment || totalRepayment < 1000) {
        showToast('error', 'Erreur', 'Le montant total de remboursement est requis');
        return;
    }

    if (!duration || duration < 1 || duration > 60) {
        showToast('error', 'Erreur', 'La durée de remboursement doit être comprise entre 1 et 60 mois');
        return;
    }

    if (!startDate) {
        showToast('error', 'Erreur', 'La date de début de remboursement est requise');
        return;
    }

    form.submit();
}

function submitVerifyPayment() {
    const form = document.getElementById('verifyPaymentForm');
    const confirmCheck = document.getElementById('confirmVerify').checked;

    if (!confirmCheck) {
        showToast('error', 'Erreur', 'Vous devez confirmer la vérification du paiement');
        return;
    }

    form.submit();
}

function submitCompletion() {
    const form = document.getElementById('completeForm');
    form.submit();
}

function submitRejection() {
    const form = document.getElementById('rejectForm');
    const reason = document.getElementById('rejReason').value;

    if (!reason || reason.length < 10) {
        showToast('error', 'Erreur', 'Le motif du rejet doit contenir au moins 10 caractères');
        return;
    }

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

// Utilities
function clearField(fieldId) {
    document.getElementById(fieldId).value = '';
    document.getElementById('filterForm').submit();
}

function refreshData() {
    const icon = event.currentTarget.querySelector('i');
    icon.classList.add('fa-spin');
    setTimeout(() => location.reload(), 500);
}

// Event listeners
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal('validationModal');
        closeModal('approvePredefinedModal');
        closeModal('checkDocumentsModal');
        closeModal('completeModal');
        closeModal('rejectModal');
        closeModal('executeTransferModal');
        closeModal('cancelTransferModal');
        closeModal('verifyPaymentModal');
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
