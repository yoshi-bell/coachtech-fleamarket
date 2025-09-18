<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    /**
     * 商品購入ページを表示する
     */
    public function create(Item $item)
    {
        // 商品が既に売却済みかチェック
        if ($item->soldItem) {
            return redirect()->route('item.show', $item->id)->with('error', 'この商品は既に売り切れています。');
        }

        // ユーザーのデフォルト住所を取得
        $user = Auth::user();
        $address = $user->profile ? $user->profile : null;

        return view('purchase.index', compact('item', 'address'));
    }

    /**
     * Stripe決済セッションを作成し、リダイレクトする
     */
    public function store(Request $request, Item $item)
    {
        // 商品が既に売却済みか再度チェック
        if ($item->soldItem) {
            return redirect()->route('item.show', $item->id)->with('error', 'この商品は既に売り切れています。');
        }

        // 配送先住所を確定
        $shipping_address = session('shipping_address_' . $item->id);
        if (!$shipping_address) {
            $profile = Auth::user()->profile;
            if ($profile) {
                $shipping_address = $profile->only(['postcode', 'address', 'building']);
            } else {
                // 住所がない場合はエラーとして購入ページに戻す
                return redirect()->back()->with('error', '配送先住所が登録されていません。');
            }
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // ユーザーが選択した支払い方法に基づいてStripeに渡す情報を決定
        $payment_method_type = $request->input('payment_method');
        $stripe_payment_methods = [];
        if ($payment_method_type == '1') { // 1: コンビニ払い
            $stripe_payment_methods[] = 'konbini';
        } elseif ($payment_method_type == '2') { // 2: クレジットカード
            $stripe_payment_methods[] = 'card';
        }

        $checkout_session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                        'images' => ['https://placehold.co/200x200.png?text=FleaMarket'], // 開発環境では公開URLが必要なためダミー画像を使用
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'payment_method_types' => $stripe_payment_methods,
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.create', ['item' => $item->id]),
            'metadata' => [
                'item_id' => $item->id,
                'buyer_id' => Auth::id(),
                'postcode' => $shipping_address['postcode'],
                'address' => $shipping_address['address'],
                'building' => $shipping_address['building'] ?? '',
            ]
        ]);

        return redirect($checkout_session->url, 303);
    }

    /**
     * Stripe決済成功後の処理
     */
    public function success(Request $request, Item $item)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($request->query('session_id'));

        // 不正なアクセスやURLの直接入力などを考慮
        if (!$session) {
            return redirect('/')->with('error', '無効なセッションです。');
        }

        // metadataからDB保存に必要な情報を取得
        $metadata = $session->metadata;

        // DBに購入記録を保存
        try {
            // 同一商品がほぼ同時に購入されることを防ぐため、ここでも売り切れチェック
            if ($item->soldItem) {
                // ここでは購入者に罪はないので、一般的なエラーメッセージが良い
                return redirect('/')->with('error', '決済処理中に問題が発生しました。商品は購入済みです。');
            }

            SoldItem::create([
                'item_id' => $metadata->item_id,
                'buyer_id' => $metadata->buyer_id,
                'postcode' => $metadata->postcode,
                'address' => $metadata->address,
                'building' => $metadata->building,
            ]);

            // 使用した一時的な配送先住所をセッションから削除
            $request->session()->forget('shipping_address_' . $item->id);
        } catch (\Exception $e) {
            // データベースエラーなど
            // ここでStripeへの返金処理などを将来的に入れることも可能
            return redirect('/')->with('error', '購入記録の保存に失敗しました。');
        }

        return redirect()->route('mypage.show', ['page' => 'buy'])->with('message', '商品を購入しました！');
    }
}
