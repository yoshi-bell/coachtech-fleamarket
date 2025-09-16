@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
<div class="item-list__tabs">
    {{-- Construct base query parameters --}}
    @php
    $baseQueryParams = [];
    if (isset($keyword) && $keyword) {
    $baseQueryParams['keyword'] = $keyword;
    }
    @endphp

    <a href="{{ route('index', $baseQueryParams) }}" class="item-list__tab @if($tab == 'all') active @endif">おすすめ</a>
    <a href="{{ route('index', array_merge($baseQueryParams, ['tab' => 'mylist'])) }}" class="item-list__tab @if($tab == 'mylist') active @endif">マイリスト</a>
</div>

<div class="item-list__grid">
    @if($tab == 'mylist' && !Auth::check())
    @else
    @forelse ($items as $item)
    <a href="{{ route('item.show', $item->id) }}" class="item-card-link">
        <div class="item-card">
            <div class="item-card__image">
                @if($item->soldItem)
                <div class="item-card__sold-overlay">
                    Sold
                </div>
                @endif
                <img src="{{ asset('storage/item_images/' . $item->img_url) }}" alt="{{ $item->name }}">
            </div>
            <div class="item-card__name">{{ $item->name }}</div>
        </div>
    </a>
    @empty
    @if($tab == 'mylist')
    <p>マイリストに商品がありません。</p>
    @else
    <p>商品がありません。</p>
    @endif
    @endforelse
    @endif
</div>
@endsection