@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-sidebar__header">
            <div class="chat-sidebar__user-info">
                @if(Auth::user()->profile && Auth::user()->profile->img_url)
                <img src="{{ asset('storage/profile_images/' . Auth::user()->profile->img_url) }}" alt="プロフィール画像" class="chat-sidebar__user-image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="プロフィール画像" class="chat-sidebar__user-image">
                @endif
                <span class="chat-sidebar__user-name">{{ Auth::user()->name }}</span>
            </div>
        </div>
        <div class="chat-sidebar__list">
            <p class="chat-sidebar__list-title">その他の取引</p>
            @foreach($otherTransactions as $transaction)
            <a href="{{ route('chat.index', $transaction->item->id) }}" class="chat-sidebar__item-link">
                <div class="chat-sidebar__item">
                    <div class="chat-sidebar__item-name">{{ $transaction->item->name }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-header__item-info">
                @if($item->img_url)
                <img src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="商品画像" class="chat-header__item-image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="商品画像" class="chat-header__item-image">
                @endif
                <div class="chat-header__item-details">
                    <h2 class="chat-header__item-name">{{ $item->name }}</h2>
                    <p class="chat-header__item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            <form action="{{ route('rating.store', $item->id) }}" method="POST" class="chat-header__complete-form">
                @csrf
                <button type="button" class="chat-header__complete-button" onclick="openRatingModal()">取引を完了する</button>
                @include('components.rating-modal')
            </form>
        </div>

        <div class="chat-messages" id="chat-messages">
            @foreach($chats as $chat)
            <div class="chat-message {{ $chat->sender_id === Auth::id() ? 'chat-message--self' : 'chat-message--other' }}">
                <div class="chat-message__content">
                    <div class="chat-message__sender-image">
                        @if($chat->sender->profile && $chat->sender->profile->img_url)
                        <img src="{{ asset('storage/profile_images/' . $chat->sender->profile->img_url) }}" alt="送信者画像">
                        @else
                        <img src="{{ asset('images/placeholder.png') }}" alt="送信者画像">
                        @endif
                    </div>
                    <div class="chat-message__bubble">
                        <p class="chat-message__text">{{ $chat->message }}</p>
                        @if($chat->image_path)
                        <img src="{{ asset('storage/chat_images/' . $chat->image_path) }}" alt="送信画像" class="chat-message__image">
                        @endif
                        <span class="chat-message__time">{{ $chat->created_at->format('H:i') }}</span>
                        @if($chat->sender_id === Auth::id())
                        <form action="{{ route('chat.destroy', $chat->id) }}" method="POST" class="chat-message__delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chat-message__delete-button" onclick="return confirm('削除しますか？')">削除</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="chat-footer">
            <form action="{{ route('chat.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="chat-form">
                @csrf
                <div class="chat-form__input-area">
                    <textarea name="message" class="chat-form__textarea" placeholder="取引メッセージを送信"></textarea>
                    <label for="chat-image" class="chat-form__image-label">
                        画像を選択
                        <input type="file" name="image" id="chat-image" class="chat-form__image-input" accept="image/png, image/jpeg">
                    </label>
                </div>
                @error('message')
                <p class="chat-form__error">{{ $message }}</p>
                @enderror
                @error('image')
                <p class="chat-form__error">{{ $message }}</p>
                @enderror
                <button type="submit" class="chat-form__submit-button">送信</button>
            </form>
        </div>
    </div>
</div>

<script>
    // スクロールを一番下に
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    function openRatingModal() {
        document.getElementById('rating-modal').style.display = 'flex';
    }
</script>
@endsection