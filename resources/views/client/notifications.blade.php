@extends('layouts.client')

@section('title', 'Notifications')

@section('content')
{{-- MODAL DE CONFIRMATION --}}
<div id="deleteModal" class="pwa-modal" style="display: none;">
    <div class="pwa-modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="pwa-modal-content">
        <div class="pwa-modal-header">
            <i class="fas fa-trash-alt text-danger"></i>
            <h3>Supprimer la notification</h3>
        </div>
        <div class="pwa-modal-body">
            <p>Êtes-vous sûr de vouloir supprimer cette notification ? Cette action est irréversible.</p>
        </div>
        <div class="pwa-modal-footer">
            <button type="button" class="pwa-btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <form id="deleteForm" method="POST" style="display: contents;">
                @csrf
                @method('DELETE')
                <button type="submit" class="pwa-btn-confirm btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(notificationId) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("client/notifications") }}/' + notificationId;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });

    function toggleNotifMenu(event, id) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('notifMenu' + id);
        const allMenus = document.querySelectorAll('.pwa-notif-actions-menu');

        allMenus.forEach(m => {
            if (m !== menu) m.classList.remove('show');
        });

        if (menu) menu.classList.toggle('show');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.pwa-notif-actions')) {
            document.querySelectorAll('.pwa-notif-actions-menu').forEach(m => m.classList.remove('show'));
        }
    });
</script>

<div class="pwa-notifications-page">
    {{-- Header --}}
    <div class="pwa-page-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <a href="{{ route('client.support') }}" class="pwa-back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pwa-header-text">
                <h1>Notifications</h1>
                <p>{{ $notifications->whereNull('read_at')->count() }} non lues</p>
            </div>
            @if($notifications->whereNull('read_at')->count() > 0)
                <form action="{{ route('client.notifications.read-all') }}" method="POST" style="display: contents;">
                    @csrf
                    <button type="submit" class="pwa-header-action" title="Tout marquer comme lu">
                        <i class="fas fa-check-double"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Liste des notifications --}}
    <div class="pwa-notifications-list">
        @forelse($notifications as $notification)
            <div class="pwa-notif-card {{ $notification->read_at ? 'read' : 'unread' }}" id="notif-{{ $notification->id }}">
                <div class="pwa-notif-icon {{ $notification->type ?? 'info' }}">
                    <i class="fas {{ $notification->icon ?? 'fa-bell' }}"></i>
                </div>

                <div class="pwa-notif-content">
                    <div class="pwa-notif-header">
                        <h4 class="pwa-notif-title">{{ $notification->title ?? 'Notification' }}</h4>
                        <span class="pwa-notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="pwa-notif-message">{{ $notification->message }}</p>

                    @if($notification->data && isset($notification->data['action_url']))
                        <a href="{{ $notification->data['action_url'] }}" class="pwa-notif-action-link">
                            Voir les détails <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="pwa-notif-actions">
                    <button class="pwa-notif-menu-btn" onclick="toggleNotifMenu(event, {{ $notification->id }})">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>

                    <div class="pwa-notif-actions-menu" id="notifMenu{{ $notification->id }}">
                        @if(!$notification->read_at)
                            <form action="{{ route('client.notifications.read', $notification->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="pwa-menu-item">
                                    <i class="fas fa-check"></i> Marquer comme lu
                                </button>
                            </form>
                        @endif
                        <button type="button" class="pwa-menu-item text-danger" onclick="confirmDelete({{ $notification->id }})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>

                @if(!$notification->read_at)
                    <div class="pwa-unread-indicator"></div>
                @endif
            </div>
        @empty
            <div class="pwa-empty-state">
                <div class="pwa-empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>Aucune notification</h3>
                <p>Vous n'avez pas encore de notifications. Revenez plus tard !</p>
                <a href="{{ route('client.support') }}" class="pwa-btn-primary">
                    <i class="fas fa-headset"></i> Voir mes tickets
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="pwa-pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<style>
    .pwa-notifications-page {
        padding: 0;
        max-width: 100%;
        background: var(--secondary-50, #f8fafc);
        min-height: 100vh;
        padding-bottom: 2rem;
    }

    /* Header */
    .pwa-page-header {
        background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%);
        padding: 1rem 1.25rem;
        padding-top: calc(1rem + env(safe-area-inset-top, 0px));
        margin: -1rem -1rem 1rem -1rem;
        position: relative;
        overflow: hidden;
        z-index: 10;
    }

    .pwa-header-bg {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
        background-size: 20px 20px;
        pointer-events: none;
    }

    .pwa-header-content {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
    }

    .pwa-back-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .pwa-back-btn:hover, .pwa-back-btn:active {
        background: rgba(255, 255, 255, 0.3);
    }

    .pwa-header-text {
        flex: 1;
        min-width: 0;
    }

    .pwa-header-text h1 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.125rem 0;
        font-family: 'Rajdhani', sans-serif;
    }

    .pwa-header-text p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.8rem;
    }

    .pwa-header-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.2s;
    }

    .pwa-header-action:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Liste notifications */
    .pwa-notifications-list {
        padding: 0 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .pwa-notif-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        padding: 1rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .pwa-notif-card.unread {
        background: linear-gradient(to right, white 97%, var(--primary-500, #3b82f6) 97%);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }

    .pwa-notif-card:active {
        transform: scale(0.98);
    }

    .pwa-unread-indicator {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 8px;
        height: 8px;
        background: var(--primary-500, #3b82f6);
        border-radius: 50%;
        box-shadow: 0 0 0 2px white;
    }

    .pwa-notif-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .pwa-notif-icon.info {
        background: #dbeafe;
        color: #2563eb;
    }

    .pwa-notif-icon.success {
        background: #d1fae5;
        color: #059669;
    }

    .pwa-notif-icon.warning {
        background: #fef3c7;
        color: #d97706;
    }

    .pwa-notif-icon.danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .pwa-notif-icon.support {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .pwa-notif-content {
        flex: 1;
        min-width: 0;
        padding-right: 1.5rem;
    }

    .pwa-notif-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .pwa-notif-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--secondary-800, #1e293b);
        margin: 0;
        line-height: 1.3;
    }

    .pwa-notif-card.read .pwa-notif-title {
        color: var(--secondary-600, #475569);
        font-weight: 500;
    }

    .pwa-notif-time {
        font-size: 0.75rem;
        color: var(--secondary-400, #94a3b8);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .pwa-notif-message {
        font-size: 0.85rem;
        color: var(--secondary-600, #475569);
        line-height: 1.4;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pwa-notif-card.read .pwa-notif-message {
        color: var(--secondary-400, #94a3b8);
    }

    .pwa-notif-action-link {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: var(--primary-600, #2563eb);
        text-decoration: none;
        font-weight: 500;
    }

    /* Actions menu */
    .pwa-notif-actions {
        position: relative;
    }

    .pwa-notif-menu-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: var(--secondary-400, #94a3b8);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .pwa-notif-menu-btn:hover {
        background: var(--secondary-100, #f1f5f9);
        color: var(--secondary-600, #475569);
    }

    .pwa-notif-actions-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        min-width: 180px;
        z-index: 100;
        overflow: hidden;
        margin-top: 0.25rem;
    }

    .pwa-notif-actions-menu.show {
        display: block;
        animation: slideDown 0.2s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pwa-menu-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.75rem 1rem;
        border: none;
        background: none;
        text-align: left;
        font-size: 0.9rem;
        color: var(--secondary-700, #334155);
        cursor: pointer;
        border-bottom: 1px solid var(--secondary-100, #f1f5f9);
        text-decoration: none;
    }

    .pwa-menu-item:hover {
        background: var(--secondary-50, #f8fafc);
    }

    .pwa-menu-item.text-danger {
        color: #dc2626;
    }

    .pwa-menu-item.text-danger:hover {
        background: #fef2f2;
    }

    .pwa-menu-item i {
        width: 20px;
        margin-right: 0.5rem;
    }

    /* Empty state */
    .pwa-empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--secondary-500, #64748b);
    }

    .pwa-empty-icon {
        width: 80px;
        height: 80px;
        background: var(--secondary-100, #f1f5f9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: var(--secondary-400, #94a3b8);
    }

    .pwa-empty-state h3 {
        font-size: 1.1rem;
        color: var(--secondary-800, #1e293b);
        margin: 0 0 0.5rem 0;
    }

    .pwa-empty-state p {
        font-size: 0.9rem;
        margin: 0 0 1.5rem 0;
        line-height: 1.5;
    }

    .pwa-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--primary-500, #3b82f6) 0%, var(--primary-600, #2563eb) 100%);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.2s;
    }

    .pwa-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    /* Pagination */
    .pwa-pagination {
        margin-top: 1.5rem;
        padding: 0 1rem;
        display: flex;
        justify-content: center;
    }

    .pwa-pagination nav {
        background: white;
        padding: 0.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    /* Modal (réutilisé du ticket) */
    .pwa-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .pwa-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease;
    }

    .pwa-modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 340px;
        position: relative;
        z-index: 1;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        overflow: hidden;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(50px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pwa-modal-header {
        background: linear-gradient(135deg, #fef2f2 0%, white 100%);
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid #fee2e2;
    }

    .pwa-modal-header i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .pwa-modal-header i.text-danger { color: #dc2626; }

    .pwa-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-800, #1e293b);
    }

    .pwa-modal-body {
        padding: 1.25rem;
        text-align: center;
    }

    .pwa-modal-body p {
        margin: 0;
        color: var(--secondary-600, #475569);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .pwa-modal-footer {
        padding: 1rem 1.25rem 1.25rem;
        display: flex;
        gap: 0.75rem;
    }

    .pwa-btn-secondary, .pwa-btn-confirm {
        flex: 1;
        padding: 0.875rem 1rem;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .pwa-btn-secondary {
        background: var(--secondary-100, #f1f5f9);
        color: var(--secondary-700, #334155);
    }

    .pwa-btn-confirm {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .pwa-btn-confirm:hover {
        transform: translateY(-1px);
    }

    /* Desktop */
    @media (min-width: 992px) {
        .pwa-notifications-page {
            max-width: 600px;
            margin: 0 auto;
        }

        .pwa-modal-content {
            max-width: 400px;
        }
    }
</style>
@endsection
