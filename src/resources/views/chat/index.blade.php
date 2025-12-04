@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container" id="chat-container" data-sold-item-id="{{ $transaction['soldItem']->id }}">
    <div class="chat-sidebar">
        <div class="chat-sidebar__list">
            <p class="chat-sidebar__list-title">その他の取引</p>
            @foreach($sidebar['otherTransactions'] as $otherTransaction)
            <a href="{{ route('chat.index', $otherTransaction->item->id) }}" class="chat-sidebar__item">
                <span class="chat-sidebar__item-name">{{ $otherTransaction->item->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-user-header">
            <div class="chat-user-header__left">
                @if($transaction['otherUser']->profile && $transaction['otherUser']->profile->img_url)
                <img src="{{ asset('storage/profile_images/' . $transaction['otherUser']->profile->img_url) }}" alt="プロフィール画像" class="chat-user-header__image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="プロフィール画像" class="chat-user-header__image">
                @endif
                <h1 class="chat-user-header__name">{{ $transaction['otherUser']->name }}さんとの<span class="responsive-break"></span>取引画面</h1>
            </div>
            @if($page['isBuyer'])
            <div class="chat-user-header__right">
                <button type="button" class="chat-user-header__complete-button" onclick="openRatingModal()">取引を完了する</button>
            </div>
            @elseif($page['shouldOpenModal'])
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    openRatingModal();
                });
            </script>
            @endif
        </div>

        <div class="chat-item-info">
            <div class="chat-item-info__image-wrapper">
                @if($transaction['item']->img_url)
                <img src="{{ asset('storage/item_images/' . $transaction['item']->img_url) }}" alt="商品画像" class="chat-item-info__image">
                @else
                <img src="{{ asset('images/placeholder.png') }}" alt="商品画像" class="chat-item-info__image">
                @endif
            </div>
            <div class="chat-item-info__details">
                <h2 class="chat-item-info__name">{{ $transaction['item']->name }}</h2>
                <p class="chat-item-info__price">¥{{ number_format($transaction['item']->price) }}</p>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages">
            @foreach($transaction['chats'] as $chat)
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
            <form action="{{ route('chat.store', $transaction['item']->id) }}" method="POST" enctype="multipart/form-data" class="chat-form">
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
        <form action="{{ route('rating.store', $transaction['soldItem']->id) }}" method="POST">
            @csrf
            @include('components.rating-modal', ['item' => $transaction['item']])
        </form>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/chat.js') }}"></script>
@endsection