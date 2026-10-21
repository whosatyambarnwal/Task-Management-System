<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks with search and filters.
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignee', 'creator']);

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by employee
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by due date
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        $tasks = $query->latest()->paginate(15)->appends($request->query());
        $projects = Project::orderBy('name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.tasks.index', compact('tasks', 'projects', 'employees'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $projects = Project::orderBy('name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.tasks.create', compact('projects', 'employees'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Verify assigned_to is an employee
        $assignee = User::findOrFail($validated['assigned_to']);
        if (!$assignee->isEmployee()) {
            return back()->withErrors(['assigned_to' => 'Tasks can only be assigned to employees.'])
                ->withInput();
        }

        $validated['created_by'] = auth()->id();

        Task::create($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignee', 'creator']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing a task.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.tasks.edit', compact('task', 'projects', 'employees'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        // Verify assigned_to is an employee
        $assignee = User::findOrFail($validated['assigned_to']);
        if (!$assignee->isEmployee()) {
            return back()->withErrors(['assigned_to' => 'Tasks can only be assigned to employees.'])
                ->withInput();
        }

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
