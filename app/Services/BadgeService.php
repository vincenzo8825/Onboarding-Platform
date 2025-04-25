<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Assegna un badge a un utente se non lo possiede già
     *
     * @param User $user L'utente a cui assegnare il badge
     * @param string $badgeSlug Il slug del badge da assegnare
     * @return bool True se l'operazione è avvenuta con successo, false altrimenti
     */
    public function awardBadge(User $user, string $badgeSlug): bool
    {
        // Trova il badge in base allo slug
        $badge = Badge::where('slug', $badgeSlug)->first();

        if (!$badge) {
            return false;
        }

        // Controlla se l'utente ha già questo badge
        $existingBadge = UserBadge::where('user_id', $user->id)
                                  ->where('badge_id', $badge->id)
                                  ->first();

        if ($existingBadge) {
            return false;
        }

        // Assegna il badge all'utente
        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'awarded_at' => Carbon::now(),
        ]);

        // Aggiorna i punti esperienza dell'utente
        if ($badge->xp_value > 0) {
            $user->xp += $badge->xp_value;
            $user->save();
        }

        // Notifica l'utente (qui puoi integrarti con il sistema di notifiche)
        // Esempio: event(new BadgeAwarded($user, $badge));

        return true;
    }

    /**
     * Verifica e assegna i badge di tipo "achievement" in base ai requisiti
     *
     * @param User $user L'utente da verificare
     * @return void
     */
    public function checkAndAwardAchievementBadges(User $user): void
    {
        $this->checkLoginStreak($user);
        $this->checkCourseCompletion($user);
        $this->checkQuizScores($user);
        $this->checkProfileCompletion($user);
    }

    /**
     * Controlla la sequenza di login dell'utente
     *
     * @param User $user
     * @return void
     */
    private function checkLoginStreak(User $user): void
    {
        // Implementazione della logica per controllare login consecutivi
        // e assegnare badge appropriati
        if ($user->login_streak >= 7) {
            $this->awardBadge($user, 'week_streak');
        }

        if ($user->login_streak >= 30) {
            $this->awardBadge($user, 'month_streak');
        }
    }

    /**
     * Controlla il completamento dei corsi
     *
     * @param User $user
     * @return void
     */
    private function checkCourseCompletion(User $user): void
    {
        $completedCoursesCount = $user->completedCourses()->count();

        if ($completedCoursesCount >= 1) {
            $this->awardBadge($user, 'first_course');
        }

        if ($completedCoursesCount >= 5) {
            $this->awardBadge($user, 'course_master');
        }

        if ($completedCoursesCount >= 10) {
            $this->awardBadge($user, 'course_expert');
        }
    }

    /**
     * Controlla i punteggi dei quiz
     *
     * @param User $user
     * @return void
     */
    private function checkQuizScores(User $user): void
    {
        // Conta i quiz con punteggio perfetto
        $perfectQuizzes = $user->quizzes()
                              ->where('score', 100)
                              ->count();

        if ($perfectQuizzes >= 1) {
            $this->awardBadge($user, 'perfect_score');
        }

        if ($perfectQuizzes >= 5) {
            $this->awardBadge($user, 'quiz_master');
        }
    }

    /**
     * Controlla il completamento del profilo
     *
     * @param User $user
     * @return void
     */
    private function checkProfileCompletion(User $user): void
    {
        // Controlla se tutti i campi del profilo sono completi
        $profileComplete = !empty($user->bio) &&
                          !empty($user->profile_picture) &&
                          !empty($user->job_title) &&
                          !empty($user->department_id);

        if ($profileComplete) {
            $this->awardBadge($user, 'profile_complete');
        }
    }
}
