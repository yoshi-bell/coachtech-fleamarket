<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $query = Item::query();

        // ログインしている場合、自分が出品した商品を除外
        if (Auth::check()) {
            $query->where('seller_id', '!=', Auth::id());
        }

        // N+1問題を防ぐため、売り切れ情報をEagerロードし、新しい順に取得
        $items = $query->with('soldItem')->latest()->get();

        return view('index', compact('items'));
    }

    public function show(Item $item)
    {
        // N+1問題を防ぐため、必要なリレーションをすべてEagerロード
        $item->load(['seller', 'condition', 'categories', 'soldItem', 'likes', 'comments.user']);

        $isLiked = false;
        if (Auth::check()) {
            $isLiked = Auth::user()->likes()->where('item_id', $item->id)->exists();
        }

        return view('item.show', compact('item', 'isLiked'));
    }
}
