<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'course_id',
        'passing_score',
        'time_limit_minutes',
        'attempts_allowed',
        'created_by',
    ];

    /**
     * Relazione con il corso
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relazione con le domande
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Relazione con i quiz completati dagli utenti
     */
    public function userQuizzes()
    {
        return $this->hasMany(UserQuiz::class);
    }

    /**
     * Relazione con il creatore del quiz
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
