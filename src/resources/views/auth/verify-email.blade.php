@extends('layouts.app_auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email__content">
    <div class="verify-email__heading">
        <h2>メール認証</h2>
    </div>

    <div class="verify-email-form">
        @if (session('message'))
            <div class="verify-email-form__success-message" role="alert">
                {{ session('message') }}
            </div>
        @endif

        @if (session('resent'))
            <div class="verify-email-form__success-message" role="alert">
                認証メールを再送信しました。
            </div>
        @endif

        <p class="verify-email-form__message">
            ご登録ありがとうございます。<br>
            ご入力いただいたメールアドレスに認証リンクを送信しました。<br>
            メールをご確認の上、認証を完了させてください。
        </p>

        {{-- 「認証はこちらから」ボタン --}}
        <div class="form__button">
            <a href="http://localhost:8025" class="form__button--auth-link">認証はこちらから</a>
        </div>

        {{-- 「認証メールを再送する」リンク --}}
        <form class="form" method="POST" action="{{ route('verification.send') }}" style="margin-top: 20px;">
            @csrf
            <button type="submit" class="login__button-submit">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection
