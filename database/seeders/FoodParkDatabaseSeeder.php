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

    public function run(): void
    {
        $sections = $this->csvSections(database_path('seeders/data/food_park.csv'));

        Schema::disableForeignKeyConstraints();

        foreach ($sections as $index => $section) {
            $table = self::SECTION_TABLES[$index] ?? null;

            if (! $table || ! Schema::hasTable($table) || $section['rows'] === []) {
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
        }

        Schema::enableForeignKeyConstraints();
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
