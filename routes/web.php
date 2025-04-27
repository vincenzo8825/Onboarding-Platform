<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\DocumentController as EmployeeDocumentController;
use App\Http\Controllers\Employee\TicketController as EmployeeTicketController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\UserChecklistController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\EmployeeBadgeController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\NotificationsController;

// Route pubbliche
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Pagine statiche
Route::get('/privacy', [\App\Http\Controllers\StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [\App\Http\Controllers\StaticPageController::class, 'terms'])->name('terms');

// Route di autenticazione personalizzate
Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Notifiche
Route::middleware(['auth'])->group(function () {
    // Aggiungi route per testare notifiche (deve venire prima della route generica)
    Route::get('notifications/test', [NotificationsController::class, 'testNotification'])->name('notifications.test');
    Route::get('notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('notifications/{id}', [NotificationsController::class, 'delete'])->name('notifications.delete');
    Route::delete('notifications', [NotificationsController::class, 'deleteAll'])->name('notifications.delete-all');

    // API route per il nuovo pannello notifiche
    Route::get('api/notifications', [NotificationsController::class, 'apiGetNotifications']);
    Route::get('api/notifications/count', [NotificationsController::class, 'apiGetCount']);
});

// Registrazione
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest');

// Reset Password
// Password Reset Routes - ensure these are unique
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
     ->middleware('guest')
     ->name('password.request');  // This is the correct name for this route

// Make sure no other route uses 'password.request' name
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
     ->middleware('guest')
     ->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
     ->middleware('guest')
     ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
     ->middleware('guest')
     ->name('password.update');

// Conferma Password
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->middleware('auth')->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm'])->middleware('auth');

// Verifica Email
Route::get('email/verify', [VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Route per utenti non autorizzati
Route::get('/unauthorized', function () {
    return view('unauthorized');
})->name('unauthorized');

// Route per pagina di attesa approvazione
Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval.index');
})->name('waiting-approval')->middleware('auth');

// Route per Admin
Route::prefix('admin')->name('admin.')->middleware([
    'auth',
    'role:admin',
    \App\Http\Middleware\EnsureUserIsApproved::class
])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Approvals routes (nuove route per la gestione approvazioni)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('index');
        Route::post('/{user}/approve', [\App\Http\Controllers\Admin\ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [\App\Http\Controllers\Admin\ApprovalController::class, 'reject'])->name('reject');
    });

    // Employees routes
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EmployeeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/{employee}/request-document', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'create'])->name('request-document');

        // Gestione approvazioni
        Route::get('/pending-approval', [\App\Http\Controllers\Admin\EmployeeController::class, 'pendingApproval'])->name('pending-approval');
        Route::post('/{employee}/approve', [\App\Http\Controllers\Admin\EmployeeController::class, 'approve'])->name('approve');
        Route::post('/{employee}/reject', [\App\Http\Controllers\Admin\EmployeeController::class, 'reject'])->name('reject');
    });

    // Programs routes
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProgramController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ProgramController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ProgramController::class, 'store'])->name('store');
        Route::get('/{program}', [\App\Http\Controllers\Admin\ProgramController::class, 'show'])->name('show');
        Route::get('/{program}/edit', [\App\Http\Controllers\Admin\ProgramController::class, 'edit'])->name('edit');
        Route::put('/{program}', [\App\Http\Controllers\Admin\ProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [\App\Http\Controllers\Admin\ProgramController::class, 'destroy'])->name('destroy');
        Route::post('/{program}/add-users', [\App\Http\Controllers\Admin\ProgramController::class, 'addUsers'])->name('add-users');
        Route::delete('/{program}/users/{user}', [\App\Http\Controllers\Admin\ProgramController::class, 'removeUser'])->name('remove-user');
    });

    // Documents routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
        Route::get('/create', [AdminDocumentController::class, 'create'])->name('create');
        Route::post('/', [AdminDocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [AdminDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [AdminDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [AdminDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [AdminDocumentController::class, 'destroy'])->name('destroy');
    });

    // Tickets routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/create', [AdminTicketController::class, 'create'])->name('create');
        Route::post('/', [AdminTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/edit', [AdminTicketController::class, 'edit'])->name('edit');
        Route::put('/{ticket}', [AdminTicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [AdminTicketController::class, 'destroy'])->name('destroy');
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [AdminTicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/change-status', [AdminTicketController::class, 'changeStatus'])->name('change-status');
        Route::post('/{ticket}/assign', [AdminTicketController::class, 'assignTicket'])->name('assign');
    });

    // Courses routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::get('/{course}', [CourseController::class, 'show'])->name('show');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{course}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
        Route::post('/{course}/assign-users', [CourseController::class, 'assignUsers'])->name('assign-users');

        // Quiz routes
        Route::get('/{course}/quiz/create', [CourseController::class, 'createQuiz'])->name('quiz.create');
        Route::post('/{course}/quiz', [QuizController::class, 'store'])->name('quiz.store');
        Route::get('/{course}/quiz/edit', [QuizController::class, 'edit'])->name('quiz.edit');
        Route::put('/{course}/quiz', [QuizController::class, 'update'])->name('quiz.update');
    });

    // Checklists routes
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ChecklistController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ChecklistController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ChecklistController::class, 'store'])->name('store');
        Route::get('/{checklist}', [\App\Http\Controllers\ChecklistController::class, 'show'])->name('show');
        Route::get('/{checklist}/edit', [\App\Http\Controllers\ChecklistController::class, 'edit'])->name('edit');
        Route::put('/{checklist}', [\App\Http\Controllers\ChecklistController::class, 'update'])->name('update');
        Route::delete('/{checklist}', [\App\Http\Controllers\ChecklistController::class, 'destroy'])->name('destroy');
        Route::post('/{checklist}/assign', [\App\Http\Controllers\ChecklistController::class, 'assignToUser'])->name('assign');
        Route::post('/{checklist}/assign-multiple', [\App\Http\Controllers\ChecklistController::class, 'assignToMultipleUsers'])->name('assign-multiple');

        // Checklist items routes
        Route::get('/{checklist}/items/create', [\App\Http\Controllers\ChecklistItemController::class, 'create'])->name('items.create');
        Route::post('/{checklist}/items', [\App\Http\Controllers\ChecklistItemController::class, 'store'])->name('items.store');
        Route::get('/{checklist}/items/{item}/edit', [\App\Http\Controllers\ChecklistItemController::class, 'edit'])->name('items.edit');
        Route::put('/{checklist}/items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'update'])->name('items.update');
        Route::delete('/{checklist}/items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'destroy'])->name('items.destroy');
    });

    // User Checklist Item Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/{user}/checklists', [\App\Http\Controllers\UserChecklistController::class, 'adminIndex'])->name('checklists.index');
        Route::post('/{user}/checklist-items/{item}/approve', [\App\Http\Controllers\UserChecklistController::class, 'approve'])->name('checklist-items.approve');
        Route::post('/{user}/checklist-items/{item}/reject', [\App\Http\Controllers\UserChecklistController::class, 'reject'])->name('checklist-items.reject');
    });

    // Reports route
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/onboarding-progress', [\App\Http\Controllers\ReportController::class, 'onboardingProgress'])->name('onboarding-progress');
        Route::get('/department-stats', [\App\Http\Controllers\ReportController::class, 'departmentStats'])->name('department-stats');
        Route::get('/course-stats', [\App\Http\Controllers\ReportController::class, 'courseStats'])->name('course-stats');
        Route::get('/quiz-stats', [\App\Http\Controllers\ReportController::class, 'quizStats'])->name('quiz-stats');
        Route::get('/monthly-trends', [\App\Http\Controllers\ReportController::class, 'monthlyTrends'])->name('monthly-trends');
        Route::get('/ticket-stats', [\App\Http\Controllers\ReportController::class, 'ticketStats'])->name('ticket-stats');
    });

    // Events routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EventController::class, 'store'])->name('store');
        Route::get('/{event}', [\App\Http\Controllers\Admin\EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [\App\Http\Controllers\Admin\EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [\App\Http\Controllers\Admin\EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [\App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('destroy');
        Route::get('/{event}/participants', [\App\Http\Controllers\Admin\EventController::class, 'participants'])->name('participants');
        Route::post('/{event}/participants', [\App\Http\Controllers\Admin\EventController::class, 'addParticipants'])->name('add-participants');
        Route::delete('/{event}/participants', [\App\Http\Controllers\Admin\EventController::class, 'removeParticipant'])->name('remove-participant');
        Route::post('/{event}/mark-attendance', [\App\Http\Controllers\Admin\EventController::class, 'markAttendance'])->name('mark-attendance');
    });

    // Badges routes
    Route::prefix('badges')->name('badges.')->group(function() {
        Route::get('/', [\App\Http\Controllers\BadgeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\BadgeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\BadgeController::class, 'store'])->name('store');
        Route::get('/{badge}', [\App\Http\Controllers\BadgeController::class, 'show'])->name('show');
        Route::get('/{badge}/edit', [\App\Http\Controllers\BadgeController::class, 'edit'])->name('edit');
        Route::put('/{badge}', [\App\Http\Controllers\BadgeController::class, 'update'])->name('update');
        Route::delete('/{badge}', [\App\Http\Controllers\BadgeController::class, 'destroy'])->name('destroy');
        Route::post('/{badge}/award', [\App\Http\Controllers\BadgeController::class, 'awardBadge'])->name('award');
        Route::get('/{badge}/award', function(\App\Models\Badge $badge) {
            return redirect()->route('admin.badges.show', $badge);
        });
    });

    // Document Requests routes
    Route::prefix('document-requests')->name('document-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'store'])->name('store');
        Route::get('/{documentRequest}', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'show'])->name('show');
        Route::get('/{documentRequest}/edit', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'edit'])->name('edit');
        Route::put('/{documentRequest}', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'update'])->name('update');
        Route::delete('/{documentRequest}', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{documentRequest}/approve', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'approve'])->name('approve');
        Route::post('/{documentRequest}/reject', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'reject'])->name('reject');
        Route::get('/{documentRequest}/download', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'download'])->name('download');
    });
});

// Route diretta per la pagina di approvazione (soluzione alternativa)
Route::get('/admin/employees/pending-approval-direct', [\App\Http\Controllers\Admin\EmployeeController::class, 'pendingApproval'])
    ->middleware(['auth', 'role:admin'])
    ->name('direct.pending-approval');

// Route per Employee
Route::prefix('employee')->name('employee.')->middleware([
    'auth',
    'role:employee',
    \App\Http\Middleware\EnsureUserIsApproved::class
])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');

    // Support FAQ route
    Route::get('/support', [\App\Http\Controllers\Employee\SupportController::class, 'index'])->name('support');

    // Programs routes
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\ProgramController::class, 'index'])->name('index');
        Route::get('/{program}', [\App\Http\Controllers\Employee\ProgramController::class, 'show'])->name('show');
        // Add other program routes as needed
    });

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\EventController::class, 'index'])->name('index');
        Route::get('/{event}/calendar', [\App\Http\Controllers\Employee\EventController::class, 'calendar'])->name('calendar-event');
        Route::get('/calendar', [\App\Http\Controllers\Employee\EventController::class, 'calendar'])->name('calendar');
        Route::get('/{event}', [\App\Http\Controllers\Employee\EventController::class, 'show'])->name('show');
        Route::post('/{event}/register', [\App\Http\Controllers\Employee\EventController::class, 'register'])->name('register');
        Route::post('/{event}/cancel', [\App\Http\Controllers\Employee\EventController::class, 'cancel'])->name('cancel');
        Route::post('/{event}/confirm', [\App\Http\Controllers\Employee\EventController::class, 'confirm'])->name('confirm');
        Route::post('/{event}/decline', [\App\Http\Controllers\Employee\EventController::class, 'decline'])->name('decline');
        Route::post('/{event}/feedback', [\App\Http\Controllers\Employee\EventController::class, 'feedback'])->name('feedback');
        Route::get('/{event}/download-ticket', [\App\Http\Controllers\Employee\EventController::class, 'downloadTicket'])->name('download-ticket');
        Route::get('/{event}/download-certificate', [\App\Http\Controllers\Employee\EventController::class, 'downloadCertificate'])->name('download-certificate');
        Route::get('/{event}/download-material/{material}', [\App\Http\Controllers\Employee\EventController::class, 'downloadMaterial'])->name('download-material');
        Route::get('/{event}/verify-ticket/{user}/{hash}', [\App\Http\Controllers\Employee\EventController::class, 'verifyTicket'])->name('verify-ticket');
    });

    // Documents routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\DocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Employee\DocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Employee\DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [\App\Http\Controllers\Employee\DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [\App\Http\Controllers\Employee\DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [\App\Http\Controllers\Employee\DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [\App\Http\Controllers\Employee\DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [\App\Http\Controllers\Employee\DocumentController::class, 'download'])->name('download');
    });

    // Tickets routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [EmployeeTicketController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeTicketController::class, 'create'])->name('create');
        Route::post('/', [EmployeeTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [EmployeeTicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/edit', [EmployeeTicketController::class, 'edit'])->name('edit');
        Route::put('/{ticket}', [EmployeeTicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [EmployeeTicketController::class, 'destroy'])->name('destroy');
        Route::post('/{ticket}/reply', [EmployeeTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [EmployeeTicketController::class, 'close'])->name('close');
    });

    // Courses routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\CourseController::class, 'index'])->name('index');
        Route::get('/{course}', [\App\Http\Controllers\Employee\CourseController::class, 'show'])->name('show');
        Route::post('/{course}/start', [\App\Http\Controllers\Employee\CourseController::class, 'start'])->name('start');
        Route::post('/{course}/complete', [\App\Http\Controllers\Employee\CourseController::class, 'complete'])->name('complete');

        // Quiz routes
        Route::get('/{course}/quiz', [\App\Http\Controllers\Employee\QuizController::class, 'take'])->name('quiz.take');
        Route::post('/{course}/quiz/submit', [\App\Http\Controllers\Employee\QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('/{course}/quiz/{userQuiz}/results', [\App\Http\Controllers\Employee\QuizController::class, 'results'])->name('quiz.results');
    });

    // Checklists routes
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserChecklistController::class, 'index'])->name('index');
        Route::get('/{checklist}', [\App\Http\Controllers\UserChecklistController::class, 'show'])->name('show');
        Route::post('/items/{item}/complete', [\App\Http\Controllers\UserChecklistController::class, 'markAsCompleted'])->name('items.complete');
    });

    // Badges route for employee
    Route::prefix('badges')->name('badges.')->group(function() {
        Route::get('/', [\App\Http\Controllers\EmployeeBadgeController::class, 'index'])->name('index');
        Route::get('/{badge}', [\App\Http\Controllers\EmployeeBadgeController::class, 'show'])->name('show');
        Route::post('/{badge}/toggle-featured', [\App\Http\Controllers\EmployeeBadgeController::class, 'toggleFeatured'])->name('toggle-featured');
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Employee\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('update');
        Route::post('/upload-photo', [\App\Http\Controllers\Employee\ProfileController::class, 'uploadPhoto'])->name('upload-photo');
    });

    // Document Requests routes
    Route::prefix('document-requests')->name('document-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'index'])->name('index');
        Route::get('/{documentRequest}', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'show'])->name('show');
        Route::post('/{documentRequest}/submit', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'submitDocument'])->name('submit');
    });
});

// Home redirect
Route::get('/home', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if (Auth::check() && $user && $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('employee.dashboard');
    }
})->name('home');
