# Task Management System (Laravel)

A complete, role-based Task Management System built with **Laravel 10**, **MySQL**, **Bootstrap 5**, **jQuery AJAX**, and **Laravel Sanctum REST API**.

---

## 1. System Overview & Features

### Core Capabilities:
- **Role-Based Access Control (RBAC):**
  - **Admin:** Complete project & task CRUD, assign tasks to employees, search & filter tasks by project, employee, status, priority, and due date, real-time analytics dashboard.
  - **Employee:** View only assigned tasks, isolated details view, real-time AJAX inline status updates, dedicated personal dashboard.
- **Server-Side Authorization & Security:**
  - Route middleware (`auth`, `admin`, `employee`).
  - Strict ownership checks on both web and API requests (employees cannot view, modify, or infer another employee's tasks).
  - Validation of all request inputs, statuses, dates, and assignees.
  - Foreign key cascades and database transactions preventing orphaned records.
  - CSRF protection across all forms and AJAX requests.
- **AJAX Task Status Updates:**
  - Dynamic inline dropdown on the employee tasks table.
  - Loading spinner indicator & disabled state during transit.
  - Automatic error handling with toast notifications and revert on failure.
- **Sanctum REST API:**
  - Token-authenticated JSON endpoints.
  - Scoped visibility (employees only see their own tasks; admins see all).
  - Proper HTTP status codes: `200`, `201`, `401`, `403`, `404`, `422`.

---

## 2. Database Design & Relationships

### Schema & Referential Integrity:
- **`users`**: `id`, `name`, `email` (unique), `password`, `role` (`admin` or `employee`), `remember_token`, timestamps.
- **`projects`**: `id`, `name`, `description`, `status` (`active`, `completed`, `on_hold`), timestamps.
- **`tasks`**: `id`, `project_id` (FK to `projects` cascade delete), `assigned_to` (FK to `users`), `created_by` (FK to `users`), `title`, `description`, `priority` (`low`, `medium`, `high`), `status` (`pending`, `in_progress`, `completed`), `due_date`, timestamps.
  - Indexes on `status`, `priority`, and `due_date` for high-performance filtering.

### Eloquent Relationships:
- `Project` **hasMany** `Task` (`$project->tasks`)
- `Task` **belongsTo** `Project` (`$task->project`)
- `User` **hasMany** assigned `Task` (`$user->assignedTasks`)
- `User` **hasMany** created `Task` (`$user->createdTasks`)
- `Task` **belongsTo** assignee `User` (`$task->assignee`)
- `Task` **belongsTo** creator `User` (`$task->creator`)

---

## 3. Demo Credentials

The database seeder provisions the following accounts (all use password: `password`):

| Role | Name | Email | Password |
|---|---|---|---|
| **Admin** | Admin User | `admin@example.com` | `password` |
| **Employee** | John Employee | `john@example.com` | `password` |
| **Employee** | Jane Employee | `jane@example.com` | `password` |

---

## 4. Installation & Setup Instructions

### Prerequisites:
- PHP >= 8.1
- Composer >= 2.x
- MySQL >= 8.0

### Steps:

1. **Clone or extract repository:**
   ```bash
   git clone https://github.com/whosatyambarnwal/Task-Management-System.git
   cd Task-Management-System
   # (Example: replace with your local project directory path, e.g., cd /path/to/Task-Management-System)
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   Copy `.env.example` to `.env` (if not already present) and configure your database settings:
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Database Setup (Choose Option A or Option B):**
   - **Option A (Recommended - Via Artisan Migrations & Seeders):**
     ```bash
     php artisan migrate:fresh --seed
     ```
   - **Option B (Direct SQL Dump Import):**
     Create a database named `task_management` in MySQL/phpMyAdmin and import the included export file:
     `database/task_management.sql`

6. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## 5. REST API Documentation

All API endpoints are prefixed with `/api` and require a valid Bearer token via the `Authorization: Bearer <TOKEN>` header.

### Generating a Test Bearer Token:
A custom Artisan command is included to generate Sanctum tokens instantly:
```bash
# Generate token for Admin:
php artisan app:generate-token admin@example.com

# Generate token for Employee:
php artisan app:generate-token john@example.com
```

### Endpoints:

#### 1. List Tasks
- **Route:** `GET /api/tasks`
- **Headers:** `Authorization: Bearer <TOKEN>`, `Accept: application/json`
- **Query Parameters (Optional):**
  - `status`: `pending` | `in_progress` | `completed`
  - `priority`: `low` | `medium` | `high`
  - `project_id`: integer
  - `assigned_to`: integer (Admin only)
  - `search`: string (matches title)
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "data": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "title": "Design Homepage Mockup",
          "priority": "high",
          "status": "in_progress",
          "project": { "id": 1, "name": "Website Redesign" },
          "assignee": { "id": 2, "name": "John Employee" }
        }
      ]
    }
  }
  ```

#### 2. Get Task Details
- **Route:** `GET /api/tasks/{task}`
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "project_id": 1,
      "assigned_to": 2,
      "title": "Design Homepage Mockup",
      "status": "in_progress",
      "priority": "high",
      "project": { "id": 1, "name": "Website Redesign" },
      "assignee": { "id": 2, "name": "John Employee" },
      "creator": { "id": 1, "name": "Admin User" }
    }
  }
  ```
- **Error (403 Forbidden):** Returned if an employee attempts to view another employee's task.

#### 3. Update Task Status (Employee & Admin)
- **Route:** `PATCH /api/tasks/{task}/status`
- **Request Body:**
  ```json
  {
    "status": "completed"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "success": true,
    "message": "Task status updated successfully.",
    "data": {
      "id": 1,
      "status": "completed",
      "updated_at": "2026-10-21T11:45:00.000000Z"
    }
  }
  ```
- **Validation Error (422 Unprocessable Content):** Returned if `status` is invalid.

#### 4. Create Task (Admin Only)
- **Route:** `POST /api/tasks`
- **Request Body:**
  ```json
  {
    "project_id": 1,
    "assigned_to": 2,
    "title": "Build Auth Middleware",
    "description": "Implement RBAC middleware",
    "priority": "high",
    "status": "pending",
    "due_date": "2026-11-01"
  }
  ```
- **Response (201 Created):** Returns created task object.
- **Error (403 Forbidden):** Returned if requested by non-admin.

#### 5. Update Task (Admin Only)
- **Route:** `PUT /api/tasks/{task}`
- **Response (200 OK):** Returns updated task object.

---

## 6. Automated Testing

Automated tests run against an isolated SQLite in-memory database to prevent modifying development data.

Run the test suite:
```bash
php artisan test
```

### Test Results Summary:
```text
PASS  Tests\Unit\ExampleTest
✓ that true is true

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response

PASS  Tests\Feature\TaskManagementTest
✓ admin can access admin dashboard
✓ employee cannot access admin pages
✓ employee sees only their own tasks
✓ employee cannot view another employees task
✓ employee cannot update another employees task through api
✓ invalid task statuses are rejected
✓ admin can create and assign task
✓ valid task status updates succeed
✓ unauthenticated users are redirected to login
✓ api requires authentication

Tests:    12 passed (34 assertions)
```
