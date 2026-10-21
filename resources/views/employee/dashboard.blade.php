@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-1">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-muted">Here is an overview of your tasks and assignments.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="card-title fw-bold">{{ $stats['total_tasks'] }}</h3>
                    <p class="card-text mb-0">My Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="card-title fw-bold">{{ $stats['pending_tasks'] }}</h3>
                    <p class="card-text mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="card-title fw-bold">{{ $stats['in_progress_tasks'] }}</h3>
                    <p class="card-text mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="card-title fw-bold">{{ $stats['completed_tasks'] }}</h3>
                    <p class="card-text mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-semibold">Recent Tasks</h5>
            <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('employee.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-primary">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td>{{ $task->project->name ?? 'N/A' }}</td>
                                <td>
                                    @if($task->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge bg-info text-dark">In Progress</span>
                                    @elseif($task->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
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
                                        <span class="badge bg-secondary">{{ ucfirst($task->priority) }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No tasks assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
