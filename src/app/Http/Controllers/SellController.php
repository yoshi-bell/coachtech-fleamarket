<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $imagePath = '';

        if ($request->hasFile('img_url')) {
            // 新しい画像がアップロードされた場合
            $path = $request->file('img_url')->store('item_images', 'public');
            $imagePath = basename($path);
        } elseif ($request->has('temp_image_path')) {
            // 一時保存された画像を使用する場合
            $tempPath = $request->input('temp_image_path');
            $fileName = basename($tempPath);
            // temp_previewsからitem_imagesへファイルを移動
            Storage::disk('public')->move($tempPath, 'item_images/' . $fileName);
            $imagePath = $fileName;
        }

        // 商品を作成
        $item = Item::create([
            'seller_id' => Auth::id(),
            'condition_id' => $validated['condition_id'],
            'name' => $validated['name'],
            'brand' => $request->input('brand'),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'img_url' => $imagePath, // 決定した画像パスを保存
        ]);

        // カテゴリーを紐付け
        $item->categories()->attach($validated['category_ids']);

        return redirect()->route('mypage.show', ['page' => 'sell'])->with('success', '商品を出品しました。');
    }
}
