<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs.
     */
    public function index()
    {
        $programs = Program::withCount('users')->latest()->paginate(10);
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $program = Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programma creato con successo.');
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program)
    {
        $program->load(['users']);

        // Get users not in this program for the "Add Users" functionality
        $availableUsers = User::whereDoesntHave('programs', function($query) use ($program) {
            $query->where('program_id', $program->id);
        })->get();

        return view('admin.programs.show', compact('program', 'availableUsers'));
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified program in storage.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programma aggiornato con successo.');
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy(Program $program)
    {
        // Check if any users are enrolled in this program
        if ($program->users()->count() > 0) {
            return redirect()->route('admin.programs.index')
                ->with('error', 'Non è possibile eliminare un programma che ha utenti iscritti.');
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programma eliminato con successo.');
    }

    /**
     * Add users to a program.
     */
    public function addUsers(Request $request, Program $program)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $program->users()->attach($request->user_ids);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Utenti aggiunti al programma con successo.');
    }

    /**
     * Remove a user from a program.
     */
    public function removeUser(Request $request, Program $program, User $user)
    {
        $program->users()->detach($user->id);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Utente rimosso dal programma con successo.');
    }
}
