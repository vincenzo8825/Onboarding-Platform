<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'content_type',
        'content',
        'file_path',
        'duration_minutes',
        'has_quiz',
        'created_by',
    ];

    /**
     * Gli attributi che dovrebbero essere castati.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_quiz' => 'boolean',
    ];

    /**
     * Relazione con il creatore del corso
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relazione con gli utenti assegnati al corso
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['assigned_at', 'due_date', 'status', 'started_at', 'completed_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Relazione con il quiz associato al corso
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Ottiene gli utenti che hanno completato il corso
     */
    public function completedByUsers()
    {
        return $this->users()->wherePivot('status', 'completed');
    }

    /**
     * Ottiene gli utenti che hanno il corso in corso
     */
    public function inProgressByUsers()
    {
        return $this->users()->wherePivot('status', 'in_progress');
    }

    /**
     * Ottiene gli utenti che non hanno ancora iniziato il corso
     */
    public function notStartedByUsers()
    {
        return $this->users()->wherePivot('status', 'not_started');
    }

    /**
     * Calcola la percentuale di completamento per un utente specifico
     */
    public function completionPercentage(User $user)
    {
        $courseUser = $this->users()->where('user_id', $user->id)->first();

        if (!$courseUser) {
            return 0;
        }

        if ($courseUser->pivot->status === 'completed') {
            return 100;
        }

        if ($courseUser->pivot->status === 'not_started') {
            return 0;
        }

        // Se il corso ha un quiz, controllare se è stato superato
        if ($this->has_quiz && $this->quiz) {
            $userQuiz = UserQuiz::where('user_id', $user->id)
                ->where('quiz_id', $this->quiz->id)
                ->where('passed', true)
                ->first();

            if ($userQuiz) {
                return 100;
            }

            return 50; // In corso ma non ha ancora superato il quiz
        }

        return 50; // In corso, ma non ci sono altri criteri per misurare l'avanzamento
    }
}
