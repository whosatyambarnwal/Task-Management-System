<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Task Management System') - Task Management System</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        @auth
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            overflow-y: auto;
        }
        .main-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        @else
        .main-wrapper {
            margin-left: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        @endauth
        .sidebar .nav-link {
            color: #adb5bd;
            font-weight: 500;
            padding: 0.65rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease-in-out;
        }
        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #0d6efd;
        }
        .sidebar-brand {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        .sidebar-brand:hover {
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <!-- Sidebar Navigation -->
    <aside class="sidebar bg-dark text-white d-flex flex-column p-3">
        <div class="sidebar-header pb-3 mb-3 border-bottom border-secondary">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="sidebar-brand d-flex align-items-center">
                <i class="bi bi-kanban me-2 text-primary fs-4"></i>
                <span>Task Manager</span>
            </a>
            <div class="text-secondary small mt-2">
                Signed in as: <span class="badge bg-secondary text-capitalize">{{ auth()->user()->role }}</span>
            </div>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                        <i class="bi bi-folder2-open me-2"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-square me-2"></i> Tasks
                    </a>
                </li>
            @elseif(auth()->user()->isEmployee())
                <li class="nav-item">
                    <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employee.tasks.index') }}" class="nav-link {{ request()->routeIs('employee.tasks.*') ? 'active' : '' }}">
                        <i class="bi bi-list-task me-2"></i> My Tasks
                    </a>
                </li>
            @endif
        </ul>

        <div class="sidebar-footer mt-auto pt-3 border-top border-secondary text-secondary small">
            <div>User: <strong class="text-white">{{ auth()->user()->name }}</strong></div>
            <div class="text-truncate">{{ auth()->user()->email }}</div>
        </div>
    </aside>
    @endauth

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        @auth
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-light bg-white border-bottom shadow-sm px-4 py-2 sticky-top">
            <div class="container-fluid p-0">
                <span class="navbar-brand fw-bold text-primary mb-0 h5">
                    Task Management System
                </span>

                <div class="d-flex align-items-center ms-auto">
                    <span class="navbar-text me-3 fw-semibold text-dark">
                        <i class="bi bi-person-circle me-1 text-secondary"></i>
                        {{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        @endauth

        <!-- Main Content Area -->
        <main class="flex-grow-1 p-4">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-error alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
