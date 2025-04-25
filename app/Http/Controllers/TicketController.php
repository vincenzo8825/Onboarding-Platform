<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TicketStatusChangedNotification;
use App\Notifications\NewTicketReplyNotification;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $priority = $request->input('priority');
        $category = $request->input('category');
        
        $query = Ticket::with('creator');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $categories = Ticket::select('category')->distinct()->pluck('category');
        
        return view('admin.tickets.index', compact('tickets', 'categories', 'status', 'priority', 'category'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $replies = $ticket->replies()->with('user')->orderBy('created_at', 'asc')->get();
        $admins = User::where('role', 'admin')->get();
        
        return view('admin.tickets.show', compact('ticket', 'replies', 'admins'));
    }

    /**
     * Change the status of a ticket.
     */
    public function changeStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        
        $oldStatus = $ticket->status;
        $ticket->status = $validated['status'];
        
        if ($validated['status'] === 'closed' || $validated['status'] === 'resolved') {
            $ticket->closed_at = now();
        } else {
            $ticket->closed_at = null;
        }
        
        $ticket->save();
        
        // Notifica al creatore del ticket
        $ticket->creator->notify(new TicketStatusChangedNotification($ticket, $oldStatus));
        
        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Stato del ticket aggiornato con successo.');
    }

    /**
     * Assign a ticket to an admin.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $ticket->assigned_to = $validated['assigned_to'];
        $ticket->status = 'in_progress';
        $ticket->save();
        
        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket assegnato con successo.');
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, Ticket $ticket)
    {
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
        
        // Se non è già assegnato, assegna all'utente corrente
        if (!$ticket->assigned_to) {
            $ticket->assigned_to = Auth::id();
            $ticket->status = 'in_progress';
            $ticket->save();
        }
        
        // Notifica al creatore del ticket
        $ticket->creator->notify(new NewTicketReplyNotification($ticket, $reply));
        
        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Risposta inviata con successo.');
    }
}
