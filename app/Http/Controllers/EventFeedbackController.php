<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventFeedbackController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        $feedback = new EventFeedback();
        $feedback->event_id = $event->id;
        $feedback->user_id = Auth::id();
        $feedback->rating = $validated['rating'];
        $feedback->comments = $validated['comments'] ?? null;
        $feedback->is_anonymous = $validated['is_anonymous'] ?? false;
        $feedback->save();

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ], 201);
    }

    /**
     * Get feedback for a specific event
     */
    public function getEventFeedback(Event $event)
    {
        // Check if user is authorized to view all feedback
        if (Gate::denies('viewFeedback', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feedback = EventFeedback::where('event_id', $event->id)
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get()
            ->map(function($item) {
                // Hide user information if feedback is anonymous
                if ($item->is_anonymous) {
                    unset($item->user);
                }
                return $item;
            });

        return response()->json([
            'feedback' => $feedback,
            'average_rating' => $feedback->avg('rating'),
            'total_feedback' => $feedback->count()
        ]);
    }

    /**
     * Get feedback submitted by the authenticated user
     */
    public function getUserFeedback()
    {
        $feedback = EventFeedback::where('user_id', Auth::id())
            ->with('event:id,name,start_date')
            ->get();

        return response()->json([
            'feedback' => $feedback
        ]);
    }
}
