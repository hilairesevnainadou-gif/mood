@extends('admin.layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('page-subtitle', 'Configuration de la plateforme')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form-grid">
        @csrf
        @method('PUT')
        <div>
            <label class="form-label">Langue</label>
            <input class="form-control" name="language" value="{{ old('language', $settings->language) }}">
        </div>
        <div>
            <label class="form-label">Fuseau horaire</label>
            <input class="form-control" name="timezone" value="{{ old('timezone', $settings->timezone) }}">
        </div>
        <div>
            <label class="form-label">Devise</label>
            <input class="form-control" name="currency" value="{{ old('currency', $settings->currency) }}">
        </div>
        <div>
            <label class="form-label">Thème</label>
            <input class="form-control" name="theme" value="{{ old('theme', $settings->theme) }}">
        </div>
        <div>
            <label class="form-label">Lignes par page</label>
            <input class="form-control" type="number" name="rows_per_page" value="{{ old('rows_per_page', $settings->rows_per_page) }}">
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="notification_email" value="1" id="notification_email" {{ $settings->notification_email ? 'checked' : '' }}>
            <label class="form-check-label" for="notification_email">Notifications email</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="notification_sms" value="1" id="notification_sms" {{ $settings->notification_sms ? 'checked' : '' }}>
            <label class="form-check-label" for="notification_sms">Notifications SMS</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="notification_push" value="1" id="notification_push" {{ $settings->notification_push ? 'checked' : '' }}>
            <label class="form-check-label" for="notification_push">Notifications push</label>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
@endsection
