<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(Item $item)
    {
        Like::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
        ]);

        $item->load('likes');
        $likeCount = $item->likes->count();

        return response()->json(['likeCount' => $likeCount]);
    }

    public function destroy(Item $item)
    {
        $like = Like::where('user_id', Auth::id())->where('item_id', $item->id)->first();
        if ($like) {
            $like->delete();
        }

        $item->load('likes');
        $likeCount = $item->likes->count();

        return response()->json(['likeCount' => $likeCount]);
    }
}
