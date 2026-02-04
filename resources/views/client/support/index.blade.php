@extends('layouts.client')

@section('title', 'Centre de Support')

@section('content')
<div class="pwa-support-container">
    {{-- Header Mobile --}}
    <div class="pwa-page-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <div class="pwa-header-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="pwa-header-text">
                <h1>Centre de Support</h1>
                <p>{{ $tickets->count() }} ticket{{ $tickets->count() > 1 ? 's' : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Stats Scrollables --}}
    <div class="pwa-stats-scroll">
        <div class="pwa-stats-track">
            <a href="{{ route('client.support', ['status' => 'open']) }}" class="pwa-stat-pill open">
                <div class="pwa-stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $tickets->where('status', 'open')->count() }}</span>
                    <span class="pwa-stat-label">Ouverts</span>
                </div>
            </a>

            <a href="{{ route('client.support', ['status' => 'in_progress']) }}" class="pwa-stat-pill in-progress">
                <div class="pwa-stat-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $tickets->where('status', 'in_progress')->count() }}</span>
                    <span class="pwa-stat-label">En cours</span>
                </div>
            </a>

            <a href="{{ route('client.support', ['status' => 'resolved']) }}" class="pwa-stat-pill resolved">
                <div class="pwa-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $tickets->where('status', 'resolved')->count() }}</span>
                    <span class="pwa-stat-label">Résolus</span>
                </div>
            </a>

            <a href="{{ route('client.support', ['status' => 'closed']) }}" class="pwa-stat-pill closed">
                <div class="pwa-stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $tickets->where('status', 'closed')->count() }}</span>
                    <span class="pwa-stat-label">Fermés</span>
                </div>
            </a>
        </div>
    </div>

    {{-- Catégories Rapides --}}
    <div class="pwa-section">
        <h3 class="pwa-section-title">Nouveau ticket par catégorie</h3>
        <div class="pwa-categories-grid">
            <a href="{{ route('client.support.create', ['category' => 'technical']) }}" class="pwa-category-card">
                <div class="pwa-cat-icon" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>Technique</span>
            </a>
            <a href="{{ route('client.support.create', ['category' => 'billing']) }}" class="pwa-category-card">
                <div class="pwa-cat-icon" style="background: #d1fae5; color: #059669;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span>Facturation</span>
            </a>
            <a href="{{ route('client.support.create', ['category' => 'account']) }}" class="pwa-category-card">
                <div class="pwa-cat-icon" style="background: #dbeafe; color: #0284c7;">
                    <i class="fas fa-user-circle"></i>
                </div>
                <span>Compte</span>
            </a>
            <a href="{{ route('client.support.create', ['category' => 'training']) }}" class="pwa-category-card">
                <div class="pwa-cat-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>Formation</span>
            </a>
            <a href="{{ route('client.support.create', ['category' => 'funding']) }}" class="pwa-category-card">
                <div class="pwa-cat-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span>Financement</span>
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="pwa-filters-wrap">
        <div class="pwa-filters-scroll">
            <a href="{{ route('client.support') }}" class="pwa-filter-chip {{ !request('status') ? 'active' : '' }}">
                <span>Tous</span>
                <span class="pwa-filter-count">{{ $tickets->count() }}</span>
            </a>
            <a href="{{ route('client.support', ['status' => 'open']) }}" class="pwa-filter-chip {{ request('status') == 'open' ? 'active' : '' }}">
                <i class="fas fa-clock text-primary me-1"></i> Ouverts
            </a>
            <a href="{{ route('client.support', ['status' => 'in_progress']) }}" class="pwa-filter-chip {{ request('status') == 'in_progress' ? 'active' : '' }}">
                <i class="fas fa-sync-alt text-warning me-1"></i> En cours
            </a>
            <a href="{{ route('client.support', ['status' => 'resolved']) }}" class="pwa-filter-chip {{ request('status') == 'resolved' ? 'active' : '' }}">
                <i class="fas fa-check-circle text-success me-1"></i> Résolus
            </a>
            <a href="{{ route('client.support', ['status' => 'closed']) }}" class="pwa-filter-chip {{ request('status') == 'closed' ? 'active' : '' }}">
                <i class="fas fa-times-circle text-secondary me-1"></i> Fermés
            </a>
        </div>
    </div>

    {{-- Liste des Tickets --}}
    <div class="pwa-tickets-list">
        @forelse($tickets as $ticket)
        <div class="pwa-card pwa-ticket-card" data-status="{{ $ticket->status }}">
            <a href="{{ route('client.support.show', $ticket->id) }}" class="pwa-ticket-link">
                <div class="pwa-ticket-header">
                    <span class="pwa-ticket-number">#{{ $ticket->ticket_number }}</span>
                    <div class="pwa-ticket-badges">
                        {!! $ticket->status_badge !!}
                        @if($ticket->hasUnreadMessages(Auth::id()))
                            <span class="pwa-badge-new">Nouveau</span>
                        @endif
                    </div>
                </div>

                <h3 class="pwa-ticket-title">{{ $ticket->subject }}</h3>
                <p class="pwa-ticket-desc">{{ Str::limit($ticket->description, 100) }}</p>

                <div class="pwa-ticket-meta">
                    <span class="pwa-meta-item">
                        <i class="fas fa-tag"></i> {{ $ticket->category_label }}
                    </span>
                    <span class="pwa-meta-item">
                        {!! $ticket->priority_badge !!}
                    </span>
                    <span class="pwa-meta-item">
                        <i class="fas fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        </div>
        @empty
        <div class="pwa-empty-state">
            <div class="pwa-empty-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h3>Aucun ticket</h3>
            <p>Créez votre premier ticket de support</p>
            <a href="{{ route('client.support.create') }}" class="pwa-btn-primary">
                <i class="fas fa-plus me-2"></i> Nouveau ticket
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="pwa-pagination">
        {{ $tickets->links() }}
    </div>
    @endif
</div>

{{-- FAB Nouveau Ticket --}}
<a href="{{ route('client.support.create') }}" class="pwa-fab" aria-label="Nouveau ticket">
    <i class="fas fa-plus"></i>
</a>

<style>
/* Utilisation des variables du layout */
.pwa-support-container {
    padding: 0 0 2rem 0;
    max-width: 100%;
}

.pwa-page-header {
    background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin: -1rem -1rem 1rem -1rem;
    position: relative;
    overflow: hidden;
}

.pwa-header-bg {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
    background-size: 20px 20px;
}

.pwa-header-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}

.pwa-header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
}

.pwa-header-text h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    font-family: 'Rajdhani', sans-serif;
}

.pwa-header-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Stats */
.pwa-stats-scroll {
    margin: 0 -1rem 1.25rem -1rem;
    padding: 0 1rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.pwa-stats-scroll::-webkit-scrollbar {
    display: none;
}

.pwa-stats-track {
    display: flex;
    gap: 0.75rem;
    width: max-content;
}

.pwa-stat-pill {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.125rem;
    background: white;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--secondary-200, #e5e7eb);
    min-width: 140px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s;
}

.pwa-stat-pill:active {
    transform: scale(0.95);
}

.pwa-stat-pill.open .pwa-stat-icon { background: #dbeafe; color: #2563eb; }
.pwa-stat-pill.in-progress .pwa-stat-icon { background: #fef3c7; color: #d97706; }
.pwa-stat-pill.resolved .pwa-stat-icon { background: #d1fae5; color: #059669; }
.pwa-stat-pill.closed .pwa-stat-icon { background: #f3f4f6; color: #6b7280; }

.pwa-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.pwa-stat-num {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--secondary-800, #1f2937);
    line-height: 1;
    display: block;
}

.pwa-stat-label {
    font-size: 0.8rem;
    color: var(--secondary-500, #6b7280);
}

/* Section */
.pwa-section {
    padding: 0 1rem;
    margin-bottom: 1.25rem;
}

.pwa-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--secondary-800, #1f2937);
    margin: 0 0 0.75rem 0;
}

/* Catégories */
.pwa-categories-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
}

.pwa-category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 0.25rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--secondary-200, #e5e7eb);
    text-decoration: none;
    transition: all 0.2s;
}

.pwa-category-card:active {
    transform: scale(0.95);
    background: var(--secondary-50, #f8fafc);
}

.pwa-cat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.pwa-category-card span {
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--secondary-700, #374151);
    text-align: center;
}

/* Filtres */
.pwa-filters-wrap {
    padding: 0 1rem;
    margin-bottom: 1rem;
}

.pwa-filters-scroll {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.pwa-filters-scroll::-webkit-scrollbar {
    display: none;
}

.pwa-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.875rem;
    background: white;
    border: 1px solid var(--secondary-200, #e5e7eb);
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--secondary-600, #6b7280);
    white-space: nowrap;
    text-decoration: none;
    transition: all 0.2s;
}

.pwa-filter-chip.active {
    background: var(--primary-500, #1b5a8d);
    color: white;
    border-color: var(--primary-500, #1b5a8d);
    box-shadow: 0 4px 10px rgba(27, 90, 141, 0.25);
}

.pwa-filter-count {
    background: var(--secondary-100, #f3f4f6);
    color: var(--secondary-700, #374151);
    padding: 0.125rem 0.5rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.pwa-filter-chip.active .pwa-filter-count {
    background: rgba(255,255,255,0.3);
    color: white;
}

/* Tickets List */
.pwa-tickets-list {
    padding: 0 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.pwa-card {
    background: white;
    border-radius: 14px;
    border: 1px solid var(--secondary-200, #e5e7eb);
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    overflow: hidden;
    transition: transform 0.2s;
}

.pwa-card:active {
    transform: scale(0.98);
}

.pwa-ticket-link {
    display: block;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
}

.pwa-ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.pwa-ticket-number {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--secondary-500, #6b7280);
    background: var(--secondary-100, #f3f4f6);
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
}

.pwa-ticket-badges {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pwa-badge-new {
    background: var(--error-500, #ef4444);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

.pwa-ticket-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--secondary-800, #1f2937);
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
}

.pwa-ticket-desc {
    font-size: 0.875rem;
    color: var(--secondary-500, #6b7280);
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.pwa-ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    font-size: 0.8rem;
    color: var(--secondary-600, #6b7280);
}

.pwa-meta-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.pwa-meta-item i {
    font-size: 0.875rem;
    color: var(--secondary-400, #9ca3af);
}

/* Empty State */
.pwa-empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: white;
    border-radius: 14px;
    border: 2px dashed var(--secondary-300, #d1d5db);
    margin: 0 1rem;
}

.pwa-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    background: var(--secondary-100, #f3f4f6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--secondary-400, #9ca3af);
}

.pwa-empty-state h3 {
    color: var(--secondary-800, #1f2937);
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}

.pwa-empty-state p {
    color: var(--secondary-500, #6b7280);
    font-size: 0.875rem;
    margin-bottom: 1.25rem;
}

.pwa-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.25rem;
    background: var(--primary-500, #1b5a8d);
    color: white;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border: none;
    width: 100%;
    max-width: 280px;
}

.pwa-btn-primary:active {
    background: var(--primary-600, #164a77);
}

/* Pagination */
.pwa-pagination {
    padding: 1.5rem 1rem;
    display: flex;
    justify-content: center;
}

.pwa-pagination .pagination {
    gap: 0.5rem;
    margin: 0;
}

/* FAB */
.pwa-fab {
    position: fixed;
    bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px) + 60px);
    right: 1.25rem;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 15px rgba(27, 90, 141, 0.4);
    z-index: 999;
    text-decoration: none;
    transition: transform 0.2s;
}

.pwa-fab:active {
    transform: scale(0.95);
}

@media (max-width: 380px) {
    .pwa-categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 992px) {
    .pwa-support-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .pwa-fab {
        display: none;
    }
}
</style>
@endsection
