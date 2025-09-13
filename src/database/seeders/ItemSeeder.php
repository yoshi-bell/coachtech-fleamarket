<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemsData = [
            [
                'seller_id' => 1,
                'condition_id' => 1,
                'name' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'img_url' => 'Armani+Mens+Clock.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 2,
                'name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'img_url' => 'HDD+Hard+Disk.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 3,
                'name' => '玉ねぎ3束',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'img_url' => 'iLoveIMG+d.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 1,
                'condition_id' => 4,
                'name' => '革靴',
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'img_url' => 'Leather+Shoes+Product+Photo.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 1,
                'condition_id' => 1,
                'name' => 'ノートPC',
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'img_url' => 'Living+Room+Laptop.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 1,
                'condition_id' => 2,
                'name' => 'マイク',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'img_url' => 'Music+Mic+4632231.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 3,
                'name' => 'ショルダーバッグ',
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'img_url' => 'Purse+fashion+pocket.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 4,
                'name' => 'タンブラー',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'img_url' => 'Tumbler+souvenir.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 1,
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'img_url' => 'Waitress+with+Coffee+Grinder.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'condition_id' => 2,
                'name' => 'メイクセット',
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'img_url' => 'Makeup+Cosmetics.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        DB::table('items')->insert($itemsData);
    }
}