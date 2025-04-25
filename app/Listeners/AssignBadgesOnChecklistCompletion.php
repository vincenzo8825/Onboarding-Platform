<?php

namespace App\Listeners;

use App\Events\ChecklistItemCompleted;
use App\Services\BadgeService;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignBadgesOnChecklistCompletion implements ShouldQueue
{
    /**
     * Il servizio per la gestione dei badge
     *
     * @var \App\Services\BadgeService
     */
    protected $badgeService;

    /**
     * Create the event listener.
     *
     * @param  \App\Services\BadgeService  $badgeService
     * @return void
     */
    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ChecklistItemCompleted  $event
     * @return void
     */
    public function handle(ChecklistItemCompleted $event)
    {
        $user = $event->user;
        $checklistItem = $event->checklistItem;

        // Controlla se è il primo item completato dall'utente
        $completedItemsCount = $user->completedChecklistItems()->count();

        if ($completedItemsCount === 1) {
            // Assegna badge per il primo item completato
            $this->badgeService->awardBadge($user, 'first_item_completed');
        }

        // Verifica se l'utente ha completato tutti gli item di una checklist
        $checklist = $checklistItem->checklist;
        $totalItems = $checklist->items()->count();
        $userCompletedItems = $user->completedChecklistItems()
                                  ->whereHas('checklist', function($query) use ($checklist) {
                                      $query->where('id', $checklist->id);
                                  })
                                  ->count();

        if ($userCompletedItems === $totalItems) {
            // Assegna badge per il completamento della checklist
            $this->badgeService->awardBadge($user, 'checklist_completed');

            // Verifica se è la prima checklist completata
            $completedChecklists = $user->completedChecklists()->count();
            if ($completedChecklists === 1) {
                $this->badgeService->awardBadge($user, 'first_checklist_completed');
            }
        }

        // Controlla e assegna badge per achievement generali
        $this->badgeService->checkAndAwardAchievementBadges($user);
    }
}
