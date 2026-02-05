@extends('admin.layouts.app')

@section('title', 'Profil utilisateur')
@section('page-title', 'Profil utilisateur')
@section('page-subtitle', $user->full_name)

@section('content')
    <div class="admin-grid">
        <div class="admin-card">
            <h3>Informations</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Téléphone:</strong> {{ $user->phone ?? 'N/A' }}</p>
            <p><strong>Statut:</strong> {{ $user->is_active ? 'Actif' : 'Inactif' }}</p>
            <p><strong>Type:</strong> {{ $user->member_type ?? 'N/A' }}</p>
        </div>
        <div class="admin-card">
            <h3>Demandes</h3>
            <p><strong>Financements:</strong> {{ $user->fundingRequests->count() }}</p>
            <p><strong>Documents:</strong> {{ $user->documents->count() }}</p>
            <p><strong>Tickets support:</strong> {{ $user->supportTickets->count() }}</p>
        </div>
    </div>

    <div class="admin-section">
        <h2>Mettre à jour le profil</h2>
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="admin-form-grid">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label">Nom</label>
                <input class="form-control" name="name" value="{{ old('name', $user->name) }}">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input class="form-control" name="email" value="{{ old('email', $user->email) }}">
            </div>
            <div>
                <label class="form-label">Téléphone</label>
                <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}">
            </div>
            <div>
                <label class="form-label">Statut membre</label>
                <input class="form-control" name="member_status" value="{{ old('member_status', $user->member_status) }}">
            </div>
            <div>
                <label class="form-label">Type membre</label>
                <input class="form-control" name="member_type" value="{{ old('member_type', $user->member_type) }}">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Compte actif</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="is_verified" {{ $user->is_verified ? 'checked' : '' }}>
                <label class="form-check-label" for="is_verified">Email vérifié</label>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Enregistrer</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Retour</a>
            </div>
        </form>
    </div>
@endsection
