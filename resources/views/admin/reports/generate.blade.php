@extends('admin.layouts.app')

@section('title', 'Rapport généré')
@section('page-title', 'Rapport généré')
@section('page-subtitle', 'Synthèse des performances')

@section('content')
    <div class="admin-card">
        <h3>Rapport du {{ $report['generated_at']->format('d/m/Y H:i') }}</h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Utilisateurs : <strong>{{ $report['users'] }}</strong></li>
            <li class="list-group-item">Transactions : <strong>{{ $report['transactions'] }}</strong></li>
            <li class="list-group-item">Demandes de financement : <strong>{{ $report['funding_requests'] }}</strong></li>
            <li class="list-group-item">Tickets support ouverts : <strong>{{ $report['support_open'] }}</strong></li>
        </ul>
    </div>

    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary mt-3">Retour</a>
@endsection
