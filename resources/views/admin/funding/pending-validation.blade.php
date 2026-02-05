@extends('admin.layouts.app')

@section('title', 'Validation des financements')
@section('page-title', 'Validation des financements')
@section('page-subtitle', 'Définir les montants pour les demandes personnalisées')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Demande</th>
                    <th>Client</th>
                    <th>Montant demandé</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->request_number ?? $request->title }}</td>
                        <td>{{ $request->user?->full_name ?? 'N/A' }}</td>
                        <td>{{ $request->formatted_amount_requested }}</td>
                        <td>{{ $request->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.funding.set-price', $request->id) }}" class="admin-inline-form">
                                @csrf
                                <input type="number" step="0.01" name="approved_amount" class="form-control" placeholder="Montant approuvé" required>
                                <input type="number" step="0.01" name="registration_fee" class="form-control" placeholder="Frais inscription" required>
                                <input type="number" name="duration" class="form-control" placeholder="Durée (mois)" required>
                                <input type="text" name="comments" class="form-control" placeholder="Notes">
                                <button class="btn btn-sm btn-success" type="submit">Valider</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">Aucune demande en attente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->links() }}
@endsection
