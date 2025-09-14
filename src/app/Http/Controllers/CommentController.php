<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'comment' => $request->input('comment'),
        ]);

        // フロントエンドでユーザー名などを表示できるよう、userとprofileリレーションを読み込む
        $newComment = Comment::with('user.profile')->find($comment->id);

        // 更新後の総コメント数を取得
        $commentCount = $item->comments()->count();

        return response()->json([
            'comment' => $newComment,
            'commentCount' => $commentCount
        ]);
    }
}
