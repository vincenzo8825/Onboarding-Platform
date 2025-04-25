<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()->latest()->paginate(10);
        return view('employee.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('employee.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Ticket creato con successo.');
    }

    public function show(Ticket $ticket)
    {
        if (Gate::denies('view', $ticket)) {
            abort(403, 'Non sei autorizzato a visualizzare questo ticket.');
        }

        $ticket->load(['replies.user', 'creator']);
        $replies = $ticket->replies;

        return view('employee.tickets.show', compact('ticket', 'replies'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if (Gate::denies('reply', $ticket)) {
            abort(403, 'Non sei autorizzato a rispondere a questo ticket.');
        }

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments/' . $ticket->id, 'public');
        }

        $ticketReply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
        ]);

        // Inviare notifica agli admin
        $admins = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewTicketReply($ticket, $ticketReply));
        }

        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Risposta inviata con successo.');
    }

    public function close(Ticket $ticket)
    {
        if (Gate::denies('close', $ticket)) {
            abort(403, 'Non sei autorizzato a chiudere questo ticket.');
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('employee.tickets.show', $ticket)
            ->with('success', 'Ticket chiuso con successo.');
    }
}
