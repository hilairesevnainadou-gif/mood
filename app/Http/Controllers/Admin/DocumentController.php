<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DocumentController extends Controller
{
    /**
     * Cache duration in seconds
     */
    protected const CACHE_DURATION = 300; // 5 minutes

    /**
     * Index groupé par utilisateur - OPTIMISÉ
     */
    public function index(Request $request)
    {
        // Requête de base optimisée avec sélection minimale
        $query = User::select(['id', 'name', 'email', 'member_type', 'created_at'])
            ->with(['documents' => function($q) {
                $q->select([
                    'id', 'user_id', 'name', 'type', 'status',
                    'created_at', 'path', 'original_filename'
                ])
                ->orderBy('status', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit(50); // Limiter le nombre de documents par utilisateur
            }])
            ->whereHas('documents');

        // Filtres optimisés avec exists() au lieu de whereHas quand possible
        if ($request->filter === 'pending') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('documents')
                    ->whereColumn('documents.user_id', 'users.id')
                    ->where('status', 'pending');
            });
        } elseif ($request->filter === 'complete') {
            // Utiliser une sous-requête plus efficace
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('documents')
                    ->whereColumn('documents.user_id', 'users.id')
                    ->where('status', '!=', 'validated');
            });
        }

        // Pagination avec eager loading optimisé
        $users = $query->paginate(10);

        // Stats en cache pour éviter les calculs lourds à chaque requête
        $stats = Cache::remember('document_stats', self::CACHE_DURATION, function () {
            return [
                'total_users' => User::whereExists(function ($q) {
                    $q->select(DB::raw(1))->from('documents')
                        ->whereColumn('documents.user_id', 'users.id');
                })->count(),
                'pending' => Document::where('status', 'pending')->count(),
                'validated' => Document::where('status', 'validated')->count(),
                'rejected' => Document::where('status', 'rejected')->count(),
            ];
        });

        return view('admin.documents.index', compact('users', 'stats'));
    }

    /**
     * Show : documents d'un utilisateur spécifique - OPTIMISÉ
     */
    public function show($userId)
    {
        // Charger l'utilisateur avec seulement les champs nécessaires
        $user = User::select(['id', 'name', 'email', 'member_type'])->findOrFail($userId);

        // Requête optimisée avec index sur status et user_id
        $documents = Document::where('user_id', $userId)
            ->select([
                'id', 'user_id', 'name', 'type', 'category', 'status',
                'path', 'original_filename', 'size', 'mime_type',
                'validated_at', 'validated_by', 'rejection_reason',
                'expiry_date', 'is_expired', 'created_at'
            ])
            ->orderByRaw("FIELD(status, 'pending', 'validated', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.documents.show', compact('user', 'documents'));
    }

    /**
     * Validation individuelle - OPTIMISÉ avec transaction
     */
    public function validateDocument(Request $request, $id)
    {
        $document = Document::select(['id', 'status'])->findOrFail($id);

        if ($document->status === 'validated') {
            return back()->with('error', 'Document déjà validé.');
        }

        try {
            DB::transaction(function () use ($document) {
                $document->validateDocument(auth()->id());
            });

            // Invalider le cache des stats
            Cache::forget('document_stats');

            return back()->with('success', 'Document validé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la validation.');
        }
    }

    /**
     * Rejet individuel - OPTIMISÉ
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $document = Document::select(['id', 'status'])->findOrFail($id);

        try {
            DB::transaction(function () use ($document, $request) {
                $document->rejectDocument($request->reason, auth()->id());
            });

            Cache::forget('document_stats');
            return back()->with('success', 'Document rejeté.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du rejet.');
        }
    }

    /**
     * Validation en masse - OPTIMISÉ avec Chunking
     */
    public function bulkValidate(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|string',
        ]);

        $ids = array_filter(explode(',', $request->document_ids));

        if (empty($ids)) {
            return back()->with('error', 'Aucun document sélectionné.');
        }

        // Limiter le nombre de documents pour éviter timeout
        $ids = array_slice($ids, 0, 100);
        $count = 0;

        // Utilisation de chunk pour traitement par lots
        Document::whereIn('id', $ids)
            ->where('status', '!=', 'validated')
            ->select(['id', 'status'])
            ->chunkById(20, function ($documents) use (&$count) {
                foreach ($documents as $doc) {
                    try {
                        $doc->validateDocument(auth()->id());
                        $count++;
                    } catch (\Exception $e) {
                        \Log::error('Erreur validation document ' . $doc->id . ': ' . $e->getMessage());
                    }
                }
            });

        Cache::forget('document_stats');
        return back()->with('success', "{$count} document(s) validé(s).");
    }

    /**
     * Validation de tous les documents d'un utilisateur - OPTIMISÉ avec Job
     */
    public function validateUserDocuments($userId)
    {
        // Pour les gros volumes, utiliser un Job en arrière-plan
        $count = Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->count();

        if ($count > 20) {
            // Déléguer à un Job pour éviter timeout
            \App\Jobs\BulkValidateDocuments::dispatch($userId, auth()->id());
            return back()->with('success', "Validation de {$count} documents en cours (traitement en arrière-plan).");
        }

        // Traitement synchrone pour petits volumes
        $processed = 0;
        Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->select(['id', 'status'])
            ->chunkById(10, function ($documents) use (&$processed) {
                foreach ($documents as $doc) {
                    $doc->validateDocument(auth()->id());
                    $processed++;
                }
            });

        Cache::forget('document_stats');
        return back()->with('success', "{$processed} document(s) validé(s) pour cet utilisateur.");
    }

    /**
     * Remettre en attente - OPTIMISÉ
     */
    public function pending($id)
    {
        $document = Document::select(['id', 'status'])->findOrFail($id);
        $document->markAsPending();

        Cache::forget('document_stats');
        return back()->with('success', 'Document remis en attente.');
    }

    /**
     * Téléchargement - OPTIMISÉ avec vérification stream
     */
    public function download($id)
    {
        $document = Document::select(['id', 'path', 'original_filename'])->findOrFail($id);

        if (!$document->path || !Storage::disk('public')->exists($document->path)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        // Utiliser response()->stream() pour les gros fichiers
        return Storage::disk('public')->download(
            $document->path,
            $document->original_filename ?? basename($document->path)
        );
    }

    /**
     * Rafraîchir les stats (endpoint AJAX optionnel)
     */
    public function refreshStats()
    {
        Cache::forget('document_stats');
        return response()->json(['message' => 'Stats refreshed']);
    }
}
