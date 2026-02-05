@extends('admin.layouts.app')

@section('title', 'Paiements à vérifier')
@section('page-title', 'Paiements à vérifier')
@section('page-subtitle', 'Validation des paiements clients')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Montant</th>
                    <th>Client</th>
                    <th>Méthode</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_number ?? $payment->reference }}</td>
                        <td>{{ $payment->formatted_amount }}</td>
                        <td>{{ $payment->fundingRequest?->user?->full_name ?? 'N/A' }}</td>
                        <td>{{ $payment->payment_method_label ?? $payment->payment_method }}</td>
                        <td>{{ $payment->status_label ?? $payment->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.funding.verify-payment', $payment->id) }}" class="admin-inline-form">
                                @csrf
                                <input type="hidden" name="action" value="validate">
                                <button class="btn btn-sm btn-success" type="submit">Valider</button>
                            </form>
                            <form method="POST" action="{{ route('admin.funding.verify-payment', $payment->id) }}" class="admin-inline-form">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Aucun paiement en attente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
