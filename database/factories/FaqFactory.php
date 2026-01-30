<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition()
    {
        return [
            'question' => $this->faker->sentence(6, true),   // Phrase de 6 mots pour la question
            'answer' => $this->faker->paragraph(3, true),    // Paragraphe pour la réponse
            'is_active' => $this->faker->boolean(80),        // 80% de chances que ce soit actif
        ];
    }
}
