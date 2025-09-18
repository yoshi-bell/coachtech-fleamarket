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
        $profileImages = [
            'profile1.png',
            'profile2.png',
            'profile3.png',
            'profile4.png',
            'profile5.png',
        ];

        $emails = [
            'test1@example.com',
            'test2@example.com',
            'test3@example.com',
            'test4@example.com',
            'test5@example.com',
        ];

        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create([
                'email' => $emails[$i],
            ]);

            $user->profile()->save(Profile::factory()->make([
                'img_url' => $profileImages[$i],
            ]));
        }
    }
}