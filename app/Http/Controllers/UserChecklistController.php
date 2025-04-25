<?php

namespace App\Http\Controllers;

use App\Events\ChecklistCompleted;
use App\Events\ChecklistItemCompleted;
use App\Models\ChecklistItem;
use App\Models\User;
use App\Models\UserChecklistItem;
use App\Models\Checklist;
use App\Notifications\ChecklistCompletedNotification;
use App\Notifications\ChecklistItemCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class UserChecklistController extends Controller
{
    /**
     * Mostra le checklist assegnate all'utente corrente
     */
    public function index()
    {
        $user = Auth::user();

        // Raggruppa gli elementi per checklist
        $checklistItems = $user->checklistItems()->with('checklist')->get();

        $checklists = [];
        foreach ($checklistItems as $item) {
            $checklistId = $item->checklist->id;

            if (!isset($checklists[$checklistId])) {
                $checklists[$checklistId] = [
                    'checklist' => $item->checklist,
                    'items' => [],
                    'total' => 0,
                    'completed' => 0
                ];
            }

            $checklists[$checklistId]['items'][] = $item;
            $checklists[$checklistId]['total']++;

            if ($item->pivot->is_completed) {
                $checklists[$checklistId]['completed']++;
            }
        }

        return view('employee.checklists.index', compact('checklists'));
    }

    /**
     * Mostra i dettagli di una checklist specifica
     */
    public function show(int $checklistId)
    {
        $user = Auth::user();

        // Ottieni tutti gli elementi della checklist assegnati all'utente
        $items = $user->checklistItems()
            ->with('checklist')
            ->whereHas('checklist', function($query) use ($checklistId) {
                $query->where('id', $checklistId);
            })
            ->orderBy('order')
            ->get();

        if ($items->isEmpty()) {
            abort(404, 'Checklist non trovata o non assegnata a questo utente');
        }

        $checklist = $items->first()->checklist;

        // Calcola il progresso
        $totalItems = $items->count();
        $completedItems = $items->filter(function($item) {
            return $item->pivot->is_completed;
        })->count();

        $progress = $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0;

        return view('employee.checklists.show', compact('checklist', 'items', 'progress'));
    }

    /**
     * Marca un elemento della checklist come completato
     */
    public function markAsCompleted(Request $request, ChecklistItem $item)
    {
        $user = Auth::user();

        // Verifica che l'elemento sia assegnato all'utente
        $pivot = $user->checklistItems()->where('checklist_item_id', $item->id)->first();

        if (!$pivot) {
            abort(403, 'Questo elemento non è assegnato all\'utente corrente');
        }

        $data = [
            'is_completed' => true,
            'completed_by' => $user->id,
            'completed_at' => now(),
        ];

        // Se l'elemento richiede l'approvazione, imposta lo stato su 'needs_review'
        if ($item->requires_approval) {
            $data['status'] = 'needs_review';
        } else {
            $data['status'] = 'completed';
        }

        // Se l'utente ha incluso delle note
        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        // Se l'elemento richiede un file e l'utente ha caricato un file
        if ($item->requires_file && $request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('checklist_files', $fileName, 'public');
            $data['file_path'] = $filePath;
        }

        // Aggiorna il record pivot
        $user->checklistItems()->updateExistingPivot($item->id, $data);

        // Trigger dell'evento quando un elemento viene completato
        event(new ChecklistItemCompleted($user, $item));

        // Notifica agli admin che un elemento è stato completato e richiede approvazione
        if ($item->requires_approval) {
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new ChecklistItemCompletedNotification($user, $item));
        }

        // Verifica se tutti gli elementi della checklist sono completati
        $checklist = $item->checklist;
        $totalChecklistItems = $checklist->items()->count();
        $completedItems = $user->checklistItems()
            ->whereIn('checklist_item_id', $checklist->items()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        // Se tutti gli elementi sono completati, notifica il completamento della checklist
        if ($totalChecklistItems > 0 && $completedItems >= $totalChecklistItems) {
            // Trigger dell'evento quando una checklist viene completata
            event(new ChecklistCompleted($user, $checklist));

            // Notifica agli admin
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new ChecklistCompletedNotification($user, $checklist));
        }

        return redirect()->back()->with('success', 'Elemento completato con successo');
    }

    /**
     * Approva un elemento della checklist completato
     */
    public function approve(Request $request, User $user, ChecklistItem $item)
    {
        // Solo gli admin possono approvare
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo gli admin possono approvare gli elementi della checklist');
        }

        // Verifica che l'elemento sia assegnato all'utente
        $pivot = $user->checklistItems()->where('checklist_item_id', $item->id)->first();

        if (!$pivot) {
            abort(404, 'Elemento non trovato o non assegnato all\'utente specificato');
        }

        // Aggiorna lo stato dell'elemento
        $notes = $request->filled('notes') ? $request->notes : null;
        $user->checklistItems()->updateExistingPivot($item->id, [
            'status' => 'completed',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $request->filled('notes') ? $pivot->pivot->notes . "\n\nNota dell'approvatore: " . $notes : $pivot->pivot->notes
        ]);

        // Notifica il dipendente dell'approvazione
        $user->notify(new \App\Notifications\ChecklistItemStatusUpdateNotification(
            Auth::user(),
            $item,
            'completed',
            $notes
        ));

        // Verifica se tutti gli elementi della checklist sono completati e approvati
        $checklist = $item->checklist;
        $totalChecklistItems = $checklist->items()->count();
        $completedItems = $user->checklistItems()
            ->whereIn('checklist_item_id', $checklist->items()->pluck('id'))
            ->whereIn('status', ['completed'])
            ->count();

        // Se tutti gli elementi sono completati, notifica il completamento della checklist
        if ($totalChecklistItems > 0 && $completedItems >= $totalChecklistItems) {
            // Trigger dell'evento quando una checklist viene completata
            event(new ChecklistCompleted($user, $checklist));

            // Notifica agli admin
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new ChecklistCompletedNotification($user, $checklist));
        }

        return redirect()->back()->with('success', 'Elemento approvato con successo');
    }

    /**
     * Rifiuta un elemento della checklist
     */
    public function reject(Request $request, User $user, ChecklistItem $item)
    {
        // Solo gli admin possono rifiutare
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo gli admin possono rifiutare gli elementi della checklist');
        }

        // Verifica che l'elemento sia assegnato all'utente
        $pivot = $user->checklistItems()->where('checklist_item_id', $item->id)->first();

        if (!$pivot) {
            abort(404, 'Elemento non trovato o non assegnato all\'utente specificato');
        }

        // Valida la richiesta
        $request->validate([
            'notes' => ['required', 'string', 'max:1000']
        ]);

        $notes = $request->notes;

        // Aggiorna lo stato dell'elemento
        $user->checklistItems()->updateExistingPivot($item->id, [
            'status' => 'rejected',
            'is_completed' => false,
            'notes' => $pivot->pivot->notes . "\n\nMotivo del rifiuto: " . $notes
        ]);

        // Notifica il dipendente del rifiuto
        $user->notify(new \App\Notifications\ChecklistItemStatusUpdateNotification(
            Auth::user(),
            $item,
            'rejected',
            $notes
        ));

        return redirect()->back()->with('success', 'Elemento rifiutato con successo');
    }

    /**
     * Mostra le checklist assegnate a un utente specifico (vista admin)
     */
    public function adminIndex(User $user)
    {
        // Raggruppa gli elementi per checklist
        $checklistItems = $user->checklistItems()->with('checklist')->get();

        $checklists = [];
        foreach ($checklistItems as $item) {
            $checklistId = $item->checklist->id;

            if (!isset($checklists[$checklistId])) {
                $checklists[$checklistId] = [
                    'checklist' => $item->checklist,
                    'items' => [],
                    'total' => 0,
                    'completed' => 0,
                    'needs_review' => 0,
                    'rejected' => 0
                ];
            }

            $checklists[$checklistId]['items'][] = $item;
            $checklists[$checklistId]['total']++;

            if ($item->pivot->is_completed) {
                $checklists[$checklistId]['completed']++;
            }

            if ($item->pivot->status === 'needs_review') {
                $checklists[$checklistId]['needs_review']++;
            } elseif ($item->pivot->status === 'rejected') {
                $checklists[$checklistId]['rejected']++;
            }
        }

        return view('admin.users.checklists', compact('user', 'checklists'));
    }
}
