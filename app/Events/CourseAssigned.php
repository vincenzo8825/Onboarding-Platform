<?php

namespace App\Events;

use App\Models\Course;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $course;
    public $user;
    public $assignedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Course $course, User $user, User $assignedBy)
    {
        $this->course = $course;
        $this->user = $user;
        $this->assignedBy = $assignedBy;
    }
}