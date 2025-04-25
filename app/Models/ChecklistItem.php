<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChecklistItem extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'checklist_id',
        'title',
        'description',
        'order',
        'requires_file',
        'requires_approval',
        'due_date',
        'is_mandatory'
    ];

    /**
     * Gli attributi che dovrebbero essere castati.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_file' => 'boolean',
        'requires_approval' => 'boolean',
        'is_mandatory' => 'boolean',
        'due_date' => 'date',
        'order' => 'integer'
    ];

    /**
     * Relazione con la checklist a cui appartiene l'elemento
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * Relazione con gli utenti che hanno questo elemento
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_checklist_items')
            ->withPivot(['is_completed', 'file_path', 'status', 'notes', 'completed_by', 'completed_at', 'approved_by', 'approved_at'])
            ->withTimestamps();
    }

    /**
     * Verifica se l'elemento richiede un file per essere completato
     */
    public function requiresFile(): bool
    {
        return $this->requires_file;
    }

    /**
     * Verifica se l'elemento richiede approvazione di un admin per essere completato
     */
    public function requiresApproval(): bool
    {
        return $this->requires_approval;
    }

    /**
     * Verifica se l'elemento è obbligatorio
     */
    public function isMandatory(): bool
    {
        return $this->is_mandatory;
    }

    /**
     * Verifica se l'elemento è completato da un utente specifico
     */
    public function isCompletedBy(User $user): bool
    {
        return $this->users()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('is_completed', true)
            ->exists();
    }

    /**
     * Ottieni lo stato di completamento per un utente specifico
     */
    public function getStatusFor(User $user): string
    {
        $pivot = $this->users()
            ->wherePivot('user_id', $user->id)
            ->first()?->pivot;

        return $pivot ? $pivot->status : 'pending';
    }
}
