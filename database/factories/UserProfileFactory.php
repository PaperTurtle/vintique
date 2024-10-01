<?php

namespace Database\Factories;

use App\Models\{User, UserProfile};
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->paragraph,
            'profile_picture' => $this->faker->imageUrl(640, 480, 'people'),
        ];
    }
}
