<?php

namespace App\Http\Controllers;

use App\Events\ChecklistAssigned;
use App\Events\ChecklistItemStatusUpdated;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\User;
use App\Notifications\ChecklistAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Checklist::query();

        // Filtri
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('assignable_to')) {
            $query->where('assignable_to', $request->assignable_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $checklists = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.checklists.index', compact('checklists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.checklists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
            'assignable_to' => ['required', Rule::in(['all', 'admin', 'employee'])]
        ]);

        $validated['created_by'] = Auth::id();

        $checklist = Checklist::create($validated);

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Checklist creata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Checklist $checklist)
    {
        $checklist->load('items', 'creator');

        return view('admin.checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checklist $checklist)
    {
        return view('admin.checklists.edit', compact('checklist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
            'assignable_to' => ['required', Rule::in(['all', 'admin', 'employee'])]
        ]);

        $checklist->update($validated);

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Checklist aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checklist $checklist)
    {
        $checklist->delete();

        return redirect()->route('admin.checklists.index')
            ->with('success', 'Checklist eliminata con successo');
    }

    /**
     * Assegna una checklist a un utente
     */
    public function assignToUser(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);

        // Prendi tutti gli elementi della checklist
        $items = $checklist->items;

        // Verifica se la checklist ha elementi
        if ($items->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Impossibile assegnare la checklist perché non contiene elementi');
        }

        // Crea record nella tabella pivot per ogni elemento e l'utente selezionato
        foreach ($items as $item) {
            $item->users()->syncWithoutDetaching([
                $validated['user_id'] => [
                    'is_completed' => false,
                    'status' => 'pending'
                ]
            ]);
        }

        // Invia notifica all'utente
        $user = User::find($validated['user_id']);
        $user->notify(new ChecklistAssignedNotification($checklist, Auth::user()));

        return redirect()->back()
            ->with('success', 'Checklist assegnata con successo');
    }

    /**
     * Assegna una checklist a più utenti
     */
    public function assignUsers(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        foreach ($validated['user_ids'] as $userId) {
            // Verifica se l'utente ha già questa checklist
            if (!$checklist->users()->where('user_id', $userId)->exists()) {
                $checklist->users()->attach($userId, [
                    'assigned_at' => now(),
                    'due_date' => $validated['due_date'] ?? null,
                ]);

                // Crea record per ogni elemento della checklist
                foreach ($checklist->items as $item) {
                    $checklist->userChecklistItems()->create([
                        'user_id' => $userId,
                        'checklist_item_id' => $item->id,
                        'status' => 'pending',
                    ]);
                }

                // Emetti l'evento per la notifica
                $user = User::find($userId);
                event(new ChecklistAssigned($checklist, $user, auth()->user()));

                // Invia direttamente la notifica per garantire che funzioni
                $user->notify(new \App\Notifications\ChecklistAssignedNotification($checklist, auth()->user()));
            }
        }

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Checklist assegnata con successo');
    }

    /**
     * Aggiorna lo stato di un elemento della checklist
     */
    public function updateItemStatus(Request $request, ChecklistItem $item)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed,needs_review',
            'notes' => 'nullable|string',
        ]);

        $userItem = $item->userItems()->where('user_id', $validated['user_id'])->first();

        if ($userItem) {
            $oldStatus = $userItem->status;
            $userItem->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $userItem->notes,
                'updated_at' => now(),
            ]);

            // Emetti l'evento per la notifica
            $user = User::find($validated['user_id']);
            event(new ChecklistItemStatusUpdated($item, $user, auth()->user(), $oldStatus, $validated['status']));
        }

        return redirect()->back()->with('success', 'Stato dell\'elemento aggiornato con successo');
    }

    /**
     * Assegna una checklist a più utenti
     */
    public function assignToMultipleUsers(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id']
        ]);

        // Prendi tutti gli elementi della checklist
        $items = $checklist->items;

        // Verifica se la checklist ha elementi
        if ($items->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Impossibile assegnare la checklist perché non contiene elementi');
        }

        // Per ogni utente selezionato
        foreach ($validated['user_ids'] as $userId) {
            // Crea record nella tabella pivot per ogni elemento
            foreach ($items as $item) {
                $item->users()->syncWithoutDetaching([
                    $userId => [
                        'is_completed' => false,
                        'status' => 'pending'
                    ]
                ]);
            }

            // Invia notifica all'utente
            $user = User::find($userId);
            $user->notify(new ChecklistAssignedNotification($checklist, Auth::user()));
        }

        return redirect()->back()
            ->with('success', 'Checklist assegnata a ' . count($validated['user_ids']) . ' utenti con successo');
    }
}
