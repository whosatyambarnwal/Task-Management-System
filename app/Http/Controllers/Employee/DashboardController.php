<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the employee dashboard.
     */
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'total_tasks' => Task::where('assigned_to', $userId)->count(),
            'pending_tasks' => Task::where('assigned_to', $userId)->where('status', 'pending')->count(),
            'in_progress_tasks' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
            'completed_tasks' => Task::where('assigned_to', $userId)->where('status', 'completed')->count(),
        ];

        $recentTasks = Task::with('project')
            ->where('assigned_to', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('employee.dashboard', compact('stats', 'recentTasks'));
    }
}
