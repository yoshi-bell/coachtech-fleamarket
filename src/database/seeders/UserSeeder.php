<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // 追加

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create(); // 10件のダミーユーザーを作成
    }
}