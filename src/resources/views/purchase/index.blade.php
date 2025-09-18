@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.store', $item->id) }}" method="post" class="purchase-form-wrapper">
    @csrf
    <div class="purchase-content">
        <div class="purchase-main">
            <div class="purchase-item">
                <div class="purchase-item__img-wrapper">
                    <img class="purchase-item__img" src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="{{ $item->name }}">
                </div>
                <div class="purchase-item__body">
                    <h2 class="purchase-item__name">{{ $item->name }}</h2>
                    <p class="purchase-item__price"><span>¥ </span>{{ number_format($item->price) }}</p>
                </div>
            </div>

            <div class="purchase-form__group">
                <h3 class="purchase-form__group-title">支払い方法</h3>
                <div class="purchase-form__payment-select-wrapper">
                    <select name="payment_method" class="purchase-form__payment-select" id="payment-method-select">
                        <option value="1">コンビニ払い</option>
                        <option value="2">クレジットカード</option>
                    </select>
                </div>
            </div>

            <div class="purchase-form__group">
                <h3 class="purchase-form__group-title">配送先</h3>
                <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}" class="purchase-form__address-change-link">変更する</a>
                @php
                // セッションから一時的な住所を取得
                $session_address = session('shipping_address_' . $item->id);
                @endphp
                <div class="purchase-form__address">
                    @if ($session_address)
                    {{-- セッションに住所がある場合 (住所変更後) --}}
                    <p class="purchase-form__address-postcode">〒{{ $session_address['postcode'] }}</p>
                    <p class="purchase-form__address-text">{{ $session_address['address'] }}</p>
                    <p class="purchase-form__address-building">{{ $session_address['building'] ?? '' }}</p>
                    @elseif ($address)
                    {{-- セッションに無く、プロフィールに住所がある場合 --}}
                    <p class="purchase-form__address-postcode">〒{{ $address->postcode }}</p>
                    <p class="purchase-form__address-text">{{ $address->address }}</p>
                    <p class="purchase-form__address-building">{{ $address->building }}</p>
                    @else
                    {{-- どちらにも住所がない場合 --}}
                    <p class="purchase-form__address-none">住所が登録されていません</p>
                    <a href="{{ route('profile.edit') }}" class="purchase-form__address-add-link">登録はこちら</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="purchase-sidebar">
            <div class="purchase-summary">
                <div class="purchase-summary__row">
                    <span class="purchase-summary__label">商品代金</span>
                    <span class="purchase-summary__value"><span>¥</span>{{ number_format($item->price) }}</span>
                </div>
                <div class="purchase-summary__row">
                    <span class="purchase-summary__label">支払い方法</span>
                    <span class="purchase-summary__value" id="payment-method-display">コンビニ払い</span>
                </div>
            </div>
            <button type="submit" class="purchase-form__submit-btn">購入する</button>
        </div>
    </div>
</form>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment-method-select');
        const paymentDisplay = document.getElementById('payment-method-display');

        paymentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex].text;
            paymentDisplay.textContent = selectedOption;
        });
    });
</script>
@endsection