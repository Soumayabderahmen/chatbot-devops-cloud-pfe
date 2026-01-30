<?php

namespace Database\Factories;

use App\Models\Coach;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoachFactory extends Factory
{
    protected $model = Coach::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'phone_number' => $this->faker->phoneNumber(),
            'diplome' => $this->faker->randomElement(['Master', 'PhD', 'Bachelor']),
            'competence' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'profile_image' => $this->faker->imageUrl(200, 200, 'people'),
            'cover_image' => $this->faker->imageUrl(640, 200, 'nature'),
            'document' => null, // ou chemin vers un fichier factice
        ];
    }
}
