<?php

namespace App\Http\Controllers;

use App\Events\BadgeAwarded;
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
        $badges = Badge::paginate(15);
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

        // Imposta un valore predefinito per il campo description se è null
        if (!isset($data['description']) || $data['description'] === null) {
            $data['description'] = '';
        }

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
     * Assegna un badge a un utente
     */
    public function awardBadge(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);

        // Verifica se l'utente ha già questo badge
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, [
                'awarded_at' => now(),
                'awarded_by' => auth()->id(),
            ]);

            // Emetti l'evento per la notifica
            event(new BadgeAwarded($badge, $user, auth()->user()));

            // Invia direttamente la notifica per garantire che funzioni
            $user->notify(new \App\Notifications\BadgeAwardedNotification($badge, auth()->user()));
        }

        return redirect()->back()->with('success', 'Badge assegnato con successo');
    }
}
