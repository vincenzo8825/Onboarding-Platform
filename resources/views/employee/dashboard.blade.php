@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
@endphp

<x-layout>
    <x-slot name="styles">
        @vite('resources/css/pages/employee.css')
    </x-slot>
    <x-slot name="sidebar">
        @include('components.sidebar')
    </x-slot>

    <div class="employee-dashboard container-fluid py-4">
        <h1 class="employee-dashboard__title mb-4">Dashboard Dipendente</h1>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Notifiche -->
        <div class="employee-dashboard__section mb-4">
            <div class="employee-dashboard__notification-panel">
                <div class="employee-dashboard__notification-header">
                    <h2 class="employee-dashboard__section-title">
                        <i class="fas fa-bell"></i> Notifiche
                    </h2>
                    <span class="employee-dashboard__notification-badge">{{ count($notifications) }}</span>
                </div>

                <div class="employee-dashboard__notification-list">
                    @forelse($notifications as $notification)
                        <div class="employee-dashboard__notification-item {{ $notification->read_at ? 'employee-dashboard__notification-item--read' : '' }}">
                            <div class="employee-dashboard__notification-icon">
                                @if($notification->type == 'App\Notifications\NewCourseAssigned')
                                    <i class="fas fa-graduation-cap"></i>
                                @elseif($notification->type == 'App\Notifications\NewTaskAssigned')
                                    <i class="fas fa-tasks"></i>
                                @elseif($notification->type == 'App\Notifications\DocumentRequested')
                                    <i class="fas fa-file-alt"></i>
                                @elseif($notification->type == 'App\Notifications\EventReminder')
                                    <i class="fas fa-calendar-alt"></i>
                                @else
                                    <i class="fas fa-info-circle"></i>
                                @endif
                            </div>

                            <div class="employee-dashboard__notification-content">
                                <h3 class="employee-dashboard__notification-title">{{ $notification->data['title'] }}</h3>
                                <p class="employee-dashboard__notification-message">{{ $notification->data['message'] }}</p>
                                <span class="employee-dashboard__notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="employee-dashboard__notification-actions">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="employee-dashboard__notification-button">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="employee-dashboard__empty-state">
                            <i class="fas fa-bell-slash employee-dashboard__empty-state-icon"></i>
                            <p class="employee-dashboard__empty-state-message">Non hai notifiche non lette</p>
                        </div>
                    @endforelse
                </div>

                @if(count($notifications) > 0)
                    <div class="employee-dashboard__notification-footer">
                        <a href="{{ route('notifications.index') }}" class="employee-dashboard__view-all">
                            Vedi tutte le notifiche <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stato Onboarding -->
        <div class="employee-dashboard__section mb-4">
            <div class="employee-dashboard__progress-panel">
                <h2 class="employee-dashboard__section-title">
                    <i class="fas fa-rocket"></i> Il tuo percorso di Onboarding
                </h2>

                @php
                    $totalSteps = $myTotalCourses + $myTotalTasks + $documentsSubmitted;
                    $completedSteps = $myCoursesCompleted + $myTasksCompleted + $documentsApproved;
                    $progressPercentage = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                @endphp

                <div class="employee-dashboard__progress-container">
                    <div class="employee-dashboard__progress-info">
                        <h3 class="employee-dashboard__progress-title">Progresso Complessivo</h3>
                        <span class="employee-dashboard__progress-percentage">{{ $progressPercentage }}%</span>
                    </div>

                    <div class="employee-dashboard__progress-bar">
                        <div class="employee-dashboard__progress-fill" style="width: {{ $progressPercentage }}%"></div>
                    </div>

                    <div class="employee-dashboard__progress-stats">
                        <div class="employee-dashboard__progress-stat">
                            <i class="fas fa-graduation-cap employee-dashboard__progress-stat-icon"></i>
                            <span class="employee-dashboard__progress-stat-value">{{ $myCoursesCompleted }}/{{ $myTotalCourses }}</span>
                            <span class="employee-dashboard__progress-stat-label">Corsi</span>
                        </div>

                        <div class="employee-dashboard__progress-stat">
                            <i class="fas fa-tasks employee-dashboard__progress-stat-icon"></i>
                            <span class="employee-dashboard__progress-stat-value">{{ $myTasksCompleted }}/{{ $myTotalTasks }}</span>
                            <span class="employee-dashboard__progress-stat-label">Task</span>
                        </div>

                        <div class="employee-dashboard__progress-stat">
                            <i class="fas fa-file-alt employee-dashboard__progress-stat-icon"></i>
                            <span class="employee-dashboard__progress-stat-value">{{ $documentsApproved }}/{{ $documentsSubmitted }}</span>
                            <span class="employee-dashboard__progress-stat-label">Documenti</span>
                        </div>
                    </div>
                </div>

                <div class="employee-dashboard__progress-next">
                    <h3 class="employee-dashboard__progress-next-title">Prossimi passi consigliati</h3>

                    @if($nextCourse)
                        <div class="employee-dashboard__next-item">
                            <div class="employee-dashboard__next-item-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="employee-dashboard__next-item-content">
                                <h4 class="employee-dashboard__next-item-title">{{ $nextCourse->title }}</h4>
                                <p class="employee-dashboard__next-item-description">{{ Str::limit($nextCourse->description, 100) }}</p>
                            </div>
                            <a href="{{ route('employee.courses.show', $nextCourse) }}" class="employee-dashboard__next-item-action btn btn-primary">
                                Inizia
                            </a>
                        </div>
                    @elseif($nextTask)
                        <div class="employee-dashboard__next-item">
                            <div class="employee-dashboard__next-item-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="employee-dashboard__next-item-content">
                                <h4 class="employee-dashboard__next-item-title">{{ $nextTask->title }}</h4>
                                <p class="employee-dashboard__next-item-description">{{ Str::limit($nextTask->description, 100) }}</p>
                            </div>
                            <a href="{{ route('employee.checklists.show', $nextTask) }}" class="employee-dashboard__next-item-action btn btn-primary">
                                Visualizza
                            </a>
                        </div>
                    @elseif($nextDocument)
                        <div class="employee-dashboard__next-item">
                            <div class="employee-dashboard__next-item-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="employee-dashboard__next-item-content">
                                <h4 class="employee-dashboard__next-item-title">{{ $nextDocument->name }}</h4>
                                <p class="employee-dashboard__next-item-description">{{ Str::limit($nextDocument->description, 100) }}</p>
                            </div>
                            <a href="{{ route('employee.documents.show', $nextDocument) }}" class="employee-dashboard__next-item-action btn btn-primary">
                                Carica
                            </a>
                        </div>
                    @else
                        <div class="employee-dashboard__empty-state">
                            <i class="fas fa-check-circle employee-dashboard__empty-state-icon"></i>
                            <p class="employee-dashboard__empty-state-message">Hai completato tutte le attività assegnate!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Task e Corsi Assegnati -->
            <div class="col-lg-8">
                <!-- Task -->
                <div class="employee-dashboard__section mb-4">
                    <div class="employee-dashboard__content-panel">
                        <div class="employee-dashboard__content-header">
                            <h2 class="employee-dashboard__section-title">
                                <i class="fas fa-tasks"></i> I tuoi Task
                            </h2>
                            <a href="{{ route('employee.checklists.index') }}" class="employee-dashboard__view-all">
                                Vedi tutti <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="employee-dashboard__content-body">
                            @forelse($checklistItems as $task)
                                <div class="employee-dashboard__task-item">
                                    <div class="employee-dashboard__task-status">
                                        @if($task->pivot->status == 'completed')
                                            <i class="fas fa-check-circle employee-dashboard__task-status-icon--completed"></i>
                                        @elseif($task->pivot->status == 'in_progress')
                                            <i class="fas fa-spinner employee-dashboard__task-status-icon--progress"></i>
                                        @else
                                            <i class="fas fa-clock employee-dashboard__task-status-icon--pending"></i>
                                        @endif
                                    </div>

                                    <div class="employee-dashboard__task-info">
                                        <h3 class="employee-dashboard__task-title">{{ $task->title }}</h3>
                                        <p class="employee-dashboard__task-description">{{ Str::limit($task->description, 100) }}</p>

                                        <div class="employee-dashboard__task-meta">
                                            @if($task->due_date)
                                                <span class="employee-dashboard__task-meta-item">
                                                    <i class="fas fa-calendar"></i>
                                                    @if(\Carbon\Carbon::parse($task->due_date)->isPast())
                                                        <span class="text-danger">Scaduta il {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</span>
                                                    @else
                                                        Scade il {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                                    @endif
                                                </span>
                                            @endif

                                            <span class="employee-dashboard__task-meta-item">
                                                <i class="fas fa-tag"></i> {{ $task->category }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="employee-dashboard__task-actions">
                                        <a href="{{ route('employee.checklists.show', $task) }}" class="employee-dashboard__task-button">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="employee-dashboard__empty-state">
                                    <i class="fas fa-tasks employee-dashboard__empty-state-icon"></i>
                                    <p class="employee-dashboard__empty-state-message">Non hai task assegnati</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Corsi -->
                <div class="employee-dashboard__section mb-4">
                    <div class="employee-dashboard__content-panel">
                        <div class="employee-dashboard__content-header">
                            <h2 class="employee-dashboard__section-title">
                                <i class="fas fa-graduation-cap"></i> I tuoi Corsi
                            </h2>
                            <a href="{{ route('employee.courses.index') }}" class="employee-dashboard__view-all">
                                Vedi tutti <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="employee-dashboard__content-body">
                            @forelse($courses as $course)
                                <div class="employee-dashboard__course-item">
                                    <div class="employee-dashboard__course-image">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="employee-dashboard__course-img">
                                        @else
                                            <div class="employee-dashboard__course-img-placeholder">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="employee-dashboard__course-info">
                                        <h3 class="employee-dashboard__course-title">{{ $course->title }}</h3>
                                        <p class="employee-dashboard__course-description">{{ Str::limit($course->description, 100) }}</p>

                                        <div class="employee-dashboard__course-progress">
                                            @php
                                                $totalLessons = isset($course->lessons) && $course->lessons !== null ? $course->lessons->count() : 0;
                                                $lessonsCompleted = $totalLessons > 0 && method_exists($course, 'lessonsCompleted')
                                                    ? $course->lessonsCompleted(auth()->user())
                                                    : 0;
                                                $progressPercent = $totalLessons > 0
                                                    ? round(($lessonsCompleted / $totalLessons) * 100)
                                                    : 0;
                                            @endphp

                                            <div class="employee-dashboard__course-progress-info">
                                                <span class="employee-dashboard__course-progress-text">
                                                    {{ $lessonsCompleted }}/{{ $totalLessons }} lezioni
                                                </span>
                                                <span class="employee-dashboard__course-progress-percent">
                                                    {{ $progressPercent }}%
                                                </span>
                                            </div>

                                            <div class="employee-dashboard__course-progress-bar">
                                                <div class="employee-dashboard__course-progress-fill" style="width: {{ $progressPercent }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="employee-dashboard__course-actions">
                                        <a href="{{ route('employee.courses.show', $course) }}" class="btn btn-sm btn-primary">
                                            @if($progressPercent == 0)
                                                Inizia
                                            @elseif($progressPercent == 100)
                                                Rivedi
                                            @else
                                                Continua
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="employee-dashboard__empty-state">
                                    <i class="fas fa-graduation-cap employee-dashboard__empty-state-icon"></i>
                                    <p class="employee-dashboard__empty-state-message">Non hai corsi assegnati</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documenti e Eventi -->
            <div class="col-lg-4">
                <!-- Documenti -->
                <div class="employee-dashboard__section mb-4">
                    <div class="employee-dashboard__content-panel">
                        <div class="employee-dashboard__content-header">
                            <h2 class="employee-dashboard__section-title">
                                <i class="fas fa-file-alt"></i> Documenti
                            </h2>
                            <a href="{{ route('employee.documents.index') }}" class="employee-dashboard__view-all">
                                Vedi tutti <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="employee-dashboard__content-body">
                            @forelse($documents as $document)
                                <div class="employee-dashboard__document-item">
                                    <div class="employee-dashboard__document-icon">
                                        @php
                                            $extension = '';
                                            if (isset($document->pivot) && $document->pivot !== null && isset($document->pivot->file_path)) {
                                                $extension = $document->pivot->file_path
                                                    ? pathinfo($document->pivot->file_path, PATHINFO_EXTENSION)
                                                    : '';
                                            }
                                        @endphp

                                        @if(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'pending')
                                            <i class="fas fa-file employee-dashboard__document-icon--pending"></i>
                                        @elseif($extension == 'pdf')
                                            <i class="fas fa-file-pdf employee-dashboard__document-icon--uploaded"></i>
                                        @elseif(in_array($extension, ['doc', 'docx']))
                                            <i class="fas fa-file-word employee-dashboard__document-icon--uploaded"></i>
                                        @elseif(in_array($extension, ['xls', 'xlsx']))
                                            <i class="fas fa-file-excel employee-dashboard__document-icon--uploaded"></i>
                                        @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                            <i class="fas fa-file-image employee-dashboard__document-icon--uploaded"></i>
                                        @else
                                            <i class="fas fa-file-alt employee-dashboard__document-icon--uploaded"></i>
                                        @endif
                                    </div>

                                    <div class="employee-dashboard__document-info">
                                        <h3 class="employee-dashboard__document-title">{{ $document->name }}</h3>

                                        <div class="employee-dashboard__document-meta">
                                            <span class="employee-dashboard__document-status">
                                                @if(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'pending')
                                                    <span class="badge bg-warning">Da caricare</span>
                                                @elseif(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'uploaded')
                                                    <span class="badge bg-info">Caricato</span>
                                                @elseif(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'approved')
                                                    <span class="badge bg-success">Approvato</span>
                                                @elseif(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'rejected')
                                                    <span class="badge bg-danger">Rifiutato</span>
                                                @endif
                                            </span>

                                            @if(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->file_path) && $document->pivot->file_path)
                                                <span class="employee-dashboard__document-date">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ isset($document->pivot->updated_at) ? \Carbon\Carbon::parse($document->pivot->updated_at)->format('d/m/Y') : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="employee-dashboard__document-actions">
                                        <a href="{{ route('employee.documents.show', $document) }}" class="employee-dashboard__document-button">
                                            @if(isset($document->pivot) && $document->pivot !== null && isset($document->pivot->status) && $document->pivot->status == 'pending')
                                                <i class="fas fa-upload"></i>
                                            @else
                                                <i class="fas fa-eye"></i>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="employee-dashboard__empty-state">
                                    <i class="fas fa-file-alt employee-dashboard__empty-state-icon"></i>
                                    <p class="employee-dashboard__empty-state-message">Non hai documenti richiesti</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Eventi -->
                <div class="employee-dashboard__section mb-4">
                    <div class="employee-dashboard__content-panel">
                        <div class="employee-dashboard__content-header">
                            <h2 class="employee-dashboard__section-title">
                                <i class="fas fa-calendar-alt"></i> Prossimi Eventi
                            </h2>
                            <a href="{{ route('employee.events.index') }}" class="employee-dashboard__view-all">
                                Vedi tutti <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="employee-dashboard__content-body">
                            @forelse($upcomingEvents as $event)
                                <div class="employee-dashboard__event-item">
                                    <div class="employee-dashboard__event-date">
                                        <span class="employee-dashboard__event-date-day">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
                                        </span>
                                        <span class="employee-dashboard__event-date-month">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M') }}
                                        </span>
                                    </div>

                                    <div class="employee-dashboard__event-info">
                                        <h3 class="employee-dashboard__event-title">{{ $event->title }}</h3>
                                        <p class="employee-dashboard__event-description">{{ Str::limit($event->description, 80) }}</p>

                                        <div class="employee-dashboard__event-meta">
                                            <span class="employee-dashboard__event-meta-item">
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($event->end_date)->format('H:i') }}
                                            </span>

                                            <span class="employee-dashboard__event-meta-item">
                                                @if($event->is_online)
                                                    <i class="fas fa-video"></i> Evento online
                                                @else
                                                    <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="employee-dashboard__event-actions">
                                        <a href="{{ route('employee.events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                            Dettagli
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="employee-dashboard__empty-state">
                                    <i class="fas fa-calendar-times employee-dashboard__empty-state-icon"></i>
                                    <p class="employee-dashboard__empty-state-message">Non ci sono eventi programmati</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Supporto -->
                <div class="employee-dashboard__section">
                    <div class="employee-dashboard__support-panel">
                        <h2 class="employee-dashboard__section-title">
                            <i class="fas fa-question-circle"></i> Hai bisogno di aiuto?
                        </h2>
                        <p class="employee-dashboard__support-text">
                            Se hai domande o problemi durante il tuo percorso di onboarding, non esitare a contattare il team di supporto.
                        </p>
                        <div class="employee-dashboard__support-actions">
                            <a href="{{ route('employee.tickets.create') }}" class="btn btn-primary">
                                <i class="fas fa-ticket-alt"></i> Apri un Ticket
                            </a>
                            <a href="{{ route('employee.support') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-info-circle"></i> FAQ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
