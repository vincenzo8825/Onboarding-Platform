<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\User;
use App\Models\UserQuiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    /**
     * Display the quiz for the user to take
     *
     * @param Course $course
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function take(Course $course)
    {
        $quiz = $course->quiz;

        if (!$quiz) {
            return redirect()->back()->with('error', 'Il corso non ha un quiz disponibile.');
        }

        // Controlla se l'utente ha già fatto il quiz e quanti tentativi ha fatto
        $userQuizzes = UserQuiz::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->get();

        $attemptsUsed = $userQuizzes->count();
        $canTakeQuiz = $attemptsUsed < $quiz->attempts_allowed;
        $bestScore = $userQuizzes->max('score') ?? 0;
        $hasPassed = $bestScore >= $quiz->passing_score;

        if (!$canTakeQuiz && !$hasPassed) {
            return redirect()->back()->with('error', 'Hai esaurito i tentativi disponibili per questo quiz.');
        }

        // Carica il quiz con le domande e risposte
        $quiz->load(['questions' => function($query) {
            $query->with('answers');
        }]);

        return view('employee.courses.quiz.take', compact('course', 'quiz', 'attemptsUsed', 'canTakeQuiz', 'bestScore', 'hasPassed'));
    }

    /**
     * Submit quiz answers and calculate results
     *
     * @param Request $request
     * @param Course $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request, Course $course)
    {
        $quiz = $course->quiz;

        if (!$quiz) {
            return redirect()->route('employee.courses.show', $course)
                ->with('error', 'Il corso non ha un quiz.');
        }

        // Valida che ci siano risposte
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
            'start_time' => 'nullable|string',
        ]);

        // Carica il quiz con le domande e risposte corrette
        $quiz->load(['questions' => function($query) {
            $query->with(['answers' => function($query) {
                $query->where('is_correct', true);
            }]);
        }]);

        // Calcola il punteggio
        $totalPoints = $quiz->questions->sum('points');
        $earnedPoints = 0;
        $answersLog = [];

        // Calcola il tempo impiegato se disponibile
        $timeSpent = 0;
        if (!empty($validated['start_time'])) {
            $startTime = Carbon::parse($validated['start_time']);
            $timeSpent = $startTime->diffInSeconds(Carbon::now());
        }

        foreach ($quiz->questions as $question) {
            $questionId = $question->id;
            $userAnswer = $validated['answers'][$questionId] ?? null;

            // Salva le risposte dell'utente per riferimento futuro
            $answersLog[$questionId] = [
                'question_text' => $question->text,
                'question_type' => $question->type,
                'points' => $question->points,
                'user_answer' => $userAnswer,
                'correct_answers' => $question->answers->pluck('text')->toArray(),
                'is_correct' => false
            ];

            // Controlla se la risposta è corretta in base al tipo di domanda
            $isCorrect = false;

            switch ($question->type) {
                case 'multiple_choice':
                    // Per domande a scelta multipla, tutte le risposte selezionate devono essere corrette
                    $userAnswers = is_array($userAnswer) ? $userAnswer : [];
                    $correctAnswerIds = $question->answers->where('is_correct', true)->pluck('id')->toArray();
                    $isCorrect = count($userAnswers) === count($correctAnswerIds) &&
                                empty(array_diff($userAnswers, $correctAnswerIds));
                    break;

                case 'single_choice':
                    // Per domande a scelta singola, la risposta deve corrispondere a una risposta corretta
                    $correctAnswerId = $question->answers->where('is_correct', true)->first()->id ?? null;
                    $isCorrect = $userAnswer == $correctAnswerId;
                    break;

                case 'true_false':
                    // Per domande vero/falso, la risposta deve essere corretta
                    $correctAnswer = $question->answers->where('is_correct', true)->first()->text ?? null;
                    $isVero = strtolower(trim($correctAnswer)) === 'vero' || strtolower(trim($correctAnswer)) === 'true';
                    $isCorrect = ($isVero && $userAnswer === 'true') || (!$isVero && $userAnswer === 'false');
                    break;

                case 'text':
                    // Per domande di testo, confronta con le risposte corrette
                    $userAnswerText = strtolower(trim($userAnswer));
                    foreach ($question->answers as $answer) {
                        if ($answer->is_correct && strtolower(trim($answer->text)) === $userAnswerText) {
                            $isCorrect = true;
                            break;
                        }
                    }
                    break;
            }

            // Aggiorna il punteggio se la risposta è corretta
            if ($isCorrect) {
                $earnedPoints += $question->points;
                $answersLog[$questionId]['is_correct'] = true;
            }
        }

        // Calcola il punteggio percentuale
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        // Salva il risultato del quiz
        $userQuiz = new UserQuiz([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'passed' => $passed,
            'answers_log' => json_encode($answersLog),
            'time_spent' => $timeSpent,
            'completed_at' => now(),
        ]);

        $userQuiz->save();

        // Se l'utente ha superato il quiz, aggiorna lo stato del corso
        if ($passed) {
            DB::table('course_user')
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
        }

        return redirect()->route('employee.courses.quiz.results', ['course' => $course, 'userQuiz' => $userQuiz])
            ->with('success', 'Quiz completato con successo.');
    }

    /**
     * Display the results of a completed quiz
     *
     * @param Course $course
     * @param UserQuiz $userQuiz
     * @return \Illuminate\View\View
     */
    public function results(Course $course, UserQuiz $userQuiz)
    {
        // Verifica che l'utente stia visualizzando il proprio quiz
        if ($userQuiz->user_id !== Auth::id()) {
            abort(403, 'Non sei autorizzato a visualizzare questi risultati.');
        }

        $quiz = $course->quiz;
        $answersLog = json_decode($userQuiz->answers_log, true);

        return view('employee.courses.quiz.results', compact('course', 'quiz', 'userQuiz', 'answersLog'));
    }
}
