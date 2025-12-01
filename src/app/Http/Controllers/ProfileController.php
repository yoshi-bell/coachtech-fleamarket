<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('profile');
        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->name;
        $user->save();

        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        if ($request->hasFile('img_url')) {
            // 新しい画像がアップロードされた場合
            // 既存の画像があれば削除
            if ($profile->img_url && Storage::disk('public')->exists('profile_images/' . $profile->img_url)) {
                Storage::disk('public')->delete('profile_images/' . $profile->img_url);
            }
            $path = $request->file('img_url')->store('profile_images', 'public');
            $profile->img_url = basename($path);
        } elseif ($request->has('temp_image_path')) {
            // 一時保存された画像を使用する場合
            $tempPath = $request->input('temp_image_path');
            $fileName = basename($tempPath);

            // 既存の画像があれば削除
            if ($profile->img_url && Storage::disk('public')->exists('profile_images/' . $profile->img_url)) {
                Storage::disk('public')->delete('profile_images/' . $profile->img_url);
            }

            // temp_profile_previewsからprofile_imagesへファイルを移動
            Storage::disk('public')->move($tempPath, 'profile_images/' . $fileName);
            $profile->img_url = $fileName;
        }

        $profile->postcode = $request->postcode;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();

        return redirect()->route('mypage.show')->with('message', 'プロフィールを更新しました！');
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $user->load([
            'profile',
            'items' => function ($query) {
                $query->orderBy('id', 'desc')->with(['condition', 'categories']);
            },
            'soldItems' => function ($query) {
                $query->orderBy('id', 'desc')->with('item');
            }
        ]);

        $displayItems = collect();

        if ($request->has('page') && $request->get('page') == 'buy') {
            $displayItems = $user->soldItems->map(function ($soldItem) {
                return $soldItem->item;
            });
            $activeTab = 'buy';
        } elseif ($request->has('page') && $request->get('page') == 'transaction') {
            // 取引中の商品（自分が購入者または出品者で、まだ評価していない商品）
            // SoldItemを主軸に取得
            $displayItems = \App\Models\SoldItem::where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('seller_id', $user->id);
                    });
            })
                ->whereDoesntHave('ratings', function ($query) use ($user) {
                    $query->where('rater_id', $user->id);
                })
                ->with(['item', 'chats'])
                ->get()
                ->sortByDesc(function ($soldItem) use ($user) {
                    $latestChat = $soldItem->chats->where('sender_id', '!=', $user->id)->sortByDesc('created_at')->first();
                    return $latestChat ? $latestChat->created_at : $soldItem->created_at;
                });

            $activeTab = 'transaction';
        } else {
            $displayItems = $user->items;
            $activeTab = 'sell';
        }

        // 未読メッセージの総数を取得
        $unreadTotalCount = \App\Models\Chat::whereHas('soldItem', function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                    ->orWhereHas('item', function ($iq) use ($user) {
                        $iq->where('seller_id', $user->id);
                    });
            })
                ->whereDoesntHave('ratings', function ($q) use ($user) {
                    $q->where('rater_id', $user->id);
                });
        })
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('mypage.index', compact('user', 'displayItems', 'activeTab', 'unreadTotalCount'));
    }
}
