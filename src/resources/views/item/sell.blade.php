@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-content">
    <h2 class="sell-content__title">商品の出品</h2>
    <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data" class="sell-form" novalidate>
        @csrf
        <div class="sell-form__group">
            <h3 class="sell-form__image-title">商品画像</h3>
            <div class="sell-form__image-upload">
                <input type="file" id="img_url" name="img_url" class="sell-form__image-input">
                <label for="img_url" class="sell-form__image-label">画像を選択する</label>
            </div>
            @error('img_url')
            <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell-form__group">
            <h3 class="sell-form__group-title">商品の詳細</h3>
            <div class="sell-form__sub-group">
                <label class="sell-form__label">カテゴリー</label>
                <div class="sell-form__category-tags">
                    @foreach($categories as $category)
                    <div class="sell-form__category-tag">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat-{{ $category->id }}" class="sell-form__category-input" {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                        <label for="cat-{{ $category->id }}">{{ $category->content }}</label>
                    </div>
                    @endforeach
                </div>
                @error('category_ids')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="sell-form__sub-group">
                <label for="condition" class="sell-form__label">商品の状態</label>
                <select id="condition" name="condition_id" class="sell-form__select">
                    <option value="">選択してください</option>
                    @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>{{ $condition->content }}</option>
                    @endforeach
                </select>
                @error('condition_id')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="sell-form__group">
            <h3 class="sell-form__group-title">商品名と説明</h3>
            <div class="sell-form__sub-group">
                <label for="name" class="sell-form__label">商品名</label>
                <input type="text" id="name" name="name" class="sell-form__input" value="{{ old('name') }}">
                @error('name')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="sell-form__sub-group">
                <label for="brand" class="sell-form__label">ブランド名</label>
                <input type="text" id="brand" name="brand" class="sell-form__input" value="{{ old('brand') }}">
                @error('brand')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
            <div class="sell-form__sub-group">
                <label for="description" class="sell-form__label">商品の説明</label>
                <textarea id="description" name="description" class="sell-form__textarea">{{ old('description') }}</textarea>
                @error('description')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="sell-form__price">
            <label for="price" class="sell-form__label">販売価格</label>
            <div class="sell-form__price-input-wrapper">
                <span class="sell-form__currency-symbol">¥</span>
                <input type="number" id="price" name="price" class="sell-form__input--price" value="{{ old('price') }}">
            </div>
            @error('price')
            <div class="form__error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="form__button-submit">出品する</button>
    </form>
</div>
@endsection