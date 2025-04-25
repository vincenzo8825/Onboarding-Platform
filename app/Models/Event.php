<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'type',
        'max_participants',
        'created_by',
        'is_mandatory',
        'is_online',
        'online_link',
        'registration_deadline',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_mandatory' => 'boolean',
        'is_online' => 'boolean',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users who are participating in the event.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('status', 'feedback', 'rating', 'attended')
            ->withTimestamps();
    }

    /**
     * Get the users who have attended the event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('status', 'feedback', 'rating', 'attended')
            ->wherePivot('attended', true)
            ->withTimestamps();
    }

    /**
     * Check if the event is full.
     */
    public function isFull()
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->participants()->count() >= $this->max_participants;
    }

    /**
     * Check if the event is in the past.
     */
    public function isPast()
    {
        return $this->end_date->isPast();
    }

    /**
     * Check if the event is in progress.
     */
    public function isInProgress()
    {
        $now = now();
        return $this->start_date->lte($now) && $this->end_date->gte($now);
    }

    /**
     * Check if the event is in the future.
     */
    public function isFuture()
    {
        return $this->start_date->isFuture();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }
}
