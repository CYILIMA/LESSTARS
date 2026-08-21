<?php

namespace Database\Factories;

use App\Models\HomepagePhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepagePhoto>
 */
class HomepagePhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => 'homepage-photos/'.$this->faker->uuid().'.jpg',
            'description' => $this->faker->sentence(8),
            'display_order' => 0,
            'status' => 'draft',
        ];
    }
}
