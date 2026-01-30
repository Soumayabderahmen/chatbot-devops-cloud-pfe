<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'role' => $this->faker->jobTitle(),
            'image' => $this->faker->imageUrl(200, 200, 'people'),
            'linkedin' => $this->faker->url(),
            'twitter' => $this->faker->url(),
            'email' => $this->faker->safeEmail(),
           'type' => $this->faker->randomElement(['founder', 'chef', 'member']),
        ];
    }
}
