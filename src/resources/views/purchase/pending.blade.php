@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-content" style="justify-content: center; text-align: center; padding: 50px;">
    <h1 style="font-size: 24px; margin-bottom: 30px;">お支払い手続きのご案内</h1>
    <p style="margin-bottom: 20px;">「{{ $item->name }}」のお支払い手続きを受け付けました。</p>
    <p>Stripeから送信されたメール、または決済ページに表示された案内に従って、お近くのコンビニエンスストアで支払いを完了してください。</p>
    <p style="margin-top: 20px;">支払いが確認され次第、購入が完了となります。</p>
    <a href="{{ route('index') }}" class="purchase-form__submit-btn" style="margin-top: 40px; text-decoration: none; max-width: 300px; display: inline-block;">トップページに戻る</a>
</div>
@endsection
