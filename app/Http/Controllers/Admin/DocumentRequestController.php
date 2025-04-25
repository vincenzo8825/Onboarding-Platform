<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\User;
use App\Notifications\DocumentRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    /**
     * Mostra l'elenco delle richieste di documenti.
     */
    public function index()
    {
        $documentRequests = DocumentRequest::with(['employee', 'admin', 'submittedDocument'])
            ->latest()
            ->paginate(10);

        return view('admin.document_requests.index', compact('documentRequests'));
    }

    /**
     * Mostra il form per creare una nuova richiesta di documento.
     */
    public function create()
    {
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee')->orWhere('name', 'new_hire');
        })->get();

        $documentTypes = [
            'Carta d\'identità',
            'Passaporto',
            'Codice fiscale',
            'Curriculum Vitae',
            'Titolo di studio',
            'Certificazione',
            'Attestato',
            'Contratto firmato',
            'Altro'
        ];

        return view('admin.document_requests.create', compact('employees', 'documentTypes'));
    }

    /**
     * Registra una nuova richiesta di documento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $documentRequest = DocumentRequest::create([
            'admin_id' => Auth::id(),
            'employee_id' => $request->employee_id,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        // Invia notifica al dipendente
        $employee = User::find($request->employee_id);
        $employee->notify(new DocumentRequestNotification($documentRequest, Auth::user()));

        return redirect()->route('admin.document-requests.index')
            ->with('success', 'Richiesta di documento inviata con successo al dipendente.');
    }

    /**
     * Mostra i dettagli di una specifica richiesta di documento.
     */
    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load(['employee', 'admin', 'submittedDocument']);
        return view('admin.document_requests.show', compact('documentRequest'));
    }

    /**
     * Mostra il form per modificare una richiesta di documento.
     */
    public function edit(DocumentRequest $documentRequest)
    {
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee')->orWhere('name', 'new_hire');
        })->get();

        $documentTypes = [
            'Carta d\'identità',
            'Passaporto',
            'Codice fiscale',
            'Curriculum Vitae',
            'Titolo di studio',
            'Certificazione',
            'Attestato',
            'Contratto firmato',
            'Altro'
        ];

        return view('admin.document_requests.edit', compact('documentRequest', 'employees', 'documentTypes'));
    }

    /**
     * Aggiorna una richiesta di documento.
     */
    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $documentRequest->update([
            'document_type' => $request->document_type,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('admin.document-requests.index')
            ->with('success', 'Richiesta di documento aggiornata con successo.');
    }

    /**
     * Elimina una richiesta di documento.
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        $documentRequest->delete();
        return redirect()->route('admin.document-requests.index')
            ->with('success', 'Richiesta di documento eliminata con successo.');
    }

    /**
     * Approva un documento caricato.
     */
    public function approve(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $documentRequest->approve($request->notes);

        return redirect()->route('admin.document-requests.show', $documentRequest)
            ->with('success', 'Documento approvato con successo.');
    }

    /**
     * Rifiuta un documento caricato.
     */
    public function reject(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $documentRequest->reject($request->notes);

        return redirect()->route('admin.document-requests.show', $documentRequest)
            ->with('success', 'Documento rifiutato. Il dipendente sarà notificato.');
    }

    public function download(DocumentRequest $documentRequest)
    {
        if (!$documentRequest->document_path || !Storage::exists($documentRequest->document_path)) {
            return back()->with('error', 'Document not found.');
        }

        return Storage::download($documentRequest->document_path);
    }
}
