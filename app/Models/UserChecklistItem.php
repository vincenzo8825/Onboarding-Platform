<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChecklistItem extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'checklist_item_id',
        'is_completed',
        'file_path',
        'status',
        'notes',
        'completed_by',
        'completed_at',
        'approved_by',
        'approved_at'
    ];

    /**
     * Gli attributi che dovrebbero essere castati.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    /**
     * Relazione con l'utente
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione con l'elemento della checklist
     */
    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    /**
     * Relazione con l'utente che ha completato l'elemento
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Relazione con l'utente che ha approvato l'elemento
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Verifica se l'elemento è completato
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * Verifica se l'elemento ha un file allegato
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Verifica se l'elemento è in attesa di revisione
     */
    public function needsReview(): bool
    {
        return $this->status === 'needs_review';
    }

    /**
     * Verifica se l'elemento è stato rifiutato
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Verifica se l'elemento è approvato
     */
    public function isApproved(): bool
    {
        return $this->status === 'completed' && $this->approved_at !== null;
    }
}
