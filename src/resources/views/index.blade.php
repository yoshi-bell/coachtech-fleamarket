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
    @forelse ($items as $item)
    <div class="item-card">
        <div class="item-card__image">
            <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
        </div>
        <div class="item-card__name">{{ $item->name }}</div>
    </div>
    @empty
    <p>商品がありません。</p>
    @endforelse
</div>
@endsection