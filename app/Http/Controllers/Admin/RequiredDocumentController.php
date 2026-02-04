<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequiredDocument;
use Illuminate\Http\Request;

class RequiredDocumentController extends Controller
{
    public function index()
    {
        $documents = RequiredDocument::orderBy('member_type')
            ->orderBy('order')
            ->get()
            ->groupBy('member_type');

        $memberTypes = RequiredDocument::getMemberTypes();
        $documentTypes = RequiredDocument::getDocumentTypes();

        return view('admin.required-documents.index', compact(
            'documents',
            'memberTypes',
            'documentTypes'
        ));
    }

    public function create()
    {
        $memberTypes = RequiredDocument::getMemberTypes();
        $documentTypes = RequiredDocument::getDocumentTypes();

        return view('admin.required-documents.create', compact(
            'memberTypes',
            'documentTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_type' => 'required|string',
            'document_type' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'category' => 'required|string',
            'order' => 'required|integer',
            'has_expiry_date' => 'boolean',
            'validity_days' => 'nullable|integer',
            'allowed_formats' => 'nullable|array',
            'max_size_mb' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        RequiredDocument::create($validated);

        return redirect()->route('admin.required-documents.index')
            ->with('success', 'Document requis ajouté avec succès.');
    }

    public function edit(RequiredDocument $requiredDocument)
    {
        $memberTypes = RequiredDocument::getMemberTypes();
        $documentTypes = RequiredDocument::getDocumentTypes();

        return view('admin.required-documents.edit', compact(
            'requiredDocument',
            'memberTypes',
            'documentTypes'
        ));
    }

    public function update(Request $request, RequiredDocument $requiredDocument)
    {
        $validated = $request->validate([
            'member_type' => 'required|string',
            'document_type' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'category' => 'required|string',
            'order' => 'required|integer',
            'has_expiry_date' => 'boolean',
            'validity_days' => 'nullable|integer',
            'allowed_formats' => 'nullable|array',
            'max_size_mb' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        $requiredDocument->update($validated);

        return redirect()->route('admin.required-documents.index')
            ->with('success', 'Document requis mis à jour avec succès.');
    }

    public function destroy(RequiredDocument $requiredDocument)
    {
        $requiredDocument->delete();

        return redirect()->route('admin.required-documents.index')
            ->with('success', 'Document requis supprimé avec succès.');
    }

    public function toggleStatus(RequiredDocument $requiredDocument)
    {
        $requiredDocument->update([
            'is_active' => !$requiredDocument->is_active
        ]);

        return back()->with('success', 'Statut du document mis à jour.');
    }
}
