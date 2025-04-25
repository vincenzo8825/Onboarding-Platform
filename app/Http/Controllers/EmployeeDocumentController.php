<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->input('category');
        $query = Document::where('visibility', 'all');
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Document::select('category')->distinct()->pluck('category');
        
        // Ottieni i documenti già visualizzati dall'utente
        $viewedDocumentIds = Auth::user()->viewedDocuments()->pluck('document_id')->toArray();
        
        return view('employee.documents.index', compact('documents', 'categories', 'category', 'viewedDocumentIds'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        // Verifica che il documento sia visibile per l'utente
        if ($document->visibility !== 'all') {
            abort(403);
        }
        
        // Registra la visualizzazione del documento
        $user = Auth::user();
        if (!$user->viewedDocuments()->where('document_id', $document->id)->exists()) {
            $user->viewedDocuments()->attach($document->id, ['viewed_at' => now()]);
        }
        
        return view('employee.documents.show', compact('document'));
    }

    /**
     * Download the document file.
     */
    public function download(Document $document)
    {
        // Verifica che il documento sia visibile per l'utente
        if ($document->visibility !== 'all') {
            abort(403);
        }
        
        // Registra la visualizzazione del documento se non è già stato visualizzato
        $user = Auth::user();
        if (!$user->viewedDocuments()->where('document_id', $document->id)->exists()) {
            $user->viewedDocuments()->attach($document->id, ['viewed_at' => now()]);
        }
        
        return Storage::disk('public')->download($document->file_path, $document->title);
    }
}