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
            'uuid' => $this->faker->uuid(),
            'full_name' => $this->faker->name(),
            'nik' => $this->faker->unique()->numerify('################'),
            'nip' => $this->faker->unique()->numerify('##################'),
            'created_by' => 1, // Assuming user ID 1 exists
            'updated_by' => 1,
        ];
    }
}
