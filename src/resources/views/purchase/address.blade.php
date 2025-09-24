@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-content">
    <h2 class="address-content__title">住所の変更</h2>

    <form action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="post" class="address-form">
        @csrf
        @method('PATCH')
        <div class="address-form__group">
            <label for="postcode" class="address-form__label">郵便番号</label>
            <input type="text" id="postcode" name="postcode" class="address-form__input" value="{{ old('postcode', $address->postcode ?? '') }}">
            @error('postcode')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="address-form__group">
            <label for="address" class="address-form__label">住所</label>
            <input type="text" id="address" name="address" class="address-form__input" value="{{ old('address', $address->address ?? '') }}">
            @error('address')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="address-form__group">
            <label for="building" class="address-form__label">建物名</label>
            <input type="text" id="building" name="building" class="address-form__input" value="{{ old('building', $address->building ?? '') }}">
            @error('building')
                <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="address-form__submit-btn">更新する</button>
    </form>
</div>
@endsection
