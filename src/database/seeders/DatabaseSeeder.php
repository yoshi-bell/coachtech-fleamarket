<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            ConditionSeeder::class,
            UserSeeder::class,
            ItemSeeder::class, // 追加
            ItemCategorySeeder::class, // 追加
        ]);
    }
}