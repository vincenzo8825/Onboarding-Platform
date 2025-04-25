<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        $status = $request->input('status', 'upcoming');
        
        $query = Event::with('creator');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status === 'upcoming') {
            $query->where('start_date', '>', now());
        } elseif ($status === 'past') {
            $query->where('end_date', '<', now());
        } elseif ($status === 'ongoing') {
            $query->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        }
        
        $events = $query->orderBy('start_date', 'asc')->paginate(10);
        
        return view('admin.events.index', [
            'events' => $events,
            'type' => $type,
            'status' => $status,
            'types' => ['training', 'workshop', 'meeting', 'webinar', 'other'],
        ]);
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
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'type' => 'required|in:training,workshop,meeting,webinar,other',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
        ]);
        
        $startDateTime = $validated['start_date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['end_date'] . ' ' . $validated['end_time'];
        
        $event = new Event([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'type' => $validated['type'],
            'max_participants' => $validated['max_participants'],
            'created_by' => Auth::id(),
            'is_mandatory' => $request->has('is_mandatory'),
        ]);
        
        $event->save();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Evento creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $participants = $event->participants()->paginate(15);
        
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
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required',
            'type' => 'required|in:training,workshop,meeting,webinar,other',
            'max_participants' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
        ]);
        
        $startDateTime = $validated['start_date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['end_date'] . ' ' . $validated['end_time'];
        
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->location = $validated['location'];
        $event->start_date = $startDateTime;
        $event->end_date = $endDateTime;
        $event->type = $validated['type'];
        $event->max_participants = $validated['max_participants'];
        $event->is_mandatory = $request->has('is_mandatory');
        
        $event->save();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Evento aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Evento eliminato con successo.');
    }

    /**
     * Manage participants for the event.
     */
    public function manageParticipants(Event $event)
    {
        $employees = User::where('role', 'employee')->get();
        $participants = $event->participants()->pluck('user_id')->toArray();
        
        return view('admin.events.participants', compact('event', 'employees', 'participants'));
    }

    /**
     * Add participants to the event.
     */
    public function addParticipants(Request $request, Event $event)
    {
        $validated = $request->validate([
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
        ]);
        
        foreach ($validated['participants'] as $userId) {
            if (!$event->participants()->where('user_id', $userId)->exists()) {
                $event->participants()->attach($userId, [
                    'status' => 'confirmed',
                ]);
            }
        }
        
        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Partecipanti aggiunti con successo.');
    }

    /**
     * Mark attendance for participants.
     */
    public function markAttendance(Request $request, Event $event)
    {
        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'boolean',
        ]);
        
        foreach ($validated['attendance'] as $userId => $attended) {
            $event->participants()->updateExistingPivot($userId, [
                'attended' => $attended,
            ]);
        }
        
        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Presenze registrate con successo.');
    }
}