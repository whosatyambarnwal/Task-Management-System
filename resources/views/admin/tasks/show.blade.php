@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb / Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Task Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}" class="text-decoration-none">Tasks</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $task->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Tasks
            </a>
            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit Task
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <!-- Task Primary Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        {{ $task->title }}
                    </h5>
                    <div>
                        <!-- Status Badge -->
                        @if($task->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                        @elseif($task->status === 'in_progress')
                            <span class="badge bg-info text-dark px-3 py-2">In Progress</span>
                        @elseif($task->status === 'completed')
                            <span class="badge bg-success px-3 py-2">Completed</span>
                        @else
                            <span class="badge bg-secondary text-capitalize px-3 py-2">{{ str_replace('_', ' ', $task->status) }}</span>
                        @endif

                        <!-- Priority Badge -->
                        @if($task->priority === 'low')
                            <span class="badge bg-secondary px-3 py-2 ms-1">Low Priority</span>
                        @elseif($task->priority === 'medium')
                            <span class="badge bg-primary px-3 py-2 ms-1">Medium Priority</span>
                        @elseif($task->priority === 'high')
                            <span class="badge bg-danger px-3 py-2 ms-1">High Priority</span>
                        @else
                            <span class="badge bg-light text-dark text-capitalize px-3 py-2 ms-1">{{ $task->priority }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted text-uppercase small mb-2">Description</h6>
                    <div class="p-3 bg-light rounded border text-secondary" style="min-height: 150px; white-space: pre-wrap;">{{ $task->description ?: 'No detailed description provided for this task.' }}</div>
                </div>
            </div>
        </div>

        <!-- Metadata & Relations Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Task Metadata
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <!-- Project -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Project</span>
                            <span class="fw-bold text-dark">{{ $task->project->name ?? 'N/A' }}</span>
                        </li>

                        <!-- Assigned To -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Assigned To</span>
                            <span class="fw-bold text-dark">
                                @if($task->assignee)
                                    <i class="bi bi-person me-1 text-primary"></i>{{ $task->assignee->name }}
                                @else
                                    <span class="text-muted fst-italic">Unassigned</span>
                                @endif
                            </span>
                        </li>

                        <!-- Created By -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Created By</span>
                            <span class="fw-bold text-dark">
                                @if($task->creator)
                                    <i class="bi bi-person-badge me-1 text-secondary"></i>{{ $task->creator->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </span>
                        </li>

                        <!-- Due Date -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Due Date</span>
                            @if($task->due_date)
                                <span class="fw-bold {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : 'text-dark' }}">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $task->due_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </li>

                        <!-- Created At -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Created At</span>
                            <span class="text-muted small">
                                {{ $task->created_at ? $task->created_at->format('M d, Y h:i A') : 'N/A' }}
                            </span>
                        </li>

                        <!-- Updated At -->
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Last Updated</span>
                            <span class="text-muted small">
                                {{ $task->updated_at ? $task->updated_at->format('M d, Y h:i A') : 'N/A' }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light border-top p-3 d-flex justify-content-between">
                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Delete Task
                        </button>
                    </form>
                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
