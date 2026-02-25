<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Document;
use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        $query = User::select(['id', 'name', 'email', 'member_type', 'is_verified', 'created_at'])
            ->with(['documents' => function($q) {
                $q->select([
                    'id', 'user_id', 'name', 'type', 'status',
                    'created_at', 'path', 'original_filename'
                ])
                ->orderBy('status', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit(50);
            }])
            ->whereHas('documents');

        // Filtres optimisés
        if ($request->filter === 'pending') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('documents')
                    ->whereColumn('documents.user_id', 'users.id')
                    ->where('status', 'pending');
            });
        } elseif ($request->filter === 'complete') {
            $query->where('is_verified', true);
        } elseif ($request->filter === 'unverified') {
            $query->where('is_verified', false)
                  ->whereExists(function ($subQuery) {
                      $subQuery->select(DB::raw(1))
                          ->from('documents')
                          ->whereColumn('documents.user_id', 'users.id');
                  });
        }

        $users = $query->paginate(10);

        // Stats en cache
        $stats = Cache::remember('document_stats', self::CACHE_DURATION, function () {
            return [
                'total_users' => User::whereExists(function ($q) {
                    $q->select(DB::raw(1))->from('documents')
                        ->whereColumn('documents.user_id', 'users.id');
                })->count(),
                'pending' => Document::where('status', 'pending')->count(),
                'validated' => Document::where('status', 'validated')->count(),
                'rejected' => Document::where('status', 'rejected')->count(),
                'verified_users' => User::where('is_verified', true)->count(),
            ];
        });

        return view('admin.documents.index', compact('users', 'stats'));
    }

    /**
     * Show : documents d'un utilisateur spécifique
     */
    public function show($userId)
    {
        $user = User::select(['id', 'name', 'email', 'member_type', 'is_verified'])->findOrFail($userId);

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

        // Récupérer les documents requis pour ce type de membre
        $requiredDocs = RequiredDocument::getByMemberType($user->member_type, true);
        $requiredTypes = $requiredDocs->pluck('document_type')->toArray();

        // Vérifier si tous les documents requis sont validés
        $validationStatus = $this->checkUserDocumentsValidation($userId, $requiredTypes);

        return view('admin.documents.show', compact('user', 'documents', 'requiredDocs', 'validationStatus'));
    }

    /**
     * Validation individuelle avec vérification complète
     */
    public function validateDocument(Request $request, $id)
    {
        $document = Document::with('user')->findOrFail($id);
        $user = $document->user;

        if ($document->status === 'validated') {
            return back()->with('error', 'Document déjà validé.');
        }

        try {
            DB::transaction(function () use ($document, $user) {
                // Valider le document
                $document->validateDocument(auth()->id());

                // Vérifier si tous les documents requis sont maintenant validés
                $this->updateUserVerificationStatus($user);
            });

            Cache::forget('document_stats');

            // Message différent selon le statut de vérification
            if ($user->fresh()->is_verified) {
                return back()->with('success', 'Document validé. ✅ L\'utilisateur est maintenant entièrement vérifié !');
            }

            return back()->with('success', 'Document validé avec succès. Des documents sont encore en attente.');

        } catch (\Exception $e) {
            Log::error('Erreur validation document: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la validation.');
        }
    }

    /**
     * Rejet individuel avec mise à jour du statut
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $document = Document::with('user')->findOrFail($id);
        $user = $document->user;

        try {
            DB::transaction(function () use ($document, $request, $user) {
                $document->rejectDocument($request->reason, auth()->id());

                // Si un document est rejeté, l'utilisateur n'est plus vérifié
                if ($user->is_verified) {
                    $user->update(['is_verified' => false]);
                    Log::info('User unverified due to rejection', ['user_id' => $user->id]);
                }
            });

            Cache::forget('document_stats');
            return back()->with('success', 'Document rejeté. Le statut de vérification a été mis à jour.');

        } catch (\Exception $e) {
            Log::error('Erreur rejet document: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du rejet.');
        }
    }

    /**
     * Validation en masse avec vérification finale
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

        $ids = array_slice($ids, 0, 100);
        $count = 0;
        $affectedUsers = [];

        Document::whereIn('id', $ids)
            ->where('status', '!=', 'validated')
            ->with('user')
            ->chunkById(20, function ($documents) use (&$count, &$affectedUsers) {
                foreach ($documents as $doc) {
                    try {
                        $doc->validateDocument(auth()->id());
                        $count++;

                        // Tracker les utilisateurs affectés
                        if (!in_array($doc->user_id, $affectedUsers)) {
                            $affectedUsers[] = $doc->user_id;
                        }
                    } catch (\Exception $e) {
                        Log::error('Erreur validation document ' . $doc->id . ': ' . $e->getMessage());
                    }
                }
            });

        // Vérifier le statut de tous les utilisateurs affectés
        foreach ($affectedUsers as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->updateUserVerificationStatus($user);
            }
        }

        Cache::forget('document_stats');

        $verifiedCount = User::whereIn('id', $affectedUsers)->where('is_verified', true)->count();

        $message = "{$count} document(s) validé(s).";
        if ($verifiedCount > 0) {
            $message .= " {$verifiedCount} utilisateur(s) maintenant vérifié(s).";
        }

        return back()->with('success', $message);
    }

    /**
     * Validation de tous les documents d'un utilisateur
     */
    public function validateUserDocuments($userId)
    {
        $user = User::findOrFail($userId);

        $count = Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->count();

        if ($count === 0) {
            // Vérifier quand même le statut
            $this->updateUserVerificationStatus($user);
            return back()->with('info', 'Tous les documents sont déjà validés.');
        }

        if ($count > 20) {
            \App\Jobs\BulkValidateDocuments::dispatch($userId, auth()->id());
            return back()->with('success', "Validation de {$count} documents en cours (traitement en arrière-plan).");
        }

        $processed = 0;
        Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->chunkById(10, function ($documents) use (&$processed) {
                foreach ($documents as $doc) {
                    $doc->validateDocument(auth()->id());
                    $processed++;
                }
            });

        // Mettre à jour le statut de vérification
        $wasVerified = $user->is_verified;
        $this->updateUserVerificationStatus($user);

        Cache::forget('document_stats');

        $message = "{$processed} document(s) validé(s).";
        if (!$wasVerified && $user->fresh()->is_verified) {
            $message .= " ✅ L'utilisateur est maintenant vérifié !";
        }

        return back()->with('success', $message);
    }

    /**
     * Remettre en attente
     */
    public function pending($id)
    {
        $document = Document::with('user')->findOrFail($id);
        $user = $document->user;

        $document->markAsPending();

        // Si on remet en attente, l'utilisateur n'est plus vérifié
        if ($user->is_verified) {
            $user->update(['is_verified' => false]);
            Log::info('User unverified due to pending status', ['user_id' => $user->id]);
        }

        Cache::forget('document_stats');
        return back()->with('success', 'Document remis en attente. Le statut de vérification a été mis à jour.');
    }

    /**
     * Téléchargement
     */
    public function download($id)
    {
        $document = Document::select(['id', 'path', 'original_filename'])->findOrFail($id);

        if (!$document->path || !Storage::disk('public')->exists($document->path)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $document->path,
            $document->original_filename ?? basename($document->path)
        );
    }

    /**
     * VÉRIFICATION COMPLÈTE : Met à jour le statut is_verified de l'utilisateur
     */
    protected function updateUserVerificationStatus(User $user): void
    {
        $requiredTypes = RequiredDocument::getByMemberType($user->member_type, true)
            ->pluck('document_type')
            ->toArray();

        // Si pas de documents requis, considérer comme vérifié
        if (empty($requiredTypes)) {
            if (!$user->is_verified) {
                $user->update(['is_verified' => true]);
                Log::info('User verified (no required docs)', ['user_id' => $user->id]);
            }
            return;
        }

        // Récupérer tous les documents validés de l'utilisateur
        $validatedTypes = Document::where('user_id', $user->id)
            ->where('status', 'validated')
            ->pluck('type')
            ->unique()
            ->toArray();

        // Vérifier si tous les types requis sont présents dans les documents validés
        $missingTypes = array_diff($requiredTypes, $validatedTypes);
        $isFullyVerified = empty($missingTypes);

        // Mettre à jour si changement
        if ($user->is_verified !== $isFullyVerified) {
            $user->update(['is_verified' => $isFullyVerified]);

            Log::info('User verification status updated', [
                'user_id' => $user->id,
                'is_verified' => $isFullyVerified,
                'missing_types' => $missingTypes,
                'validated_types' => $validatedTypes
            ]);
        }
    }

    /**
     * Vérifier la validation des documents d'un utilisateur
     */
    protected function checkUserDocumentsValidation(int $userId, array $requiredTypes): array
    {
        if (empty($requiredTypes)) {
            return [
                'is_complete' => true,
                'validated_count' => 0,
                'required_count' => 0,
                'missing_types' => [],
                'validated_types' => []
            ];
        }

        $userDocs = Document::where('user_id', $userId)
            ->where('status', 'validated')
            ->pluck('type')
            ->unique()
            ->toArray();

        $missingTypes = array_diff($requiredTypes, $userDocs);

        return [
            'is_complete' => empty($missingTypes),
            'validated_count' => count($userDocs),
            'required_count' => count($requiredTypes),
            'missing_types' => $missingTypes,
            'validated_types' => $userDocs
        ];
    }

    /**
     * Rafraîchir les stats
     */
    public function refreshStats()
    {
        Cache::forget('document_stats');
        return response()->json(['message' => 'Stats refreshed']);
    }
}
