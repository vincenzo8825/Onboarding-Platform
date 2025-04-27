<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller per la gestione delle notifiche.
 *
 * Il sistema di notifiche di Laravel è integrato tramite il trait Notifiable nel modello User.
 *
 * Come inviare notifiche:
 * 1. Creare una classe di notifica: php artisan make:notification NomeNotifica
 * 2. Configurare i canali in via() (database, mail, ecc.)
 * 3. Personalizzare il messaggio in toMail() e/o toArray() per il database
 * 4. Inviare la notifica con uno dei seguenti metodi:
 *    - A singolo utente: $user->notify(new NomeNotifica($data));
 *    - A più utenti: Notification::send($users, new NomeNotifica($data));
 *
 * Le notifiche tra admin e dipendente possono essere inviate:
 * - Quando un dipendente carica un documento: notifica all'admin
 * - Quando un admin approva un documento: notifica al dipendente
 * - Quando un admin risponde a un ticket: notifica al dipendente
 * - Quando un dipendente crea o risponde a un ticket: notifica agli admin
 * - Quando un admin richiede un documento: notifica al dipendente
 */
class NotificationsController extends Controller
{
    /**
     * Display a listing of all notifications.
     *
     * @param Request $request Il request con eventuali filtri
     * @return \Illuminate\View\View Vista delle notifiche
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $filter = $request->input('filter');

        // Laravel offre delle notifiche attraverso il trait Notifiable nel modello User
        $query = $user->notifications();

        // Filtra in base alla richiesta
        if ($filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param string $id ID della notifica
     * @return \Illuminate\Http\JsonResponse Risposta JSON
     */
    public function markAsRead($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        try {
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
            }

            $notification->markAsRead();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Errore nel marcare la notifica come letta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Errore nel processare la richiesta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse Risposta JSON
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            Auth::user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Errore nel marcare tutte le notifiche come lette: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Errore nel processare la richiesta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a notification.
     *
     * @param Request $request Il request
     * @param string $id ID della notifica
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse Risposta
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
                }
                return redirect()->back()->with('error', 'Notifica non trovata');
            }

            $notification->delete();

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Notifica eliminata con successo');
        } catch (\Exception $e) {
            Log::error('Errore nell\'eliminare la notifica: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Errore nel processare la richiesta: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Errore nell\'eliminare la notifica');
        }
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            Auth::user()->notifications()->delete();

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Tutte le notifiche sono state eliminate');
        } catch (\Exception $e) {
            Log::error('Errore nell\'eliminare tutte le notifiche: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Errore nel processare la richiesta: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Errore nell\'eliminare tutte le notifiche');
        }
    }

    /**
     * Test notification system
     */
    public function testNotification()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Creiamo una notifica di test
        $user->notify(new \App\Notifications\TestNotification());

        // Notifico nella sessione
        return redirect()->back()->with('success', 'Notifica di test inviata con successo! Controlla le tue notifiche.');
    }

    /**
     * API endpoint to get notifications for the notifications panel
     */
    public function apiGetNotifications(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $type = $request->query('type', 'all');

        // Query di base
        $query = $user->notifications();

        // Filtriamo in base al tipo
        if ($type === 'unread') {
            $query->whereNull('read_at');
        } else if ($type === 'read') {
            $query->whereNotNull('read_at');
        }

        // Limitiamo e ordiniamo
        $notifications = $query->latest()->take(10)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $user->notifications()->count()
        ]);
    }

    /**
     * API endpoint to get the count of unread notifications
     */
    public function apiGetCount()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
