@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">My Tasks</h2>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('employee.tasks.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label small text-muted">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label small text-muted">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="priority" class="form-label small text-muted">Priority</label>
                    <select name="priority" id="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('employee.tasks.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Priority</th>
                            <th style="min-width: 170px;">Status</th>
                            <th>Due Date</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>{{ $loop->iteration + ($tasks->currentPage() - 1) * $tasks->perPage() }}</td>
                                <td>
                                    <a href="{{ route('employee.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-primary">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td>{{ $task->project->name ?? 'N/A' }}</td>
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
                                    <div class="d-flex align-items-center">
                                        <select class="form-select form-select-sm status-select" data-task-id="{{ $task->id }}" data-original="{{ $task->status }}">
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </td>
                                <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No tasks found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tasks->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-end">
                    {{ $tasks->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.status-select').on('change', function() {
        var select = $(this);
        var taskId = select.data('task-id');
        var newStatus = select.val();
        var originalStatus = select.data('original');

        // Disable select and show loading
        select.prop('disabled', true);
        select.closest('td').append('<span class="spinner-border spinner-border-sm ms-2 status-spinner" role="status"></span>');

        $.ajax({
            url: '/employee/tasks/' + taskId + '/status',
            method: 'PATCH',
            data: { status: newStatus },
            dataType: 'json',
            success: function(response) {
                select.data('original', newStatus);
                // Show success toast/alert
                showAlert('success', response.message || 'Status updated successfully!');
            },
            error: function(xhr) {
                // Revert to original
                select.val(originalStatus);
                var message = 'Failed to update status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert('danger', message);
            },
            complete: function() {
                select.prop('disabled', false);
                select.closest('td').find('.status-spinner').remove();
            }
        });
    });

    function showAlert(type, message) {
        var alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed" style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('body').append(alert);
        setTimeout(function() { alert.alert('close'); }, 3000);
    }
});
</script>
@endpush
