<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Filtri
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Course::select('category')->distinct()->pluck('category');

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:50',
            'content_type' => 'required|in:pdf,video,link,text',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'duration_minutes' => 'nullable|integer|min:1',
            'has_quiz' => 'boolean',
        ]);

        $course = new Course($validated);
        $course->created_by = Auth::id();

        // Gestione del file se caricato
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('courses', 'public');
            $course->file_path = $path;
        }

        $course->save();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso creato con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $assignedUsers = $course->users()->paginate(15);
        return view('admin.courses.show', compact('course', 'assignedUsers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:50',
            'content_type' => 'required|in:pdf,video,link,text',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'has_quiz' => 'boolean',
        ]);

        // Aggiornamento dei campi validati
        $course->fill($validated);

        // Gestione del file se caricato un nuovo file
        if ($request->hasFile('file')) {
            // Elimina il vecchio file
            if ($course->file_path) {
                Storage::disk('public')->delete($course->file_path);
            }

            // Carica il nuovo file
            $path = $request->file('file')->store('courses', 'public');
            $course->file_path = $path;
        }

        $course->save();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso aggiornato con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Elimina il file se esiste
        if ($course->file_path) {
            Storage::disk('public')->delete($course->file_path);
        }

        // Elimina il corso e tutte le relazioni associate
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso eliminato con successo');
    }

    /**
     * Assegna corso a uno o più utenti
     */
    public function assignUsers(Request $request, Course $course)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        $course->users()->syncWithoutDetaching(
            collect($validated['user_ids'])->mapWithKeys(function ($userId) use ($validated) {
                return [$userId => [
                    'assigned_at' => now(),
                    'due_date' => $validated['due_date'] ?? null,
                    'status' => 'not_started'
                ]];
            })
        );

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Corso assegnato a ' . count($validated['user_ids']) . ' utenti con successo');
    }

    /**
     * Mostra form per la creazione di un quiz per questo corso
     */
    public function createQuiz(Course $course)
    {
        return view('admin.courses.quiz.create', compact('course'));
    }
}
