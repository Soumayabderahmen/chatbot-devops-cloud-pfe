<?php

namespace Database\Factories;

use App\Models\ChatbotSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotSettingFactory extends Factory
{
    protected $model = ChatbotSetting::class;

    public function definition()
    {
        return [
            'enabled' => $this->faker->boolean(90),
            'bot_name' => $this->faker->firstName . ' Bot',
            'welcome_message' => $this->faker->sentence(),
            'position' => $this->faker->randomElement(['bottom-right', 'bottom-left', 'top-right', 'top-left']),
            'primary_color' => $this->faker->safeHexColor(),
            'timeout_message' => 'Sorry for the delay...',
            'training_text' => $this->faker->paragraph(),
        ];
    }
}
