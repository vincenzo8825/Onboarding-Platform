<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        // Set default filter values
        $type = request('type', '');
        $status = request('status', 'upcoming');
        $view = request('view', 'all');

        // Get all unique event types for the filter dropdown
        $types = Event::distinct()->pluck('type')->toArray();

        // Get IDs of events the user is registered for
        $registeredEvents = DB::table('event_participants')
            ->where('user_id', Auth::id())
            ->pluck('event_id')
            ->toArray();

        // Base query
        $query = Event::query();

        // Apply type filter
        if (!empty($type)) {
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

        // Apply view filter (all events or only registered events)
        if ($view === 'registered') {
            $query->whereIn('id', $registeredEvents);
        }

        // Get paginated results
        $events = $query->orderBy('start_date', 'asc')->paginate(9);

        return view('employee.events.index', compact(
            'events',
            'types',
            'type',
            'status',
            'view',
            'registeredEvents'
        ));
    }

    /**
     * Display the specified event details
     */
    public function show(Event $event)
    {
        $isRegistered = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        $hasAttended = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('attended', true)
            ->exists();

        $hasFeedback = EventFeedback::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        // Ottiene le informazioni sul partecipante corrente
        $participantInfo = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        // Ottiene gli altri partecipanti (escludendo l'utente corrente) e li pagina
        $participants = $event->participants()
            ->where('users.id', '!=', Auth::id())
            ->with('roles')
            ->paginate(8);

        return view('employee.events.show', compact(
            'event',
            'isRegistered',
            'hasAttended',
            'hasFeedback',
            'participantInfo',
            'participants'
        ));
    }

    /**
     * Confirm participation to an event after being invited
     */
    public function confirm(Request $request, Event $event)
    {
        // Check if user is invited
        $isInvited = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'invited')
            ->exists();

        if (!$isInvited) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei stato invitato a questo evento o hai già risposto all\'invito.');
        }

        // Update status to confirmed
        DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->update(['status' => 'confirmed']);

        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Hai confermato la tua partecipazione all\'evento!');
    }

    /**
     * Decline invitation to an event
     */
    public function decline(Request $request, Event $event)
    {
        // Check if user is invited
        $isInvited = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'invited')
            ->exists();

        if (!$isInvited) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei stato invitato a questo evento o hai già risposto all\'invito.');
        }

        // Remove user from participants
        DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Hai rifiutato l\'invito all\'evento.');
    }

    /**
     * Register to an event
     */
    public function register(Request $request, Event $event)
    {
        // Check if the event is in the past
        if ($event->isPast()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non puoi registrarti a un evento già concluso.');
        }

        // Check if the event has reached maximum participants
        if ($event->max_participants && $event->participants()->count() >= $event->max_participants) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'L\'evento ha raggiunto il numero massimo di partecipanti.');
        }

        // Check if user is already registered
        $isRegistered = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($isRegistered) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Sei già registrato a questo evento.');
        }

        // Register the user
        DB::table('event_participants')->insert([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'registered_at' => now(),
            'status' => 'registered',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Ti sei registrato con successo all\'evento!');
    }

    /**
     * Cancel registration to an event
     */
    public function cancel(Request $request, Event $event)
    {
        // Check if user is registered
        $isRegistered = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isRegistered) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei registrato a questo evento.');
        }

        // Check if the event is in the past
        if ($event->isPast()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non puoi annullare la registrazione a un evento già concluso.');
        }

        // Cancel the registration
        DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);

        return redirect()->route('employee.events.show', $event)
            ->with('success', 'La tua registrazione è stata annullata con successo.');
    }

    /**
     * Submit feedback for an event
     */
    public function feedback(Request $request, Event $event)
    {
        // Validate the request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string|min:10|max:1000',
        ]);

        // Check if the event is in the past
        if (!$event->isPast()) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Puoi lasciare feedback solo per eventi già conclusi.');
        }

        // Check if user attended the event
        $attended = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('attended', true)
            ->exists();

        if (!$attended) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Puoi lasciare feedback solo se hai partecipato all\'evento.');
        }

        // Check if user has already submitted feedback
        $hasFeedback = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->whereNotNull('feedback')
            ->exists();

        if ($hasFeedback) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Hai già inviato un feedback per questo evento.');
        }

        // Save feedback
        DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->update([
                'feedback' => $request->feedback,
                'rating' => $request->rating,
                'updated_at' => now()
            ]);

        return redirect()->route('employee.events.show', $event)
            ->with('success', 'Il tuo feedback è stato inviato con successo!');
    }

    /**
     * Add event to calendar
     */
    public function calendar(Request $request, Event $event = null)
    {
        // Se l'evento è passato come parametro nella rotta, usalo
        // altrimenti cerca il parametro nella query string
        if ($event === null && $request->has('event')) {
            $event = Event::findOrFail($request->event);
        }

        // Se non abbiamo un evento, restituisci un errore
        if ($event === null) {
            abort(404, 'Evento non trovato');
        }

        // Rimuovo temporaneamente la registrazione nel database per evitare errori
        // DB::table('event_participants')
        //     ->updateOrInsert(
        //         [
        //             'event_id' => $event->id,
        //             'user_id' => Auth::id(),
        //         ],
        //         [
        //             'added_to_calendar' => true,
        //             'updated_at' => now()
        //         ]
        //     );

        // Genera il contenuto del calendario in formato iCalendar
        $content = "BEGIN:VCALENDAR\r\n";
        $content .= "VERSION:2.0\r\n";
        $content .= "PRODID:-//Onboarding Platform//Calendar//IT\r\n";
        $content .= "BEGIN:VEVENT\r\n";
        $content .= "UID:" . md5($event->id . $event->title) . "@onboarding.com\r\n";
        $content .= "SUMMARY:" . $event->title . "\r\n";
        $content .= "DESCRIPTION:" . str_replace("\n", "\\n", $event->description) . "\r\n";
        $content .= "LOCATION:" . $event->location . "\r\n";
        $content .= "DTSTART:" . $event->start_date->format('Ymd\THis') . "\r\n";
        $content .= "DTEND:" . $event->end_date->format('Ymd\THis') . "\r\n";
        $content .= "END:VEVENT\r\n";
        $content .= "END:VCALENDAR\r\n";

        // Restituisci il file per il download
        return response($content)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="evento-' . $event->id . '.ics"');
    }

    /**
     * Download event ticket
     */
    public function downloadTicket(Event $event)
    {
        // Check if user is registered and confirmed for this event
        $participant = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->first();

        if (!$participant) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei registrato o la tua partecipazione non è confermata per questo evento.');
        }

        // Get user information
        $user = Auth::user();

        // Generate PDF ticket using Barryvdh\DomPDF\Facade\Pdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.events.ticket', [
            'event' => $event,
            'user' => $user,
            'participant' => $participant
        ]);

        return $pdf->download('biglietto-evento-' . $event->id . '.pdf');
    }

    /**
     * Verify a ticket
     */
    public function verifyTicket(Event $event, $userId, $hash)
    {
        // Verify the hash
        $correctHash = md5($event->id . $userId . 'ticket');

        if ($hash !== $correctHash) {
            return response()->json([
                'valid' => false,
                'message' => 'Biglietto non valido. Hash non corrisponde.'
            ]);
        }

        // Check if user is registered and confirmed for this event
        $participant = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->first();

        if (!$participant) {
            return response()->json([
                'valid' => false,
                'message' => 'Utente non registrato o non confermato per questo evento.'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Biglietto valido!',
            'event' => $event->title,
            'user_id' => $userId
        ]);
    }

    /**
     * Download certificate of attendance
     */
    public function downloadCertificate(Event $event)
    {
        // Check if user is registered and has attended this event
        $participant = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('attended', true)
            ->first();

        if (!$participant) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non puoi scaricare il certificato perché non hai partecipato a questo evento.');
        }

        // Get user information
        $user = Auth::user();

        // Generate PDF certificate
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.events.certificate', [
            'event' => $event,
            'user' => $user,
            'participant' => $participant
        ]);

        return $pdf->download('certificato-evento-' . $event->id . '.pdf');
    }

    /**
     * Download event material
     */
    public function downloadMaterial(Event $event, $materialId)
    {
        // Check if user is registered for this event
        $isRegistered = DB::table('event_participants')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isRegistered) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Non sei registrato a questo evento e non puoi accedere ai materiali.');
        }

        // Find the material
        $material = DB::table('event_materials')
            ->where('id', $materialId)
            ->where('event_id', $event->id)
            ->first();

        if (!$material) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Il materiale richiesto non esiste.');
        }

        // Download the file
        $path = storage_path('app/public/event_materials/' . $material->file_path);

        if (!file_exists($path)) {
            return redirect()->route('employee.events.show', $event)
                ->with('error', 'Il file richiesto non esiste.');
        }

        return response()->download($path, $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION));
    }
}
