<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GeoLocationSeeder::class,
            SettingsSeeder::class,
            PageSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call(DemoDataSeeder::class);
        }

        $this->call(PostSeeder::class);
    }
}
