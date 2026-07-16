<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodParkProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIdsByOriginalId = $this->categoryIdsByOriginalId();

        foreach ($this->csvRows(database_path('seeders/data/food_park_products.csv')) as $row) {
            $attributes = Product::factory()
                ->fromFoodParkCsvRow($row, $categoryIdsByOriginalId)
                ->make()
                ->getAttributes();

            DB::table('products')->updateOrInsert(
                ['slug' => $row['slug']],
                $attributes
            );
        }
    }

    private function categoryIdsByOriginalId(): array
    {
        $map = [];

        foreach ($this->csvRows(database_path('seeders/data/food_park_categories.csv')) as $row) {
            $categoryId = Category::query()
                ->where('slug', $row['slug'])
                ->value('id');

            if ($categoryId) {
                $map[(int) $row['id']] = $categoryId;
            }
        }

        return $map;
    }

    private function csvRows(string $path): array
    {
        $file = fopen($path, 'rb');
        $header = fgetcsv($file);
        $rows = [];

        while (($row = fgetcsv($file)) !== false) {
            $rows[] = array_combine($header, $row);
        }

        fclose($file);

        return $rows;
    }
}
