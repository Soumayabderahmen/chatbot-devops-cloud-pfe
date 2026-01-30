<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'chat_session_id' => null, // À adapter avec ChatSessionFactory
            'message' => $this->faker->sentence(12),
            'sender' => $this->faker->randomElement(['user', 'bot']),
            'intent' => $this->faker->word(),
            'response_time' => $this->faker->randomFloat(2, 0.5, 5.0),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
