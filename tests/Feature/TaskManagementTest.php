<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employee1;
    protected User $employee2;
    protected Project $project;
    protected Task $task1;
    protected Task $task2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Employees
        $this->employee1 = User::create([
            'name' => 'Alice Employee',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $this->employee2 = User::create([
            'name' => 'Bob Employee',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        // Create Project
        $this->project = Project::create([
            'name' => 'Alpha Project',
            'description' => 'Test project description',
            'status' => 'active',
        ]);

        // Create Tasks
        $this->task1 = Task::create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
            'title' => 'Task for Alice',
            'description' => 'Description for Alice task',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
        ]);

        $this->task2 = Task::create([
            'project_id' => $this->project->id,
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->admin->id,
            'title' => 'Task for Bob',
            'description' => 'Description for Bob task',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => now()->addDays(10)->toDateString(),
        ]);
    }

    /**
     * Test 1: Admin can access the Admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    /**
     * Test 2: Employee cannot access Admin pages.
     */
    public function test_employee_cannot_access_admin_pages(): void
    {
        $response = $this->actingAs($this->employee1)->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $responseProject = $this->actingAs($this->employee1)->get(route('admin.projects.index'));
        $responseProject->assertStatus(403);

        $responseTasks = $this->actingAs($this->employee1)->get(route('admin.tasks.index'));
        $responseTasks->assertStatus(403);
    }

    /**
     * Test 3: Employee sees only their own tasks.
     */
    public function test_employee_sees_only_their_own_tasks(): void
    {
        $response = $this->actingAs($this->employee1)->get(route('employee.tasks.index'));
        $response->assertStatus(200);
        $response->assertSee('Task for Alice');
        $response->assertDontSee('Task for Bob');
    }

    /**
     * Test 4: Employee cannot view another employee's task by changing its ID.
     */
    public function test_employee_cannot_view_another_employees_task(): void
    {
        $response = $this->actingAs($this->employee1)->get(route('employee.tasks.show', $this->task2));
        $response->assertStatus(403);
    }

    /**
     * Test 5: Employee cannot update another employee's task through the API.
     */
    public function test_employee_cannot_update_another_employees_task_through_api(): void
    {
        Sanctum::actingAs($this->employee1);

        $response = $this->patchJson("/api/tasks/{$this->task2->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task2->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test 6: Invalid task statuses are rejected.
     */
    public function test_invalid_task_statuses_are_rejected(): void
    {
        Sanctum::actingAs($this->employee1);

        $response = $this->patchJson("/api/tasks/{$this->task1->id}/status", [
            'status' => 'invalid_status_value',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);

        // Web route validation
        $webResponse = $this->actingAs($this->employee1)
            ->patch(route('employee.tasks.update-status', $this->task1), [
                'status' => 'invalid_status_value',
            ]);
        $webResponse->assertSessionHasErrors(['status']);
    }

    /**
     * Test 7: Admin can create or assign a task.
     */
    public function test_admin_can_create_and_assign_task(): void
    {
        $taskData = [
            'project_id' => $this->project->id,
            'assigned_to' => $this->employee1->id,
            'title' => 'New Deployment Pipeline',
            'description' => 'Configure automated CI/CD pipeline',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.tasks.store'), $taskData);
        $response->assertRedirect(route('admin.tasks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Deployment Pipeline',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Test 8: Valid task status updates succeed (web and API).
     */
    public function test_valid_task_status_updates_succeed(): void
    {
        // Test via Employee Web route (JSON / AJAX mode)
        $response = $this->actingAs($this->employee1)
            ->patchJson(route('employee.tasks.update-status', $this->task1), [
                'status' => 'completed',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Task status updated successfully.',
            'task' => [
                'id' => $this->task1->id,
                'status' => 'completed',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task1->id,
            'status' => 'completed',
        ]);

        // Test via Sanctum API
        Sanctum::actingAs($this->employee1);
        $apiResponse = $this->patchJson("/api/tasks/{$this->task1->id}/status", [
            'status' => 'in_progress',
        ]);

        $apiResponse->assertStatus(200);
        $apiResponse->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->task1->id,
                'status' => 'in_progress',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task1->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test 9: Unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('employee.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test 10: API endpoints require authentication and return 401 when unauthenticated.
     */
    public function test_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }
}
