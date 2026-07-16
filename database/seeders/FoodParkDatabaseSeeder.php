<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FoodParkDatabaseSeeder extends Seeder
{
    private const SECTION_TABLES = [
        'abouts',
        'addresses',
        'admin_menus',
        'admin_menu_items',
        'app_download_sections',
        'banner_sliders',
        'blogs',
        'blog_categories',
        'blog_comments',
        'categories',
        'chats',
        'chefs',
        'contacts',
        'counters',
        'coupons',
        'custom_page_builders',
        'daily_offers',
        'delivery_areas',
        'failed_jobs',
        'footer_infos',
        'jobs',
        null,
        'orders',
        'order_items',
        'order_placed_notifications',
        'payment_gateway_settings',
        'personal_access_tokens',
        'privacy_policies',
        'products',
        'product_galleries',
        'product_options',
        'product_ratings',
        'product_sizes',
        'reservations',
        'reservation_times',
        'section_titles',
        'settings',
        'sliders',
        'social_links',
        'subscribers',
        'testimonials',
        'trams_and_conditions',
        'users',
        'why_choose_us',
        'wishlists',
    ];

    private const IMPORT_ORDER = [
        2, 43, 17, 7, 9, 0, 4, 5, 11, 12, 13, 14, 15, 18, 19, 20, 25, 26, 27,
        34, 35, 36, 37, 38, 39, 40, 41, 42, 3, 1, 6, 28, 8, 10, 16, 29, 30,
        32, 31, 22, 23, 24, 33, 44,
    ];

    public function run(): void
    {
        $sections = $this->csvSections(database_path('seeders/data/food_park.csv'));

        Schema::disableForeignKeyConstraints();

        foreach (self::IMPORT_ORDER as $index) {
            $section = $sections[$index] ?? null;
            $table = self::SECTION_TABLES[$index] ?? null;

            if (! $section || ! $table || ! Schema::hasTable($table) || $section['rows'] === []) {
                continue;
            }

            $columns = Schema::getColumnListing($table);

            foreach ($section['rows'] as $row) {
                $attributes = array_intersect_key($row, array_flip($columns));
                $attributes = array_map(
                    fn ($value) => $value === 'NULL' ? null : $value,
                    $attributes
                );

                DB::table($table)->updateOrInsert(
                    ['id' => $attributes['id']],
                    $attributes
                );
            }

            $this->syncSequence($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    private function syncSequence(string $table): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            "select setval(pg_get_serial_sequence(?, 'id'), coalesce((select max(id) from {$table}), 1), true)",
            [$table]
        );
    }

    private function csvSections(string $path): array
    {
        $file = fopen($path, 'rb');
        $sections = [];
        $header = null;
        $rows = [];

        while (($row = fgetcsv($file)) !== false) {
            if (($row[0] ?? null) === 'id') {
                if ($header !== null) {
                    $sections[] = [
                        'header' => $header,
                        'rows' => $rows,
                    ];
                }

                $header = $row;
                $rows = [];

                continue;
            }

            if ($header !== null && count($row) === count($header)) {
                $rows[] = array_combine($header, $row);
            }
        }

        if ($header !== null) {
            $sections[] = [
                'header' => $header,
                'rows' => $rows,
            ];
        }

        fclose($file);

        return $sections;
    }
}
