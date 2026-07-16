<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->slug(),
            'thumb_image' => '/uploads/test.jpg',
            'category_id' => function(){
                return Category::inRandomOrder()->first()->id;
            },
            'short_description' => fake()->paragraph(),
            'long_description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 200),
            'offer_price' => fake()->randomFloat(2, 1, 100),
            'quantity' => 100,
            'sku' => fake()->unique()->ean13(),
            'seo_title' => fake()->sentence(),
            'seo_description' => fake()->paragraph(),
            'show_at_home' => fake()->boolean(),
            'status' => fake()->boolean(),
        ];
    }

    public function fromFoodParkCsvRow(array $row, array $categoryIdsByOriginalId): static
    {
        return $this->state(function () use ($row, $categoryIdsByOriginalId) {
            $originalCategoryId = (int) $row['category_id'];

            return [
                'name' => $row['name'],
                'slug' => $row['slug'],
                'thumb_image' => $row['thumb_image'],
                'category_id' => $categoryIdsByOriginalId[$originalCategoryId] ?? $originalCategoryId,
                'short_description' => $row['short_description'],
                'long_description' => $row['long_description'],
                'price' => (float) $row['price'],
                'offer_price' => (float) $row['offer_price'],
                'quantity' => (int) $row['quantity'],
                'sku' => $row['sku'] ?: null,
                'seo_title' => $row['seo_title'] ?: null,
                'seo_description' => $row['seo_description'] ?: null,
                'show_at_home' => (bool) $row['show_at_home'],
                'status' => (bool) $row['status'],
                'created_at' => $row['created_at'] ?: now(),
                'updated_at' => $row['updated_at'] ?: now(),
            ];
        });
    }
}
