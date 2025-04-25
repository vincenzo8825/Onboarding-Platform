<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponse extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal_note'
    ];

    /**
     * Gli attributi che dovrebbero essere castati.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_internal_note' => 'boolean'
    ];

    /**
     * Relazione con il ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relazione con l'utente che ha scritto la risposta
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se questa risposta è una nota interna (visibile solo agli admin)
     */
    public function isInternalNote(): bool
    {
        return $this->is_internal_note;
    }
}
