<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $this->normalizeSliders($now);
        $this->normalizeWhyChooseUs($now);
        $this->normalizeProducts($now);
        $this->seedDailyOffers($now);
        $this->seedBannerSliders($now);
        $this->seedChefs($now);
        $this->seedAppDownloadSection($now);
        $this->seedTestimonials($now);
        $this->seedCounter($now);
        $this->seedBlogs($now);
        $this->seedFooter($now);
        $this->seedReservationTimes($now);
        $this->seedContentPages($now);
    }

    private function normalizeSliders($now): void
    {
        $slides = [
            [
                'image' => '/frontend/images/banner2_img_1.jpg',
                'offer' => '25%',
                'title' => 'Delicious meals delivered fresh',
                'sub_title' => 'Order your favorite dishes from Food Park today.',
                'short_description' => 'Enjoy quick service, fresh ingredients, and restaurant favorites prepared for every craving.',
            ],
            [
                'image' => '/frontend/images/banner2_img_2.jpg',
                'offer' => '35%',
                'title' => 'Taste the best from our kitchen',
                'sub_title' => 'Fresh menus, daily offers, and easy online ordering.',
                'short_description' => 'Choose from chef specials, family meals, and quick bites made with care.',
            ],
            [
                'image' => '/frontend/images/banner2_img_3.jpg',
                'offer' => '15%',
                'title' => 'Your table is only a click away',
                'sub_title' => 'Reserve, order, and enjoy Food Park anytime.',
                'short_description' => 'Book a table or place an order from a menu built for simple, satisfying meals.',
            ],
        ];

        DB::table('sliders')
            ->orderBy('id')
            ->limit(count($slides))
            ->get()
            ->each(function ($slider, $index) use ($slides, $now) {
                DB::table('sliders')
                    ->where('id', $slider->id)
                    ->update([
                        ...$slides[$index],
                        'button_link' => '/products',
                        'status' => 1,
                        'updated_at' => $now,
                    ]);
            });
    }

    private function normalizeWhyChooseUs($now): void
    {
        $items = [
            ['icon' => 'fas fa-utensils', 'title' => 'Fresh Ingredients', 'short_description' => 'We prepare meals with carefully selected ingredients and bold flavors.'],
            ['icon' => 'fas fa-truck', 'title' => 'Fast Delivery', 'short_description' => 'Orders move quickly from the kitchen to your door.'],
            ['icon' => 'fas fa-headset', 'title' => 'Friendly Support', 'short_description' => 'Our team is ready to help with orders, reservations, and questions.'],
        ];

        DB::table('why_choose_us')
            ->orderBy('id')
            ->limit(count($items))
            ->get()
            ->each(function ($item, $index) use ($items, $now) {
                DB::table('why_choose_us')
                    ->where('id', $item->id)
                    ->update([
                        ...$items[$index],
                        'status' => 1,
                        'updated_at' => $now,
                    ]);
            });
    }

    private function normalizeProducts($now): void
    {
        $images = [
            '/frontend/images/menu2_img_1.jpg',
            '/frontend/images/menu2_img_2.jpg',
            '/frontend/images/menu2_img_3.jpg',
            '/frontend/images/menu2_img_4.jpg',
            '/frontend/images/menu2_img_5.jpg',
            '/frontend/images/menu2_img_6.jpg',
        ];

        DB::table('products')
            ->orderBy('id')
            ->limit(6)
            ->get()
            ->each(function ($product, $index) use ($images, $now) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'thumb_image' => $images[$index % count($images)],
                        'show_at_home' => 1,
                        'status' => 1,
                        'updated_at' => $now,
                    ]);
            });
    }

    private function seedDailyOffers($now): void
    {
        if (DB::table('daily_offers')->exists()) {
            return;
        }

        DB::table('products')
            ->where('status', 1)
            ->orderBy('id')
            ->limit(6)
            ->pluck('id')
            ->each(function ($productId) use ($now) {
                DB::table('daily_offers')->insert([
                    'product_id' => $productId,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    private function seedBannerSliders($now): void
    {
        if (DB::table('banner_sliders')->exists()) {
            return;
        }

        DB::table('banner_sliders')->insert([
            [
                'title' => 'Fresh meals for every table',
                'sub_title' => 'Order signature dishes prepared by our kitchen team.',
                'url' => '/products',
                'banner' => '/frontend/images/banner2_img_1.jpg',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Weekend family specials',
                'sub_title' => 'Enjoy restaurant favorites with fast local delivery.',
                'url' => '/products',
                'banner' => '/frontend/images/banner2_img_2.jpg',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedChefs($now): void
    {
        if (DB::table('chefs')->exists()) {
            return;
        }

        DB::table('chefs')->insert([
            [
                'image' => '/frontend/images/chef_1.jpg',
                'name' => 'Alex Morgan',
                'title' => 'Head Chef',
                'fb' => '#',
                'in' => '#',
                'x' => '#',
                'web' => '#',
                'show_at_home' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => '/frontend/images/chef_2.jpg',
                'name' => 'Sarah Williams',
                'title' => 'Pastry Chef',
                'fb' => '#',
                'in' => '#',
                'x' => '#',
                'web' => '#',
                'show_at_home' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => '/frontend/images/chef_3.jpg',
                'name' => 'Daniel Carter',
                'title' => 'Sous Chef',
                'fb' => '#',
                'in' => '#',
                'x' => '#',
                'web' => '#',
                'show_at_home' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedAppDownloadSection($now): void
    {
        if (DB::table('app_download_sections')->exists()) {
            return;
        }

        DB::table('app_download_sections')->insert([
            'image' => '/frontend/images/download_img.png',
            'background' => '/frontend/images/download_bg.jpg',
            'title' => 'Get your food faster with our app',
            'short_description' => 'Browse the menu, place orders, and track delivery from one simple mobile experience.',
            'play_store_link' => '#',
            'apple_store_link' => '#',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedTestimonials($now): void
    {
        if (DB::table('testimonials')->exists()) {
            return;
        }

        DB::table('testimonials')->insert([
            [
                'image' => '/frontend/images/client_img_1.jpg',
                'name' => 'Mariam Johnson',
                'title' => 'Customer',
                'review' => 'The food arrived warm, fresh, and exactly as ordered. This has become my favorite lunch spot.',
                'rating' => 5,
                'show_at_home' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'image' => '/frontend/images/client_img_3.jpg',
                'name' => 'Chris Okafor',
                'title' => 'Customer',
                'review' => 'Great taste, quick service, and a menu that always has something new to try.',
                'rating' => 5,
                'show_at_home' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedCounter($now): void
    {
        if (DB::table('counters')->exists()) {
            return;
        }

        DB::table('counters')->insert([
            'background' => '/frontend/images/counter_bg.jpg',
            'counter_icon_one' => 'fas fa-utensils',
            'counter_count_one' => '120',
            'counter_name_one' => 'Daily Orders',
            'counter_icon_two' => 'fas fa-smile',
            'counter_count_two' => '850',
            'counter_name_two' => 'Happy Customers',
            'counter_icon_three' => 'fas fa-user-tie',
            'counter_count_three' => '24',
            'counter_name_three' => 'Expert Chefs',
            'counter_icon_four' => 'fas fa-award',
            'counter_count_four' => '18',
            'counter_name_four' => 'Food Awards',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedBlogs($now): void
    {
        if (! DB::table('blog_categories')->exists()) {
            DB::table('blog_categories')->insert([
                'name' => 'Restaurant News',
                'slug' => 'restaurant-news',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (DB::table('blogs')->exists()) {
            return;
        }

        $categoryId = DB::table('blog_categories')->where('status', 1)->value('id');
        $userId = DB::table('users')->value('id');

        if (! $categoryId || ! $userId) {
            return;
        }

        foreach ([
            ['Fresh ingredients make every order better', '/frontend/images/blog_1.jpg'],
            ['How our chefs prepare the daily menu', '/frontend/images/blog_2.jpg'],
            ['Simple tips for choosing your next meal', '/frontend/images/blog_3.jpg'],
        ] as [$title, $image]) {
            DB::table('blogs')->insert([
                'user_id' => $userId,
                'category_id' => $categoryId,
                'image' => $image,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Discover menu highlights, kitchen updates, and practical food tips from the Food Park team.',
                'seo_title' => $title,
                'seo_description' => 'Food Park restaurant update.',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedFooter($now): void
    {
        if (! DB::table('footer_infos')->exists()) {
            DB::table('footer_infos')->insert([
                'short_info' => 'Food Park serves fresh restaurant meals, daily offers, and quick online ordering for every appetite.',
                'address' => '12 Food Park Avenue, Lagos',
                'phone' => '+234 800 123 4567',
                'email' => 'hello@foodpark.test',
                'copyright' => 'Copyright 2026 Food Park. All rights reserved.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (DB::table('social_links')->exists()) {
            return;
        }

        DB::table('social_links')->insert([
            ['icon' => 'fab fa-facebook-f', 'name' => 'Facebook', 'link' => '#', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'link' => '#', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['icon' => 'fab fa-x-twitter', 'name' => 'X', 'link' => '#', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedReservationTimes($now): void
    {
        if (DB::table('reservation_times')->exists()) {
            return;
        }

        DB::table('reservation_times')->insert([
            ['start_time' => '10:00 AM', 'end_time' => '12:00 PM', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['start_time' => '12:00 PM', 'end_time' => '02:00 PM', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['start_time' => '06:00 PM', 'end_time' => '08:00 PM', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedContentPages($now): void
    {
        if (! DB::table('abouts')->exists()) {
            DB::table('abouts')->insert([
                'image' => '/frontend/images/about_chef.jpg',
                'title' => 'About Food Park',
                'main_title' => 'Fresh food, friendly service, and easy ordering',
                'description' => 'Food Park is a demo restaurant experience built around quick ordering, curated menus, and reliable service.',
                'video_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('contacts')->exists()) {
            DB::table('contacts')->insert([
                'phone_one' => '+234 800 123 4567',
                'phone_two' => '+234 800 765 4321',
                'mail_one' => 'hello@foodpark.test',
                'mail_two' => 'support@foodpark.test',
                'address' => '12 Food Park Avenue, Lagos',
                'map_link' => 'https://maps.google.com',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('privacy_policies')->exists()) {
            DB::table('privacy_policies')->insert([
                'content' => '<p>We respect customer privacy and only use submitted information to process orders, reservations, and support requests.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('trams_and_conditions')->exists()) {
            DB::table('trams_and_conditions')->insert([
                'content' => '<p>Orders, reservations, and promotions are subject to restaurant availability and confirmation.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
