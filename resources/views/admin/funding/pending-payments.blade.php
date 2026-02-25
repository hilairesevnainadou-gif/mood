@extends('admin.layouts.app')

@section('title', 'Paiements en attente')
@section('page-title', 'Validation des paiements')
@section('page-subtitle', 'Vérifiez et confirmez les transactions Kkiapay')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--admin-text);">
                <i class="fas fa-shield-alt me-2" style="color: var(--admin-accent);"></i>
                Validation des paiements
            </h1>
            <p class="mb-0" style="color: var(--admin-text-muted);">Vérifiez et confirmez les transactions en attente de crédit</p>
        </div>
        <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2"
           style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux demandes</span>
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="admin-card d-flex align-items-center gap-3" style="padding: 20px; border-left: 4px solid var(--admin-warning);">
                <div style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clock" style="font-size: 1.5rem; color: var(--admin-warning);"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="font-size: 1.75rem; color: var(--admin-warning);">{{ $stats['total_pending'] ?? 0 }}</h3>
                    <span style="font-size: 0.8rem; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">En attente</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="admin-card d-flex align-items-center gap-3" style="padding: 20px; border-left: 4px solid var(--admin-accent);">
                <div style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-wallet" style="font-size: 1.5rem; color: var(--admin-accent);"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="font-size: 1.5rem; color: var(--admin-accent);">
                        {{ number_format($stats['total_amount_pending'] ?? 0, 0, ',', ' ') }} <small style="font-size: 0.75rem;">FCFA</small>
                    </h3>
                    <span style="font-size: 0.8rem; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Montant total</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Info --}}
    <div class="alert mb-4" role="alert" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: var(--admin-accent); border-radius: 12px;">
        <div class="d-flex align-items-start gap-3">
            <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-info-circle" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1" style="color: var(--admin-text);">Processus de vérification</h6>
                <p class="mb-0" style="color: var(--admin-text-muted);">Les demandes ci-dessous ont été payées via Kkiapay. Cliquez sur "Vérifier" pour confirmer le paiement.</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="admin-card mb-4" style="padding: 0; overflow: hidden; border-radius: 16px;">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.funding.pending-payments') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                        <i class="fas fa-search me-2" style="color: var(--admin-accent);"></i>Recherche
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius: 10px 0 0 10px; border-color: var(--admin-border); background: var(--admin-bg);">
                            <i class="fas fa-search" style="color: var(--admin-text-muted);"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="N° demande, transaction, client..." 
                               value="{{ request('search') }}"
                               style="border-radius: 0 10px 10px 0; border-color: var(--admin-border); padding: 12px 16px;">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">Date début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"
                           style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px;">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                           style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px;">
                </div>
                <div class="col-lg-4 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2"
                            style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 10px; padding: 12px 24px; font-weight: 500;">
                        <i class="fas fa-filter"></i>
                        <span>Filtrer</span>
                    </button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ route('admin.funding.pending-payments') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2"
                           style="border-radius: 10px; padding: 12px 24px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
                            <i class="fas fa-undo"></i>
                            <span>Réinitialiser</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-color: var(--admin-border) !important;">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">Transactions à valider</h5>
                <span class="badge" style="background: var(--admin-accent); color: #fff; font-size: 0.75rem; padding: 6px 12px; border-radius: 20px;">
                    {{ $payments->total() }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size: 0.9rem;">
                <thead style="background: var(--admin-bg);">
                    <tr style="color: var(--admin-text-muted); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th class="ps-4 py-3">Demande</th>
                        <th class="py-3">Client</th>
                        <th class="py-3">Montants</th>
                        <th class="py-3">Transaction Kkiapay</th>
                        <th class="py-3">Date</th>
                        <th class="text-end pe-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="background: #fff;">
                    @forelse($payments as $payment)
                        <tr style="border-bottom: 1px solid var(--admin-border); transition: all 0.2s ease;">
                            {{-- Request Info --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex flex-column gap-2">
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: var(--admin-accent); border-radius: 6px; padding: 4px 8px; font-family: monospace; font-size: 0.8rem; width: fit-content;">
                                        {{ $payment->request_number }}
                                    </span>
                                    <span class="fw-semibold" style="color: var(--admin-text); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $payment->title }}">
                                        {{ Str::limit($payment->title, 30) }}
                                    </span>
                                    <span class="badge" style="background: {{ $payment->is_predefined ? 'rgba(16, 185, 129, 0.1)' : 'rgba(139, 92, 246, 0.1)' }}; color: {{ $payment->is_predefined ? 'var(--admin-success)' : '#8b5cf6' }}; border-radius: 20px; padding: 4px 10px; font-size: 0.7rem; width: fit-content;">
                                        <i class="fas {{ $payment->is_predefined ? 'fa-box' : 'fa-pen-nib' }} me-1" style="font-size: 0.6rem;"></i>
                                        {{ $payment->is_predefined ? 'Prédéfinie' : 'Personnalisée' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Client --}}
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $email = $payment->user?->email ?? 'default@example.com';
                                        $hue = crc32($email) % 360;
                                        $initials = strtoupper(substr($payment->user?->first_name ?? 'N', 0, 1) . substr($payment->user?->last_name ?? 'A', 0, 1));
                                    @endphp
                                    <div style="width: 40px; height: 40px; background: hsl({{ $hue }}, 70%, 45%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                    <div class="d-flex flex-column" style="min-width: 0;">
                                        <span class="fw-semibold" style="color: var(--admin-text);">{{ $payment->user?->full_name ?? 'N/A' }}</span>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">{{ $payment->user?->email ?? '' }}</small>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">{{ $payment->user?->phone ?? '' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Amounts --}}
                            <td class="py-3">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                        <span style="color: var(--admin-text-muted);">Approuvé:</span>
                                        <span class="fw-semibold font-monospace" style="color: var(--admin-accent);">{{ number_format($payment->amount_approved ?? $payment->amount_requested, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                        <span style="color: var(--admin-text-muted);">Attendu:</span>
                                        <span class="fw-semibold font-monospace" style="color: var(--admin-warning);">{{ number_format($payment->expected_payment ?? 0, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="p-2 rounded-2" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                            <span style="color: var(--admin-success); font-weight: 600;">Payé:</span>
                                            <span class="fw-bold font-monospace" style="color: var(--admin-success);">{{ number_format($payment->kkiapay_amount_paid, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>
                                    @if(($payment->kkiapay_amount_paid ?? 0) != ($payment->expected_payment ?? 0))
                                        <div class="p-2 rounded-2" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                                            <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                                <span style="color: var(--admin-warning); font-weight: 600;">Écart:</span>
                                                <span class="fw-bold font-monospace" style="color: var(--admin-warning);">
                                                    {{ number_format(($payment->kkiapay_amount_paid ?? 0) - ($payment->expected_payment ?? 0), 0, ',', ' ') }} FCFA
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Transaction Details --}}
                            <td class="py-3">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="color: var(--admin-text-muted); font-size: 0.8rem;">ID:</span>
                                        <code style="background: var(--admin-bg); padding: 4px 8px; border-radius: 6px; color: var(--admin-accent); font-family: monospace; font-size: 0.8rem; max-width: 100px; overflow: hidden; text-overflow: ellipsis;" title="{{ $payment->kkiapay_transaction_id }}">
                                            {{ Str::limit($payment->kkiapay_transaction_id, 12) }}
                                        </code>
                                        <button class="btn btn-link p-0" onclick="copyToClipboard('{{ $payment->kkiapay_transaction_id }}')" 
                                                style="color: var(--admin-text-muted); font-size: 0.9rem;" title="Copier">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                        <span style="color: var(--admin-text-muted);">Tél:</span>
                                        <span style="color: var(--admin-text);">{{ $payment->kkiapay_phone ?? 'N/A' }}</span>
                                    </div>
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: var(--admin-success); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; width: fit-content;">
                                        <i class="fas fa-check-circle me-1"></i>SUCCESS
                                    </span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="py-3">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center gap-2" style="color: var(--admin-success);">
                                        <i class="fas fa-calendar-check" style="font-size: 0.9rem;"></i>
                                        <span class="fw-semibold" style="font-size: 0.85rem;">{{ $payment->paid_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                    </div>
                                    <small style="color: var(--admin-text-muted); font-size: 0.8rem; font-style: italic;">{{ $payment->paid_at?->diffForHumans() ?? '' }}</small>
                                    @if($payment->validated_at)
                                        <small style="color: var(--admin-accent); font-size: 0.75rem;">
                                            <i class="fas fa-check-double me-1"></i>Validé le {{ $payment->validated_at->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="pe-4 py-3">
                                <div class="d-flex flex-column gap-2 align-items-end">
                                    @if(!$payment->validated_at)
                                        <button class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" 
                                                style="min-width: 140px; border-radius: 8px; padding: 8px 16px; font-size: 0.85rem; font-weight: 500; background: var(--admin-success); border: none;"
                                                onclick="openVerifyModal({{ $payment->id }}, '{{ $payment->request_number }}', '{{ $payment->user?->full_name ?? 'N/A' }}', '{{ number_format($payment->kkiapay_amount_paid, 0, ',', ' ') }} FCFA', '{{ $payment->kkiapay_transaction_id }}')">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Vérifier</span>
                                        </button>
                                    @else
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: var(--admin-accent); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 20px; padding: 6px 12px; font-size: 0.8rem;">
                                            <i class="fas fa-check-double me-1"></i>Déjà validé
                                        </span>
                                    @endif

                                    <div class="d-flex gap-2 w-100">
                                        <a href="{{ route('admin.funding.show-request', $payment->id) }}" 
                                           class="btn btn-outline-primary flex-fill d-flex align-items-center justify-content-center gap-1"
                                           style="border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; border-color: var(--admin-border); color: var(--admin-accent);">
                                            <i class="fas fa-eye"></i>
                                            <span>Voir</span>
                                        </a>
                                        @if($payment->kkiapay_transaction_id)
                                            <a href="https://app.kkiapay.me/dashboard/transactions/details/{{ $payment->kkiapay_transaction_id }}"
                                               target="_blank"
                                               class="btn btn-dark flex-fill d-flex align-items-center justify-content-center gap-1"
                                               style="border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; background: #1e293b; border: none;"
                                               title="Voir sur Kkiapay">
                                                <i class="fas fa-external-link-alt"></i>
                                                <span>Kkiapay</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div style="padding: 2rem;">
                                    <div style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                        <i class="fas fa-check-double" style="font-size: 2rem; color: var(--admin-success);"></i>
                                    </div>
                                    <h5 style="color: var(--admin-text); font-weight: 600; margin-bottom: 0.5rem;">Aucun paiement en attente</h5>
                                    <p style="color: var(--admin-text-muted); margin-bottom: 1.5rem;">Tous les paiements ont été vérifiés ou aucune demande n'a encore été payée.</p>
                                    <a href="{{ route('admin.funding.pending-validation') }}" class="btn btn-primary d-inline-flex align-items-center gap-2"
                                       style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 10px; padding: 12px 24px; font-weight: 500;">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Retour aux demandes</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-color: var(--admin-border) !important;">
                <small style="color: var(--admin-text-muted);">
                    Affichage de <strong style="color: var(--admin-text);">{{ $payments->firstItem() }}</strong> à <strong style="color: var(--admin-text);">{{ $payments->lastItem() }}</strong> sur <strong style="color: var(--admin-text);">{{ $payments->total() }}</strong> paiements
                </small>
                <div>
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Verification Modal --}}
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            <div class="modal-header border-bottom" style="background: var(--admin-bg); padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: var(--admin-success);"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="color: var(--admin-text);">Confirmer la vérification</h5>
                        <small style="color: var(--admin-text-muted);">Vérifiez les détails avant de continuer</small>
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close" style="filter: invert(0.3);"></button>
            </div>

            <div class="modal-body p-4">
                <div class="alert mb-4" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: var(--admin-warning); border-radius: 10px;">
                    <div class="d-flex gap-3">
                        <i class="fas fa-exclamation-triangle mt-1"></i>
                        <div>
                            <strong style="color: var(--admin-text);">Vérification requise</strong>
                            <p class="mb-0 small" style="color: var(--admin-text-muted);">Assurez-vous que la transaction existe dans votre dashboard Kkiapay avant confirmation.</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card mb-4" style="background: var(--admin-bg); padding: 20px; border-radius: 12px;">
                    <div class="row g-3">
                        <div class="col-6">
                            <small style="color: var(--admin-text-muted); display: block; margin-bottom: 4px;">Demande</small>
                            <strong style="color: var(--admin-text);" id="verifyRequestNumber">-</strong>
                        </div>
                        <div class="col-6">
                            <small style="color: var(--admin-text-muted); display: block; margin-bottom: 4px;">Client</small>
                            <strong style="color: var(--admin-text);" id="verifyClient">-</strong>
                        </div>
                        <div class="col-12">
                            <small style="color: var(--admin-text-muted); display: block; margin-bottom: 4px;">Transaction ID</small>
                            <code style="background: #fff; padding: 8px 12px; border-radius: 6px; color: var(--admin-accent); font-family: monospace; display: inline-block;" id="verifyTransaction">-</code>
                        </div>
                        <div class="col-12 pt-2" style="border-top: 1px solid var(--admin-border);">
                            <div class="d-flex justify-content-between align-items-center">
                                <small style="color: var(--admin-text-muted);">Montant payé</small>
                                <strong style="color: var(--admin-success); font-size: 1.5rem;" id="verifyAmount">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="verifyForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmVerify" name="confirm_verify" value="1" required
                                       style="width: 20px; height: 20px; border-color: var(--admin-success); cursor: pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="confirmVerify" style="color: var(--admin-text);">
                                    J'ai vérifié cette transaction sur Kkiapay
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                            Notes de vérification (optionnel)
                        </label>
                        <textarea class="form-control" id="verifyNotes" name="verification_notes" rows="3" placeholder="Commentaires..."
                                  style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; resize: vertical;"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top" style="background: var(--admin-bg); padding: 16px 24px;">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()"
                        style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
                    Annuler
                </button>
                <button type="button" class="btn btn-success d-flex align-items-center gap-2" onclick="submitVerify()"
                        style="background: var(--admin-success); border: none; border-radius: 10px; padding: 10px 24px; font-weight: 500;">
                    <i class="fas fa-check-circle"></i>
                    <span>Confirmer et continuer</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;" id="toastContainer"></div>

@push('styles')
<style>
    /* Animations */
    .admin-card {
        animation: fadeIn 0.4s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Table responsive */
    @media (max-width: 1200px) {
        .table-responsive {
            border-radius: 0 0 16px 16px;
        }
    }
    
    @media (max-width: 768px) {
        .admin-card {
            padding: 16px !important;
        }
        
        td, th {
            padding: 12px 8px !important;
        }
        
        .badge {
            font-size: 0.7rem;
        }
    }
    
    /* Form check styling */
    .form-check-input:checked {
        background-color: var(--admin-success);
        border-color: var(--admin-success);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    
    /* Modal enhancements */
    .modal.show .modal-dialog {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Toast styling */
    .toast {
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--admin-shadow-lg);
        border: none;
    }
    
    .toast-success {
        border-left: 4px solid var(--admin-success);
    }
    
    .toast-error {
        border-left: 4px solid var(--admin-danger);
    }
    
    .toast-warning {
        border-left: 4px solid var(--admin-warning);
    }
</style>
@endpush

@push('scripts')
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
        const container = document.getElementById('toastContainer');
        const toastId = 'toast-' + Date.now();

        const colors = {
            success: 'var(--admin-success)',
            error: 'var(--admin-danger)',
            warning: 'var(--admin-warning)',
            info: 'var(--admin-accent)'
        };

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const toastHtml = `
            <div id="${toastId}" class="toast toast-${type} align-items-center mb-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-3 p-3">
                        <i class="fas ${icons[type]}" style="font-size: 1.25rem; color: ${colors[type]};"></i>
                        <div>
                            <strong style="color: var(--admin-text); display: block; margin-bottom: 2px;">${title}</strong>
                            <div style="color: var(--admin-text-muted); font-size: 0.9rem;">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-3 m-auto" data-bs-dismiss="toast" aria-label="Close" style="filter: invert(0.3);"></button>
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
        
        // Animation staggered sur les cartes
        const cards = document.querySelectorAll('.admin-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
@endsection