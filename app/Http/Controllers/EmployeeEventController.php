<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        $status = $request->input('status', 'upcoming');
        $view = $request->input('view', 'all');
        
        $query = Event::query();
        
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
        
        if ($view === 'registered') {
            $query->whereHas('participants', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }
        
        $events = $query->orderBy('start_date', 'asc')->paginate(10);
        
        // Get the events the user is registered for
        $registeredEvents = Auth::user()->events()->pluck('event_id')->toArray();
        
        return view('employee.events.index', [
            'events' => $events,
            'type' => $type,
            'status' => $status,
            'view' => $view,
            'types' => ['training', 'workshop', 'meeting', 'webinar', 'other'],
            'registeredEvents' => $registeredEvents,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $isRegistered = $event->participants()->where('user_id', Auth::id())->exists();
        $participantInfo = null;
        
        if ($isRegistered) {
            $participantInfo = $event->participants()->where('user_id', Auth::id())->first()->pivot;
        }
        
        return view('employee.events.show', compact('event', 'isRegistered', 'participantInfo'));
    }

    /**
     * Register the authenticated user for the event.
     */
    public function register(Event $event)
    {
        // Check if the event is full
        if ($event->isFull()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Questo evento è al completo.');
        }
        
        // Check if the user is already registered
        if ($event->participants()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Sei già registrato a questo evento.');
        }
        
        // Register the user
        $event->participants()->attach(Auth::id(), [
            'status' => 'registered',
        ]);
        
        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Ti sei registrato con successo all\'evento.');
    }

    /**
     * Cancel the authenticated user's registration for the event.
     */
    public function cancel(Event $event)
    {
        // Check if the user is registered
        if (!$event->participants()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei registrato a questo evento.');
        }
        
        // Check if the event is in the past
        if ($event->isPast()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non puoi annullare la registrazione per un evento passato.');
        }
        
        // Cancel the registration
        $event->participants()->updateExistingPivot(Auth::id(), [
            'status' => 'cancelled',
        ]);
        
        return redirect()->route('employee.events.show', $event)
            ->with('success', 'La tua registrazione è stata annullata.');
    }

    /**
     * Submit feedback for the event.
     */
    public function feedback(Request $request, Event $event)
    {
        $validated = $request->validate([
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        
        // Check if the user is registered and attended
        $participant = $event->participants()->where('user_id', Auth::id())->first();
        
        if (!$participant || $participant->pivot->status !== 'confirmed' || !$participant->pivot->attended) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Puoi fornire feedback solo per eventi a cui hai partecipato.');
        }
        
        // Submit feedback
        $event->participants()->updateExistingPivot(Auth::id(), [
            'feedback' => $validated['feedback'],
            'rating' => $validated['rating'],
        ]);
        
        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Il tuo feedback è stato inviato con successo.');
    }
}