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
                @endif
            </div>
            <div class="mypage__user-details">
                <div class="mypage__username">{{ $user->name }}</div>
                @if($user->average_rating > 0)
                <div class="mypage-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $user->average_rating)
                            <span class="mypage-rating__star--filled">★</span>
                        @else
                            <span class="mypage-rating__star--empty">★</span>
                        @endif
                    @endfor
                </div>
                @endif
            </div>
            <a href="{{ route('profile.edit') }}" class="mypage__edit-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="mypage__tabs">
        <a href="{{ route('mypage.show') }}" class="mypage__tab @if($activeTab == 'sell') active @endif">出品した商品</a>
        <a href="{{ route('mypage.show', ['page' => 'buy']) }}" class="mypage__tab @if($activeTab == 'buy') active @endif">購入した商品</a>
        <a href="{{ route('mypage.show', ['page' => 'transaction']) }}" class="mypage__tab @if($activeTab == 'transaction') active @endif">
            取引中の商品
            @if(isset($unreadTotalCount) && $unreadTotalCount > 0)
            <span class="mypage__tab-badge">{{ $unreadTotalCount }}</span>
            @endif
        </a>
    </div>

    <div class="mypage__item-grid">
        @forelse ($displayItems as $item)
        @php
        $isTransaction = $activeTab == 'transaction';
        // 取引タブの場合は $item は SoldItem インスタンス、それ以外は Item インスタンス
        $displayItem = $isTransaction ? $item->item : $item;
        // 取引タブの場合は $item 自体が SoldItem、それ以外はリレーションから取得
        $soldItem = $isTransaction ? $item : ($item->soldItem ?? null);
        @endphp
        <a href="{{ $isTransaction ? route('chat.index', $displayItem->id) : route('item.show', $displayItem->id) }}" class="item-card-link">
            <div class="item-card">
                <div class="item-card__image">
                    @if($activeTab == 'sell' && $soldItem)
                    <div class="item-card__sold-overlay">
                        Sold
                    </div>
                    @endif
                    @if($isTransaction && $soldItem)
                    @php
                    $unreadCount = $soldItem->chats->where('sender_id', '!=', Auth::id())->where('read_at', null)->count();
                    @endphp
                    @if($unreadCount > 0)
                    <div class="item-card__badge">
                        {{ $unreadCount }}
                    </div>
                    @endif
                    @endif
                    @if($displayItem->img_url)
                    <img src="{{ asset('storage/item_images/' . $displayItem->img_url) }}" alt="商品画像">
                    @else
                    <img src="{{ asset('images/placeholder.png') }}" alt="商品画像">
                    @endif
                </div>
                <div class="item-card__name">{{ $displayItem->name }}</div>
            </div>
        </a>
        @empty
        @endforelse
    </div>
</div>
@endsection