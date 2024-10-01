<?php

namespace App\Console\Commands;

use App\Models\{User, UserProfile};
use Illuminate\Console\Command;

/**
 * A Laravel console command to create user profiles for users who do not already have one.
 *
 * This command identifies users in the database who do not have an associated user profile.
 * It then creates a new user profile for each of these users. This ensures that all users
 * in the system have a corresponding user profile entity.
 *
 * Usage: Run the command using the Laravel Artisan command line tool.
 * Command: `php artisan userprofile:create`.
 */
class CreateUserProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userprofile:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user profile for all users who do not have one';

    /**
     * Execute the console command.
     *
     * This method retrieves all users who do not have an associated profile and creates
     * a new UserProfile for each. It outputs information about the process to the console,
     * indicating the creation of user profiles.
     */
    public function handle(): void
    {
        $usersWithoutProfile = User::doesntHave('profile')->get();

        foreach ($usersWithoutProfile as $user) {
            UserProfile::firstOrCreate([
                'user_id' => $user->id,
            ]);

            $this->info("Created profile for user: {$user->id}");
        }

        $this->info('All missing user profiles have been created.');
    }
}
