<?php

namespace Database\Factories;

use App\Models\ChatbotReaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotReactionFactory extends Factory
{
    protected $model = ChatbotReaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'chat_session_id' => $this->faker->uuid(),
            'message' => $this->faker->sentence(),
            'reaction' => $this->faker->randomElement(['like', 'dislike']),
        ];
    }
}
