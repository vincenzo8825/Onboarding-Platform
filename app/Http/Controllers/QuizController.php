<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::all();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    /**
     * Crea un nuovo quiz per un corso
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'attempts_allowed' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,single_choice,true_false,text',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required_unless:questions.*.type,text|array|min:2',
            'questions.*.answers.*.text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required_unless:questions.*.type,text|boolean',
        ]);

        // Creare il quiz
        $quiz = new Quiz([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'course_id' => $course->id,
            'passing_score' => $validated['passing_score'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'attempts_allowed' => $validated['attempts_allowed'],
            'created_by' => Auth::id(),
        ]);

        $quiz->save();

        // Creare le domande e risposte
        foreach ($validated['questions'] as $questionData) {
            $question = new QuizQuestion([
                'quiz_id' => $quiz->id,
                'text' => $questionData['text'],
                'type' => $questionData['type'],
                'points' => $questionData['points']
            ]);

            $question->save();

            // Creare le risposte (se non è una domanda di testo)
            if ($questionData['type'] !== 'text' && isset($questionData['answers'])) {
                foreach ($questionData['answers'] as $answerData) {
                    $answer = new QuizAnswer([
                        'question_id' => $question->id,
                        'text' => $answerData['text'],
                        'is_correct' => $answerData['is_correct'] ?? false
                    ]);

                    $answer->save();
                }
            }
        }

        // Aggiorna il corso per indicare che ha un quiz
        $course->update(['has_quiz' => true]);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Quiz creato con successo.');
    }

    /**
     * Mostra quiz per prendere un test
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
     * Elabora le risposte al quiz
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

        foreach ($quiz->questions as $question) {
            $questionId = $question->id;
            $userAnswer = $validated['answers'][$questionId] ?? null;

            // Salva le risposte dell'utente per riferimento futuro
            $answersLog[$questionId] = [
                'question_text' => $question->text,
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
                    $correctAnswerIds = $question->answers->pluck('id')->toArray();
                    $isCorrect = count($userAnswers) === count($correctAnswerIds) &&
                                 empty(array_diff($userAnswers, $correctAnswerIds));
                    break;

                case 'single_choice':
                case 'true_false':
                    // Per domande a scelta singola, la risposta deve corrispondere a una risposta corretta
                    $correctAnswerId = $question->answers->first()->id ?? null;
                    $isCorrect = $userAnswer == $correctAnswerId;
                    break;

                case 'text':
                    // Per domande di testo, richiede revisione manuale (impostato sempre come non corretto per ora)
                    $isCorrect = false;
                    // Si potrebbe implementare un sistema di parole chiave o revisione manuale in futuro
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
     * Mostra i risultati di un quiz
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

    /**
     * Mostra il form per modificare un quiz
     */
    public function edit(Course $course)
    {
        $quiz = $course->quiz;

        if (!$quiz) {
            return redirect()->route('admin.courses.show', $course)
                ->with('error', 'Il corso non ha un quiz da modificare.');
        }

        // Carica il quiz con le domande e risposte
        $quiz->load(['questions' => function($query) {
            $query->with('answers')->orderBy('position');
        }]);

        return view('admin.courses.quiz.edit', compact('course', 'quiz'));
    }

    /**
     * Aggiorna un quiz esistente
     */
    public function update(Request $request, Course $course)
    {
        $quiz = $course->quiz;

        if (!$quiz) {
            return redirect()->route('admin.courses.show', $course)
                ->with('error', 'Il corso non ha un quiz da aggiornare.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'attempts_allowed' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|exists:quiz_questions,id',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,single_choice,true_false,text',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required_unless:questions.*.type,text|array|min:2',
            'questions.*.answers.*.id' => 'nullable|exists:quiz_answers,id',
            'questions.*.answers.*.text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required_unless:questions.*.type,text|boolean',
        ]);

        // Aggiorna il quiz
        $quiz->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'passing_score' => $validated['passing_score'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'attempts_allowed' => $validated['attempts_allowed'],
        ]);

        // Array per tenere traccia delle domande e risposte da conservare
        $keepQuestionIds = [];
        $keepAnswerIds = [];

        // Aggiorna o crea domande e risposte
        foreach ($validated['questions'] as $index => $questionData) {
            if (isset($questionData['id'])) {
                // Aggiorna domande esistenti
                $question = QuizQuestion::find($questionData['id']);
                $question->update([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'points' => (int)$questionData['points'],
                    'position' => (int)$index + 1,
                ]);
                $keepQuestionIds[] = $question->id;
            } else {
                // Crea nuove domande
                $question = new QuizQuestion([
                    'quiz_id' => $quiz->id,
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'points' => (int)$questionData['points'],
                    'position' => (int)$index + 1,
                ]);
                $question->save();
                $keepQuestionIds[] = $question->id;
            }

            // Gestisci le risposte se non è una domanda di testo
            if ($questionData['type'] !== 'text' && isset($questionData['answers'])) {
                foreach ($questionData['answers'] as $answerData) {
                    if (isset($answerData['id'])) {
                        // Aggiorna risposte esistenti
                        $answer = QuizAnswer::find($answerData['id']);
                        $answer->update([
                            'text' => $answerData['text'],
                            'is_correct' => $answerData['is_correct'] ?? false,
                        ]);
                        $keepAnswerIds[] = $answer->id;
                    } else {
                        // Crea nuove risposte
                        $answer = new QuizAnswer([
                            'question_id' => $question->id,
                            'text' => $answerData['text'],
                            'is_correct' => $answerData['is_correct'] ?? false,
                        ]);
                        $answer->save();
                        $keepAnswerIds[] = $answer->id;
                    }
                }
            }
        }

        // Elimina domande e risposte rimosse
        QuizAnswer::whereNotIn('id', $keepAnswerIds)
            ->whereIn('question_id', $quiz->questions->pluck('id'))
            ->delete();

        QuizQuestion::whereNotIn('id', $keepQuestionIds)
            ->where('quiz_id', $quiz->id)
            ->delete();

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Quiz aggiornato con successo.');
    }

    // Implementa gli altri metodi (show, edit, update, destroy)
}
