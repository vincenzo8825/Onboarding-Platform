<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'employee_id',
        'document_type',
        'description',
        'due_date',
        'submitted_document_id',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Ottieni l'admin che ha richiesto il documento.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Ottieni il dipendente a cui è stato richiesto il documento.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Ottieni il documento caricato in risposta alla richiesta.
     */
    public function submittedDocument()
    {
        return $this->belongsTo(Document::class, 'submitted_document_id');
    }

    /**
     * Verifica se la richiesta è in attesa.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se il documento è stato caricato.
     */
    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    /**
     * Verifica se il documento è stato approvato.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Verifica se il documento è stato rifiutato.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Segna la richiesta come completata con il documento caricato.
     */
    public function markAsSubmitted($documentId)
    {
        $this->update([
            'submitted_document_id' => $documentId,
            'status' => 'submitted',
        ]);
    }

    /**
     * Approva il documento caricato.
     */
    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Rifiuta il documento caricato.
     */
    public function reject($notes)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes,
        ]);
    }
}
