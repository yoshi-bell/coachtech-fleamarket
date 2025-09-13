<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * 配送先住所の変更ページを表示する
     */
    public function edit(Item $item)
    {
        // セッションから一時的な住所を取得
        $session_address = session('shipping_address_' . $item->id);

        // セッションに住所があればそれを使い、なければプロフィールの住所を使う
        if ($session_address) {
            // ビューで同じように扱えるよう、配列をオブジェクトに変換
            $address = (object) $session_address;
        } else {
            $address = Auth::user()->profile;
        }

        return view('purchase.address', compact('item', 'address'));
    }

    /**
     * 配送先住所の更新処理を行う
     */
    public function update(AddressRequest $request, Item $item)
    {
        $validated = $request->validated();

        // 変更後の住所をセッションに保存
        $request->session()->put('shipping_address_' . $item->id, $validated);

        return redirect()->route('purchase.create', $item->id);
    }
}
