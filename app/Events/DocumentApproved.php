<?php

namespace App\Events;

use App\Models\Document;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $document;
    public $approver;

    /**
     * Create a new event instance.
     */
    public function __construct(Document $document, User $approver)
    {
        $this->document = $document;
        $this->approver = $approver;
    }
}