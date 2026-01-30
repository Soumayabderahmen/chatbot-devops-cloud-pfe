<?php

namespace Database\Factories;

use App\Models\Tutorial;
use Illuminate\Database\Eloquent\Factories\Factory;

class TutorialFactory extends Factory
{
    protected $model = Tutorial::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(6),
            'video_url' => $this->faker->url(),
            'type' => $this->faker->randomElement(['video', 'article', 'interactive']),
        ];
    }
}
