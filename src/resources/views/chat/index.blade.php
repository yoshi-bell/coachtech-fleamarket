@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-sidebar__list">
            <p class="chat-sidebar__list-title">その他の取引</p>
            @foreach($otherTransactions as $transaction)
            <a href="{{ route('chat.index', $transaction->item->id) }}" class="chat-sidebar__item">
                <span class="chat-sidebar__item-name">{{ $transaction->item->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-user-header">
            <div class="chat-user-header__left">
                @if($otherUser->profile && $otherUser->profile->img_url)
                <img src="{{ asset('storage/profile_images/' . $otherUser->profile->img_url) }}" alt="プロフィール画像" class="chat-user-header__image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="プロフィール画像" class="chat-user-header__image">
                @endif
                <h1 class="chat-user-header__name">「{{ $otherUser->name }}」さんとの取引画面</h1>
            </div>
            @php
            $isBuyer = Auth::id() === $soldItem->buyer_id;
            $isSeller = Auth::id() === $item->seller_id;
            // 購入者が評価済みかどうか確認
            $buyerRating = $soldItem->ratings->where('rater_id', $soldItem->buyer_id)->first();
            // 出品者が評価済みかどうか確認
            $sellerRating = $soldItem->ratings->where('rater_id', $item->seller_id)->first();

            // 出品者側で、購入者が評価済みかつ自分が未評価の場合、モーダルを自動表示するためのフラグ
            $shouldOpenModal = $isSeller && $buyerRating && !$sellerRating;
            @endphp

            @if($isBuyer)
            <div class="chat-user-header__right">
                <button type="button" class="chat-user-header__complete-button" onclick="openRatingModal()">取引を完了する</button>
            </div>
            @elseif($shouldOpenModal)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    openRatingModal();
                });
            </script>
            @endif
        </div>

        <div class="chat-item-info">
            <div class="chat-item-info__image-wrapper">
                @if($item->img_url)
                <img src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="商品画像" class="chat-item-info__image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="商品画像" class="chat-item-info__image">
                @endif
            </div>
            <div class="chat-item-info__details">
                <h2 class="chat-item-info__name">{{ $item->name }}</h2>
                <p class="chat-item-info__price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages">
            @foreach($chats as $chat)
            <div class="chat-message {{ $chat->sender_id === Auth::id() ? 'chat-message--self' : 'chat-message--other' }}">
                <div class="chat-message__content">
                    <div class="chat-message__header">
                        <div class="chat-message__sender-image">
                            @if($chat->sender->profile && $chat->sender->profile->img_url)
                            <img src="{{ asset('storage/profile_images/' . $chat->sender->profile->img_url) }}" alt="送信者画像">
                            @else
                            <img src="{{ asset('images/placeholder.png') }}" alt="送信者画像">
                            @endif
                        </div>
                        <span class="chat-message__sender-name">{{ $chat->sender->name }}</span>
                    </div>
                    <div class="chat-message__bubble">
                        <p class="chat-message__text">{{ $chat->message }}</p>
                        @if($chat->image_path)
                        <img src="{{ asset('storage/' . $chat->image_path) }}" alt="送信画像" class="chat-message__image">
                        @endif
                        <span class="chat-message__time">{{ $chat->created_at->format('H:i') }}</span>
                    </div>
                    @if($chat->sender_id === Auth::id())
                    <div class="chat-message__actions">
                        <button type="button" class="chat-message__edit-button">編集</button>

                        <form action="{{ route('chat.update', $chat->id) }}" method="POST" class="chat-message__edit-form" style="display: none;">
                            @csrf
                            @method('PATCH')
                            <textarea name="message" class="chat-message__edit-textarea">{{ $chat->message }}</textarea>
                            <div class="chat-message__edit-buttons">
                                <button type="submit" class="chat-message__update-button">更新</button>
                                <button type="button" class="chat-message__cancel-button">キャンセル</button>
                            </div>
                        </form>

                        <form action="{{ route('chat.destroy', $chat->id) }}" method="POST" class="chat-message__delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chat-message__delete-button" onclick="return confirm('削除しますか？')">削除</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="chat-footer">
            @if($errors->any())
            <div class="chat-footer__errors">
                @error('message')
                <p class="chat-form__error">{{ $message }}</p>
                @enderror
                @error('image')
                <p class="chat-form__error">{{ $message }}</p>
                @enderror
            </div>
            @endif
            <form action="{{ route('chat.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="chat-form">
                @csrf
                <div class="chat-form__input-area">
                    <textarea name="message" id="chat-message-input" class="chat-form__textarea" placeholder="取引メッセージを記入してください"></textarea>
                    <label for="chat-image" class="chat-form__image-label">
                        画像を追加
                        <input type="file" name="image" id="chat-image" class="chat-form__image-input" accept="image/png, image/jpeg">
                    </label>
                </div>
                <button type="submit" class="chat-form__submit-button">
                    <img src="{{ asset('images/send-icon.png') }}" alt="送信" class="chat-form__submit-icon">
                </button>
            </form>
        </div>

        {{-- Rating Form & Modal --}}
        <form action="{{ route('rating.store', $soldItem->id) }}" method="POST">
            @csrf
            @include('components.rating-modal')
        </form>
    </div>
</div>

<script>
    // スクロールを一番下に
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    function openRatingModal() {
        document.getElementById('rating-modal').style.display = 'flex';
    }

    // FN009: 入力情報保持機能
    const chatMessageInput = document.getElementById('chat-message-input');
    const chatForm = chatMessageInput.closest('form');
    const soldItemId = "{{ $soldItem->id }}"; // Blade変数からsoldItem.idを取得
    const localStorageKey = `chat_message_for_${soldItemId}`;

    // ページロード時にlocalStorageからメッセージを復元
    document.addEventListener('DOMContentLoaded', function() {
        const savedMessage = localStorage.getItem(localStorageKey);
        if (savedMessage) {
            chatMessageInput.value = savedMessage;
        }
    });

    // 入力欄の変更をlocalStorageに保存
    chatMessageInput.addEventListener('input', function() {
        localStorage.setItem(localStorageKey, chatMessageInput.value);
    });

    // メッセージ送信時にlocalStorageをクリア
    chatForm.addEventListener('submit', function() {
        localStorage.removeItem(localStorageKey);
    });

    // FN010: メッセージ編集機能のUI切り替え
    document.addEventListener('DOMContentLoaded', function() {
        // 編集ボタンの処理
        document.querySelectorAll('.chat-message__edit-button').forEach(button => {
            button.addEventListener('click', function() {
                const content = this.closest('.chat-message__content');
                const bubble = content.querySelector('.chat-message__bubble');
                const editForm = content.querySelector('.chat-message__edit-form');
                const actions = content.querySelector('.chat-message__actions');

                bubble.style.display = 'none';
                actions.querySelector('.chat-message__delete-form').style.display = 'none';
                this.style.display = 'none';
                editForm.style.display = 'block';
            });
        });

        // キャンセルボタンの処理
        document.querySelectorAll('.chat-message__cancel-button').forEach(button => {
            button.addEventListener('click', function() {
                const content = this.closest('.chat-message__content');
                const bubble = content.querySelector('.chat-message__bubble');
                const editForm = content.querySelector('.chat-message__edit-form');
                const actions = content.querySelector('.chat-message__actions');

                bubble.style.display = ''; // or 'flex' if it was
                actions.querySelector('.chat-message__delete-form').style.display = '';
                actions.querySelector('.chat-message__edit-button').style.display = '';
                editForm.style.display = 'none';
            });
        });
    });
</script>
@endsection