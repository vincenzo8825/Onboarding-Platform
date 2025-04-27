<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use App\Models\Ticket;
use App\Models\Course;
use App\Models\Document;
use App\Models\ChecklistItem;
use App\Models\UserChecklistItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\UserTask;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteggi per le dashboard card
        $employeeCount = User::whereHas('roles', function($q) {
            $q->where('name', 'employee');
        })->count();

        $newHireCount = User::whereHas('roles', function($q) {
            $q->where('name', 'new_hire');
        })->count();

        $activeTicketCount = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        // Conteggi per le richieste di documenti
        $pendingDocumentRequestsCount = \App\Models\DocumentRequest::where('status', 'pending')->count();

        // Statistiche per dipartimento
        $departmentStats = [];
        $departments = Department::all();

        foreach ($departments as $department) {
            $totalEmployees = $department->users()->count();

            if ($totalEmployees > 0) {
                $onboardingCompleted = 0;

                foreach ($department->users as $user) {
                    if ($user->onboardingProgress() == 100) {
                        $onboardingCompleted++;
                    }
                }

                $departmentStats[$department->name] = $totalEmployees > 0 ?
                    round(($onboardingCompleted / $totalEmployees) * 100) : 0;
            }
        }

        // Dati mensili
        $currentYear = date('Y');
        $monthsData = [
            'hiring' => array_fill(1, 12, 0),
            'completed' => array_fill(1, 12, 0)
        ];

        // Assunzioni per mese
        $hiringByMonth = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['employee', 'new_hire']);
            })
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($hiringByMonth as $month => $count) {
            $monthsData['hiring'][$month] = $count;
        }

        // Onboarding completati per mese (simulazione)
        $completedByMonth = DB::table('course_user')
            ->whereNotNull('completed_at')
            ->whereYear('completed_at', $currentYear)
            ->selectRaw('MONTH(completed_at) as month, COUNT(DISTINCT user_id) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($completedByMonth as $month => $count) {
            $monthsData['completed'][$month] = $count;
        }

        // Ottieni gli ultimi 5 ticket
        $latestTickets = Ticket::with('user')->latest()->take(5)->get();

        // Ottieni gli eventi imminenti
        $upcomingEvents = Event::where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        // Nuovi utenti in attesa di approvazione
        $pendingApprovalCount = User::where('is_approved', false)
            ->whereHas('roles', function($query) {
                $query->where('name', 'employee');
            })
            ->count();

        return view('admin.dashboard', compact(
            'employeeCount',
            'newHireCount',
            'activeTicketCount',
            'pendingDocumentRequestsCount',
            'departmentStats',
            'monthsData',
            'latestTickets',
            'upcomingEvents',
            'pendingApprovalCount'
        ));
    }

    /**
     * Helper function to check if a table exists
     */
    private function schema_has_table($table)
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
