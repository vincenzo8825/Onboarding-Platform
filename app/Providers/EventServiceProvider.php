<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Eventi per documenti
        'App\Events\DocumentUploaded' => [
            'App\Listeners\SendDocumentUploadedNotification',
        ],
        'App\Events\DocumentApproved' => [
            'App\Listeners\SendDocumentApprovedNotification',
        ],
        'App\Events\DocumentRejected' => [
            'App\Listeners\SendDocumentRejectedNotification',
        ],
        'App\Events\DocumentRequested' => [
            'App\Listeners\SendDocumentRequestedNotification',
        ],
        
        // Eventi per checklist
        'App\Events\ChecklistCompleted' => [
            'App\Listeners\SendChecklistCompletedNotification',
        ],
        'App\Events\ChecklistAssigned' => [
            'App\Listeners\SendChecklistAssignedNotification',
        ],
        'App\Events\ChecklistItemCompleted' => [
            'App\Listeners\SendChecklistItemCompletedNotification',
        ],
        'App\Events\ChecklistItemStatusUpdated' => [
            'App\Listeners\SendChecklistItemStatusUpdateNotification',
        ],
        
        // Eventi per corsi
        'App\Events\CourseAssigned' => [
            'App\Listeners\SendCourseAssignedNotification',
        ],
        'App\Events\CourseCompleted' => [
            'App\Listeners\SendCourseCompletedNotification',
        ],
        
        // Eventi per eventi/appuntamenti
        'App\Events\EventCreated' => [
            'App\Listeners\SendEventCreatedNotification',
        ],
        'App\Events\EventUpdated' => [
            'App\Listeners\SendEventUpdatedNotification',
        ],
        'App\Events\EventReminder' => [
            'App\Listeners\SendEventReminderNotification',
        ],
        
        // Eventi per ticket
        'App\Events\TicketCreated' => [
            'App\Listeners\SendNewTicketNotification',
        ],
        'App\Events\TicketReplied' => [
            'App\Listeners\SendNewTicketReplyNotification',
        ],
        'App\Events\TicketStatusChanged' => [
            'App\Listeners\SendTicketStatusChangedNotification',
        ],
        
        // Eventi per badge
        'App\Events\BadgeAwarded' => [
            'App\Listeners\SendBadgeAwardedNotification',
        ],
        
        // Eventi di autenticazione
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}