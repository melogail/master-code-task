<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'contact_info' => fake()->phoneNumber(),
            'is_active' => fake()->boolean(),
            'created_at' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'updated_at' => fake()->dateTimeBetween('-1 year', '+1 year'),
        ];
    }
}
