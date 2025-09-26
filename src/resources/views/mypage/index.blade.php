@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage__content">
    <div class="mypage__header">
        <div class="mypage__profile-info">
            <div class="mypage__profile-image">
                @if($user->profile && $user->profile->img_url)
                <img src="{{ asset('storage/profile_images/' . $user->profile->img_url) }}" alt="プロフィール画像">
                            @else
                            <img src="{{ asset('images/placeholder.png') }}" alt="プロフィール画像" class="mypage__profile-image-placeholder">
                            @endif            </div>
            <div class="mypage__username">{{ $user->name }}</div>
            <a href="{{ route('profile.edit') }}" class="mypage__edit-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="mypage__tabs">
        <a href="{{ route('mypage.show') }}" class="mypage__tab @if($activeTab == 'sell') active @endif">出品した商品</a>
        <a href="{{ route('mypage.show', ['page' => 'buy']) }}" class="mypage__tab @if($activeTab == 'buy') active @endif">購入した商品</a>
    </div>

    <div class="mypage__item-grid">
        @forelse ($displayItems as $item)
        <a href="{{ route('item.show', $item->id) }}" class="item-card-link">
            <div class="item-card">
                <div class="item-card__image">
                    @if($activeTab == 'sell' && $item->soldItem)
                    <div class="item-card__sold-overlay">
                        Sold
                    </div>
                    @endif
                    @if($item->img_url)
                    <img src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="商品画像">
                    @else
                    <img src="{{ asset('images/placeholder.png') }}" alt="商品画像">
                    @endif
                </div>
                <div class="item-card__name">{{ $item->name }}</div>
            </div>
        </a>
        @empty
        @endforelse
    </div>
</div>
@endsection