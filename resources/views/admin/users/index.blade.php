@extends('admin.layouts.app')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', 'Gérer les comptes clients et leurs accès')

@section('content')
    <!-- Header avec statistiques visuelles -->
    <div class="page-header-section">
        <div class="stats-cards">
            @php
                $totalUsers = $users->total() ?? 0;
            @endphp
            <div class="stat-card total" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" data-target="{{ $totalUsers }}">0</span>
                    <span class="stat-label">Total utilisateurs</span>
                </div>
                <div class="stat-trend">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <div class="stat-card active" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" data-target="{{ $activeCount }}">0</span>
                    <span class="stat-label">Actifs</span>
                </div>
                <div class="stat-percentage">{{ $totalUsers > 0 ? round(($activeCount / $totalUsers) * 100) : 0 }}%</div>
            </div>

            <div class="stat-card inactive" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" data-target="{{ $inactiveCount }}">0</span>
                    <span class="stat-label">Inactifs</span>
                </div>
                <div class="stat-percentage">{{ $totalUsers > 0 ? round(($inactiveCount / $totalUsers) * 100) : 0 }}%</div>
            </div>

            <div class="stat-card new" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-number" data-target="{{ $newThisMonth }}">0</span>
                    <span class="stat-label">Nouveaux ce mois</span>
                </div>
                <div class="stat-badge">{{ $totalUsers > 0 && $newThisMonth > 0 ? '+'.round(($newThisMonth / $totalUsers) * 100, 1) : '0%' }}</div>
            </div>
        </div>
    </div>

    <!-- Toolbar avancée -->
    <div class="toolbar-container" data-aos="fade-up" data-aos-delay="400">
        <div class="toolbar-main">
            <form method="GET" class="search-filter-form" id="searchForm">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text"
                           name="search"
                           class="search-input"
                           placeholder="Rechercher par nom, email, téléphone..."
                           value="{{ $search ?? '' }}"
                           autocomplete="off"
                           id="searchInput">
                    @if($search)
                        <button type="button" class="btn-clear" onclick="clearSearch()" title="Effacer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>

                <div class="filter-group">
                    <select name="status" class="filter-select" onchange="submitForm()">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>✅ Actifs</option>
                        <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>⛔ Inactifs</option>
                    </select>

                    <select name="sort" class="filter-select" onchange="submitForm()">
                        <option value="recent" {{ ($sort ?? 'recent') == 'recent' ? 'selected' : '' }}>🕐 Plus récents</option>
                        <option value="name" {{ ($sort ?? '') == 'name' ? 'selected' : '' }}>📅 Nom A-Z</option>
                        <option value="name_desc" {{ ($sort ?? '') == 'name_desc' ? 'selected' : '' }}>📅 Nom Z-A</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary btn-submit">
                    <i class="fa-solid fa-filter"></i>
                    <span>Appliquer</span>
                </button>
            </form>

            <div class="toolbar-actions">
                <a href="{{ route('admin.users.export') }}?{{ http_build_query(request()->except('page')) }}"
                   class="btn-secondary btn-export"
                   title="Exporter en CSV">
                    <i class="fa-solid fa-file-csv"></i>
                    <span>Exporter</span>
                </a>
            </div>
        </div>

        @if($search || $status)
            <div class="active-filters">
                <span class="filters-label">Filtres actifs :</span>
                @if($search)
                    <span class="filter-tag">
                        Recherche: "{{ $search }}"
                        <button onclick="clearSearch()"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                @if($status)
                    <span class="filter-tag">
                        Statut: {{ $status == 'active' ? 'Actifs' : 'Inactifs' }}
                        <button onclick="clearStatus()"><i class="fa-solid fa-xmark"></i></button>
                    </span>
                @endif
                <button class="btn-clear-all" onclick="clearAllFilters()">
                    <i class="fa-solid fa-trash-can"></i> Tout effacer
                </button>
            </div>
        @endif
    </div>

    <!-- Table des utilisateurs -->
    <div class="data-card" data-aos="fade-up" data-aos-delay="500">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="th-user">
                            <div class="th-content">
                                <i class="fa-solid fa-user"></i>
                                <span>Utilisateur</span>
                            </div>
                        </th>
                        <th class="th-contact">
                            <div class="th-content">
                                <i class="fa-solid fa-address-card"></i>
                                <span>Contact</span>
                            </div>
                        </th>
                        <th class="th-status">
                            <div class="th-content">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Statut</span>
                            </div>
                        </th>
                        <th class="th-date">
                            <div class="th-content">
                                <i class="fa-solid fa-calendar"></i>
                                <span>Inscription</span>
                            </div>
                        </th>
                        <th class="th-actions">
                            <div class="th-content">
                                <i class="fa-solid fa-gear"></i>
                                <span>Actions</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="user-row" data-user-id="{{ $user->id }}">
                            <td class="td-user">
                                <div class="user-cell">
                                    <div class="avatar-wrapper">
                                        @if($user->profile_photo)
                                            <img src="{{ $user->profile_photo_url }}"
                                                 alt="{{ $user->full_name }}"
                                                 class="user-avatar-img">
                                        @else
                                            <div class="avatar-placeholder" style="background: {{ 'hsl(' . (crc32($user->email) % 360) . ', 70%, 50%)' }}">
                                                {{ $user->initials }}
                                            </div>
                                        @endif
                                        <span class="status-indicator {{ $user->is_active ? 'active' : 'inactive' }}"
                                              title="{{ $user->is_active ? 'Actif' : 'Inactif' }}"></span>
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name">{{ $user->full_name }}</span>
                                        <span class="user-meta">ID: {{ $user->member_id ?? $user->id }} • {{ $user->member_type ?? 'Particulier' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="td-contact">
                                <div class="contact-stack">
                                    <a href="mailto:{{ $user->email }}" class="contact-item email">
                                        <i class="fa-solid fa-envelope"></i>
                                        <span>{{ $user->email }}</span>
                                    </a>
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="contact-item phone">
                                            <i class="fa-solid fa-phone"></i>
                                            <span>{{ $user->phone }}</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="td-status">
                                <span class="status-pill {{ $user->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                                @if($user->is_verified)
                                    <span class="verified-badge" title="Compte vérifié">
                                        <i class="fa-solid fa-badge-check"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="td-date">
                                <div class="date-stack">
                                    <span class="date-primary">{{ $user->created_at->format('d/m/Y') }}</span>
                                    <span class="date-secondary">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="td-actions">
                                <div class="action-group">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="btn-icon btn-view"
                                       title="Voir le profil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if($user->is_active)
                                        <button type="button"
                                                class="btn-icon btn-deactivate"
                                                title="Désactiver le compte"
                                                onclick="openDeactivateModal('{{ $user->id }}', '{{ $user->full_name }}')">
                                            <i class="fa-solid fa-user-slash"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn-icon btn-activate"
                                                title="Activer le compte"
                                                onclick="openActivateModal('{{ $user->id }}', '{{ $user->full_name }}')">
                                            <i class="fa-solid fa-user-check"></i>
                                        </button>
                                    @endif

                                    <div class="dropdown">
                                        <button class="btn-icon btn-more" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                    <i class="fa-solid fa-eye text-primary"></i>
                                                    Voir le profil complet
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="mailto:{{ $user->email }}">
                                                    <i class="fa-solid fa-envelope text-info"></i>
                                                    Envoyer un email
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                @if($user->is_active)
                                                    <button type="button" class="dropdown-item text-warning" onclick="openDeactivateModal('{{ $user->id }}', '{{ $user->full_name }}')">
                                                        <i class="fa-solid fa-user-slash"></i>
                                                        Désactiver le compte
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item text-success" onclick="openActivateModal('{{ $user->id }}', '{{ $user->full_name }}')">
                                                        <i class="fa-solid fa-user-check"></i>
                                                        Activer le compte
                                                    </button>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state-cell">
                                <div class="empty-state">
                                    <div class="empty-illustration">
                                        <i class="fa-solid fa-users-viewfinder"></i>
                                    </div>
                                    <h3>Aucun utilisateur trouvé</h3>
                                    <p>Essayez de modifier vos critères de recherche ou de filtrage</p>
                                    @if($search || $status)
                                        <button class="btn-reset-filters" onclick="clearAllFilters()">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            Réinitialiser les filtres
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination moderne -->
        @if($users->hasPages())
            <div class="pagination-footer">
                <div class="pagination-info">
                    Affichage de <strong>{{ $users->firstItem() }}</strong> à <strong>{{ $users->lastItem() }}</strong>
                    sur <strong>{{ $users->total() }}</strong> utilisateurs
                </div>
                <div class="pagination-nav">
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de désactivation -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon warning">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>
                    <h5 class="modal-title">Désactiver le compte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir désactiver le compte de <strong id="deactivateUserName"></strong> ?</p>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>L'utilisateur ne pourra plus se connecter jusqu'à réactivation.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="" id="deactivateForm" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fa-solid fa-user-slash"></i>
                            Désactiver
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'activation -->
    <div class="modal fade" id="activateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon success">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <h5 class="modal-title">Activer le compte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir activer le compte de <strong id="activateUserName"></strong> ?</p>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>L'utilisateur pourra à nouveau accéder à la plateforme.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="" id="activateForm" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-user-check"></i>
                            Activer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    :root {
        --color-primary: #3b82f6;
        --color-success: #10b981;
        --color-warning: #f59e0b;
        --color-danger: #ef4444;
        --color-gray-50: #f8fafc;
        --color-gray-100: #f1f5f9;
        --color-gray-200: #e2e8f0;
        --color-gray-300: #cbd5e1;
        --color-gray-400: #94a3b8;
        --color-gray-500: #64748b;
        --color-gray-600: #475569;
        --color-gray-700: #334155;
        --color-gray-800: #1e293b;
        --color-gray-900: #0f172a;
    }

    /* Header Section */
    .page-header-section {
        margin-bottom: 24px;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
        border: 1px solid var(--color-gray-200);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.total::before { background: var(--color-primary); }
    .stat-card.active::before { background: var(--color-success); }
    .stat-card.inactive::before { background: var(--color-gray-400); }
    .stat-card.new::before { background: var(--color-warning); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-card.total .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-card.active .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card.inactive .stat-icon { background: linear-gradient(135deg, #64748b, #475569); }
    .stat-card.new .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        display: block;
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--color-gray-900);
        line-height: 1;
        font-feature-settings: "tnum";
        font-variant-numeric: tabular-nums;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--color-gray-500);
        font-weight: 500;
        margin-top: 4px;
    }

    .stat-percentage, .stat-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 0.875rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .stat-card.active .stat-percentage { background: #d1fae5; color: #059669; }
    .stat-card.inactive .stat-percentage { background: #f1f5f9; color: #64748b; }
    .stat-card.new .stat-badge { background: #fef3c7; color: #d97706; }

    .stat-trend {
        position: absolute;
        top: 20px;
        right: 20px;
        color: var(--color-gray-400);
        font-size: 1.25rem;
    }

    /* Toolbar */
    .toolbar-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--color-gray-200);
    }

    .toolbar-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-box > i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-gray-400);
        font-size: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 12px 44px 12px 48px;
        border: 2px solid var(--color-gray-200);
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: var(--color-gray-50);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .btn-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: var(--color-gray-200);
        color: var(--color-gray-600);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-clear:hover {
        background: var(--color-gray-300);
        color: var(--color-gray-800);
    }

    .filter-group {
        display: flex;
        gap: 8px;
    }

    .filter-select {
        padding: 12px 40px 12px 16px;
        border: 2px solid var(--color-gray-200);
        border-radius: 12px;
        font-size: 0.9rem;
        background: var(--color-gray-50) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 14px center;
        appearance: none;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 160px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--color-primary);
        background-color: white;
    }

    .btn-primary, .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary {
        background: var(--color-gray-100);
        color: var(--color-gray-700);
        border: 2px solid var(--color-gray-200);
    }

    .btn-secondary:hover {
        background: var(--color-gray-200);
        border-color: var(--color-gray-300);
    }

    .toolbar-actions {
        display: flex;
        gap: 12px;
    }

    /* Active Filters */
    .active-filters {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--color-gray-200);
        flex-wrap: wrap;
    }

    .filters-label {
        font-size: 0.875rem;
        color: var(--color-gray-500);
        font-weight: 500;
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: #eff6ff;
        color: var(--color-primary);
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .filter-tag button {
        background: none;
        border: none;
        color: var(--color-primary);
        cursor: pointer;
        padding: 0;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .filter-tag button:hover {
        background: var(--color-primary);
        color: white;
    }

    .btn-clear-all {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: #fef2f2;
        color: var(--color-danger);
        border: none;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-clear-all:hover {
        background: #fee2e2;
    }

    /* Data Card */
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--color-gray-200);
        overflow: hidden;
    }

    .table-container {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.9rem;
    }

    .modern-table thead {
        background: var(--color-gray-50);
    }

    .modern-table th {
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: var(--color-gray-600);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--color-gray-200);
        white-space: nowrap;
    }

    .th-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .th-content i {
        color: var(--color-gray-400);
        font-size: 0.9rem;
    }

    .modern-table td {
        padding: 20px;
        border-bottom: 1px solid var(--color-gray-100);
        vertical-align: middle;
    }

    .user-row:hover td {
        background: var(--color-gray-50);
    }

    .user-row:last-child td {
        border-bottom: none;
    }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .user-avatar-img, .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--color-gray-200);
    }

    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
    }

    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 3px solid white;
    }

    .status-indicator.active { background: var(--color-success); }
    .status-indicator.inactive { background: var(--color-gray-400); }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-name {
        font-weight: 600;
        color: var(--color-gray-900);
        font-size: 1rem;
    }

    .user-meta {
        font-size: 0.875rem;
        color: var(--color-gray-500);
        font-family: 'SF Mono', monospace;
    }

    /* Contact Cell */
    .contact-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--color-gray-600);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s ease;
    }

    .contact-item:hover {
        color: var(--color-primary);
    }

    .contact-item i {
        color: var(--color-gray-400);
        font-size: 0.9rem;
        width: 16px;
        text-align: center;
    }

    .contact-item.email { font-weight: 500; }

    /* Status Cell */
    .td-status {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pill.active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pill.inactive {
        background: #f1f5f9;
        color: #475569;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-pill.active .status-dot { background: var(--color-success); }
    .status-pill.inactive .status-dot { background: var(--color-gray-400); }

    .verified-badge {
        color: var(--color-primary);
        font-size: 1.25rem;
    }

    /* Date Cell */
    .date-stack {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .date-primary {
        font-weight: 600;
        color: var(--color-gray-700);
    }

    .date-secondary {
        font-size: 0.875rem;
        color: var(--color-gray-400);
    }

    /* Actions */
    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .btn-view {
        background: #eff6ff;
        color: var(--color-primary);
    }

    .btn-view:hover {
        background: var(--color-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-deactivate {
        background: #fef2f2;
        color: var(--color-danger);
    }

    .btn-deactivate:hover {
        background: var(--color-danger);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-activate {
        background: #f0fdf4;
        color: var(--color-success);
    }

    .btn-activate:hover {
        background: var(--color-success);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-more {
        background: var(--color-gray-100);
        color: var(--color-gray-600);
    }

    .btn-more:hover {
        background: var(--color-gray-200);
        color: var(--color-gray-900);
    }

    .form-action {
        display: inline;
        margin: 0;
    }

    /* Dropdown */
    .dropdown-menu {
        border: 1px solid var(--color-gray-200);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 8px;
        min-width: 220px;
    }

    .dropdown-item {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--color-gray-700);
        transition: all 0.2s ease;
        width: 100%;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: var(--color-gray-50);
        color: var(--color-gray-900);
    }

    .dropdown-item i {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-color: var(--color-gray-200);
    }

    /* Modals */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    .modal-header {
        border-bottom: 1px solid var(--color-gray-200);
        padding: 24px;
        position: relative;
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 16px;
    }

    .modal-icon.warning {
        background: #fef3c7;
        color: #d97706;
    }

    .modal-icon.success {
        background: #d1fae5;
        color: #059669;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-gray-900);
        margin: 0;
    }

    .btn-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        opacity: 1;
        background: var(--color-gray-100);
        transition: all 0.2s ease;
    }

    .btn-close:hover {
        background: var(--color-gray-200);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-body p {
        color: var(--color-gray-600);
        margin-bottom: 16px;
        font-size: 1rem;
    }

    .modal-body strong {
        color: var(--color-gray-900);
    }

    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border-radius: 12px;
        border: none;
        font-size: 0.9rem;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .alert i {
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .modal-footer {
        border-top: 1px solid var(--color-gray-200);
        padding: 20px 24px;
        gap: 12px;
    }

    .modal-footer .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .modal-footer .btn-secondary {
        background: var(--color-gray-100);
        color: var(--color-gray-700);
        border: none;
    }

    .modal-footer .btn-secondary:hover {
        background: var(--color-gray-200);
    }

    .modal-footer .btn-warning {
        background: var(--color-warning);
        color: white;
        border: none;
    }

    .modal-footer .btn-warning:hover {
        background: #d97706;
    }

    .modal-footer .btn-success {
        background: var(--color-success);
        color: white;
        border: none;
    }

    .modal-footer .btn-success:hover {
        background: #059669;
    }

    /* Empty State */
    .empty-state-cell {
        padding: 80px 20px;
        text-align: center;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .empty-illustration {
        width: 100px;
        height: 100px;
        background: var(--color-gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-gray-400);
        font-size: 2.5rem;
    }

    .empty-state h3 {
        color: var(--color-gray-900);
        font-weight: 700;
        margin: 0;
        font-size: 1.25rem;
    }

    .empty-state p {
        color: var(--color-gray-500);
        margin: 0;
        max-width: 400px;
    }

    .btn-reset-filters {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: white;
        border: 2px solid var(--color-gray-200);
        border-radius: 10px;
        color: var(--color-gray-700);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 8px;
    }

    .btn-reset-filters:hover {
        border-color: var(--color-primary);
        color: var(--color-primary);
        background: #eff6ff;
    }

    /* Pagination */
    .pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-top: 1px solid var(--color-gray-200);
        background: var(--color-gray-50);
    }

    .pagination-info {
        font-size: 0.9rem;
        color: var(--color-gray-600);
    }

    .pagination-info strong {
        color: var(--color-gray-900);
        font-weight: 600;
    }

    .pagination-nav {
        display: flex;
    }

    /* Responsive */
    @media (max-width: 1280px) {
        .stats-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .toolbar-main {
            flex-direction: column;
            align-items: stretch;
        }

        .search-filter-form {
            flex-direction: column;
        }

        .search-box {
            min-width: 100%;
        }

        .filter-group {
            width: 100%;
        }

        .filter-select {
            flex: 1;
        }

        .toolbar-actions {
            justify-content: flex-end;
        }

        .modern-table .th-contact,
        .modern-table .td-contact {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }

        .modern-table .th-date,
        .modern-table .td-date {
            display: none;
        }

        .pagination-footer {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }

        .action-group {
            gap: 4px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
        }
    }

    @media (max-width: 480px) {
        .modern-table .th-status,
        .modern-table .td-status {
            display: none;
        }

        .user-cell {
            gap: 12px;
        }

        .user-avatar-img, .avatar-placeholder {
            width: 40px;
            height: 40px;
        }

        .user-name {
            font-size: 0.9rem;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script>
    // Initialisation AOS
    AOS.init({
        duration: 600,
        once: true,
        offset: 30
    });

    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target')) || 0;
            const duration = 1500;
            const step = target / (duration / 16);
            let current = 0;

            const update = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('fr-FR');
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = target.toLocaleString('fr-FR');
                }
            };

            if (target > 0) update();
            else counter.textContent = '0';
        });
    }

    // Soumission du formulaire
    function submitForm() {
        document.getElementById('searchForm').submit();
    }

    // Effacer la recherche
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        submitForm();
    }

    // Effacer le statut
    function clearStatus() {
        document.querySelector('select[name="status"]').value = '';
        submitForm();
    }

    // Effacer tous les filtres
    function clearAllFilters() {
        window.location.href = '{{ route("admin.users.index") }}';
    }

    // Modal de désactivation
    function openDeactivateModal(userId, userName) {
        document.getElementById('deactivateUserName').textContent = userName;
        document.getElementById('deactivateForm').action = '{{ route("admin.users.deactivate", "") }}/' + userId;
        
        const modal = new bootstrap.Modal(document.getElementById('deactivateModal'));
        modal.show();
    }

    // Modal d'activation
    function openActivateModal(userId, userName) {
        document.getElementById('activateUserName').textContent = userName;
        document.getElementById('activateForm').action = '{{ route("admin.users.activate", "") }}/' + userId;
        
        const modal = new bootstrap.Modal(document.getElementById('activateModal'));
        modal.show();
    }

    // Recherche en temps réel (debounce)
    let searchTimeout;
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        if (e.target.value.length > 2 || e.target.value.length === 0) {
            searchTimeout = setTimeout(() => {
                submitForm();
            }, 600);
        }
    });

    // Animation au chargement
    document.addEventListener('DOMContentLoaded', () => {
        animateCounters();
    });
</script>
@endpush