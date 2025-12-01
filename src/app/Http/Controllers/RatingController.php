<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingRequest;
use App\Models\Item;
use App\Models\Rating;
use App\Mail\TransactionCompleted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function store(RatingRequest $request, Item $item)
    {
        $soldItem = $item->soldItem;
        if (!$soldItem) {
            abort(404);
        }

        $rater = Auth::user();
        // 出品者か購入者か判定し、評価される相手を特定
        // soldItem->buyer_id が購入者
        // item->seller_id が出品者
        // 自分が購入者なら、相手は出品者。自分が出品者なら、相手は購入者。

        if ($rater->id === $item->seller_id) {
            $ratedUserId = $soldItem->buyer_id;
        } elseif ($rater->id === $soldItem->buyer_id) {
            $ratedUserId = $item->seller_id;
        } else {
            abort(403); // 取引に関係ないユーザー
        }

        Rating::create([
            'rater_id' => $rater->id,
            'rated_user_id' => $ratedUserId,
            'sold_item_id' => $soldItem->id,
            'rating' => $request->rating,
        ]);

        // 取引完了メール送信（出品者へ）
        // 購入者が評価した場合のみ送信（今回は仕様上、購入者が評価して完了とするフローがメインだが、相互評価も考慮）
        // ここでは「評価がついた＝取引完了」とみなす簡易実装
        if ($rater->id !== $item->seller_id) {
            Mail::to($item->seller->email)->send(new TransactionCompleted($item, $rater));
        }

        return redirect()->route('index')->with('message', '取引が完了しました！');
    }
}
