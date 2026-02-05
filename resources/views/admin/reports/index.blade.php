@extends('admin.layouts.app')

@section('title', 'Rapports')
@section('page-title', 'Rapports')
@section('page-subtitle', 'Indicateurs de performance')

@section('content')
    <div class="admin-grid">
        <div class="admin-card">
            <h3>Utilisateurs</h3>
            <p class="admin-metric">{{ $report['users'] }}</p>
        </div>
        <div class="admin-card">
            <h3>Transactions</h3>
            <p class="admin-metric">{{ $report['transactions'] }}</p>
        </div>
        <div class="admin-card">
            <h3>Financements</h3>
            <p class="admin-metric">{{ $report['funding_requests'] }}</p>
        </div>
        <div class="admin-card">
            <h3>Tickets ouverts</h3>
            <p class="admin-metric">{{ $report['support_open'] }}</p>
        </div>
    </div>

    <a href="{{ route('admin.reports.generate') }}" class="btn btn-primary mt-3">
        Générer un rapport
    </a>
@endsection
