<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coachtech-Fleamaket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&display=swap" rel="stylesheet">
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
                    <input type="text" placeholder="なにをお探しですか?">
                </div>
                <nav>
                    <ul class="header-nav">
                        @auth
                            <li class="header-nav__item">
                                <form action="/logout" method="post">
                                    @csrf
                                    <button class="header-nav__button">ログアウト</button>
                                </form>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="/mypage">マイページ</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__button" href="/sell">出品</a>
                            </li>
                        @endauth
                        @guest
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="/login">ログイン</a>
                            </li>
                            <li class="header-nav__item">
                                <a class="header-nav__link" href="/register">会員登録</a>
                            </li>
                        @endguest
                    </ul>
                </nav>
            </div>
        </div>
    </header>


    <main>
        @yield('content')
    </main>
@yield('modal')
</body>

</html>