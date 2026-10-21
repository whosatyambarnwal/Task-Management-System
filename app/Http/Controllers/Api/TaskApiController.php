<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    /**
     * GET /api/tasks — list tasks visible to the authenticated user.
     * Admin sees all tasks, Employee sees only their own.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Task::with(['project:id,name', 'assignee:id,name', 'creator:id,name']);

        // Scope: employees see only their own tasks
        if ($user->isEmployee()) {
            $query->where('assigned_to', $user->id);
        }

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('assigned_to') && $user->isAdmin()) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * GET /api/tasks/{task} — show a single task if authorized.
     */
    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        // Employee can only see their own tasks
        if ($user->isEmployee() && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $task->load(['project:id,name', 'assignee:id,name', 'creator:id,name']);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    /**
     * PATCH /api/tasks/{task}/status — update task status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = $request->user();

        // Employee can only update their own tasks
        if ($user->isEmployee() && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'data' => [
                'id' => $task->id,
                'status' => $task->status,
                'updated_at' => $task->updated_at,
            ],
        ]);
    }

    /**
     * POST /api/tasks — Admin-only: create a new task.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'required|in:low,medium,high',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Verify assignee is an employee
        $assignee = User::findOrFail($validated['assigned_to']);
        if (!$assignee->isEmployee()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['assigned_to' => ['Tasks can only be assigned to employees.']],
            ], 422);
        }

        $validated['created_by'] = $user->id;
        $validated['status'] = $validated['status'] ?? 'pending';

        $task = Task::create($validated);
        $task->load(['project:id,name', 'assignee:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    /**
     * PUT /api/tasks/{task} — Admin-only: update a task.
     */
    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }

        $validated = $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'assigned_to' => 'sometimes|exists:users,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'sometimes|in:low,medium,high',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        if (isset($validated['assigned_to'])) {
            $assignee = User::findOrFail($validated['assigned_to']);
            if (!$assignee->isEmployee()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['assigned_to' => ['Tasks can only be assigned to employees.']],
                ], 422);
            }
        }

        $task->update($validated);
        $task->load(['project:id,name', 'assignee:id,name', 'creator:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }
}
