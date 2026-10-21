@extends('layouts.app')

@section('title', 'Manage Tasks')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Tasks</h2>
            <p class="text-muted mb-0">Manage and track tasks across all projects and team members.</p>
        </div>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Create Task
        </a>
    </div>

    <!-- Search and Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.tasks.index') }}" class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-md-3">
                    <label for="search" class="form-label small fw-semibold text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search task title...">
                    </div>
                </div>

                <!-- Project Filter -->
                <div class="col-md-2">
                    <label for="project_id" class="form-label small fw-semibold text-muted mb-1">Project</label>
                    <select class="form-select" id="project_id" name="project_id">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee Filter -->
                <div class="col-md-2">
                    <label for="assigned_to" class="form-label small fw-semibold text-muted mb-1">Assignee</label>
                    <select class="form-select" id="assigned_to" name="assigned_to">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <label for="status" class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="col-md-1">
                    <label for="priority" class="form-label small fw-semibold text-muted mb-1">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'project_id', 'assigned_to', 'status', 'priority', 'due_date']))
                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 5%;">#</th>
                            <th scope="col" style="width: 25%;">Title</th>
                            <th scope="col" style="width: 15%;">Project</th>
                            <th scope="col" style="width: 15%;">Assigned To</th>
                            <th scope="col" style="width: 10%;">Priority</th>
                            <th scope="col" style="width: 10%;">Status</th>
                            <th scope="col" style="width: 10%;">Due Date</th>
                            <th scope="col" class="text-end pe-4" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $tasks->firstItem() ? $tasks->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="fw-bold text-dark text-decoration-none">
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
                                            <i class="bi bi-person text-secondary me-1"></i>
                                            <span>{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Unassigned</span>
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
                                    @if($task->due_date)
                                        <span class="small {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-info" title="View Task">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-outline-primary" title="Edit Task">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Task">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-clipboard-x fs-1 text-secondary mb-2 d-block"></i>
                                        <h5 class="fw-semibold text-dark">No tasks found</h5>
                                        <p class="text-muted small mb-3">
                                            @if(request()->anyFilled(['search', 'project_id', 'assigned_to', 'status', 'priority']))
                                                Try adjusting your filter criteria or reset the search.
                                            @else
                                                Get started by creating your first task.
                                            @endif
                                        </p>
                                        @if(request()->anyFilled(['search', 'project_id', 'assigned_to', 'status', 'priority']))
                                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                                                Reset Filters
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i> Create Task
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($tasks->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
