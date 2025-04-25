<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checklist extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'is_active',
        'is_default',
        'assignable_to'
    ];

    /**
     * Gli attributi che dovrebbero essere castati.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    /**
     * Relazione con l'utente che ha creato la checklist
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relazione con gli elementi della checklist
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }

    /**
     * Verifica se la checklist è attiva
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Verifica se la checklist è assegnata di default ai nuovi utenti
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Verifica se la checklist è assegnabile a tutti
     */
    public function isAssignableToAll(): bool
    {
        return $this->assignable_to === 'all';
    }

    /**
     * Verifica se la checklist è assegnabile solo ad admin
     */
    public function isAssignableToAdmin(): bool
    {
        return $this->assignable_to === 'admin';
    }

    /**
     * Verifica se la checklist è assegnabile solo a dipendenti
     */
    public function isAssignableToEmployee(): bool
    {
        return $this->assignable_to === 'employee';
    }

    /**
     * Verifica se la checklist è assegnabile a un utente specifico
     */
    public function isAssignableTo(User $user): bool
    {
        if ($this->isAssignableToAll()) {
            return true;
        }

        if ($this->isAssignableToAdmin() && $user->isAdmin()) {
            return true;
        }

        if ($this->isAssignableToEmployee() && $user->isEmployee()) {
            return true;
        }

        return false;
    }

    /**
     * Ottieni il numero totale di elementi nella checklist
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items()->count();
    }
}
