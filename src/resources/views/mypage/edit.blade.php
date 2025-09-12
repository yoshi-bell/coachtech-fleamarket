@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile__content">
    <div class="profile__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PATCH') {{-- またはPUT --}}

        <div class="form__group profile__image-group">
            <div class="profile__image-preview">
                @if($user->profile && $user->profile->img_url)
                    <img src="{{ asset('storage/profile_images/' . $user->profile->img_url) }}" alt="プロフィール画像">
                @else
                    <div class="profile__image-placeholder"></div>
                @endif
            </div>
            <label for="img_url" class="profile__image-select-button">画像を選択する</label>
            <input type="file" id="img_url" name="img_url" accept="image/jpeg,image/png" style="display: none;">
            <div class="form__error">
                @error('img_url')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" />
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="postcode" value="{{ old('postcode', $user->profile->postcode ?? '') }}" />
                </div>
                <div class="form__error">
                    @error('postcode')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="address" value="{{ old('address', $user->profile->address ?? '') }}" />
                </div>
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="building" value="{{ old('building', $user->profile->building ?? '') }}" />
                </div>
                <div class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection