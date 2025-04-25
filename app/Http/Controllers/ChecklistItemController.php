<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistItemController extends Controller
{
    /**
     * Costruttore con middleware per autenticazione e controllo ruoli
     */
    public function __construct()
    {
        // Non uso i middleware qui per evitare errori di linter
    }

    /**
     * Mostra la lista degli elementi di una checklist
     */
    public function index(Checklist $checklist)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        $items = $checklist->items()->orderBy('order')->get();
        return view('admin.checklist_items.index', compact('checklist', 'items'));
    }

    /**
     * Mostra il form per creare un nuovo elemento
     */
    public function create(Checklist $checklist)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        return view('admin.checklists.items.create', compact('checklist'));
    }

    /**
     * Salva un nuovo elemento della checklist
     */
    public function store(Request $request, Checklist $checklist)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'requires_file' => ['boolean'],
            'requires_approval' => ['boolean'],
            'due_date' => ['nullable', 'date'],
            'is_mandatory' => ['boolean'],
        ]);

        // Se l'ordine non è specificato, aggiungi l'elemento alla fine
        if (!isset($validated['order'])) {
            $validated['order'] = $checklist->items()->max('order') + 1;
        }

        $validated['checklist_id'] = $checklist->id;

        $item = ChecklistItem::create($validated);

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Elemento aggiunto con successo');
    }

    /**
     * Mostra un elemento della checklist
     */
    public function show(ChecklistItem $item)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        $item->load('checklist');
        return view('admin.checklist_items.show', compact('item'));
    }

    /**
     * Mostra il form per modificare un elemento
     */
    public function edit(Checklist $checklist, ChecklistItem $item)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        // Verifica che l'elemento appartenga alla checklist
        if ($item->checklist_id !== $checklist->id) {
            abort(404);
        }

        return view('admin.checklists.items.edit', compact('checklist', 'item'));
    }

    /**
     * Aggiorna un elemento della checklist
     */
    public function update(Request $request, Checklist $checklist, ChecklistItem $item)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        // Verifica che l'elemento appartenga alla checklist
        if ($item->checklist_id !== $checklist->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'requires_file' => ['boolean'],
            'requires_approval' => ['boolean'],
            'due_date' => ['nullable', 'date'],
            'is_mandatory' => ['boolean'],
        ]);

        $item->update($validated);

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Elemento aggiornato con successo');
    }

    /**
     * Elimina un elemento della checklist
     */
    public function destroy(Checklist $checklist, ChecklistItem $item)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        // Verifica che l'elemento appartenga alla checklist
        if ($item->checklist_id !== $checklist->id) {
            abort(404);
        }

        $item->delete();

        return redirect()->route('admin.checklists.show', $checklist)
            ->with('success', 'Elemento eliminato con successo');
    }

    /**
     * Modifica l'ordine degli elementi di una checklist
     */
    public function reorder(Request $request, Checklist $checklist)
    {
        // Verifica manuale dei permessi
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Accesso non autorizzato');
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['exists:checklist_items,id']
        ]);

        // Aggiorna l'ordine degli elementi in base all'array ricevuto
        foreach ($validated['items'] as $index => $itemId) {
            ChecklistItem::where('id', $itemId)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
