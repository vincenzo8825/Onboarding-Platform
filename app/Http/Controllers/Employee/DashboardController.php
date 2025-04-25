<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Program;
use App\Models\Document;
use App\Models\Ticket;
use App\Models\ChecklistItem;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get tasks (checklist items)
        $myTotalTasks = $user->checklistItems()->count();
        $myTasksCompleted = $user->checklistItems()->wherePivot('is_completed', 1)->count();

        // Get courses
        $myTotalCourses = $user->courses()->count();
        $myCoursesCompleted = $user->courses()->wherePivot('status', 'completed')->count();

        // Get documents
        $documentsSubmitted = Document::where('uploaded_by', $user->id)->count();
        $documentsApproved = Document::where('uploaded_by', $user->id)
            ->where('status', 'approved')
            ->count();

        // Calculate days at company
        $daysAtCompany = $user->created_at ? (int)Carbon::parse($user->created_at)->diffInDays(Carbon::now()) : 0;

        // Calculate onboarding percentage
        $totalItems = $myTotalTasks + $myTotalCourses + ($documentsSubmitted > 0 ? $documentsSubmitted : 0);
        $completedItems = $myTasksCompleted + $myCoursesCompleted + $documentsApproved;
        $onboardingPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        // Get upcoming events
        $upcomingEvents = Event::upcoming()->limit(5)->get();

        // Get recent support tickets
        $supportTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get recent tasks
        $tasks = $user->checklistItems()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Get recent courses
        $courses = $user->courses()
            ->limit(5)
            ->get();

        // Get documents that need attention (not approved)
        $documentsTodo = Document::where('uploaded_by', $user->id)
            ->where('status', '!=', 'approved')
            ->count();

        // Get recent documents
        $documents = Document::where('uploaded_by', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('employee.dashboard', [
            'myProgramCount' => $user->programs()->count(),
            'upcomingEventCount' => Event::upcoming()->count(),
            'myTotalTasks' => $myTotalTasks,
            'myTasksCompleted' => $myTasksCompleted,
            'myTotalCourses' => $myTotalCourses,
            'myCoursesCompleted' => $myCoursesCompleted,
            'documentsSubmitted' => $documentsSubmitted,
            'documentsApproved' => $documentsApproved,
            'daysAtCompany' => $daysAtCompany,
            'onboardingPercentage' => $onboardingPercentage,
            'events' => $upcomingEvents,
            'tickets' => $supportTickets,
            'checklistItems' => $tasks,
            'courses' => $courses,
            'documentsTodo' => $documentsTodo,
            'documents' => $documents
        ]);
    }
}
