<div class="position-sticky pt-3">
    <ul class="nav flex-column">
        @if(auth()->user()->role === 'admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
                    <i class="fas fa-users me-2"></i>Dipendenti
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}" href="{{ route('admin.programs.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i>Programmi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.badges.*') ? 'active' : '' }}" href="{{ route('admin.badges.index') }}">
                    <i class="fas fa-award me-2"></i>Badge
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                    <i class="fas fa-chart-bar me-2"></i>Report
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employee.programs.*') ? 'active' : '' }}" href="{{ route('employee.programs.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i>I miei programmi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}" href="{{ route('employee.profile.show') }}">
                    <i class="fas fa-user me-2"></i>Profilo
                </a>
            </li>
        @endif
    </ul>
</div>
