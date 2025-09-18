<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coachtech-Fleamarket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="header__logo-image">
                </a>
                <div class="header__search">
                    <form action="/" method="get" novalidate>
                        <input type="text" name="keyword" placeholder="なにをお探しですか?" value="{{ request('keyword') }}">
                    </form>
                </div>
                <nav>
                    <ul class="header-nav">
                        <li class="header-nav__item">
                            @auth
                            <form action="/logout" method="post" novalidate>
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                            @endauth
                            @guest
                            <a class="header-nav__link" href="/login">ログイン</a>
                            @endguest
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/mypage">マイページ</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__sell-button" href="/sell">出品</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    @yield('js')
</body>

</html>