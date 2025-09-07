@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="mypage-header">
        <div class="mypage-profile-info">
            <div class="mypage-profile-image">
                @if($user->profile && $user->profile->img_url)
                <img src="{{ asset('storage/profile_images/' . $user->profile->img_url) }}" alt="プロフィール画像">
                @else
                <div class="mypage-profile-image-placeholder"></div>
                @endif
            </div>
            <div class="mypage-username">{{ $user->name }}</div>
            <a href="{{ route('profile.edit') }}" class="mypage-edit-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="mypage-tabs">
        <a href="{{ route('mypage.show') }}" class="mypage-tab @if($activeTab == 'sell') active @endif">出品した商品</a>
        <a href="{{ route('mypage.show', ['page' => 'buy']) }}" class="mypage-tab @if($activeTab == 'buy') active @endif">購入した商品</a>
    </div>

    <div class="mypage-item-grid">
        @forelse ($displayItems as $item)
        <div class="item-card">
            <div class="item-card__image">
                @if($item->img_url)
                <img src="{{ asset('storage/profile_images/' . $item->img_url) }}" alt="商品画像"> {{-- 仮の画像パス --}}
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="商品画像"> {{-- 仮の画像 --}}
                @endif
            </div>
            <div class="item-card__name">{{ $item->name }}</div>
        </div>
        @empty
        <p>表示する商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection