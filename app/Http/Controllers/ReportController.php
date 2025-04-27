<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Checklist;
use App\Models\Course;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Mostra la dashboard principale delle statistiche
     */
    public function index()
    {
        // Conteggi principali
        $totalUsers = User::count();
        $activeUsers = User::whereHas('roles', function($q) {
            $q->where('name', 'employee');
        })->count();

        $completedOnboarding = User::whereHas('roles', function($q) {
            $q->where('name', 'employee');
        })->whereDoesntHave('checklistItems', function($q) {
            $q->where('status', '!=', 'completed');
        })->count();

        $openTickets = Ticket::where('status', 'open')->count();
        $resolvedTickets = Ticket::where('status', 'closed')->count();

        // Dati per i grafici
        $departmentData = $this->getDepartmentChecklistData();
        $courseCompletionData = $this->getCourseCompletionData();
        $monthlyProgressData = $this->getMonthlyProgressData();
        $userCompletionData = $this->getUserCompletionRateData();

        return view('admin.reports.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'completedOnboarding',
            'openTickets',
            'resolvedTickets',
            'departmentData',
            'courseCompletionData',
            'monthlyProgressData',
            'userCompletionData'
        ));
    }

    /**
     * Dettaglio del progresso di onboarding
     */
    public function onboardingProgress()
    {
        $users = User::with(['roles', 'department', 'tasks'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'employee');
            })
            ->get();

        $userProgress = [];
        $completedCount = 0;
        $inProgressCount = 0;
        $delayedCount = 0;

        foreach ($users as $user) {
            $progress = $user->getOnboardingProgress();

            // Determine progress class for the bar
            $progressClass = 'danger';
            if ($progress >= 75) {
                $progressClass = 'success';
            } elseif ($progress >= 50) {
                $progressClass = 'info';
            } elseif ($progress >= 25) {
                $progressClass = 'warning';
            }

            // Determine status text and class
            $status = 'In corso';
            $statusClass = 'warning';

            if ($progress == 100) {
                $status = 'Completato';
                $statusClass = 'success';
                $completedCount++;
            } elseif ($progress < 25) {
                $status = 'Iniziato';
                $statusClass = 'danger';
                $delayedCount++;
            } else {
                $inProgressCount++;
            }

            $userProgress[] = [
                'user' => $user,
                'progress' => $progress,
                'progress_class' => $progressClass,
                'status' => $status,
                'status_class' => $statusClass
            ];
        }

        return view('admin.reports.onboarding-progress', compact('userProgress', 'completedCount', 'inProgressCount', 'delayedCount'));
    }

    /**
     * Statistiche per dipartimento
     */
    public function departmentStats()
    {
        $departments = Department::withCount('users')->get();

        // Calcolo percentuale completamento checklist per dipartimento
        $departmentStats = [];

        foreach ($departments as $department) {
            $users = $department->users;
            $totalProgress = 0;
            $userCount = $users->count();

            foreach ($users as $user) {
                $totalItems = $user->checklistItems()->count();
                if ($totalItems > 0) {
                    $completedItems = $user->checklistItems()->where('status', 'completed')->count();
                    $userProgress = ($completedItems / $totalItems) * 100;
                    $totalProgress += $userProgress;
                }
            }

            $avgProgress = $userCount > 0 ? $totalProgress / $userCount : 0;

            $departmentStats[] = [
                'name' => $department->name,
                'users_count' => $userCount,
                'avg_progress' => round($avgProgress, 1)
            ];
        }

        return view('admin.reports.department-stats', compact('departmentStats'));
    }

    /**
     * Statistiche corsi formativi
     */
    public function courseStats()
    {
        $courses = Course::withCount('users')->get();

        $courseStats = [];

        foreach ($courses as $course) {
            $totalUsers = $course->users()->count();
            $completedUsers = $course->users()->wherePivot('status', 'completed')->count();
            $inProgressUsers = $course->users()->wherePivot('status', 'in_progress')->count();
            $notStartedUsers = $course->users()->wherePivot('status', 'not_started')->count();

            $completionRate = $totalUsers > 0 ? ($completedUsers / $totalUsers) * 100 : 0;

            $courseStats[] = [
                'id' => $course->id,
                'title' => $course->title,
                'total_users' => $totalUsers,
                'completed_users' => $completedUsers,
                'in_progress_users' => $inProgressUsers,
                'not_started_users' => $notStartedUsers,
                'completion_rate' => round($completionRate, 1)
            ];
        }

        return view('admin.reports.course-stats', compact('courseStats'));
    }

    /**
     * Trend mensili di completamento onboarding
     */
    public function monthlyTrends()
    {
        // Ultimi 6 mesi
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        // Conteggio completamenti per mese
        $completions = [];
        $newHires = [];
        foreach ($months as $month) {
            $yearMonth = explode('-', $month);
            $year = $yearMonth[0];
            $monthNum = $yearMonth[1];

            $count = UserCourse::where('status', 'completed')
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $monthNum)
                ->count();

            $completions[] = $count;

            // Calcolo nuovi dipendenti per mese
            $newHiresCount = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->count();

            $newHires[] = $newHiresCount;
        }

        $monthLabels = $months->map(function($month) {
            return date('M Y', strtotime($month . '-01'));
        })->toArray();

        return view('admin.reports.monthly-trends', compact('monthLabels', 'completions', 'newHires'));
    }

    /**
     * Statistiche di completamento quiz
     */
    public function quizStats()
    {
        $quizStats = UserQuiz::select(
                'quiz_id',
                DB::raw('AVG(score) as avg_score'),
                DB::raw('COUNT(*) as attempts'),
                DB::raw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_count')
            )
            ->with('quiz')
            ->groupBy('quiz_id')
            ->get()
            ->map(function($item) {
                return [
                    'quiz_name' => $item->quiz->title,
                    'avg_score' => round($item->avg_score, 1),
                    'attempts' => $item->attempts,
                    'pass_rate' => $item->attempts > 0 ? round(($item->passed_count / $item->attempts) * 100, 1) : 0
                ];
            });

        return view('admin.reports.quiz-stats', compact('quizStats'));
    }

    /**
     * Statistiche ticket supporto
     */
    public function ticketStats()
    {
        $openTickets = Ticket::where('status', 'open')->count();
        $inProgressTickets = Ticket::where('status', 'in_progress')->count();
        $closedTickets = Ticket::where('status', 'closed')->count();
        $totalTickets = Ticket::count();

        // Tempo medio di risoluzione (in giorni)
        $avgResolutionTime = Ticket::whereNotNull('closed_at')
            ->selectRaw('AVG(DATEDIFF(closed_at, created_at)) as avg_days')
            ->first()
            ->avg_days ?? 0;

        // Ticket per categoria
        $categoryCounts = Ticket::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        return view('admin.reports.ticket-stats', compact(
            'openTickets',
            'inProgressTickets',
            'closedTickets',
            'totalTickets',
            'avgResolutionTime',
            'categoryCounts'
        ));
    }

    /**
     * Metodi privati per generare dati per i grafici
     */
    private function getDepartmentChecklistData()
    {
        $departments = Department::all();
        $labels = [];
        $data = [];

        foreach ($departments as $department) {
            $users = $department->users;
            $totalProgress = 0;
            $userCount = $users->count();

            foreach ($users as $user) {
                $totalItems = $user->checklistItems()->count();
                if ($totalItems > 0) {
                    $completedItems = $user->checklistItems()->where('status', 'completed')->count();
                    $userProgress = ($completedItems / $totalItems) * 100;
                    $totalProgress += $userProgress;
                }
            }

            $avgProgress = $userCount > 0 ? $totalProgress / $userCount : 0;

            $labels[] = $department->name;
            $data[] = round($avgProgress, 1);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getCourseCompletionData()
    {
        // Otteniamo tutti i corsi senza withCount per evitare l'errore
        $courses = Course::all();

        $courseStats = [];

        foreach ($courses as $course) {
            // Contiamo manualmente per ogni stato
            $completedCount = DB::table('course_user')
                ->where('course_id', $course->id)
                ->where('status', 'completed')
                ->count();

            $inProgressCount = DB::table('course_user')
                ->where('course_id', $course->id)
                ->where('status', 'in_progress')
                ->count();

            $notStartedCount = DB::table('course_user')
                ->where('course_id', $course->id)
                ->where('status', 'not_started')
                ->count();

            $courseStats[] = [
                'id' => $course->id,
                'title' => $course->title,
                'completed_count' => $completedCount,
                'in_progress_count' => $inProgressCount,
                'not_started_count' => $notStartedCount
            ];
        }

        // Prepara i dati per i grafici
        $labels = collect($courseStats)->pluck('title')->toArray();
        $completedData = collect($courseStats)->pluck('completed_count')->toArray();
        $inProgressData = collect($courseStats)->pluck('in_progress_count')->toArray();
        $notStartedData = collect($courseStats)->pluck('not_started_count')->toArray();

        return [
            'labels' => $labels,
            'completed' => $completedData,
            'inProgress' => $inProgressData,
            'notStarted' => $notStartedData
        ];
    }

    private function getMonthlyProgressData()
    {
        // Ultimi 6 mesi
        $months = collect();
        $completions = [];
        $newHires = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $yearMonth = $date->format('Y-m');
            $months->push($date->format('M Y'));

            $year = $date->year;
            $month = $date->month;

            // Completamenti in questo mese
            $completions[] = UserCourse::where('status', 'completed')
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $month)
                ->count();

            // Nuovi dipendenti in questo mese
            $newHires[] = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        }

        return [
            'labels' => $months->toArray(),
            'completions' => $completions,
            'newHires' => $newHires
        ];
    }

    private function getUserCompletionRateData()
    {
        $completedCount = User::whereHas('roles', function($q) {
            $q->where('name', 'employee');
        })->whereDoesntHave('checklistItems', function($q) {
            $q->where('status', '!=', 'completed');
        })->count();

        $inProgressCount = User::whereHas('roles', function($q) {
            $q->where('name', 'employee');
        })->whereHas('checklistItems', function($q) {
            $q->where('status', '!=', 'completed');
        })->count();

        return [
            'labels' => ['Completato', 'In corso'],
            'data' => [$completedCount, $inProgressCount]
        ];
    }
}
