<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $type = $request->input('type', '');
        $status = $request->input('status', 'upcoming');

        // Get all event types from the database
        $typesCollection = Event::distinct()->pluck('type');
        $types = array_filter($typesCollection->toArray()); // Remove empty values

        // Create query based on filters
        $query = Event::query();

        // Apply type filter if provided
        if ($type) {
            $query->where('type', $type);
        }

        // Apply status filter
        if ($status === 'upcoming') {
            $query->where('start_date', '>', now());
        } elseif ($status === 'ongoing') {
            $query->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        } elseif ($status === 'past') {
            $query->where('end_date', '<', now());
        }
        // Il filtro 'all' non applica restrizioni di data

        // Get paginated results
        $events = $query->orderBy('start_date', 'desc')->paginate(10);

        return view('admin.events.index', compact('events', 'types', 'type', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date',
            'end_time' => 'required',
            'type' => 'required|string',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'sometimes|boolean',
            'is_online' => 'sometimes|boolean',
            'online_link' => 'nullable|url|required_if:is_online,1',
            'registration_deadline' => 'nullable|date|before:start_date'
        ]);

        // Combine date and time inputs
        $startDateTime = $validated['start_date'] . ' ' . $validated['start_time'] . ':00';
        $endDateTime = $validated['end_date'] . ' ' . $validated['end_time'] . ':00';

        // Create event data array
        $eventData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'type' => $validated['type'],
            'max_participants' => $validated['max_participants'] ?? null,
            'is_mandatory' => isset($validated['is_mandatory']),
            'created_by' => Auth::id(),
        ];

        // Add optional fields if present
        if (isset($validated['is_online'])) {
            $eventData['is_online'] = $validated['is_online'];
        }

        if (isset($validated['online_link'])) {
            $eventData['online_link'] = $validated['online_link'];
        }

        if (isset($validated['registration_deadline'])) {
            $eventData['registration_deadline'] = $validated['registration_deadline'];
        }

        $event = Event::create($eventData);

        // Reindirizza alla pagina degli eventi con il filtro "all" per mostrare tutti gli eventi
        return redirect()->route('admin.events.index', ['status' => 'all'])
            ->with('success', 'Evento creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['attendees']);

        // Ottiene i partecipanti e li pagina
        $participants = $event->participants()
            ->with('roles') // Precarica eventuali relazioni utili
            ->paginate(10); // 10 partecipanti per pagina

        return view('admin.events.show', compact('event', 'participants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date',
            'end_time' => 'required',
            'type' => 'required|string',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'sometimes|boolean',
            'is_online' => 'sometimes|boolean',
            'online_link' => 'nullable|url|required_if:is_online,1',
            'registration_deadline' => 'nullable|date|before:start_date'
        ]);

        // Combine date and time inputs
        $startDateTime = $validated['start_date'] . ' ' . $validated['start_time'] . ':00';
        $endDateTime = $validated['end_date'] . ' ' . $validated['end_time'] . ':00';

        // Create event data array
        $eventData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'type' => $validated['type'],
            'max_participants' => $validated['max_participants'] ?? null,
            'is_mandatory' => isset($validated['is_mandatory']),
        ];

        // Add optional fields if present
        if (isset($validated['is_online'])) {
            $eventData['is_online'] = $validated['is_online'];
        }

        if (isset($validated['online_link'])) {
            $eventData['online_link'] = $validated['online_link'];
        }

        if (isset($validated['registration_deadline'])) {
            $eventData['registration_deadline'] = $validated['registration_deadline'];
        }

        $event->update($eventData);

        // Reindirizza alla pagina degli eventi con il filtro "all" per mostrare tutti gli eventi
        return redirect()->route('admin.events.index', ['status' => 'all'])
            ->with('success', 'Evento aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index', ['status' => 'all'])
            ->with('success', 'Evento eliminato con successo!');
    }

    /**
     * Display participants of an event.
     */
    public function participants(Event $event)
    {
        $event->load(['participants', 'attendees']);
        $availableUsers = User::whereDoesntHave('eventsParticipating', function($query) use ($event) {
            $query->where('event_id', $event->id);
        })->get();

        return view('admin.events.participants', compact('event', 'availableUsers'));
    }

    /**
     * Add participants to an event.
     */
    public function addParticipants(Request $request, Event $event)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $event->participants()->attach($request->user_ids, [
            'registered_at' => now(),
            'status' => 'invited',
            'registered_by' => Auth::id()
        ]);

        // Invia notifiche ai partecipanti
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            $user->notify(new \App\Notifications\EventInvitation($event));
        }

        return redirect()->route('admin.events.participants', $event)
            ->with('success', 'Inviti inviati con successo!');
    }

    /**
     * Mark attendance for event participants.
     */
    public function markAttendance(Request $request, Event $event)
    {
        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'exists:users,id',
        ]);

        // Aggiorna lo stato "attended" per tutti i partecipanti
        // Prima imposta tutti a non presenti
        $event->participants()->updateExistingPivot($event->participants->pluck('id')->toArray(), [
            'attended' => false
        ]);

        // Poi imposta come presenti gli utenti selezionati
        if (!empty($validated['attendance'])) {
            foreach ($validated['attendance'] as $userId => $value) {
                $event->participants()->updateExistingPivot($userId, [
                    'attended' => true,
                    'marked_by' => Auth::id(),
                    'marked_at' => now()
                ]);
            }
        }

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Presenze registrate con successo!');
    }

    /**
     * Remove a participant from an event.
     */
    public function removeParticipant(Request $request, Event $event)
    {
        $request->validate([
            'participant_id' => 'required|exists:users,id',
        ]);

        $userId = $request->participant_id;

        // Verifico se l'utente è effettivamente un partecipante all'evento
        $isParticipant = $event->participants()->where('users.id', $userId)->exists();

        if (!$isParticipant) {
            return redirect()->route('admin.events.participants', $event)
                ->with('error', 'L\'utente selezionato non è un partecipante di questo evento.');
        }

        // Rimuovo l'utente dai partecipanti
        $event->participants()->detach($userId);

        return redirect()->route('admin.events.participants', $event)
            ->with('success', 'Partecipante rimosso con successo.');
    }
}
