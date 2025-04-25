<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'badge_id',
        'awarded_at',
        'awarded_by',
        'award_reason',
        'is_featured'
    ];

    /**
     * Gli attributi che dovrebbero essere castati a tipi nativi.
     *
     * @var array
     */
    protected $casts = [
        'awarded_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Ottieni l'utente proprietario di questo badge.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ottieni il badge associato.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Ottieni l'utente che ha assegnato il badge.
     */
    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}
