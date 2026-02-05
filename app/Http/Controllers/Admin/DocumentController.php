<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }

    public function validateDocument($id)
    {
        $document = Document::findOrFail($id);
        $document->update([
            'status' => 'validated',
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Document validé.');
    }
}
