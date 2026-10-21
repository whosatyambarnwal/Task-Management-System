<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Employee users
        $employee1 = User::create([
            'name' => 'John Employee',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        $employee2 = User::create([
            'name' => 'Jane Employee',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        // Create Projects
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of the company website with modern UI/UX.',
            'status' => 'active',
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Build a cross-platform mobile application for clients.',
            'status' => 'active',
        ]);

        $project3 = Project::create([
            'name' => 'API Integration',
            'description' => 'Integrate third-party APIs for payment and notification services.',
            'status' => 'on_hold',
        ]);

        // Create Tasks
        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $employee1->id,
            'created_by' => $admin->id,
            'title' => 'Design Homepage Mockup',
            'description' => 'Create wireframes and high-fidelity mockups for the homepage.',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(7),
        ]);

        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $employee2->id,
            'created_by' => $admin->id,
            'title' => 'Implement Responsive CSS',
            'description' => 'Convert mockups to responsive HTML/CSS templates.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => now()->addDays(14),
        ]);

        Task::create([
            'project_id' => $project2->id,
            'assigned_to' => $employee1->id,
            'created_by' => $admin->id,
            'title' => 'Setup React Native Project',
            'description' => 'Initialize the React Native project with navigation and state management.',
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => now()->subDays(2),
        ]);

        Task::create([
            'project_id' => $project2->id,
            'assigned_to' => $employee2->id,
            'created_by' => $admin->id,
            'title' => 'Build Login Screen',
            'description' => 'Implement the login and registration screens for the mobile app.',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => now()->addDays(5),
        ]);

        Task::create([
            'project_id' => $project3->id,
            'assigned_to' => $employee1->id,
            'created_by' => $admin->id,
            'title' => 'Research Payment Gateways',
            'description' => 'Compare Stripe, PayPal, and Razorpay for integration.',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => now()->addDays(21),
        ]);

        Task::create([
            'project_id' => $project1->id,
            'assigned_to' => $employee2->id,
            'created_by' => $admin->id,
            'title' => 'SEO Optimization',
            'description' => 'Implement meta tags, schema markup, and improve page load speed.',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => now()->addDays(30),
        ]);
    }
}
