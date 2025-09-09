<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemCategoryMap = [
            '腕時計' => ['ファッション', 'メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ3束' => ['キッチン'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['ファッション', 'レディース', 'コスメ'],
        ];

        $categories = Category::all()->keyBy('content');

        foreach ($itemCategoryMap as $itemName => $categoryNames) {
            $item = Item::where('name', $itemName)->first();

            if ($item) {
                $categoryIds = [];
                foreach ($categoryNames as $categoryName) {
                    if (isset($categories[$categoryName])) {
                        $categoryIds[] = $categories[$categoryName]->id;
                    }
                }
                $item->categories()->attach($categoryIds);
            }
        }
    }
}