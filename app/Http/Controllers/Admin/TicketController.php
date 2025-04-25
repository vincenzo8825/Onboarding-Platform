<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $priority = $request->input('priority');
        $category = $request->input('category');

        $ticketsQuery = Ticket::query();

        // Apply filters
        if ($status) {
            $ticketsQuery->where('status', $status);
        }

        if ($priority) {
            $ticketsQuery->where('priority', $priority);
        }

        if ($category) {
            $ticketsQuery->where('category', $category);
        }

        $tickets = $ticketsQuery->with(['user', 'creator', 'assignedTo', 'replies'])
                                ->latest()
                                ->paginate(10);

        // Get all unique categories for filter dropdown
        $categories = Ticket::distinct('category')->pluck('category')->filter()->toArray();

        return view('admin.tickets.index', compact('tickets', 'status', 'priority', 'category', 'categories'));
    }

    /**
     * Show the form for creating a new ticket.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.tickets.create');
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'nullable|string|max:100',
        ]);

        $ticket = new Ticket();
        $ticket->title = $validated['title'];
        $ticket->description = $validated['description'];
        $ticket->priority = $validated['priority'];
        $ticket->category = $validated['category'] ?? 'Generale';
        $ticket->status = 'open';
        $ticket->user_id = Auth::id();
        $ticket->save();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket creato con successo!');
    }

    /**
     * Display the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\View\View
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'creator', 'assignedTo', 'replies']);

        // Recupero gli amministratori per il form di assegnazione
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->get();

        $replies = $ticket->replies;

        return view('admin.tickets.show', compact('ticket', 'admins', 'replies'));
    }

    /**
     * Show the form for editing the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\View\View
     */
    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified ticket in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'category' => 'nullable|string|max:100',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->title = $validated['title'];
        $ticket->description = $validated['description'];
        $ticket->priority = $validated['priority'];
        $ticket->status = $validated['status'];
        $ticket->category = $validated['category'] ?? $ticket->category;

        if ($ticket->status === 'closed' && !$ticket->closed_at) {
            $ticket->closed_at = now();
        } elseif ($ticket->status !== 'closed') {
            $ticket->closed_at = null;
        }

        if (isset($validated['assigned_to'])) {
            $ticket->assigned_to = $validated['assigned_to'];
        }

        $ticket->save();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket aggiornato con successo!');
    }

    /**
     * Remove the specified ticket from storage.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket eliminato con successo!');
    }

    /**
     * Reply to a ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal_note' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments/' . $ticket->id, 'public');
        }

        $ticketReply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => $validated['is_internal_note'] ?? false,
            'attachment_path' => $attachmentPath,
        ]);

        // Update ticket status if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Inviare notifica all'utente (ma non se è una nota interna)
        if (!($validated['is_internal_note'] ?? false)) {
            $ticket->user->notify(new \App\Notifications\NewTicketReply($ticket, $ticketReply));
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Risposta inviata con successo.');
    }

    /**
     * Close a ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(Ticket $ticket)
    {
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket chiuso con successo.');
    }

    /**
     * Aggiorna lo stato di un ticket
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->status = $validated['status'];

        if ($ticket->status === 'closed' && !$ticket->closed_at) {
            $ticket->closed_at = now();
        } elseif ($ticket->status !== 'closed') {
            $ticket->closed_at = null;
        }

        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Stato del ticket aggiornato con successo.');
    }

    /**
     * Assegna un ticket a un utente
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->assigned_to = $validated['assigned_to'];
        $ticket->save();

        // Se il ticket è open, lo aggiorniamo a in_progress
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
            $ticket->save();
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket assegnato con successo.');
    }
}
