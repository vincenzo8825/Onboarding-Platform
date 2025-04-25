<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $programs = $user->programs;

        return view('employee.programs.index', compact('programs'));
    }

    public function show(Program $program)
    {
        $user = Auth::user();

        // Verifica che l'utente sia assegnato al programma
        $userProgram = $user->programs->where('id', $program->id)->first();

        if (!$userProgram) {
            return redirect()->route('employee.programs.index')
                ->with('error', 'Non sei autorizzato a visualizzare questo programma.');
        }

        // Carica le relazioni del programma necessarie per la vista
        $program->load(['courses', 'checklists.items', 'events']);

        // Calcola il progresso dell'utente per questo programma
        // Verificare se l'utente ha completato i corsi associati a questo programma
        $userCompletedCoursesCount = 0;

        if ($program->courses->count() > 0) {
            $programCourseIds = $program->courses->pluck('id')->toArray();
            $userCompletedCourses = $user->completedCourses()
                ->whereIn('course_id', $programCourseIds)
                ->get();

            $userCompletedCoursesCount = $userCompletedCourses->count();
        }

        $totalCourses = $program->courses->count();

        // Aggiunge informazioni sul progresso che saranno utilizzate nella vista
        $progress = [
            'completedItems' => $userCompletedCoursesCount,
            'totalItems' => max($totalCourses, 1),  // Evita divisione per zero
            'percentage' => $totalCourses > 0
                ? ($userCompletedCoursesCount / $totalCourses) * 100
                : 0
        ];

        return view('employee.programs.show', compact('program', 'progress'));
    }
}
