@extends('layouts.client')

@section('title', 'Paiement - ' . $request->request_number)

@section('content')
<div class="payment-pro">
    {{-- Header --}}
    <div class="pro-pay-header">
        <a href="{{ route('client.requests.show', $request->id) }}" class="pro-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Paiement</h1>
        <span class="pro-pay-amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
    </div>

    <div class="pro-pay-content">
        {{-- Carte Récapitulatif --}}
        <div class="pro-pay-card">
            <div class="pro-pay-motive">
                <span class="label">Motif de paiement</span>
                <div class="motive-code" onclick="copyMotif()">
                    <span id="motifText">{{ $payment->payment_motif }}</span>
                    <i class="fas fa-copy"></i>
                </div>
                <span class="help">Utilisez ce code lors du transfert</span>
            </div>

            <div class="pro-pay-divider"></div>

            <div class="pro-pay-detail">
                <span>Demande</span>
                <strong>{{ $request->request_number }}</strong>
            </div>
            <div class="pro-pay-detail">
                <span>Montant</span>
                <strong>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="pro-instructions">
            <h3>Comment payer ?</h3>
            <div class="pro-step-list">
                <div class="pro-instruction-step">
                    <div class="step-num">1</div>
                    <p>Effectuez un transfert du montant indiqué vers notre numéro de paiement</p>
                </div>
                <div class="pro-instruction-step">
                    <div class="step-num">2</div>
                    <p>Utilisez le <strong>motif de paiement</strong> affiché ci-dessus</p>
                </div>
                <div class="pro-instruction-step">
                    <div class="step-num">3</div>
                    <p>Confirmez votre paiement ci-dessous</p>
                </div>
            </div>
        </div>

        {{-- Formulaire Confirmation --}}
        <form action="{{ route('client.requests.payment.confirm', $request->id) }}" method="POST" class="pro-pay-form" id="payForm">
            @csrf

            <div class="pro-form-group">
                <label>Numéro utilisé pour le paiement</label>
                <div class="pro-input-wrap">
                    <span class="prefix">+225</span>
                    <input type="tel" name="phone_used" value="{{ old('phone_used', $user->phone) }}"
                           placeholder="07 XX XX XX XX" pattern="[0-9]{8,10}" required>
                </div>
            </div>

            <div class="pro-form-group">
                <label>Opérateur</label>
                <div class="pro-operators">
                    @foreach($operators as $operator)
                    <label class="pro-operator-card">
                        <input type="radio" name="operator_id" value="{{ $operator->id }}" required {{ $loop->first ? 'checked' : '' }}>
                        <div class="operator-content">
                            <div class="operator-logo operator-{{ strtolower($operator->code) }}">
                                <i class="fas fa-signal"></i>
                            </div>
                            <span class="operator-name">{{ $operator->name }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pro-form-group">
                <label>ID Transaction (optionnel)</label>
                <input type="text" name="transaction_id" placeholder="Ex: TX123456" class="pro-input">
            </div>

            <button type="submit" class="pro-btn-confirm" id="btnConfirm">
                <span>J'ai effectué le paiement</span>
                <i class="fas fa-check-circle"></i>
            </button>
        </form>

        {{-- Aide --}}
        <div class="pro-help">
            <i class="fas fa-shield-alt"></i>
            <span>Paiement sécurisé • Validation sous 5 min</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-pro { background: #f5f7fa; min-height: 100vh; padding-bottom: 2rem; }
.pro-pay-header {
    background: linear-gradient(135deg, #1b5a8d 0%, #0f3a5c 100%);
    padding: 1.5rem; padding-top: calc(1.5rem + env(safe-area-inset-top, 0px));
    color: white; display: flex; flex-direction: column; align-items: center;
    position: relative;
}
.pro-pay-header .pro-back {
    position: absolute; left: 1.25rem; top: calc(1.5rem + env(safe-area-inset-top, 0px));
    width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;
}
.pro-pay-header h1 { font-size: 1.1rem; font-weight: 600; margin: 0 0 0.5rem 0; opacity: 0.9; }
.pro-pay-amount { font-family: 'Rajdhani', sans-serif; font-size: 2.25rem; font-weight: 700; }

.pro-pay-content { padding: 0 1.25rem; margin-top: -2rem; position: relative; z-index: 10; }

.pro-pay-card {
    background: white; border-radius: 20px; padding: 1.75rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 1.5rem;
}
.pro-pay-motive { text-align: center; }
.pro-pay-motive .label { display: block; font-size: 0.9rem; color: #6b7280; margin-bottom: 0.75rem; }
.motive-code {
    display: inline-flex; align-items: center; gap: 1rem;
    background: #f0f9ff; border: 2px dashed #3b82f6;
    padding: 1rem 2rem; border-radius: 12px; cursor: pointer;
    transition: all 0.2s;
}
.motive-code:active { transform: scale(0.98); background: #e0f2fe; }
.motive-code span { font-family: 'Rajdhani', sans-serif; font-size: 2rem; font-weight: 700; color: #1e40af; letter-spacing: 4px; }
.motive-code i { color: #3b82f6; font-size: 1.25rem; }
.pro-pay-motive .help { display: block; margin-top: 0.75rem; font-size: 0.85rem; color: #6b7280; }

.pro-pay-divider { height: 1px; background: #e5e7eb; margin: 1.5rem 0; }
.pro-pay-detail { display: flex; justify-content: space-between; margin-bottom: 0.75rem; }
.pro-pay-detail span { color: #6b7280; font-size: 0.9rem; }
.pro-pay-detail strong { color: #1f2937; font-weight: 600; }

.pro-instructions { margin-bottom: 1.5rem; }
.pro-instructions h3 { font-size: 1rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem; padding-left: 0.5rem; }
.pro-step-list { display: flex; flex-direction: column; gap: 1rem; }
.pro-instruction-step { display: flex; align-items: flex-start; gap: 1rem; background: white; padding: 1.25rem; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.step-num {
    width: 28px; height: 28px; border-radius: 50%; background: #1b5a8d; color: white;
    display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0;
}
.pro-instruction-step p { margin: 0; color: #4b5563; font-size: 0.95rem; line-height: 1.5; }

.pro-pay-form { display: flex; flex-direction: column; gap: 1.25rem; }
.pro-form-group label { display: block; font-size: 0.9rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
.pro-input-wrap { display: flex; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.pro-input-wrap .prefix {
    background: #f3f4f6; padding: 1rem; color: #6b7280; font-weight: 600;
    display: flex; align-items: center; border-right: 1px solid #e5e7eb;
}
.pro-input-wrap input { flex: 1; border: none; padding: 1rem; font-size: 1rem; outline: none; }
.pro-input { width: 100%; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

.pro-operators { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.pro-operator-card { position: relative; cursor: pointer; }
.pro-operator-card input { position: absolute; opacity: 0; }
.operator-content {
    background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 1rem;
    display: flex; flex-direction: column; align-items: center; gap: 0.5rem; text-align: center;
    transition: all 0.2s;
}
.pro-operator-card input:checked + .operator-content { border-color: #1b5a8d; background: #f0f9ff; }
.operator-logo { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.operator-mtn { background: #ffcc00; color: #333; }
.operator-orange { background: #ff6600; color: white; }
.operator-moov { background: #d32f2f; color: white; }
.operator-name { font-size: 0.8rem; font-weight: 600; color: #374151; }

.pro-btn-confirm {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;
    border: none; border-radius: 14px; padding: 1.25rem; font-size: 1.1rem;
    font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.75rem;
    box-shadow: 0 4px 6px rgba(16,185,129,0.3); margin-top: 0.5rem;
    cursor: pointer; transition: all 0.2s;
}
.pro-btn-confirm:active { transform: translateY(2px); box-shadow: 0 2px 4px rgba(16,185,129,0.3); }

.pro-help { text-align: center; margin-top: 1.5rem; color: #6b7280; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.pro-help i { color: #10b981; }
</style>
@endpush

@push('scripts')
<script>
function copyMotif() {
    const motif = document.getElementById('motifText').textContent;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(motif).then(() => {
            if (window.toast) window.toast.success('Copié !', 'Motif copié dans le presse-papiers');
        });
    } else {
        // Fallback
        const el = document.createElement('textarea');
        el.value = motif;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        if (window.toast) window.toast.success('Copié !');
    }
}

document.getElementById('payForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnConfirm');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Vérification...';
    btn.disabled = true;
});
</script>
@endpush
