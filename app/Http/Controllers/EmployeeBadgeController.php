<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeBadgeController extends Controller
{
    /**
     * Display a listing of the badges awarded to the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();

        // Recupera i badge dell'utente tramite query diretta sulla tabella pivot
        $badges = DB::table('badges')
            ->join('user_badges', 'badges.id', '=', 'user_badges.badge_id')
            ->where('user_badges.user_id', $user->id)
            ->select(
                'badges.*',
                'user_badges.awarded_at as pivot_awarded_at',
                'user_badges.awarded_by as pivot_awarded_by',
                'user_badges.award_reason as pivot_award_reason',
                'user_badges.is_featured as pivot_is_featured'
            )
            ->orderBy('pivot_awarded_at', 'desc')
            ->get();

        // Converti le proprietà pivot in un oggetto per compatibilità con la vista
        $badges = $badges->map(function($badge) {
            $badge->pivot = (object)[
                'awarded_at' => new \DateTime($badge->pivot_awarded_at),
                'awarded_by' => $badge->pivot_awarded_by,
                'award_reason' => $badge->pivot_award_reason,
                'is_featured' => (bool)$badge->pivot_is_featured
            ];
            return $badge;
        });

        // Statistiche sui badge per la vista
        $stats = [
            'total' => $badges->count(),
            'total_xp' => $badges->sum('xp_value'),
            'badges_by_type' => [
                'achievement' => $badges->filter(function($badge) {
                    return $badge->type === 'achievement';
                })->count(),
                'completion' => $badges->filter(function($badge) {
                    return $badge->type === 'completion';
                })->count(),
                'special' => $badges->filter(function($badge) {
                    return $badge->type === 'special';
                })->count(),
            ],
            'categories' => $badges->groupBy('category')->map->count(),
        ];

        return view('employee.badges.index', compact('badges', 'stats'));
    }

    /**
     * Toggle il badge in evidenza per l'utente autenticato.
     */
    public function toggleFeatured(Request $request, Badge $badge)
    {
        $user = Auth::user();

        // Verifica se l'utente ha questo badge
        $hasBadge = DB::table('user_badges')
            ->where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if (!$hasBadge) {
            return redirect()->route('employee.badges.index')
                ->with('error', 'Non puoi modificare un badge che non ti è stato assegnato.');
        }

        // Ottieni lo stato attuale di is_featured
        $currentlyFeatured = DB::table('user_badges')
            ->where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->value('is_featured');

        // Aggiorna lo stato opposto
        DB::table('user_badges')
            ->where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->update(['is_featured' => !$currentlyFeatured]);

        $message = $currentlyFeatured
            ? 'Badge rimosso dai preferiti.'
            : 'Badge aggiunto ai preferiti.';

        return redirect()->route('employee.badges.index')
            ->with('success', $message);
    }

    /**
     * Mostra i dettagli di un singolo badge.
     */
    public function show(Badge $badge)
    {
        $user = Auth::user();

        // Verifica se l'utente ha questo badge
        $hasBadge = DB::table('user_badges')
            ->where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if (!$hasBadge) {
            return redirect()->route('employee.badges.index')
                ->with('error', 'Non puoi visualizzare un badge che non ti è stato assegnato.');
        }

        // Recupera i dettagli del badge con le informazioni della pivot
        $badgeDetails = DB::table('badges')
            ->join('user_badges', 'badges.id', '=', 'user_badges.badge_id')
            ->where('user_badges.user_id', $user->id)
            ->where('badges.id', $badge->id)
            ->select(
                'badges.*',
                'user_badges.awarded_at',
                'user_badges.awarded_by',
                'user_badges.award_reason',
                'user_badges.is_featured'
            )
            ->first();

        // Converti in un oggetto per compatibilità con la vista
        $badgeDetails->pivot = (object)[
            'awarded_at' => new \DateTime($badgeDetails->awarded_at),
            'awarded_by' => $badgeDetails->awarded_by,
            'award_reason' => $badgeDetails->award_reason,
            'is_featured' => (bool)$badgeDetails->is_featured
        ];

        // Aggiungi i metodi helper per compatibilità con la vista
        $badgeDetails->isAchievement = function() use ($badgeDetails) {
            return $badgeDetails->type === 'achievement';
        };

        $badgeDetails->isCompletion = function() use ($badgeDetails) {
            return $badgeDetails->type === 'completion';
        };

        $badgeDetails->isSpecial = function() use ($badgeDetails) {
            return $badgeDetails->type === 'special';
        };

        // Recupera l'utente che ha assegnato il badge (se esiste)
        $awardedBy = null;
        if ($badgeDetails->awarded_by) {
            $awardedBy = User::find($badgeDetails->awarded_by);
        }

        // Recupera altri utenti che hanno lo stesso badge
        $otherUsers = DB::table('user_badges')
            ->join('users', 'user_badges.user_id', '=', 'users.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('user_badges.badge_id', $badge->id)
            ->where('users.id', '!=', $user->id)
            ->select(
                'users.*',
                'departments.name as department_name',
                'user_badges.awarded_at'
            )
            ->orderBy('user_badges.awarded_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($user) {
                $user->pivot = (object)[
                    'awarded_at' => new \DateTime($user->awarded_at)
                ];
                $user->department = (object)[
                    'name' => $user->department_name
                ];
                return $user;
            });

        // Conta gli utenti totali con questo badge
        $totalUsersWithBadge = DB::table('user_badges')
            ->where('badge_id', $badge->id)
            ->count();

        $badgeDetails->users = function() use ($totalUsersWithBadge) {
            return (object)[
                'count' => function() use ($totalUsersWithBadge) {
                    return $totalUsersWithBadge;
                }
            ];
        };

        return view('employee.badges.show', compact('badgeDetails', 'awardedBy', 'otherUsers'));
    }
}
