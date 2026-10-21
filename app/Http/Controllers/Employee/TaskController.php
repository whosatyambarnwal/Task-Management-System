<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the employee's tasks.
     */
    public function index(Request $request)
    {
        $query = Task::with(['project'])
            ->where('assigned_to', auth()->id());

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->latest()->paginate(15)->appends($request->query());

        return view('employee.tasks.index', compact('tasks'));
    }

    /**
     * Display the specified task (only if assigned to this employee).
     */
    public function show(Task $task)
    {
        // Authorization: only show task if assigned to the logged-in employee
        if ($task->assigned_to !== auth()->id()) {
            abort(403, 'You are not authorized to view this task.');
        }

        $task->load(['project', 'creator']);
        return view('employee.tasks.show', compact('task'));
    }

    /**
     * Update the status of the employee's own task (AJAX-friendly).
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Authorization: only allow status update if task is assigned to logged-in employee
        if ($task->assigned_to !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to update this task.'], 403);
            }
            abort(403, 'You are not authorized to update this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task status updated successfully.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                ],
            ]);
        }

        return redirect()->route('employee.tasks.index')
            ->with('success', 'Task status updated successfully.');
    }
}
