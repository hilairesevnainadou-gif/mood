@extends('layouts.client')

@section('title', 'Centre de Support')

@section('content')
<div class="content-wrapper">
    {{-- Header Section --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="app-title mb-1">Centre de Support</h1>
            <p class="text-secondary">{{ $tickets->count() }} ticket{{ $tickets->count() > 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('client.support.create') }}" class="btn-primary">
            <i class="fas fa-plus me-2"></i> Nouveau ticket
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('client.support.index', ['status' => 'open']) }}" class="text-decoration-none">
                <div class="p-3 rounded bg-white shadow-sm border border-secondary-200">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--info-50); color: var(--info-600);">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div>
                            <span class="h4 mb-0 d-block">{{ $tickets->where('status', 'open')->count() }}</span>
                            <small class="text-secondary">Ouverts</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('client.support.index', ['status' => 'in_progress']) }}" class="text-decoration-none">
                <div class="p-3 rounded bg-white shadow-sm border border-secondary-200">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--warning-50); color: var(--warning-600);">
                            <i class="fas fa-sync-alt fa-lg"></i>
                        </div>
                        <div>
                            <span class="h4 mb-0 d-block">{{ $tickets->where('status', 'in_progress')->count() }}</span>
                            <small class="text-secondary">En cours</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('client.support.index', ['status' => 'resolved']) }}" class="text-decoration-none">
                <div class="p-3 rounded bg-white shadow-sm border border-secondary-200">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--success-50); color: var(--success-600);">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <span class="h4 mb-0 d-block">{{ $tickets->where('status', 'resolved')->count() }}</span>
                            <small class="text-secondary">Résolus</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('client.support.index', ['status' => 'closed']) }}" class="text-decoration-none">
                <div class="p-3 rounded bg-white shadow-sm border border-secondary-200">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--secondary-100); color: var(--secondary-600);">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>
                        <div>
                            <span class="h4 mb-0 d-block">{{ $tickets->where('status', 'closed')->count() }}</span>
                            <small class="text-secondary">Fermés</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Quick Categories --}}
    <div class="mb-4">
        <h6 class="section-title mb-3">Nouveau ticket par catégorie</h6>
        <div class="row g-2">
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'technical']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--info-50); color: var(--info-600);">
                            <i class="fas fa-cogs fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Technique</small>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'billing']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--success-50); color: var(--success-600);">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Facturation</small>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'account']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--primary-50); color: var(--primary-600);">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Compte</small>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'training']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--warning-50); color: var(--warning-600);">
                            <i class="fas fa-graduation-cap fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Formation</small>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'funding']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--error-50); color: var(--error-600);">
                            <i class="fas fa-money-bill-wave fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Financement</small>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-2">
                <a href="{{ route('client.support.create', ['category' => 'other']) }}" class="text-decoration-none">
                    <div class="p-3 rounded bg-white shadow-sm border border-secondary-200 text-center">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: var(--secondary-100); color: var(--secondary-600);">
                            <i class="fas fa-ellipsis-h fa-lg"></i>
                        </div>
                        <small class="d-block text-secondary">Autre</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('client.support.index') }}"
               class="btn {{ !request('status') ? 'btn-primary' : 'btn-secondary' }}">
                Tous <span class="badge-count ms-1">{{ $tickets->count() }}</span>
            </a>
            <a href="{{ route('client.support.index', ['status' => 'open']) }}"
               class="btn {{ request('status') == 'open' ? 'btn-primary' : 'btn-secondary' }}">
                <i class="fas fa-clock me-1"></i> Ouverts
            </a>
            <a href="{{ route('client.support.index', ['status' => 'in_progress']) }}"
               class="btn {{ request('status') == 'in_progress' ? 'btn-primary' : 'btn-secondary' }}">
                <i class="fas fa-sync-alt me-1"></i> En cours
            </a>
            <a href="{{ route('client.support.index', ['status' => 'resolved']) }}"
               class="btn {{ request('status') == 'resolved' ? 'btn-primary' : 'btn-secondary' }}">
                <i class="fas fa-check-circle me-1"></i> Résolus
            </a>
            <a href="{{ route('client.support.index', ['status' => 'closed']) }}"
               class="btn {{ request('status') == 'closed' ? 'btn-primary' : 'btn-secondary' }}">
                <i class="fas fa-times-circle me-1"></i> Fermés
            </a>
        </div>
    </div>

    {{-- Tickets List --}}
    <div class="d-flex flex-column gap-3">
        @forelse($tickets as $ticket)
        <div class="p-3 rounded bg-white shadow-sm border border-secondary-200">
            <a href="{{ route('client.support.show', $ticket->id) }}" class="text-decoration-none text-dark">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-secondary">{{ $ticket->ticket_number }}</span>
                    <div class="d-flex gap-2">
                        {!! $ticket->status_badge !!}
                        @if($ticket->hasUnreadMessages(Auth::id()))
                            <span class="badge bg-danger">Nouveau</span>
                        @endif
                    </div>
                </div>
                <h5 class="mb-2">{{ $ticket->subject }}</h5>
                <p class="text-secondary mb-3">{{ Str::limit($ticket->description, 100) }}</p>
                <div class="d-flex flex-wrap gap-3 text-secondary small">
                    <span><i class="fas fa-tag me-1"></i> {{ $ticket->category_label }}</span>
                    <span>{!! $ticket->priority_badge !!}</span>
                    <span><i class="fas fa-clock me-1"></i> {{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </a>
        </div>
        @empty
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-ticket-alt fa-3x text-secondary-400"></i>
            </div>
            <h4>Aucun ticket</h4>
            <p class="text-secondary mb-3">Créez votre premier ticket de support</p>
            <a href="{{ route('client.support.create') }}" class="btn-primary">
                <i class="fas fa-plus me-2"></i> Nouveau ticket
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
