<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $badges = Badge::all();
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug - Stampa i dati ricevuti dalla richiesta
        // dd($request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'criteria' => 'nullable|string',
            'is_automatic' => 'boolean',
        ]);

        // Aggiungi campi predefiniti che potrebbero non essere nel form
        $data = $validated;
        $data['type'] = 'special'; // Default type: 'special' (assegnazione manuale)
        $data['slug'] = Str::slug($data['name']); // Genera uno slug dal nome
        $data['is_active'] = true; // Attiva il badge di default

        if (isset($data['is_automatic']) && $data['is_automatic']) {
            $data['type'] = 'achievement'; // Se è automatico, è un achievement
        }

        Badge::create($data);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        $users = $badge->users()->paginate(10);
        return view('admin.badges.show', compact('badge', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'criteria' => 'nullable|string',
            'is_automatic' => 'boolean',
        ]);

        $badge->update($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge eliminato con successo.');
    }

    /**
     * Award a badge to a user.
     */
    public function awardToUser(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Verifica se l'utente ha già il badge
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, [
                'awarded_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', "Badge '{$badge->name}' assegnato a {$user->name} con successo.");
        }

        return redirect()->back()
            ->with('error', "L'utente ha già questo badge.");
    }
}
