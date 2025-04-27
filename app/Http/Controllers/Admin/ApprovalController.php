<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /**
     * Mostra la pagina di gestione delle approvazioni
     */
    public function index()
    {
        $pendingUsers = User::where('is_approved', false)
            ->with('roles', 'department')
            ->latest()
            ->paginate(10);

        return view('admin.approvals.index', compact('pendingUsers'));
    }

    /**
     * Approva un utente
     */
    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->save();

        // Invia email di notifica all'utente (da implementare)
        // $user->notify(new UserApproved());

        return redirect()->route('admin.approvals.index')
            ->with('success', 'Utente approvato con successo!');
    }

    /**
     * Rifiuta un utente
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:255'
        ]);

        // Opzionalmente, invia email con motivo del rifiuto (da implementare)
        // $user->notify(new UserRejected($request->rejection_reason));

        // Elimina la foto del profilo se esiste
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        // Elimina l'utente
        $user->delete();

        return redirect()->route('admin.approvals.index')
            ->with('success', 'Utente rifiutato con successo!');
    }
}
