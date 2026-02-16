@extends('admin.layouts.app')

@section('title', 'Profil utilisateur - ' . $user->full_name)
@section('page-title', 'Profil utilisateur')
@section('page-subtitle', $user->full_name)

@section('content')
    <!-- Header avec actions rapides -->
    <div class="profile-header">
        <div class="profile-identity">
            <div class="profile-avatar-large">
                @if($user->profile_photo)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->full_name }}">
                @else
                    <span class="avatar-initials-large">{{ $user->initials }}</span>
                @endif
                <span class="status-badge-large {{ $user->is_active ? 'online' : 'offline' }}"></span>
            </div>
            <div class="profile-info">
                <h1>{{ $user->full_name }}</h1>
                <div class="profile-meta">
                    <span class="member-id"><i class="fa-solid fa-id-card"></i> {{ $user->member_id ?? 'ID: ' . $user->id }}</span>
                    <span class="separator">•</span>
                    <span class="member-type">{{ $user->member_type ?? 'Particulier' }}</span>
                    <span class="separator">•</span>
                    <span class="join-date">Inscrit le {{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="profile-badges">
                    @if($user->is_verified)
                        <span class="badge-verified"><i class="fa-solid fa-check-circle"></i> Email vérifié</span>
                    @endif
                    @if($user->is_active)
                        <span class="badge-active"><i class="fa-solid fa-circle-check"></i> Compte actif</span>
                    @else
                        <span class="badge-inactive"><i class="fa-solid fa-circle-xmark"></i> Compte inactif</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="profile-actions">
            <a href="mailto:{{ $user->email }}" class="btn-action-outline">
                <i class="fa-solid fa-envelope"></i> Contacter
            </a>
            @if($user->is_active)
                <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}" class="inline-form" onsubmit="return confirm('Désactiver cet utilisateur ?')">
                    @csrf
                    <button type="submit" class="btn-action-danger">
                        <i class="fa-solid fa-user-slash"></i> Désactiver
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" class="inline-form" onsubmit="return confirm('Activer cet utilisateur ?')">
                    @csrf
                    <button type="submit" class="btn-action-success">
                        <i class="fa-solid fa-user-check"></i> Activer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="profile-stats-grid">
        <div class="stat-card-profile">
            <div class="stat-icon-profile wallet">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="stat-content-profile">
                <span class="stat-value-profile">{{ number_format($stats['wallet_balance'] ?? 0, 0, ',', ' ') }} XOF</span>
                <span class="stat-label-profile">Solde wallet</span>
            </div>
        </div>
        <div class="stat-card-profile">
            <div class="stat-icon-profile funding">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div class="stat-content-profile">
                <span class="stat-value-profile">{{ $stats['funding_count'] ?? 0 }}</span>
                <span class="stat-label-profile">Demandes financement</span>
            </div>
        </div>
        <div class="stat-card-profile">
            <div class="stat-icon-profile documents">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="stat-content-profile">
                <span class="stat-value-profile">{{ $stats['documents_validated'] ?? 0 }}/{{ ($stats['documents_validated'] ?? 0) + ($stats['documents_pending'] ?? 0) }}</span>
                <span class="stat-label-profile">Documents validés</span>
            </div>
        </div>
        <div class="stat-card-profile">
            <div class="stat-icon-profile transactions">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div class="stat-content-profile">
                <span class="stat-value-profile">{{ $stats['total_transactions'] ?? 0 }}</span>
                <span class="stat-label-profile">Transactions</span>
            </div>
        </div>
    </div>

    <div class="profile-content-grid">
        <!-- Colonne gauche : Informations -->
        <div class="profile-column">
            <!-- Informations personnelles -->
            <div class="profile-card">
                <div class="card-header-profile">
                    <h3><i class="fa-solid fa-user"></i> Informations personnelles</h3>
                </div>
                <div class="card-body-profile">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nom complet</span>
                            <span class="info-value">{{ $user->full_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Téléphone</span>
                            <span class="info-value">{{ $user->phone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date de naissance</span>
                            <span class="info-value">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Non renseignée' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Genre</span>
                            <span class="info-value">{{ $user->gender ? ucfirst($user->gender) : 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Adresse</span>
                            <span class="info-value">{{ $user->address ? $user->address . ', ' . $user->city : 'Non renseignée' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="profile-card">
                <div class="card-header-profile">
                    <h3><i class="fa-solid fa-briefcase"></i> Informations professionnelles</h3>
                </div>
                <div class="card-body-profile">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Entreprise</span>
                            <span class="info-value">{{ $user->company_name ?? 'Non renseignée' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Type d'entreprise</span>
                            <span class="info-value">{{ $user->company_type ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Secteur</span>
                            <span class="info-value">{{ $user->sector ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Poste</span>
                            <span class="info-value">{{ $user->job_title ?? 'Non renseigné' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Édition -->
        <div class="profile-column">
            <div class="profile-card">
                <div class="card-header-profile">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Modifier le profil</h3>
                </div>
                <div class="card-body-profile">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="profile-form">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type membre</label>
                                <select class="form-control" name="member_type">
                                    <option value="">Sélectionner...</option>
                                    <option value="particulier" {{ old('member_type', $user->member_type) == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                    <option value="entreprise" {{ old('member_type', $user->member_type) == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Statut membre</label>
                                <select class="form-control" name="member_status">
                                    <option value="">Sélectionner...</option>
                                    <option value="actif" {{ old('member_status', $user->member_status) == 'actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactif" {{ old('member_status', $user->member_status) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    <option value="en_attente" {{ old('member_status', $user->member_status) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-check-group">
                            <label class="form-check-modern">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                <span class="label-text">Compte actif</span>
                            </label>

                            <label class="form-check-modern">
                                <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                <span class="label-text">Email vérifié</span>
                            </label>
                        </div>

                        <div class="form-actions-modern">
                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn-cancel">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="profile-card">
                <div class="card-header-profile">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Activité récente</h3>
                </div>
                <div class="card-body-profile">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon login">
                                <i class="fa-solid fa-right-to-bracket"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Dernière connexion</span>
                                <span class="activity-time">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon register">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <span class="activity-text">Inscription</span>
                                <span class="activity-time">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Header profil */
    .profile-header {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .profile-identity {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-avatar-large {
        position: relative;
        width: 80px;
        height: 80px;
        flex-shrink: 0;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
    }

    .avatar-initials-large {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.75rem;
        font-weight: 600;
        border: 3px solid #e2e8f0;
    }

    .status-badge-large {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
    }

    .status-badge-large.online { background: #10b981; }
    .status-badge-large.offline { background: #94a3b8; }

    .profile-info h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px 0;
    }

    .profile-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-size: 0.9rem;
        flex-wrap: wrap;
    }

    .profile-meta i {
        color: #94a3b8;
    }

    .separator {
        color: #cbd5e1;
    }

    .profile-badges {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .badge-verified, .badge-active, .badge-inactive {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .badge-verified {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-active {
        background: #d1fae5;
        color: #059669;
    }

    .badge-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .profile-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-action-outline, .btn-action-danger, .btn-action-success {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
    }

    .btn-action-outline {
        background: #fff;
        color: #64748b;
        border: 2px solid #e2e8f0;
    }

    .btn-action-outline:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-action-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .btn-action-danger:hover {
        background: #dc2626;
        color: #fff;
    }

    .btn-action-success {
        background: #f0fdf4;
        color: #16a34a;
    }

    .btn-action-success:hover {
        background: #16a34a;
        color: #fff;
    }

    /* Stats */
    .profile-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card-profile {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .stat-icon-profile {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
    }

    .stat-icon-profile.wallet { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-icon-profile.funding { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon-profile.documents { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon-profile.transactions { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .stat-content-profile {
        display: flex;
        flex-direction: column;
    }

    .stat-value-profile {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label-profile {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* Layout colonnes */
    .profile-content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .profile-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Cards */
    .profile-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .card-header-profile {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-profile h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .card-header-profile i {
        color: #3b82f6;
    }

    .card-body-profile {
        padding: 24px;
    }

    /* Info grid */
    .info-grid {
        display: grid;
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.8rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 500;
    }

    /* Formulaire */
    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
    }

    .form-control {
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Checkbox modernes */
    .form-check-group {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .form-check-modern {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        position: relative;
    }

    .form-check-modern input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark {
        width: 24px;
        height: 24px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .form-check-modern input:checked + .checkmark {
        background: #3b82f6;
        border-color: #3b82f6;
    }

    .form-check-modern input:checked + .checkmark::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: #fff;
        font-size: 0.8rem;
    }

    .label-text {
        font-size: 0.95rem;
        color: #374151;
        user-select: none;
    }

    /* Actions formulaire */
    .form-actions-modern {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        padding: 12px 24px;
        background: #f3f4f6;
        color: #6b7280;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
        color: #374151;
    }

    /* Activité */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .activity-icon.login { background: #dbeafe; color: #1d4ed8; }
    .activity-icon.register { background: #d1fae5; color: #059669; }

    .activity-content {
        display: flex;
        flex-direction: column;
    }

    .activity-text {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 500;
    }

    .activity-time {
        font-size: 0.85rem;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .profile-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .profile-content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-identity {
            flex-direction: column;
        }

        .profile-meta {
            justify-content: center;
        }

        .profile-stats-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions-modern {
            flex-direction: column;
        }
    }
</style>
@endpush
