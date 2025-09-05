<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\User;
use App\Models\PicExternal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Placement>
 */
class PlacementFactory extends Factory
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
            'name' => $this->faker->jobTitle() . ' Placement',
            'client_id' => Client::factory(),
            'pic_external_id' => PicExternal::factory(),
            'pic_internal_id' => User::factory(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
            'deleted_by' => null,
        ];
    }
}