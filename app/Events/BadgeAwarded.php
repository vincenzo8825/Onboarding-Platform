<?php

namespace App\Events;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeAwarded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $badge;
    public $user;
    public $awardedBy;

    /**
     * Create a new event instance.
     *
     * @param Badge $badge Il badge assegnato
     * @param User $user L'utente che ha ricevuto il badge
     * @param User $awardedBy L'utente che ha assegnato il badge
     */
    public function __construct(Badge $badge, User $user, User $awardedBy)
    {
        $this->badge = $badge;
        $this->user = $user;
        $this->awardedBy = $awardedBy;
    }
}
