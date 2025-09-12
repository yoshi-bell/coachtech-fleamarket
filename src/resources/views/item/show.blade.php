@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
<div class="item__content">
    <div class="item__card">
        <div class="item__image">
            <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
        </div>
        <div class="item__info">
            <h1 class="item__name">{{ $item->name }}</h1>
            <span class="item__brand">{{ $item->brand ?? 'なし' }}</span>
            <p class="item__price"> ¥{{ number_format($item->price) }}<span>(税込)</span></p>

            <div class="item__actions">
                <div class="item__counts">
                    <div class="item__count-box">
                        <i class="far fa-star"></i>
                        <span>{{ $item->likes->count() }}</span>
                    </div>
                    <div class="item__count-box">
                        <i class="far fa-comment"></i>
                        <span>{{ $item->comments->count() }}</span>
                    </div>
                </div>
                <button class="item__purchase-button">購入手続きへ</button>
            </div>

            <div class="item__description-section">
                <h3>商品説明</h3>
                <p class="item__description">{!! nl2br(e($item->description)) !!}</p>
            </div>

            <div class="item__info-section">
                <h3>商品の情報</h3>
                <div class="item__categories">
                    <span class="item__info-label">カテゴリー:</span>
                    @foreach($item->categories as $category)
                    <span class="item__category">{{ $category->content }}</span>
                    @endforeach
                </div>
                <div class="item__conditions">
                    <span class="item__info-label">商品の状態:</span>
                    <span class="item__condition">{{ $item->condition->content }}</span>
                </div>
            </div>

            <div class="item__comments-section">
                <h3>コメント</h3>
                @forelse($item->comments as $comment)
                <div class="item__comment-item">
                    <p><strong>{{ $comment->user->name }}</strong>: {{ $comment->comment }}</p>
                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                @empty
                <p>まだコメントはありません。</p>
                @endforelse
                <form action="#" method="post" class="item__comment-form">
                    @csrf
                    <textarea name="comment" placeholder="コメントを追加..."></textarea>
                    <button type="submit">コメントする</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection