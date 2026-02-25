<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequiredDocumentController extends Controller
{
    /**
     * Affiche la liste des documents requis
     */
    public function index()
    {
        $documents = RequiredDocument::orderBy('member_type')
            ->orderBy('order')
            ->get()
            ->groupBy('member_type');

        $memberTypes = RequiredDocument::getMemberTypes();
        $categories = $this->getCategories();

        return view('admin.required-documents.index', compact(
            'documents',
            'memberTypes',
            'categories'
        ));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $memberTypes = RequiredDocument::getMemberTypes();
        $documentTypes = RequiredDocument::getDocumentTypes();
        $categories = $this->getCategories();
        $fileFormats = $this->getFileFormats();

        return view('admin.required-documents.create', compact(
            'memberTypes',
            'documentTypes',
            'categories',
            'fileFormats'
        ));
    }

    /**
     * Enregistre un nouveau document
     */
    public function store(Request $request)
    {
        Log::info('Store method called', $request->all());

        try {
            // Validation
            $validated = $request->validate([
                'member_type' => 'required|string|in:particulier,entreprise',
                'document_type' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_required' => 'sometimes|boolean',
                'category' => 'required|string|in:personal,business,financial,project,verification,other',
                'order' => 'required|integer|min:0',
                'has_expiry_date' => 'sometimes|boolean',
                'validity_days' => 'nullable|integer|min:1',
                'allowed_formats' => 'nullable|array',
                'allowed_formats.*' => 'string|in:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max_size_mb' => 'required|integer|min:1|max:50',
                'is_active' => 'sometimes|boolean'
            ], [
                'member_type.required' => 'Le type de membre est obligatoire.',
                'document_type.required' => 'Le type de document est obligatoire.',
                'name.required' => 'Le nom du document est obligatoire.',
                'category.required' => 'La catégorie est obligatoire.',
                'order.required' => 'L\'ordre d\'affichage est obligatoire.',
                'max_size_mb.required' => 'La taille maximale est obligatoire.',
            ]);

            // Vérifier doublon
            $exists = RequiredDocument::where('member_type', $validated['member_type'])
                ->where('document_type', $validated['document_type'])
                ->exists();

            if ($exists) {
                Log::warning('Duplicate document detected', [
                    'member_type' => $validated['member_type'],
                    'document_type' => $validated['document_type']
                ]);

                return back()
                    ->withInput()
                    ->withErrors(['document_type' => 'Ce type de document existe déjà pour ce membre.'])
                    ->with('error', 'Un document avec ce type existe déjà.');
            }

            // Préparer les données
            $data = [
                'member_type' => $validated['member_type'],
                'document_type' => $validated['document_type'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_required' => $request->has('is_required'),
                'category' => $validated['category'],
                'order' => $validated['order'],
                'has_expiry_date' => $request->has('has_expiry_date'),
                'validity_days' => $request->has('has_expiry_date') ? ($validated['validity_days'] ?? null) : null,
                'allowed_formats' => isset($validated['allowed_formats']) ? array_values($validated['allowed_formats']) : [],
                'max_size_mb' => $validated['max_size_mb'],
                'is_active' => $request->has('is_active')
            ];

            Log::info('Data prepared for creation', $data);

            // Création
            $document = RequiredDocument::create($data);

            Log::info('Document created successfully', ['id' => $document->id]);

            return redirect()->route('admin.required-documents.index')
                ->with('success', 'Document requis créé avec succès.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Store failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Formulaire d'édition
     */
    public function edit(RequiredDocument $requiredDocument)
    {
        $memberTypes = RequiredDocument::getMemberTypes();
        $documentTypes = RequiredDocument::getDocumentTypes();
        $categories = $this->getCategories();
        $fileFormats = $this->getFileFormats();

        return view('admin.required-documents.edit', compact(
            'requiredDocument',
            'memberTypes',
            'documentTypes',
            'categories',
            'fileFormats'
        ));
    }

    /**
     * Met à jour un document
     */
    public function update(Request $request, RequiredDocument $requiredDocument)
    {
        Log::info('Update method called', [
            'document_id' => $requiredDocument->id,
            'data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'member_type' => 'required|string|in:particulier,entreprise',
                'document_type' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_required' => 'sometimes|boolean',
                'category' => 'required|string|in:personal,business,financial,project,verification,other',
                'order' => 'required|integer|min:0',
                'has_expiry_date' => 'sometimes|boolean',
                'validity_days' => 'nullable|integer|min:1',
                'allowed_formats' => 'nullable|array',
                'allowed_formats.*' => 'string|in:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max_size_mb' => 'required|integer|min:1|max:50',
                'is_active' => 'sometimes|boolean'
            ]);

            // Vérifier doublon (exclure current)
            $exists = RequiredDocument::where('member_type', $validated['member_type'])
                ->where('document_type', $validated['document_type'])
                ->where('id', '!=', $requiredDocument->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors(['document_type' => 'Ce type de document existe déjà pour ce membre.'])
                    ->with('error', 'Un document avec ce type existe déjà.');
            }

            // Préparer les données
            $data = [
                'member_type' => $validated['member_type'],
                'document_type' => $validated['document_type'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_required' => $request->has('is_required'),
                'category' => $validated['category'],
                'order' => $validated['order'],
                'has_expiry_date' => $request->has('has_expiry_date'),
                'validity_days' => $request->has('has_expiry_date') ? ($validated['validity_days'] ?? null) : null,
                'allowed_formats' => isset($validated['allowed_formats']) ? array_values($validated['allowed_formats']) : [],
                'max_size_mb' => $validated['max_size_mb'],
                'is_active' => $request->has('is_active')
            ];

            $requiredDocument->update($data);

            Log::info('Document updated successfully', ['id' => $requiredDocument->id]);

            return redirect()->route('admin.required-documents.index')
                ->with('success', 'Document mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Update failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un document
     */
    public function destroy(RequiredDocument $requiredDocument)
    {
        try {
            $requiredDocument->delete();
            return redirect()->route('admin.required-documents.index')
                ->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Active/Désactive un document
     */
    public function toggleStatus(RequiredDocument $requiredDocument)
    {
        try {
            $requiredDocument->update([
                'is_active' => !$requiredDocument->is_active
            ]);

            return back()->with('success',
                $requiredDocument->is_active
                    ? 'Document activé avec succès.'
                    : 'Document désactivé avec succès.'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // Méthodes helper
    private function getCategories()
    {
        return [
            'personal' => 'Personnel',
            'business' => 'Entreprise',
            'financial' => 'Financier',
            'project' => 'Projet',
            'verification' => 'Vérification',
            'other' => 'Autre'
        ];
    }

    private function getFileFormats()
    {
        return [
            'pdf' => 'PDF',
            'jpg' => 'JPG',
            'jpeg' => 'JPEG',
            'png' => 'PNG',
            'doc' => 'DOC',
            'docx' => 'DOCX',
            'xls' => 'XLS',
            'xlsx' => 'XLSX'
        ];
    }
}
