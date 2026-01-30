<?php

namespace Database\Factories;

use App\Models\Startup;
use App\Models\User;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class StartupFactory extends Factory
{
    protected $model = Startup::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'logo_startup' => $this->faker->imageUrl(200, 200, 'business'),
            'phone_number' => $this->faker->phoneNumber(),
            'website_link' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'sector_id' => Sector::factory(),
        ];
    }
}
