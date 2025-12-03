<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatingRequest;
use App\Models\SoldItem;
use App\Models\Rating;
use App\Mail\TransactionCompleted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function store(RatingRequest $request, SoldItem $soldItem)
    {
        $rater = Auth::user();
        $item = $soldItem->item; // メールなどで使用するために$itemを取得

        // 評価される相手を特定
        if ($rater->id === $item->seller_id) {
            $ratedUserId = $soldItem->buyer_id;
        } elseif ($rater->id === $soldItem->buyer_id) {
            $ratedUserId = $item->seller_id;
        } else {
            abort(403); // 取引に関係ないユーザー
        }

        // 既に評価済みでないか確認（二重評価防止）
        $existingRating = Rating::where('sold_item_id', $soldItem->id)
                                ->where('rater_id', $rater->id)
                                ->exists();
        if ($existingRating) {
            return back()->withErrors(['message' => 'すでに評価済みです。']);
        }

        Rating::create([
            'rater_id' => $rater->id,
            'rated_user_id' => $ratedUserId,
            'sold_item_id' => $soldItem->id,
            'rating' => $request->rating,
        ]);

        // 購入者が評価した場合のみメール送信
        if ($rater->id === $soldItem->buyer_id) {
            Mail::to($item->seller->email)->send(new TransactionCompleted($item, $rater));
        }

        return redirect()->route('index')->with('message', '評価を送信しました。取引完了です！');
    }
}
