@extends('layouts.app')

@section('title', 'Paiement Échoué')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-times text-danger fa-2x"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-3">Paiement Échoué</h2>
                    <p class="text-muted mb-4">Nous n'avons pas pu traiter votre paiement.</p>
                    
                    @if($transaction->metadata['failure_reason'] ?? false)
                        <div class="alert alert-danger mb-4">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            {{ $transaction->metadata['failure_reason'] }}
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('payment.retry', $transaction->id) }}" class="btn btn-danger btn-lg">
                            <i class="fa-solid fa-redo me-2"></i> Réessayer le paiement
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-home me-2"></i> Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection