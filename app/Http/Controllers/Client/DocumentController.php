<?php

namespace App\Http\Controllers\Client;

use App\Models\Document;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\RequiredDocument;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Afficher la liste des documents
     */
 public function index()
{
    $user = Auth::user();

    // Documents de profil uniquement
    $documents = Document::where('user_id', $user->id)
        ->profileDocuments()
        ->recentFirst()
        ->get();

    // Types de documents requis selon le type d'utilisateur
    $requiredDocuments = RequiredDocument::getByMemberType($user->member_type, true);

    // Vérifier les documents manquants
    $missingDocuments = [];
    $completedRequired = 0;

    foreach ($requiredDocuments as $requiredDoc) {
        // Récupérer tous les documents de ce type (collection déjà chargée)
        $docsOfType = $documents->where('type', $requiredDoc->document_type);

        // Vérifier si un document validé et non expiré existe (utilisation de filter pour collection)
        $hasValidated = $docsOfType
            ->where('status', 'validated')
            ->filter(function ($doc) {
                if ($doc->expiry_date) {
                    return !$doc->is_expired && $doc->expiry_date->isFuture();
                }
                return true;
            })
            ->isNotEmpty();

        // Vérifier si un document est en attente (pending)
        $hasPending = $docsOfType->where('status', 'pending')->isNotEmpty();

        if ($hasValidated && $requiredDoc->is_required) {
            // Document validé = compté comme complété
            $completedRequired++;
        } elseif (!$hasValidated && !$hasPending && $requiredDoc->is_required) {
            // Manquant uniquement si : pas de document validé ET pas de document en attente
            $missingDocuments[] = $requiredDoc;
        }
        // Si hasPending = true mais hasValidated = false : ce n'est pas manquant, mais pas complété non plus
    }

    // Calculer le pourcentage de complétion
    $totalRequired = $requiredDocuments->where('is_required', true)->count();
    $completionPercentage = $totalRequired > 0 ? round(($completedRequired / $totalRequired) * 100) : 100;

    // Déterminer le mode d'affichage
    $showMissingOnly = count($missingDocuments) > 0 && $completionPercentage < 100;
    $showDocumentsOnly = $completionPercentage === 100;

    return view('client.documents.index', compact(
        'documents',
        'requiredDocuments',
        'missingDocuments',
        'completionPercentage',
        'completedRequired',
        'totalRequired',
        'showMissingOnly',
        'showDocumentsOnly'
    ));
}
    /**
     * Afficher le formulaire d'upload
     */

    public function uploadForm(Request $request)
    {
        $type = $request->get('type');
        $user = Auth::user();

        if (!$type) {
            return redirect()->route('client.documents.index')
                ->with('error', 'Type de document non spécifié.');
        }

        $requiredDoc = RequiredDocument::where('member_type', $user->member_type)
            ->where('document_type', $type)
            ->where('is_active', true)
            ->first();

        if (!$requiredDoc) {
            return redirect()->route('client.documents.index')
                ->with('error', 'Ce type de document n\'est pas disponible pour votre profil.');
        }

        // Vérifier s'il y a déjà un document en attente
        $existingDocument = Document::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_profile_document', true)
            ->whereIn('status', ['pending', 'validated'])
            ->first();

        return view('client.documents.upload', compact('requiredDoc', 'existingDocument', 'type'));
    }

    /**
     * Traiter l'upload d'un document (CORRIGÉ)
     */
    public function uploadDocument(Request $request)
    {
        $user = Auth::user();

        // Log pour debug
        Log::info('Upload document started', [
            'user_id' => $user->id,
            'type' => $request->input('type'),
            'has_file' => $request->hasFile('document')
        ]);

        try {
            // Validation de base (champs texte)
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'expiry_date' => 'nullable|date|after:today'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse(false, $validator->errors()->first(), null, 422);
            }

            $documentType = $request->input('type');

            // Vérifier si le type de document est autorisé
            $documentInfo = RequiredDocument::where('member_type', $user->member_type)
                ->where('document_type', $documentType)
                ->where('is_active', true)
                ->first();

            if (!$documentInfo) {
                return $this->jsonResponse(false, 'Type de document non autorisé.', null, 403);
            }

            // Vérifier doublons
            $existingPending = Document::where('user_id', $user->id)
                ->where('type', $documentType)
                ->where('is_profile_document', true)
                ->where('status', 'pending')
                ->first();

            if ($existingPending) {
                return $this->jsonResponse(false, 'Vous avez déjà un document en attente.', route('client.documents.index'), 422);
            }

            $existingValid = Document::where('user_id', $user->id)
                ->where('type', $documentType)
                ->where('is_profile_document', true)
                ->where('status', 'validated')
                ->where(function ($q) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>', now());
                })
                ->first();

            if ($existingValid) {
                return $this->jsonResponse(false, 'Vous avez déjà un document validé.', route('client.documents.index'), 422);
            }

            // Validation du fichier
            if (!$request->hasFile('document')) {
                return $this->jsonResponse(false, 'Aucun fichier sélectionné.', null, 422);
            }

            $file = $request->file('document');

            // Vérifier que le fichier est valide
            if (!$file->isValid()) {
                return $this->jsonResponse(false, 'Fichier invalide ou corrompu.', null, 422);
            }

            // Validation taille et format
            $maxSize = ($documentInfo->max_size_mb ?? 5) * 1024; // en KB
            if ($file->getSize() > ($maxSize * 1024)) {
                return $this->jsonResponse(false, 'Fichier trop volumineux (max ' . ($documentInfo->max_size_mb ?? 5) . ' Mo)', null, 422);
            }

            if ($documentInfo->allowed_formats && count($documentInfo->allowed_formats) > 0) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $documentInfo->allowed_formats)) {
                    return $this->jsonResponse(false, 'Format non supporté. Formats: ' . implode(', ', $documentInfo->allowed_formats), null, 422);
                }
            }

            // Vérifier date d'expiration si requise
            if ($documentInfo->has_expiry_date && !$request->filled('expiry_date')) {
                return $this->jsonResponse(false, 'La date d\'expiration est requise.', null, 422);
            }

            // Upload fichier
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

            $path = 'documents/users/' . $user->id . '/' . $documentType;

            try {
                $filePath = $file->storeAs($path, $filename, 'public');
            } catch (\Exception $e) {
                Log::error('Storage error: ' . $e->getMessage());
                return $this->jsonResponse(false, 'Erreur lors de l\'enregistrement du fichier.', null, 500);
            }

            // Calculer date expiration
            $expiryDate = $request->input('expiry_date');
            if (!$expiryDate && $documentInfo->validity_days) {
                $expiryDate = now()->addDays($documentInfo->validity_days)->format('Y-m-d');
            }

            // Création document
            $document = new Document();
            $document->user_id = $user->id;
            $document->type = $documentType;
            $document->name = $request->input('name');
            $document->original_filename = $originalName;
            $document->path = $filePath;
            $document->size = $file->getSize();
            $document->mime_type = $file->getMimeType();
            $document->description = $request->input('description');
            $document->expiry_date = $expiryDate;
            $document->status = 'pending';
            $document->uploaded_at = now();
            $document->is_profile_document = true;
            $document->is_required = $documentInfo->is_required ?? true;
            $document->category = $documentInfo->category ?? 'other';

            $document->save();

            // Vérifier expiration immédiatement
            if ($expiryDate && now()->gt($expiryDate)) {
                $document->update(['is_expired' => true]);
            }

            // Notification (optionnel)
            try {
                if (class_exists(\App\Models\Notification::class)) {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'document_uploaded',
                        'title' => 'Document uploadé',
                        'message' => 'Votre document "' . $documentInfo->name . '" est en attente de validation.',
                        'data' => json_encode(['document_id' => $document->id]),
                        'read' => false
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Notification failed: ' . $e->getMessage());
            }

            Log::info('Upload successful', ['document_id' => $document->id]);

            return $this->jsonResponse(
                true,
                'Document uploadé avec succès ! Il sera validé par notre équipe.',
                route('client.documents.index')
            );
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->jsonResponse(false, 'Erreur serveur: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Réponse JSON
     */
    private function jsonResponse(bool $success, string $message, ?string $redirect = null, int $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'redirect' => $redirect
        ], $status);
    }
    /**
     * Afficher un document
     */
    public function viewDocumentPage($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Récupérer le nom du type de document
        $requiredDocuments = RequiredDocument::where('is_active', true)->get();
        $documentTypeName = $requiredDocuments->firstWhere('document_type', $document->type)->name ?? $document->type;

        // Obtenir les informations du fichier
        $fileExists = Storage::disk('public')->exists($document->path);
        $fileSize = $fileExists ? Storage::disk('public')->size($document->path) : 0;
        $formattedSize = $this->formatFileSize($fileSize);

        return view('client.documents.view', compact(
            'document',
            'documentTypeName',
            'formattedSize',
            'fileExists'
        ));
    }

    /**
     * Télécharger un document
     */
    public function downloadDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'Document non trouvé');
        }

        return Storage::disk('public')->download(
            $document->path,
            $document->original_filename
        );
    }

    /**
     * Visualiser un document directement
     */
    public function viewDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'Document non trouvé');
        }

        // Récupérer le contenu du fichier
        $file = Storage::disk('public')->get($document->path);
        $mimeType = Storage::disk('public')->mimeType($document->path);

        // Retourner la réponse
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"');
    }

    /**
     * Générer une URL temporaire pour visualiser un document
     */
    public function viewDocumentUrl($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (!Storage::disk('public')->exists($document->path)) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé'
            ], 404);
        }

        try {
            // Générer une URL (publique ou temporaire)
            if (config('filesystems.default') === 'public') {
                $url = Storage::disk('public')->url($document->path);
            } else {
                // Pour S3 ou autres, générer une URL temporaire
                $url = Storage::disk('public')->temporaryUrl(
                    $document->path,
                    now()->addMinutes(30)
                );
            }

            // Déterminer le type MIME
            $mimeType = Storage::disk('public')->mimeType($document->path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'mime_type' => $mimeType,
                'filename' => $document->original_filename,
                'is_image' => $document->isImage(),
                'is_pdf' => $document->isPdf()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating document URL: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer l\'URL du document'
            ], 500);
        }
    }

    /**
     * Supprimer un document
     */
    public function deleteDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si on peut supprimer (seulement les documents en attente ou rejetés)
        if ($document->status === 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer un document validé.'
            ], 403);
        }

        try {
            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }

            // Supprimer l'enregistrement
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du document.'
            ], 500);
        }
    }

    /**
     * Mettre à jour la description d'un document
     */
    public function updateDescription(Request $request, $id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'description' => 'nullable|string|max:500'
        ]);

        $document->update([
            'description' => $validated['description']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Description mise à jour avec succès.',
            'description' => $document->description
        ]);
    }

    /**
     * Mettre à jour la date d'expiration
     */
    public function updateExpiryDate(Request $request, $id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'expiry_date' => 'required|date|after:today'
        ]);

        $document->updateExpiryDate($validated['expiry_date']);

        return response()->json([
            'success' => true,
            'message' => 'Date d\'expiration mise à jour avec succès.',
            'expiry_date' => $document->expiry_date->format('Y-m-d'),
            'formatted_expiry_date' => $document->expiry_date->format('d/m/Y'),
            'is_expired' => $document->is_expired
        ]);
    }

    /**
     * Renouveler un document expiré
     */
    public function renewDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le document est expiré
        if (!$document->is_expired && $document->expiry_date && $document->expiry_date > now()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce document n\'est pas encore expiré.'
            ], 400);
        }

        // Récupérer les infos du type de document
        $documentInfo = RequiredDocument::where('document_type', $document->type)
            ->where('is_active', true)
            ->first();

        // Calculer la nouvelle date d'expiration
        $newExpiryDate = null;
        if ($documentInfo && $documentInfo->validity_days) {
            $newExpiryDate = now()->addDays($documentInfo->validity_days);
        }

        // Marquer comme non expiré et mettre à jour la date
        $document->update([
            'is_expired' => false,
            'expiry_date' => $newExpiryDate,
            'status' => 'pending' // Remettre en attente pour validation
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document renouvelé avec succès. Il sera à nouveau validé par notre équipe.',
            'expiry_date' => $newExpiryDate ? $newExpiryDate->format('Y-m-d') : null
        ]);
    }

    /**
     * Vérifier l'état d'un document
     */
    public function checkDocumentStatus($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier l'expiration
        $document->checkExpiry();

        return response()->json([
            'success' => true,
            'status' => $document->status,
            'status_label' => $document->status_label,
            'status_color' => $document->status_color,
            'is_expired' => $document->is_expired,
            'expiry_date' => $document->expiry_date ? $document->expiry_date->format('Y-m-d') : null,
            'formatted_expiry_date' => $document->expiry_date ? $document->expiry_date->format('d/m/Y') : null,
            'validated_at' => $document->validated_at ? $document->validated_at->format('d/m/Y H:i') : null,
            'validated_by' => $document->validator ? $document->validator->name : null,
            'rejection_reason' => $document->rejection_reason
        ]);
    }

    /**
     * Formater la taille d'un fichier
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Obtenir les statistiques des documents
     */
    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'total' => Document::where('user_id', $user->id)
                ->profileDocuments()
                ->count(),
            'validated' => Document::where('user_id', $user->id)
                ->profileDocuments()
                ->validated()
                ->count(),
            'pending' => Document::where('user_id', $user->id)
                ->profileDocuments()
                ->pending()
                ->count(),
            'rejected' => Document::where('user_id', $user->id)
                ->profileDocuments()
                ->rejected()
                ->count(),
            'expired' => Document::where('user_id', $user->id)
                ->profileDocuments()
                ->expired()
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Lister les documents
     */
    public function apiIndex()
    {
        $user = Auth::user();

        $documents = Document::where('user_id', $user->id)
            ->profileDocuments()
            ->recentFirst()
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->type,
                    'type_label' => $document->type_label,
                    'name' => $document->name,
                    'original_filename' => $document->original_filename,
                    'description' => $document->description,
                    'status' => $document->status,
                    'status_label' => $document->status_label,
                    'status_color' => $document->status_color,
                    'formatted_size' => $document->formatted_size,
                    'uploaded_at' => $document->uploaded_at->format('Y-m-d H:i:s'),
                    'formatted_uploaded_at' => $document->uploaded_at->format('d/m/Y H:i'),
                    'expiry_date' => $document->expiry_date ? $document->expiry_date->format('Y-m-d') : null,
                    'formatted_expiry_date' => $document->expiry_date ? $document->expiry_date->format('d/m/Y') : null,
                    'is_expired' => $document->is_expired,
                    'file_url' => $document->file_url,
                    'file_icon' => $document->file_icon,
                    'is_image' => $document->isImage(),
                    'is_pdf' => $document->isPdf(),
                    'can_download' => $document->path && Storage::disk('public')->exists($document->path),
                    'can_delete' => in_array($document->status, ['pending', 'rejected'])
                ];
            });

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }
}
