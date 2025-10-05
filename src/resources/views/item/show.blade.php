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
                    @auth
                    @if(Auth::id() == $item->seller_id)
                    {{-- Seller's view --}}
                    <div class="item__count-box">
                        <i class="far fa-star" style="cursor: default;"></i>
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </div>
                    @else
                    {{-- Other users' view --}}
                    <form id="like-form" class="item__count-box" data-item-id="{{ $item->id }}" data-is-liked="{{ $isLiked ? 'true' : 'false' }}" novalidate>
                        @csrf
                        <button type="submit" class="like-button">
                            <i id="like-icon" class="far fa-star {{ $isLiked ? 'liked' : '' }}"></i>
                        </button>
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </form>
                    @endif
                    @endauth
                    @guest
                    {{-- Guest's view --}}
                    <div class="item__count-box">
                        <i class="far fa-star" style="cursor: default;"></i>
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </div>
                    @endguest
                    <div class="item__count-box">
                        <a href="#item__comments-section" class="comment-link">
                            <i class="far fa-comment"></i>
                        </a>
                        <span class="comment-count-display">{{ $item->comments->count() }}</span>
                    </div>
                </div>
                @if($item->soldItem)
                <button class="item__purchase-button--sold-out" disabled>完売いたしました</button>
                @elseif(Auth::check() && Auth::id() == $item->seller_id)
                <button class="item__purchase-button--sold-out" disabled>出品した商品です</button>
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
                <h3>コメント(<span class="comment-count-display">{{ $item->comments->count() }}</span>)</h3>
                @forelse($item->comments as $comment)
                <div class="item__comment-item">
                    <div class="comment__user-info">
                        <img src="{{ $comment->user->profile->img_url ? asset('storage/profile_images/' . $comment->user->profile->img_url) : asset('images/placeholder.png') }}" alt="User Profile" class="comment__user-image">
                        <span class="comment__username">{{ $comment->user->name }}</span>
                    </div>
                    <div class="comment__text-area">
                        <p class="comment__text">{{ $comment->comment }}</p>
                        <small class="comment__time">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <p id="no-comment-message">まだコメントはありません。</p>
                @endforelse
                <div class="comment__form-heading">商品へのコメント</div>
                <form id="comment-form" action="{{ route('comment.store', ['item' => $item->id]) }}" method="post" class="item__comment-form" novalidate>
                    @csrf
                    <textarea name="comment" placeholder="コメントを追加..." required></textarea>
                    <div id="comment-error-message" class="form__error"></div>
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
            likeForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const itemId = this.dataset.itemId;
                let isLiked = this.dataset.isLiked === 'true';
                const likeIcon = document.getElementById('like-icon');
                const likeCountSpan = document.getElementById('like-count');

                const likeMethod = isLiked ? 'DELETE' : 'POST';
                const url = `/like/${itemId}`;

                fetch(url, {
                        method: likeMethod,
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

        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const commentTextarea = this.querySelector('textarea[name="comment"]');
                const commentsSection = document.getElementById('item__comments-section');
                const noCommentMessage = document.getElementById('no-comment-message');
                const commentErrorMessageDiv = document.getElementById('comment-error-message');

                // 以前のエラーメッセージをクリア
                commentErrorMessageDiv.textContent = '';

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Comment submission successful:', data);

                        // 新しいコメント要素を作成
                        const newCommentDiv = document.createElement('div');
                        newCommentDiv.classList.add('item__comment-item');
                        newCommentDiv.innerHTML = `
                        <div class="comment__user-info">
                            <img src="{{ asset('storage/profile_images/') }}/${data.comment.user.profile && data.comment.user.profile.img_url ? data.comment.user.profile.img_url : 'default_profile.png'}" alt="User Profile" class="comment__user-image">
                            <span class="comment__username">${data.comment.user.name}</span>
                        </div>
                        <div class="comment__text-area">
                            <p class="comment__text">${data.comment.comment}</p>
                            <small class="comment__time">たった今</small>
                        </div>
                    `;

                        // 「まだコメントはありません。」のメッセージがあれば削除
                        if (noCommentMessage) {
                            noCommentMessage.remove();
                        }

                        // コメントリストの先頭に追加 (h3の直後)
                        const h3Element = commentsSection.querySelector('h3');
                        if (h3Element) {
                            // 基本的な処理: h3見出しの直後にコメントを追加
                            h3Element.after(newCommentDiv);
                        } else {
                            // h3要素が存在しなくなる万が一のケースに備え、prependで先頭に追加するフォールバック処理
                            commentsSection.prepend(newCommentDiv);
                        }

                        // コメント数を更新
                        const countSpans = document.querySelectorAll('.comment-count-display');
                        countSpans.forEach(span => {
                            span.textContent = data.commentCount;
                        });

                        // テキストエリアをクリア
                        commentTextarea.value = '';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'コメントの投稿中にエラーが発生しました。';
                        if (error.errors) {
                            // バリデーションエラーメッセージを表示
                            errorMessage = Object.values(error.errors).flat().join('\n');
                        } else if (error.message) {
                            errorMessage = error.message;
                        }
                        commentErrorMessageDiv.textContent = errorMessage;
                    });
            });
        }
    });
</script>
@endsection