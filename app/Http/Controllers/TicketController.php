<?php

namespace App\Http\Controllers;

use App\Events\TicketReplied;
use App\Events\TicketStatusChanged;
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

        // Invia notifica direttamente per il cambio di stato
        if (auth()->user()->role === 'admin') {
            // Se è un admin a cambiare lo stato, notifica il creatore
            if ($ticket->creator) {
                $ticket->creator->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, $validated['status']));
            }
        } else {
            // Se è un dipendente a cambiare lo stato, notifica l'admin assegnato o tutti gli admin
            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, $validated['status']));
            } else {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, $validated['status']));
                }
            }
        }

        // Emetti l'evento per la notifica come backup
        event(new TicketStatusChanged($ticket, $validated['status'], auth()->user()));

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
    /**
     * Aggiunge una risposta a un ticket
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $reply = new TicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->user_id = auth()->id();
        $reply->message = $validated['message'];
        $reply->save();

        // Gestione allegati
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket_attachments', 'public');
                $reply->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Aggiorna lo stato del ticket se necessario
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'reopened']);
            // Invia notifica direttamente per il cambio di stato
            if (auth()->user()->role === 'admin') {
                // Se è un admin a rispondere, notifica il creatore
                if ($ticket->creator) {
                    $ticket->creator->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, 'reopened'));
                }
            } else {
                // Se è un dipendente a rispondere, notifica l'admin assegnato o tutti gli admin
                if ($ticket->assignedTo) {
                    $ticket->assignedTo->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, 'reopened'));
                } else {
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new \App\Notifications\TicketStatusChangedNotification($ticket, 'reopened'));
                    }
                }
            }

            event(new TicketStatusChanged($ticket, 'reopened', auth()->user()));
        }

        // Invia notifica direttamente per la risposta al ticket
        if (auth()->user()->role === 'admin') {
            // Se è un admin a rispondere, notifica il creatore
            if ($ticket->creator) {
                $ticket->creator->notify(new \App\Notifications\NewTicketReplyNotification($ticket, $reply));
            }
        } else {
            // Se è un dipendente a rispondere, notifica l'admin assegnato o tutti gli admin
            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify(new \App\Notifications\NewTicketReplyNotification($ticket, $reply));
            } else {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewTicketReplyNotification($ticket, $reply));
                }
            }
        }

        // Emetti l'evento per la notifica come backup
        event(new TicketReplied($ticket, $reply, auth()->user()));

        return redirect()->back()->with('success', 'Risposta inviata con successo');
    }

    /**
     * Cambia lo stato di un ticket
     */
    // public function changeStatus(Request $request, Ticket $ticket)
    // {
    //     $validated = $request->validate([
    //         'status' => 'required|in:open,in_progress,resolved,closed',
    //     ]);
    //
    //     $oldStatus = $ticket->status;
    //     $ticket->update(['status' => $validated['status']]);
    //
    //     // Emetti l'evento per la notifica
    //     event(new TicketStatusChanged($ticket, $validated['status'], auth()->user()));
    //
    //     return redirect()->back()->with('success', 'Stato del ticket aggiornato con successo');
    // }
}
