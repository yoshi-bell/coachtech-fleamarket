<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
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
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user, $index) {
                $imageNumber = $index + 1; // 1から始まる番号を生成
                $user->profile()->save(Profile::factory()->make([
                    'img_url' => 'profile' . $imageNumber . '.png',
                ]));
            });
    }
}