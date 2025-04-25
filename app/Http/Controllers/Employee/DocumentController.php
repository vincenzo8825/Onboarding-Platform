<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentUploadedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents available to the employee.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category');

        // Documenti aziendali disponibili per l'utente (pubblici o assegnati)
        $documentsQuery = Document::where('visibility', 'all');

        if ($category) {
            $documentsQuery->where('category', $category);
        }

        $documents = $documentsQuery->latest()->paginate(9);

        // Documenti caricati dal dipendente
        $myDocuments = Document::where('uploaded_by', $user->id)
            ->latest()
            ->get();

        // Lista di categorie per il filtro
        $categories = Document::distinct('category')->pluck('category')->toArray();

        // Lista di ID dei documenti già visualizzati
        $viewedDocumentIds = [];

        return view('employee.documents.index', compact('documents', 'myDocuments', 'categories', 'category', 'viewedDocumentIds'));
    }

    /**
     * Show the form for uploading a new document.
     */
    public function create()
    {
        return view('employee.documents.create');
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'needs_approval' => 'boolean'
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $user->id,
            'visibility' => 'all', // Default to 'all' instead of using is_public
            'status' => $request->has('needs_approval') ? 'pending' : 'approved'
        ]);

        // Se il documento richiede approvazione, notifica agli admin
        if ($request->has('needs_approval')) {
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new DocumentUploadedNotification($document, $user));
        }

        return redirect()->route('employee.documents.index')
            ->with('success', 'Documento caricato con successo' .
                ($request->has('needs_approval') ? ' e in attesa di approvazione' : ''));
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        return view('employee.documents.show', compact('document'));
    }

    /**
     * Show the form for editing user's own document.
     */
    public function edit(Document $document)
    {
        // Only allow editing own documents
        if ($document->uploaded_by !== Auth::id()) {
            abort(403, 'You do not have permission to edit this document.');
        }

        return view('employee.documents.edit', compact('document'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Document $document)
    {
        // Only allow updating own documents
        if ($document->uploaded_by !== Auth::id()) {
            abort(403, 'You do not have permission to update this document.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
            'category' => 'required|string|max:50',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'pending', // Reset to pending if updated
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        return redirect()->route('employee.documents.index')->with('success', 'Document updated successfully and pending approval.');
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document)
    {
        // Only allow deleting own documents
        if ($document->uploaded_by !== Auth::id()) {
            abort(403, 'You do not have permission to delete this document.');
        }

        // Delete the file
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('employee.documents.index')->with('success', 'Document deleted successfully.');
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File non trovato');
        }

        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }
}
