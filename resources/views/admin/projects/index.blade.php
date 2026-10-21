@extends('layouts.app')

@section('title', 'Manage Projects')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Projects</h2>
            <p class="text-muted mb-0">Manage all projects and view their task counts.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Create Project
        </a>
    </div>

    <!-- Projects Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4" style="width: 5%;">#</th>
                            <th scope="col" style="width: 25%;">Name</th>
                            <th scope="col" style="width: 35%;">Description</th>
                            <th scope="col" style="width: 12%;">Status</th>
                            <th scope="col" style="width: 10%;">Tasks Count</th>
                            <th scope="col" class="text-end pe-4" style="width: 13%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">
                                    {{ $projects->firstItem() ? $projects->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $project->name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ Str::limit($project->description, 50, '...') ?: 'No description provided' }}
                                    </span>
                                </td>
                                <td>
                                    @if($project->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($project->status === 'completed')
                                        <span class="badge bg-primary">Completed</span>
                                    @elseif($project->status === 'on_hold')
                                        <span class="badge bg-warning text-dark">On Hold</span>
                                    @else
                                        <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $project->tasks_count }} tasks
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline-primary" title="Edit Project">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project? All associated tasks will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger ms-1" title="Delete Project">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-folder-x fs-1 text-secondary mb-2 d-block"></i>
                                        <h5 class="fw-semibold text-dark">No projects found</h5>
                                        <p class="text-muted small mb-3">Get started by creating your first project.</p>
                                        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i> Create Project
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($projects->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
