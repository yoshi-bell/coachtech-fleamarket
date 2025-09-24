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

        // ユーザー名の更新
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
        } else {
            $displayItems = $user->items;
            $activeTab = 'sell';
        }

        return view('mypage.index', compact('user', 'displayItems', 'activeTab'));
    }
}
