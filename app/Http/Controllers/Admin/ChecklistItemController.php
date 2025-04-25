<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistItemController extends Controller
{
    /**
     * Display a listing of the checklist items.
     */
    public function index(Checklist $checklist)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $items = $checklist->items()->orderBy('position')->get();
        return view('admin.checklists.items.index', compact('checklist', 'items'));
    }

    /**
     * Show the form for creating a new checklist item.
     */
    public function create(Checklist $checklist)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.checklists.items.create', compact('checklist'));
    }

    /**
     * Store a newly created checklist item in storage.
     */
    public function store(Request $request, Checklist $checklist)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'requires_file' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        // Calculate the next position
        $position = $checklist->items()->max('position') + 1;

        // Set default values for boolean fields if not present
        $validated['is_required'] = $validated['is_required'] ?? false;
        $validated['requires_file'] = $validated['requires_file'] ?? false;
        $validated['requires_approval'] = $validated['requires_approval'] ?? false;
        $validated['position'] = $position;

        $checklistItem = $checklist->items()->create($validated);

        return redirect()->route('admin.checklists.items.index', $checklist)
            ->with('success', 'Elemento aggiunto con successo.');
    }

    /**
     * Display the specified checklist item.
     */
    public function show(Checklist $checklist, ChecklistItem $item)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.checklists.items.show', compact('checklist', 'item'));
    }

    /**
     * Show the form for editing the specified checklist item.
     */
    public function edit(Checklist $checklist, ChecklistItem $item)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.checklists.items.edit', compact('checklist', 'item'));
    }

    /**
     * Update the specified checklist item in storage.
     */
    public function update(Request $request, Checklist $checklist, ChecklistItem $item)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'requires_file' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        // Set default values for boolean fields if not present
        $validated['is_required'] = $request->has('is_required');
        $validated['requires_file'] = $request->has('requires_file');
        $validated['requires_approval'] = $request->has('requires_approval');

        $item->update($validated);

        return redirect()->route('admin.checklists.items.index', $checklist)
            ->with('success', 'Elemento aggiornato con successo.');
    }

    /**
     * Remove the specified checklist item from storage.
     */
    public function destroy(Checklist $checklist, ChecklistItem $item)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $item->delete();

        // Re-order positions after deletion
        $checklist->items()->where('position', '>', $item->position)
            ->decrement('position');

        return redirect()->route('admin.checklists.items.index', $checklist)
            ->with('success', 'Elemento eliminato con successo.');
    }

    /**
     * Reorder checklist items.
     */
    public function reorder(Request $request, Checklist $checklist)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:checklist_items,id',
        ]);

        $items = $request->input('items');

        foreach ($items as $position => $id) {
            ChecklistItem::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
