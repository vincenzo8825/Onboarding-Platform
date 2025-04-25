<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->input('category');
        $query = Document::with('uploader');
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Document::select('category')->distinct()->pluck('category');
        
        return view('admin.documents.index', compact('documents', 'categories', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'document_file' => 'required|file|max:10240',
            'is_required' => 'boolean',
            'visibility' => 'required|in:all,admin,specific_departments',
        ]);
        
        // Gestione del file
        $path = $request->file('document_file')->store('documents', 'public');
        
        $document = new Document([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'is_required' => $request->has('is_required'),
            'visibility' => $validated['visibility'],
        ]);
        
        $document->save();
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento caricato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $viewCount = $document->viewedBy()->count();
        $recentViews = $document->viewedBy()->orderBy('document_views.viewed_at', 'desc')->take(10)->get();
        
        return view('admin.documents.show', compact('document', 'viewCount', 'recentViews'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return view('admin.documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'document_file' => 'nullable|file|max:10240',
            'is_required' => 'boolean',
            'visibility' => 'required|in:all,admin,specific_departments',
        ]);
        
        $document->title = $validated['title'];
        $document->description = $validated['description'];
        $document->category = $validated['category'];
        $document->is_required = $request->has('is_required');
        $document->visibility = $validated['visibility'];
        
        // Gestione del file se è stato caricato un nuovo file
        if ($request->hasFile('document_file')) {
            // Elimina il vecchio file
            Storage::disk('public')->delete($document->file_path);
            
            // Carica il nuovo file
            $path = $request->file('document_file')->store('documents', 'public');
            $document->file_path = $path;
        }
        
        $document->save();
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Elimina il file
        Storage::disk('public')->delete($document->file_path);
        
        // Elimina il documento dal database
        $document->delete();
        
        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento eliminato con successo.');
    }
}
