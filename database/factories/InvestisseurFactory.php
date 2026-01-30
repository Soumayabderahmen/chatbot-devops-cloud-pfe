<?php

namespace Database\Factories;

use App\Models\Investisseur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvestisseurFactory extends Factory
{
    protected $model = Investisseur::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'video_presentation' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'website_link' => $this->faker->url(),
            'social_links' => json_encode([
                'facebook' => $this->faker->url(),
                'linkedin' => $this->faker->url(),
                'twitter' => $this->faker->url(),
            ]),
            'profile_image' => $this->faker->imageUrl(200, 200, 'people'),
            'cover_image' => $this->faker->imageUrl(640, 200, 'business'),
        ];
    }
}
