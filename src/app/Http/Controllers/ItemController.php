<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $keyword = $request->input('keyword');
        $items = collect();

        if ($tab === 'mylist' && Auth::check()) {
            $likedItemsQuery = Auth::user()->likes()->with('item.soldItem')->latest();

            if ($keyword) {
                $likedItemsQuery->whereHas('item', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                });
            }

            $items = $likedItemsQuery->get()->map(function ($like) {
                return $like->item;
            });
        } else {
            $query = Item::query();

            if (Auth::check()) {
                $query->where('seller_id', '!=', Auth::id());
            }

            if ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            }
            $items = $query->with('soldItem')->latest()->get();
        }
        return view('index', compact('items', 'tab', 'keyword'));
    }

    public function show(Item $item)
    {
        $item->load(['seller', 'condition', 'categories', 'soldItem', 'likes', 'comments.user']);

        $isLiked = false;
        if (Auth::check()) {
            $isLiked = Auth::user()->likes()->where('item_id', $item->id)->exists();
        }
        return view('item.show', compact('item', 'isLiked'));
    }
}
