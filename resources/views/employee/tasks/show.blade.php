@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container py-4">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Task Details</h2>
        <a href="{{ route('employee.tasks.index') }}" class="btn btn-outline-secondary">
            &larr; Back to Tasks
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Task Details Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="mb-3 text-dark fw-bold">{{ $task->title }}</h4>

            <!-- Description in well/muted bg -->
            <div class="p-3 bg-light rounded border mb-4">
                <span class="d-block small text-muted text-uppercase fw-semibold mb-1">Description</span>
                <p class="mb-0 text-secondary" style="white-space: pre-wrap;">{{ $task->description ?: 'No description provided.' }}</p>
            </div>

            <!-- Details Row -->
            <div class="row g-3">
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Project</span>
                    <span class="fs-6 fw-medium text-dark">{{ $task->project->name ?? 'N/A' }}</span>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Created By</span>
                    <span class="fs-6 fw-medium text-dark">{{ $task->creator->name ?? 'N/A' }}</span>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Due Date</span>
                    <span class="fs-6 fw-medium text-dark">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Priority</span>
                    <div>
                        @if($task->priority === 'low')
                            <span class="badge bg-secondary">Low</span>
                        @elseif($task->priority === 'medium')
                            <span class="badge bg-primary">Medium</span>
                        @elseif($task->priority === 'high')
                            <span class="badge bg-danger">High</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($task->priority) }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Status</span>
                    <div>
                        @if($task->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($task->status === 'in_progress')
                            <span class="badge bg-info text-dark">In Progress</span>
                        @elseif($task->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Created At</span>
                    <span class="fs-6 text-muted">{{ $task->created_at ? $task->created_at->format('M d, Y H:i') : 'N/A' }}</span>
                </div>
                <div class="col-sm-6 col-md-4">
                    <span class="d-block small text-muted text-uppercase fw-semibold">Updated At</span>
                    <span class="fs-6 text-muted">{{ $task->updated_at ? $task->updated_at->format('M d, Y H:i') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Form Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 fw-semibold">Update Status</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('employee.tasks.update-status', $task) }}" class="row g-3 align-items-end">
                @csrf
                @method('PATCH')
                <div class="col-sm-6 col-md-4">
                    <label for="status" class="form-label small text-muted">Select Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6 col-md-4">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
