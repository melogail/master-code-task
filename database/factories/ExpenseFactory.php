<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Vendor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryIds = Category::pluck('id')->toArray();
        $vendorIds = Vendor::pluck('id')->toArray();

        return [
            'category_id' => fake()->randomElement($categoryIds),
            'vendor_id' => fake()->randomElement($vendorIds),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'date' => fake()->dateTimeThisYear(),
            'description' => fake()->sentence(),
        ];
    }
}
