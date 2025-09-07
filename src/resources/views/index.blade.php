@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
<div class="item-list__tabs">
    <a href="/" class="item-list__tab @if(!request()->has('tab') || request()->get('tab') == 'recommended') active @endif">おすすめ</a>
    <a href="/?tab=mylist" class="item-list__tab @if(request()->get('tab') == 'mylist') active @endif">マイリスト</a>
</div>

<div class="item-list__grid">
    {{-- 商品カードの繰り返し --}}
    @for ($i = 0; $i < 12; $i++) {{-- 仮で12個のダミー商品を表示 --}}
        <div class="item-card">
            <div class="item-card__image">
                <img src="{{ asset('images/placeholder.png') }}" alt="商品画像"> {{-- 仮の画像 --}}
            </div>
            <div class="item-card__name">商品名</div>
        </div>
    @endfor
</div>
@endsection