@extends('admin.layouts.app')

@section('title', 'Formations')
@section('page-title', 'Formations')
@section('page-subtitle', 'Gestion du catalogue de formation')

@section('content')
    <div class="admin-toolbar">
        <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Ajouter une formation
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Niveau</th>
                    <th>Durée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trainings as $training)
                    <tr>
                        <td>{{ $training->title }}</td>
                        <td>{{ $training->level ?? 'N/A' }}</td>
                        <td>{{ $training->duration ?? 'N/A' }}</td>
                        <td>{{ $training->is_active ? 'Active' : 'Inactive' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">Aucune formation enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $trainings->links() }}
@endsection
