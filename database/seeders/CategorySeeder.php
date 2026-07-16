<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->csvRows(database_path('seeders/data/food_park_categories.csv')) as $row) {
            Category::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'status' => (bool) $row['status'],
                    'show_at_home' => (bool) $row['show_at_home'],
                    'created_at' => $row['created_at'] ?: now(),
                    'updated_at' => $row['updated_at'] ?: now(),
                ]
            );
        }
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
