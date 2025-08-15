<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'full_name' => fake()->name(),
            'nik' => fake()->unique()->numerify('##########'),
            'created_by' => 1, // Assuming user ID 1 exists
            'updated_by' => 1,
        ];
    }
}
