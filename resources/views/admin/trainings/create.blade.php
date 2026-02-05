@extends('admin.layouts.app')

@section('title', 'Nouvelle formation')
@section('page-title', 'Nouvelle formation')
@section('page-subtitle', 'Créer un nouveau module de formation')

@section('content')
    <form method="POST" action="{{ route('admin.trainings.store') }}" class="admin-form-grid">
        @csrf
        <div>
            <label class="form-label">Titre</label>
            <input class="form-control" name="title" value="{{ old('title') }}" required>
        </div>
        <div>
            <label class="form-label">Niveau</label>
            <input class="form-control" name="level" value="{{ old('level') }}">
        </div>
        <div>
            <label class="form-label">Durée (minutes)</label>
            <input class="form-control" type="number" name="duration_minutes" value="{{ old('duration_minutes') }}">
        </div>
        <div>
            <label class="form-label">Activer</label>
            <select class="form-select" name="is_active">
                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="admin-form-full">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="4" name="description">{{ old('description') }}</textarea>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Créer</button>
            <a href="{{ route('admin.trainings.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
@endsection
