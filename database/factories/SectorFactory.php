<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Tech', 'Health', 'Finance', 'Education', 'E-commerce', 'Agriculture', 'Transport', 'Energy', 'Media', 'Tourism'])
        ];
    }
}
