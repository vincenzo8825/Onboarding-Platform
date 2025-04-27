@php
$isAdmin = Auth::user()->hasRole('admin');
$isEmployee = Auth::user()->hasRole('employee');
@endphp

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('employee.dashboard') }}">


    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if(auth()->user()->hasRole('admin'))
        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Gestione Utenti
        </div>

        <!-- Nav Item - Employees -->
        <li class="nav-item {{ request()->routeIs('admin.employees.index') || request()->routeIs('admin.employees.show') || request()->routeIs('admin.employees.edit') || request()->routeIs('admin.employees.create') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.employees.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Dipendenti</span>
            </a>
        </li>

        <!-- Nav Item - Pending Approvals -->
        {{-- <li class="nav-item {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.approvals.index') }}">
                <i class="fas fa-fw fa-user-check"></i>
                <span>Approvazioni
                    @inject('userModel', 'App\Models\User')
                    @if($pendingCount = $userModel::where('is_approved', false)->count())
                        <span class="badge badge-danger badge-counter">{{ $pendingCount }}</span>
                    @endif
                </span>
            </a>
        </li> --}}

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Onboarding
        </div>

        <!-- Nav Item - Programs -->
        <li class="nav-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.programs.index') }}">
                <i class="fas fa-fw fa-project-diagram"></i>
                <span>Programmi</span>
            </a>
        </li>

        <!-- Nav Item - Courses -->
        <li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.courses.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Corsi</span>
            </a>
        </li>

        <!-- Nav Item - Events -->
        <li class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.events.index') }}">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Eventi</span>
            </a>
        </li>

        <!-- Nav Item - Checklists -->
        <li class="nav-item {{ request()->routeIs('admin.checklists.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.checklists.index') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Checklist</span>
            </a>
        </li>

        <!-- Nav Item - Badges -->
        <li class="nav-item {{ request()->routeIs('admin.badges.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.badges.index') }}">
                <i class="fas fa-fw fa-award"></i>
                <span>Badge</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Documenti e Supporto
        </div>

        <!-- Nav Item - Documents -->
        <li class="nav-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.documents.index') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Documenti</span>
            </a>
        </li>

        <!-- Nav Item - Document Requests -->
        <li class="nav-item {{ request()->routeIs('admin.document-requests.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.document-requests.index') }}">
                <i class="fas fa-fw fa-file-upload"></i>
                <span>Richieste Documenti</span>
            </a>
        </li>

        <!-- Nav Item - Tickets -->
        <li class="nav-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.tickets.index') }}">
                <i class="fas fa-fw fa-ticket-alt"></i>
                <span>Tickets</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Analisi
        </div>

        <!-- Nav Item - Reports -->
        <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.reports.index') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Report</span>
            </a>
        </li>

    @else
        <!-- Employee Menu Items -->
        <li class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Il tuo Onboarding
        </div>

        <!-- Nav Item - Programs -->
        <li class="nav-item {{ request()->routeIs('employee.programs.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.programs.index') }}">
                <i class="fas fa-fw fa-project-diagram"></i>
                <span>Programmi</span>
            </a>
        </li>

        <!-- Nav Item - Courses -->
        <li class="nav-item {{ request()->routeIs('employee.courses.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.courses.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Corsi</span>
            </a>
        </li>

        <!-- Nav Item - Events -->
        <li class="nav-item {{ request()->routeIs('employee.events.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.events.index') }}">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Eventi</span>
            </a>
        </li>

        <!-- Nav Item - Checklists -->
        <li class="nav-item {{ request()->routeIs('employee.checklists.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.checklists.index') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Checklist</span>
            </a>
        </li>

        <!-- Nav Item - Badges -->
        <li class="nav-item {{ request()->routeIs('employee.badges.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.badges.index') }}">
                <i class="fas fa-fw fa-award"></i>
                <span>Badge</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Documenti e Supporto
        </div>

        <!-- Nav Item - Documents -->
        <li class="nav-item {{ request()->routeIs('employee.documents.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.documents.index') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Documenti</span>
            </a>
        </li>

        <!-- Nav Item - Document Requests -->
        <li class="nav-item {{ request()->routeIs('employee.document-requests.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.document-requests.index') }}">
                <i class="fas fa-fw fa-file-upload"></i>
                <span>Richieste Documenti</span>
            </a>
        </li>

        <!-- Nav Item - Tickets -->
        <li class="nav-item {{ request()->routeIs('employee.tickets.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.tickets.index') }}">
                <i class="fas fa-fw fa-ticket-alt"></i>
                <span>Supporto</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Il tuo Profilo
        </div>

        <!-- Nav Item - Profile -->
        <li class="nav-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('employee.profile.show') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Profilo</span>
            </a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
