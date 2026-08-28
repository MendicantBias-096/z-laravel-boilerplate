<?php

declare(strict_types=1);

namespace App\Modules\Access\Database\Factories;

use App\Modules\Access\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /** @var class-string<Profile> */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'locale' => fake()->randomElement(['es', 'en']),
        ];
    }
}
