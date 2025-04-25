<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\User;
use App\Notifications\DocumentSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentRequestController extends Controller
{
    /**
     * Mostra l'elenco delle richieste di documenti per il dipendente corrente.
     */
    public function index()
    {
        $pendingRequests = DocumentRequest::where('employee_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        $completedRequests = DocumentRequest::where('employee_id', Auth::id())
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->latest()
            ->get();

        return view('employee.document_requests.index', compact('pendingRequests', 'completedRequests'));
    }

    /**
     * Mostra il form per caricare un documento in risposta a una richiesta.
     */
    public function show(DocumentRequest $documentRequest)
    {
        // Verifica che la richiesta appartenga al dipendente corrente
        if ($documentRequest->employee_id !== Auth::id()) {
            abort(403, 'Non sei autorizzato a visualizzare questa richiesta.');
        }

        return view('employee.document_requests.show', compact('documentRequest'));
    }

    /**
     * Carica un documento in risposta a una richiesta.
     */
    public function submitDocument(Request $request, DocumentRequest $documentRequest)
    {
        // Verifica che la richiesta appartenga al dipendente corrente
        if ($documentRequest->employee_id !== Auth::id()) {
            abort(403, 'Non sei autorizzato a rispondere a questa richiesta.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Crea il documento
        $document = Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $documentRequest->document_type,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'visibility' => 'admin', // Solo gli admin possono vedere questo documento
            'uploaded_by' => Auth::id(),
        ]);

        // Aggiorna la richiesta di documento
        $documentRequest->markAsSubmitted($document->id);

        // Notifica l'admin che ha fatto la richiesta
        $admin = User::find($documentRequest->admin_id);
        $admin->notify(new DocumentSubmittedNotification($documentRequest, $user));

        return redirect()->route('employee.document-requests.index')
            ->with('success', 'Documento caricato con successo e in attesa di approvazione.');
    }
}
