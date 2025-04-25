<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'deadline_days',
        'is_required',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'onboarding_task_user')
            ->withPivot('completed_at', 'notes');
    }
}