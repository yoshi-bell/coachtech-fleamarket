<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('item.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像を 'public/item_images' ディレクトリに保存し、パスを取得
        $path = $request->file('img_url')->store('item_images', 'public');

        // 商品を作成
        $item = Item::create([
            'seller_id' => Auth::id(),
            'condition_id' => $validated['condition_id'],
            'name' => $validated['name'],
            'brand' => $request->input('brand'),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'img_url' => basename($path), // ファイル名のみをDBに保存
        ]);

        // カテゴリーを紐付け
        $item->categories()->attach($validated['category_ids']);

        return redirect()->route('mypage.show', ['page' => 'sell'])->with('success', '商品を出品しました。');
    }
}
