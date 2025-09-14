@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_detail.css') }}">
@endsection

@section('content')
<div class="item__content">
    <div class="item__card">
        <div class="item__image">
            @if($item->soldItem)
            <div class="item__sold-overlay">
                Sold
            </div>
            @endif
            <img src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="{{ $item->name }}">
        </div>
        <div class="item__info">
            <h1 class="item__name">{{ $item->name }}</h1>
            <span class="item__brand">{{ $item->brand ?? 'なし' }}</span>
            <p class="item__price"> ¥{{ number_format($item->price) }}<span>(税込)</span></p>

            <div class="item__actions">
                <div class="item__counts">
                    <form id="like-form" class="item__count-box" data-item-id="{{ $item->id }}" data-is-liked="{{ $isLiked ? 'true' : 'false' }}">
                        @csrf
                        <button type="submit" class="like-button">
                            <i id="like-icon" class="far fa-star {{ $isLiked ? 'liked' : '' }}"></i>
                        </button>
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </form>
                    <div class="item__count-box">
                        <a href="#item__comments-section" class="comment-link">
                            <i class="far fa-comment"></i>
                        </a>
                        <span>{{ $item->comments->count() }}</span>
                    </div>
                </div>
                @if($item->soldItem)
                <button class="item__purchase-button--sold-out" disabled>完売いたしました</button>
                @else
                <a href="{{ route('purchase.create', ['item' => $item->id]) }}" class="item__purchase-button">購入手続きへ</a>
                @endif
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

            <div id="item__comments-section" class="item__comments-section">
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

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const likeForm = document.getElementById('like-form');
        if (likeForm) {
            likeForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const itemId = this.dataset.itemId;
                let isLiked = this.dataset.isLiked === 'true';
                const likeIcon = document.getElementById('like-icon');
                const likeCountSpan = document.getElementById('like-count');

                const method = isLiked ? 'DELETE' : 'POST';
                const url = `/like/${itemId}`;

                fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        likeCountSpan.textContent = data.likeCount;
                        isLiked = !isLiked;
                        this.dataset.isLiked = isLiked ? 'true' : 'false';
                        likeIcon.classList.toggle('liked');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }
    });
</script>
@endsection