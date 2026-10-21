@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="text-muted mb-0">Here is an overview of projects, tasks, and system activities.</p>
        </div>
        <div>
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle me-1"></i> New Task
            </a>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-folder-plus me-1"></i> New Project
            </a>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Total Projects -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-white-50">Projects</span>
                        <i class="bi bi-folder2-open fs-4 text-white-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['total_projects'] ?? 0 }}</h3>
                        <small class="text-white-50">Total Projects</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-white-50">Tasks</span>
                        <i class="bi bi-card-checklist fs-4 text-white-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['total_tasks'] ?? 0 }}</h3>
                        <small class="text-white-50">Total Tasks</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-warning text-dark shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-dark-50">Pending</span>
                        <i class="bi bi-hourglass-split fs-4 text-dark-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['pending_tasks'] ?? 0 }}</h3>
                        <small class="text-muted">Pending Tasks</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-info text-dark shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-dark-50">In Progress</span>
                        <i class="bi bi-arrow-repeat fs-4 text-dark-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['in_progress_tasks'] ?? 0 }}</h3>
                        <small class="text-muted">In Progress</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-white-50">Completed</span>
                        <i class="bi bi-check2-circle fs-4 text-white-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['completed_tasks'] ?? 0 }}</h3>
                        <small class="text-white-50">Completed Tasks</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees -->
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="card bg-secondary text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-semibold text-white-50">Employees</span>
                        <i class="bi bi-people fs-4 text-white-50"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $stats['total_employees'] ?? 0 }}</h3>
                        <small class="text-white-50">Total Employees</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="card-title fw-bold mb-0 text-dark">
                <i class="bi bi-clock-history me-2 text-primary"></i>Recent Tasks
            </h5>
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">
                View All Tasks <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 30%;">Title</th>
                        <th scope="col">Project</th>
                        <th scope="col">Assigned To</th>
                        <th scope="col">Status</th>
                        <th scope="col">Priority</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('admin.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-dark">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $task->project->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($task->assignee)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle text-secondary me-2"></i>
                                        <span>{{ $task->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($task->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($task->status === 'in_progress')
                                    <span class="badge bg-info text-dark">In Progress</span>
                                @elseif($task->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $task->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($task->priority === 'low')
                                    <span class="badge bg-secondary">Low</span>
                                @elseif($task->priority === 'medium')
                                    <span class="badge bg-primary">Medium</span>
                                @elseif($task->priority === 'high')
                                    <span class="badge bg-danger">High</span>
                                @else
                                    <span class="badge bg-light text-dark text-capitalize">{{ $task->priority }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No recent tasks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
