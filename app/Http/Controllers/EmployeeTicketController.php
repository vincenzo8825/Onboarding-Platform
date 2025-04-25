<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewTicketNotification;
use App\Notifications\NewTicketReplyNotification;

class EmployeeTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('employee.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);
        
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open';
        
        $ticket = Ticket::create($validated);
        
        // Notifica agli admin
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewTicketNotification($ticket));
        
        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Ticket creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // Verifica che il ticket appartenga all'utente corrente
        if ($ticket->created_by !== Auth::id()) {
            abort(403);
        }
        
        $replies = $ticket->replies()->with('user')->orderBy('created_at', 'asc')->get();
        
        return view('employee.tickets.show', compact('ticket', 'replies'));
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        // Verifica che il ticket appartenga all'utente corrente
        if ($ticket->created_by !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['ticket_id'] = $ticket->id;
        
        // Gestione dell'allegato
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('ticket-attachments', 'public');
            $validated['attachment'] = $path;
        }
        
        $reply = TicketReply::create($validated);
        
        // Se il ticket era chiuso, riapri
        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->status = 'open';
            $ticket->save();
        }
        
        // Notifica all'assegnatario del ticket
        if ($ticket->assigned_to) {
            $assignee = User::find($ticket->assigned_to);
            $assignee->notify(new NewTicketReplyNotification($ticket, $reply));
        } else {
            // Notifica a tutti gli admin
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewTicketReplyNotification($ticket, $reply));
        }
        
        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Risposta inviata con successo.');
    }
}