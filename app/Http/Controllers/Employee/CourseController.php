<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Visualizza tutti i corsi assegnati all'utente
     */
    public function index()
    {
        $user = Auth::user();

        // Ottieni i corsi assegnati all'utente corrente con il loro stato
        $courses = $user->courses()
            ->withPivot(['status', 'due_date', 'assigned_at', 'started_at', 'completed_at'])
            ->paginate(10);

        // Per ciascun corso, controlla se ha un quiz associato
        $courses->each(function($course) use ($user) {
            if ($course->has_quiz && $course->quiz) {
                $quiz = $course->quiz;

                // Verifica se l'utente ha già fatto il quiz
                $userQuiz = UserQuiz::where('user_id', $user->id)
                    ->where('quiz_id', $quiz->id)
                    ->where('passed', true)
                    ->first();

                $course->quiz_passed = (bool) $userQuiz;
            } else {
                $course->quiz_passed = null;
            }
        });

        return view('employee.courses.index', compact('courses'));
    }

    /**
     * Visualizza un singolo corso
     */
    public function show(Course $course)
    {
        $user = Auth::user();

        // Verifica che l'utente sia assegnato al corso
        $courseUser = $user->courses()->wherePivot('course_id', $course->id)->first();

        if (!$courseUser) {
            return redirect()->route('employee.courses.index')
                ->with('error', 'Non sei autorizzato a visualizzare questo corso.');
        }

        // Ottieni i dettagli di completamento
        $completionStatus = $courseUser->pivot->status;
        $completionDate = $courseUser->pivot->completed_at;
        $dueDate = $courseUser->pivot->due_date;
        $startedDate = $courseUser->pivot->started_at;

        // Se il corso ha un quiz, ottieni il risultato del quiz dell'utente
        $quizResults = collect();
        if ($course->has_quiz && $course->quiz) {
            $quizResults = UserQuiz::where('user_id', $user->id)
                ->where('quiz_id', $course->quiz->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('employee.courses.show', compact(
            'course',
            'completionStatus',
            'completionDate',
            'dueDate',
            'startedDate',
            'quizResults'
        ));
    }

    /**
     * Imposta un corso come iniziato
     */
    public function start(Course $course)
    {
        $user = Auth::user();

        // Verifica che l'utente sia assegnato al corso
        $courseUser = $user->courses()->wherePivot('course_id', $course->id)->first();

        if (!$courseUser) {
            return redirect()->route('employee.courses.index')
                ->with('error', 'Non sei autorizzato ad accedere a questo corso.');
        }

        // Se il corso è già stato completato, non fare nulla
        if ($courseUser->pivot->status === 'completed') {
            return redirect()->route('employee.courses.show', $course)
                ->with('info', 'Questo corso è già stato completato.');
        }

        // Imposta lo stato del corso come "in corso" se non lo è già
        if ($courseUser->pivot->status !== 'in_progress') {
            DB::table('course_user')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->update([
                    'status' => 'in_progress',
                    'started_at' => now()
                ]);
        }

        return redirect()->route('employee.courses.show', $course)
            ->with('success', 'Corso avviato con successo.');
    }

    /**
     * Imposta un corso come completato
     */
    public function complete(Course $course)
    {
        $user = Auth::user();

        // Verifica che l'utente sia assegnato al corso
        $courseUser = $user->courses()->wherePivot('course_id', $course->id)->first();

        if (!$courseUser) {
            return redirect()->route('employee.courses.index')
                ->with('error', 'Non sei autorizzato ad accedere a questo corso.');
        }

        // Se il corso è già stato completato, non fare nulla
        if ($courseUser->pivot->status === 'completed') {
            return redirect()->route('employee.courses.show', $course)
                ->with('info', 'Questo corso è già stato completato.');
        }

        // Se il corso ha un quiz, verifica che l'utente lo abbia superato
        if ($course->has_quiz && $course->quiz) {
            $quizPassed = UserQuiz::where('user_id', $user->id)
                ->where('quiz_id', $course->quiz->id)
                ->where('passed', true)
                ->exists();

            if (!$quizPassed) {
                return redirect()->route('employee.courses.show', $course)
                    ->with('error', 'Devi superare il quiz per completare questo corso.');
            }
        }

        // Imposta lo stato del corso come "completato"
        DB::table('course_user')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        return redirect()->route('employee.courses.show', $course)
            ->with('success', 'Corso completato con successo!');
    }
}
