@extends('admin.layouts.app')

@section('title', 'Documents')
@section('page-title', 'Documents')
@section('page-subtitle', 'Validation des justificatifs')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td>{{ $document->name }}</td>
                        <td>{{ $document->user?->full_name ?? 'N/A' }}</td>
                        <td>{{ $document->type_label ?? $document->type }}</td>
                        <td>{{ $document->status_label ?? $document->status }}</td>
                        <td>{{ optional($document->created_at)->format('d/m/Y') }}</td>
                        <td>
                            @if ($document->status !== 'validated')
                                <form method="POST" action="{{ route('admin.documents.validate', $document->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">Valider</button>
                                </form>
                            @else
                                <span class="text-success">Validé</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Aucun document disponible.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $documents->links() }}
@endsection
