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
    protected const CACHE_DURATION = 300; // 5 minutes

    /**
     * Index - VERSION ULTRA OPTIMISÉE
     */
    public function index(Request $request)
    {
        // Désactiver le query log pour économiser de la mémoire sur les grosses requêtes
        DB::disableQueryLog();

        $cacheKey = "documents_index_{$request->filter}_{$request->page}";

        $data = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = User::select(['id', 'name', 'email', 'member_type', 'is_verified', 'created_at'])
                ->with(['documents' => function($q) {
                    $q->select(['id', 'user_id', 'name', 'type', 'status', 'created_at', 'path', 'original_filename'])
                      ->orderBy('status', 'asc')
                      ->orderBy('created_at', 'desc')
                      ->limit(20); // Réduit de 50 à 20
                }])
                ->whereHas('documents', function($q) {
                    $q->where('is_profile_document', true);
                });

            // Filtres simplifiés sans sous-requêtes complexes
            if ($request->filter === 'pending') {
                $pendingUserIds = Document::where('status', 'pending')
                    ->where('is_profile_document', true)
                    ->distinct()
                    ->pluck('user_id');
                $query->whereIn('id', $pendingUserIds);
            }
            elseif ($request->filter === 'complete') {
                $query->where('is_verified', true);
            }
            elseif ($request->filter === 'unverified') {
                $query->where('is_verified', false);
            }

            return [
                'users' => $query->paginate(10),
                'stats' => $this->getQuickStats()
            ];
        });

        return view('admin.documents.index', $data);
    }

    /**
     * Stats rapides sans transaction complexe
     */
    private function getQuickStats()
    {
        return Cache::remember('doc_stats_v2', self::CACHE_DURATION, function () {
            return [
                'total_users' => User::whereHas('documents')->count(),
                'pending' => Document::where('status', 'pending')->count(),
                'validated' => Document::where('status', 'validated')->count(),
                'rejected' => Document::where('status', 'rejected')->count(),
                'verified_users' => User::where('is_verified', true)->count(),
            ];
        });
    }

    /**
     * Show - VERSION OPTIMISÉE
     */
    public function show($userId)
    {
        $user = User::select(['id', 'name', 'email', 'member_type', 'is_verified'])
            ->findOrFail($userId);

        // Documents avec pagination mémoire au lieu de get()
        $documents = Document::where('user_id', $userId)
            ->select(['id', 'user_id', 'name', 'type', 'category', 'status', 'path',
                     'original_filename', 'size', 'mime_type', 'validated_at',
                     'validated_by', 'rejection_reason', 'expiry_date', 'is_expired', 'created_at'])
            ->orderByRaw("FIELD(status, 'pending', 'validated', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->limit(100) // Limite de sécurité
            ->get();

        // Cache des documents requis pour TOUS les appels
        $requiredDocs = $this->getCachedRequiredDocs($user->member_type);
        $requiredTypes = $requiredDocs->pluck('document_type')->toArray();

        // Vérification optimisée en une requête
        $validationStatus = $this->fastValidationCheck($userId, $requiredTypes);

        return view('admin.documents.show', compact('user', 'documents', 'requiredDocs', 'validationStatus'));
    }

    /**
     * Cache centralisé des documents requis
     */
    private function getCachedRequiredDocs($memberType)
    {
        return Cache::remember("req_docs_{$memberType}", 600, function () use ($memberType) {
            return RequiredDocument::where('member_type', $memberType)
                ->where('is_active', true)
                ->select(['id', 'document_type', 'name', 'description', 'is_required',
                         'category', 'has_expiry_date', 'validity_days', 'allowed_formats', 'max_size_mb'])
                ->get();
        });
    }

    /**
     * Vérification rapide en une requête SQL
     */
    private function fastValidationCheck($userId, $requiredTypes)
    {
        if (empty($requiredTypes)) {
            return ['is_complete' => true, 'validated_count' => 0, 'required_count' => 0,
                    'missing_types' => [], 'validated_types' => []];
        }

        $validatedTypes = Document::where('user_id', $userId)
            ->where('status', 'validated')
            ->whereIn('type', $requiredTypes)
            ->where('is_profile_document', true)
            ->distinct()
            ->pluck('type')
            ->toArray();

        $missingTypes = array_diff($requiredTypes, $validatedTypes);

        return [
            'is_complete' => empty($missingTypes),
            'validated_count' => count($validatedTypes),
            'required_count' => count($requiredTypes),
            'missing_types' => $missingTypes,
            'validated_types' => $validatedTypes
        ];
    }

    /**
     * Validation individuelle - VERSION OPTIMISÉE
     */
    public function validateDocument(Request $request, $id)
    {
        // Récupération simple sans transaction initiale
        $document = Document::findOrFail($id);

        if ($document->status === 'validated') {
            return back()->with('error', 'Document déjà validé.');
        }

        $user = User::select(['id', 'member_type', 'is_verified'])->find($document->user_id);

        if (!$user) {
            return back()->with('error', 'Utilisateur non trouvé.');
        }

        try {
            // Mise à jour simple sans transaction complexe
            $document->status = 'validated';
            $document->validated_at = now();
            $document->validated_by = auth()->id();
            $document->save();

            // Vérification optimisée sans boucle
            $this->quickVerifyUser($user);

            // Invalidation du cache
            $this->clearUserCache($user->id, $user->member_type);
            Cache::forget('doc_stats_v2');

            $isNowVerified = $user->fresh()->is_verified;

            return back()->with('success', $isNowVerified
                ? 'Document validé. ✅ L\'utilisateur est maintenant entièrement vérifié !'
                : 'Document validé avec succès. Des documents sont encore en attente.');

        } catch (\Exception $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la validation.');
        }
    }

    /**
     * Vérification utilisateur ultra-rapide
     */
    private function quickVerifyUser(User $user)
    {
        $requiredTypes = $this->getCachedRequiredDocs($user->member_type)
            ->pluck('document_type')
            ->toArray();

        if (empty($requiredTypes)) {
            if (!$user->is_verified) {
                $user->update(['is_verified' => true]);
            }
            return;
        }

        // Comptage direct en SQL
        $validatedCount = Document::where('user_id', $user->id)
            ->where('status', 'validated')
            ->whereIn('type', $requiredTypes)
            ->where('is_profile_document', true)
            ->distinct()
            ->count('type');

        $shouldBeVerified = ($validatedCount >= count($requiredTypes));

        if ($user->is_verified !== $shouldBeVerified) {
            $user->update(['is_verified' => $shouldBeVerified]);
            Log::info('User verification updated', ['user_id' => $user->id, 'verified' => $shouldBeVerified]);
        }
    }

    /**
     * Rejet - VERSION OPTIMISÉE
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|min:10|max:1000']);

        $document = Document::findOrFail($id);
        $user = User::select(['id', 'is_verified', 'member_type'])->find($document->user_id);

        try {
            $document->status = 'rejected';
            $document->rejection_reason = $request->reason;
            $document->validated_by = auth()->id();
            $document->validated_at = now();
            $document->save();

            if ($user && $user->is_verified) {
                $user->update(['is_verified' => false]);
            }

            $this->clearUserCache($user->id ?? null, $user->member_type ?? null);
            Cache::forget('doc_stats_v2');

            return back()->with('success', 'Document rejeté.');

        } catch (\Exception $e) {
            Log::error('Rejet error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du rejet.');
        }
    }

    /**
     * Validation en masse - VERSION SIMPLIFIÉE
     */
    public function bulkValidate(Request $request)
    {
        $request->validate(['document_ids' => 'required|string']);

        $ids = array_slice(array_filter(explode(',', $request->document_ids)), 0, 30); // Max 30

        if (empty($ids)) {
            return back()->with('error', 'Aucun document sélectionné.');
        }

        $count = 0;
        $userIds = [];

        // Traitement par lots de 10
        foreach (array_chunk($ids, 10) as $chunk) {
            Document::whereIn('id', $chunk)
                ->where('status', '!=', 'validated')
                ->update([
                    'status' => 'validated',
                    'validated_at' => now(),
                    'validated_by' => auth()->id()
                ]);

            $count += count($chunk);

            // Récupération des user_ids affectés
            $chunkUserIds = Document::whereIn('id', $chunk)->pluck('user_id')->toArray();
            $userIds = array_merge($userIds, $chunkUserIds);
        }

        $userIds = array_unique($userIds);

        // Vérification par lots des utilisateurs
        foreach (array_slice($userIds, 0, 10) as $uid) { // Max 10 users
            $user = User::select(['id', 'member_type', 'is_verified'])->find($uid);
            if ($user) {
                $this->quickVerifyUser($user);
                $this->clearUserCache($uid, $user->member_type);
            }
        }

        Cache::forget('doc_stats_v2');

        return back()->with('success', "{$count} document(s) validé(s).");
    }

    /**
     * Validation tous documents d'un user - AVEC JOB
     */
    public function validateUserDocuments($userId)
    {
        $user = User::select(['id', 'member_type', 'is_verified'])->findOrFail($userId);

        $count = Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->count();

        if ($count === 0) {
            $this->quickVerifyUser($user);
            return back()->with('info', 'Tous les documents sont déjà validés.');
        }

        // TOUJOURS utiliser un job si > 5 documents pour éviter timeout
        if ($count > 5) {
            \App\Jobs\BulkValidateDocuments::dispatch($userId, auth()->id());
            return back()->with('success', "Validation de {$count} documents en cours (arrière-plan).");
        }

        // Sinon update direct
        Document::where('user_id', $userId)
            ->where('status', '!=', 'validated')
            ->update([
                'status' => 'validated',
                'validated_at' => now(),
                'validated_by' => auth()->id()
            ]);

        $this->quickVerifyUser($user);
        $this->clearUserCache($userId, $user->member_type);
        Cache::forget('doc_stats_v2');

        return back()->with('success', "{$count} document(s) validé(s).");
    }

    /**
     * Remettre en attente
     */
    public function pending($id)
    {
        $document = Document::findOrFail($id);
        $user = User::select(['id', 'is_verified', 'member_type'])->find($document->user_id);

        $document->update([
            'status' => 'pending',
            'validated_at' => null,
            'validated_by' => null,
            'rejection_reason' => null
        ]);

        if ($user && $user->is_verified) {
            $user->update(['is_verified' => false]);
        }

        $this->clearUserCache($user->id ?? null, $user->member_type ?? null);
        Cache::forget('doc_stats_v2');

        return back()->with('success', 'Document remis en attente.');
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
     * Nettoyage du cache utilisateur
     */
    private function clearUserCache($userId, $memberType)
    {
        if ($userId) {
            Cache::forget("user_docs_{$userId}");
        }
        if ($memberType) {
            Cache::forget("req_docs_{$memberType}");
            Cache::forget("required_types_{$memberType}");
        }
        Cache::forget('document_stats');
    }

    /**
     * Rafraîchir les stats
     */
    public function refreshStats()
    {
        Cache::forget('doc_stats_v2');
        Cache::forget('document_stats');
        return response()->json(['message' => 'Stats refreshed']);
    }
}
