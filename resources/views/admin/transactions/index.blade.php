@extends('admin.layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Suivi des opérations financières')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->reference ?? $transaction->transaction_id }}</td>
                        <td>{{ $transaction->type }}</td>
                        <td>{{ $transaction->formatted_total ?? number_format($transaction->total_amount ?? $transaction->amount, 0, ',', ' ') . ' XOF' }}</td>
                        <td>{{ $transaction->status }}</td>
                        <td>{{ optional($transaction->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($transaction->status !== 'completed')
                                <form method="POST" action="{{ route('admin.transactions.validate', $transaction->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">Valider</button>
                                </form>
                            @else
                                <span class="text-success">Confirmée</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Aucune transaction disponible.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
@endsection
