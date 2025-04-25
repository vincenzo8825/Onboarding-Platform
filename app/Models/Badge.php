<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'xp_value',
        'is_active',
        'difficulty',
        'category',
        'criteria',
        'type',
        'image_path',
        'requirements'
    ];

    /**
     * Gli attributi che dovrebbero essere castati a tipi nativi.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'xp_value' => 'integer',
    ];

    /**
     * Ottieni gli utenti che hanno guadagnato questo badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at', 'awarded_by', 'award_reason', 'is_featured')
            ->withTimestamps();
    }

    /**
     * Ottieni tutte le assegnazioni di questo badge agli utenti.
     */
    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Verifica se il badge è di tipo achievement (raggiungimento)
     */
    public function isAchievement(): bool
    {
        return $this->type === 'achievement';
    }

    /**
     * Verifica se il badge è di tipo completion (completamento corso)
     */
    public function isCompletion(): bool
    {
        return $this->type === 'completion';
    }

    /**
     * Verifica se il badge è di tipo special (assegnato manualmente)
     */
    public function isSpecial(): bool
    {
        return $this->type === 'special';
    }
}
