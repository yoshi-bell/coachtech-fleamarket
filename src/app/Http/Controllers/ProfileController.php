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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile'); // プロフィール情報をEagerロード
        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // ユーザー名の更新
        /** @var \App\Models\User $user */
        $user->name = $request->name;
        $user->save();

        // プロフィールの更新または新規作成
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        // 画像アップロードの処理
        if ($request->hasFile('img_url')) {
            // 古い画像があれば削除
            if ($profile->img_url && Storage::disk('public')->exists('profile_images/' . $profile->img_url)) {
                Storage::disk('public')->delete('profile_images/' . $profile->img_url);
            }
            $path = $request->file('img_url')->store('profile_images', 'public');
            $profile->img_url = basename($path);
        }

        $profile->postcode = $request->postcode;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();

        return redirect()->back()->with('message', 'プロフィールを更新しました！');
    }

    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load(['profile', 'items.condition', 'items.categories', 'soldItems.item']); // Eager load relationships

        $displayItems = collect(); // Initialize an empty collection

        if ($request->has('page') && $request->get('page') == 'buy') {
            // Display purchased items
            $displayItems = $user->soldItems->map(function ($soldItem) {
                return $soldItem->item; // Get the actual item from soldItem record
            });
            $activeTab = 'buy';
        } else {
            // Default to displaying listed items
            $displayItems = $user->items;
            $activeTab = 'sell';
        }

        return view('mypage.index', compact('user', 'displayItems', 'activeTab'));
    }
}
