<?php

namespace Database\Factories;

use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportMessageFactory extends Factory
{
    protected $model = SupportMessage::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'message' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['bug', 'feature', 'question', 'other']),
            'file_path' => null,
            'status' => $this->faker->randomElement(['pending', 'resolved']),
        ];
    }
}
