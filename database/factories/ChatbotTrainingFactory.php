<?php

namespace Database\Factories;

use App\Models\ChatbotTraining;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotTrainingFactory extends Factory
{
    protected $model = ChatbotTraining::class;

    public function definition()
    {
        return [
            'intent_name' => $this->faker->word(),
            'training_text' => $this->faker->sentence(10),
            'source' => $this->faker->randomElement(['manual', 'imported']),
        ];
    }
}
