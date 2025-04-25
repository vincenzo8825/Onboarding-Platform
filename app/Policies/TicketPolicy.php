<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tutti gli utenti possono vedere l'elenco dei propri ticket
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Gli admin possono vedere tutti i ticket, i dipendenti solo i propri
        return $user->hasRole('admin') || $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Tutti gli utenti possono creare ticket
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Gli admin possono modificare tutti i ticket, i dipendenti solo i propri
        return $user->hasRole('admin') || $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Solo gli admin possono eliminare i ticket
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can reply to the model.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        // Gli admin possono rispondere a tutti i ticket, i dipendenti solo ai propri
        return $user->hasRole('admin') || $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can close the model.
     */
    public function close(User $user, Ticket $ticket): bool
    {
        // Gli admin possono chiudere tutti i ticket, i dipendenti solo i propri
        return $user->hasRole('admin') || $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        // Solo gli admin possono ripristinare i ticket
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        // Solo gli admin possono eliminare permanentemente i ticket
        return $user->hasRole('admin');
    }
}
