<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Marca una notifica specifica come letta.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = DatabaseNotification::find($id);

        if ($notification && $notification->notifiable_id == $user->id) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'message' => 'Notifica segnata come letta con successo.']);
        }

        return response()->json(['success' => false, 'message' => 'Notifica non trovata.'], 404);
    }

    /**
     * Marca tutte le notifiche come lette.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'Tutte le notifiche sono state segnate come lette.']);
    }

    /**
     * Mostra tutte le notifiche ordinate dalla più recente.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = DatabaseNotification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }
}
