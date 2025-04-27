<?php

namespace App\Http\Controllers;

use App\Events\DocumentApproved;
use App\Events\DocumentRejected;
use App\Events\DocumentRequested;
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

    // Quando un admin approva un documento
    // Nel metodo approve
    public function approve(Document $document)
    {
        // Codice esistente per l'approvazione del documento
        $document->update(['status' => 'approved']);

        // Emetti l'evento per la notifica
        event(new DocumentApproved($document, auth()->user()));

        // Invia direttamente la notifica per garantire che funzioni
        $document->user->notify(new \App\Notifications\DocumentApprovedNotification($document, auth()->user()));

        return redirect()->back()->with('success', 'Documento approvato con successo');
    }

    /**
     * Rifiuta un documento
     */
    public function reject(Request $request, Document $document)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
            'rejected_by' => auth()->id()
        ]);

        // Emetti l'evento per la notifica
        event(new DocumentRejected($document, auth()->user()));

        return redirect()->back()->with('success', 'Documento rifiutato con successo');
    }

    /**
     * Richiedi un documento a un utente
     */
    public function requestDocument(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date|after:today',
        ]);

        $document = Document::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'requested',
            'due_date' => $validated['due_date'],
            'requested_by' => auth()->id(),
            'requested_at' => now(),
        ]);

        // Emetti l'evento per la notifica
        $user = User::find($validated['user_id']);
        event(new DocumentRequested($document, $user, auth()->user()));

        return redirect()->route('admin.documents.index')
            ->with('success', 'Richiesta documento inviata con successo');
    }
}
