<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-token {email : The email of the user} {name=api-token : Name for the token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Sanctum API bearer token for testing endpoints';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->argument('name');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: {$email}");
            return Command::FAILURE;
        }

        $token = $user->createToken($name)->plainTextToken;

        $this->info("Generated Sanctum API token for {$user->name} ({$user->role}):");
        $this->line($token);

        return Command::SUCCESS;
    }
}
