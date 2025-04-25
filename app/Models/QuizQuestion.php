<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'text',
        'type',
        'points',
        'position',
    ];

    /**
     * Relazione con il quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relazione con le risposte
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }
}
