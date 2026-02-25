@extends('layouts.app')

@section('title', 'Paiement Réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-check text-success fa-2x"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-3">Paiement Réussi !</h2>
                    <p class="text-muted mb-4">Votre transaction a été traitée avec succès.</p>
                    
                    <div class="bg-light rounded p-4 mb-4 text-start">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Référence</div>
                            <div class="col-6 fw-bold text-end">{{ $transaction->transaction_id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Montant</div>
                            <div class="col-6 fw-bold text-end text-success">{{ $transaction->formatted_amount }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-muted">Date</div>
                            <div class="col-6 fw-bold text-end">{{ $transaction->completed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-home me-2"></i> Retour au tableau de bord
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-list me-2"></i> Voir mes transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection