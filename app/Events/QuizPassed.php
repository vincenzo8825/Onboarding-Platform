<?php

namespace App\Events;

use App\Models\User;
use App\Models\Quiz;
use App\Models\UserQuiz;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizPassed
{
    use Dispatchable, SerializesModels;

    /**
     * L'utente che ha superato il quiz
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Il quiz superato
     *
     * @var \App\Models\Quiz
     */
    public $quiz;

    /**
     * I dati del tentativo del quiz
     *
     * @var \App\Models\UserQuiz
     */
    public $userQuiz;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Quiz $quiz, UserQuiz $userQuiz)
    {
        $this->user = $user;
        $this->quiz = $quiz;
        $this->userQuiz = $userQuiz;
    }
}
