<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'position',
        'phone',
        'address',
        'emergency_contact',
        'bio',
        'profile_photo',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Check if the user has the given role.
     *
     * @param string $role
     * @return bool
     */
    // Add this method to your User model
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Removed duplicate hasRole method

    /**
     * Verifica se l'utente ha il ruolo di admin
     */
    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    /**
     * Verifica se l'utente ha il ruolo di dipendente
     */
    public function isEmployee(): bool
    {
        return $this->roles()->where('name', 'employee')->exists();
    }

    public function isNewHire()
    {
        return $this->hasRole('new_hire');
    }

    public function onboardingProgress()
    {
        // Calcola la percentuale di completamento dell'onboarding
        $totalTasks = OnboardingTask::count();
        if ($totalTasks === 0) {
            return 100; // Se non ci sono task, consideriamo l'onboarding completato
        }

        $completedTasks = $this->completedTasks()->count();
        return ($completedTasks / $totalTasks) * 100;
    }

    /**
     * Alias di onboardingProgress per compatibilità
     */
    public function getOnboardingProgress()
    {
        return $this->onboardingProgress();
    }

    public function completedTasks()
    {
        return $this->belongsToMany(OnboardingTask::class, 'onboarding_task_user')
            ->withPivot('completed_at')
            ->wherePivotNotNull('completed_at');
    }

    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', (array)$roles)->exists();
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_user')
            ->withTimestamps();
    }

    /**
     * Relazione con i corsi assegnati all'utente
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['assigned_at', 'due_date', 'status', 'started_at', 'completed_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Relazione con i quiz completati dall'utente
     */
    public function quizzes()
    {
        return $this->hasMany(UserQuiz::class);
    }

    /**
     * Verifica se l'utente ha completato un corso specifico
     */
    public function hasCompletedCourse(Course $course)
    {
        return $this->courses()
            ->where('course_id', $course->id)
            ->wherePivot('status', 'completed')
            ->exists();
    }

    /**
     * Verifica se l'utente ha superato un quiz specifico
     */
    public function hasPassedQuiz(Quiz $quiz)
    {
        return $this->quizzes()
            ->where('quiz_id', $quiz->id)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Ottieni i corsi completati dall'utente
     */
    public function completedCourses()
    {
        return $this->courses()->wherePivot('status', 'completed');
    }

    /**
     * Ottieni i corsi in corso dall'utente
     */
    public function inProgressCourses()
    {
        return $this->courses()->wherePivot('status', 'in_progress');
    }

    /**
     * Ottieni i corsi non ancora iniziati dall'utente
     */
    public function notStartedCourses()
    {
        return $this->courses()->wherePivot('status', 'not_started');
    }

    /**
     * Relazione con gli elementi della checklist assegnati all'utente
     */
    public function checklistItems()
    {
        return $this->belongsToMany(ChecklistItem::class, 'user_checklist_items')
            ->withPivot(['is_completed', 'file_path', 'status', 'notes', 'completed_by', 'completed_at', 'approved_by', 'approved_at'])
            ->withTimestamps();
    }

    /**
     * Relazione con i badge guadagnati dall'utente
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at', 'awarded_by', 'award_reason', 'is_featured')
            ->withTimestamps();
    }

    /**
     * Ottieni tutte le assegnazioni di badge per questo utente.
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Ottieni i badge assegnati da questo utente.
     */
    public function awardedBadges()
    {
        return $this->hasMany(UserBadge::class, 'awarded_by');
    }

    /**
     * Get the document requests made by this admin user.
     */
    public function documentRequestsMade()
    {
        return $this->hasMany(DocumentRequest::class, 'admin_id');
    }

    /**
     * Get the document requests received by this employee.
     */
    public function documentRequestsReceived()
    {
        return $this->hasMany(DocumentRequest::class, 'employee_id');
    }

    /**
     * Get all events the user is registered to participate in
     */
    public function eventsParticipating()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot('registered_at', 'attended', 'feedback')
            ->withTimestamps();
    }

    /**
     * Get all tasks assigned to the user
     */
    public function tasks()
    {
        return $this->belongsToMany(OnboardingTask::class, 'onboarding_task_user')
            ->withPivot('completed_at', 'notes');
    }

    /**
     * Get all events the user has attended
     */
    public function eventsAttended()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot('status', 'feedback', 'rating', 'attended')
            ->wherePivot('attended', true)
            ->withTimestamps();
    }
}
