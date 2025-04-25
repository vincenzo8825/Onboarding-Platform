<?php

namespace App\Events;

use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * L'utente che ha completato il corso
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Il corso completato
     *
     * @var \App\Models\Course
     */
    public $course;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Course $course)
    {
        $this->user = $user;
        $this->course = $course;
    }
}
