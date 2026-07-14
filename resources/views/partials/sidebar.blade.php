{{--
    共通サイドバー部品。
    使い方: @include('partials.sidebar', ['active' => 'qna'])
    $active に現在ページのキーを渡すと、その項目がハイライトされる。
      home / reservation / board / qna / calendar / notification / news / shop

    ◆アイコンは仮（絵文字）です。あとで画像に差し替える場合は各 .menu-icon の中身を
      <img src="..."> などに置き換えてください（レイアウトは .menu-icon 側で固定済み）。

    ◆PC: 通常はアイコンのみの細いレール。マウスを重ねると横に広がりラベルが出る（本文の上に重なる）。
    ◆スマホ: 左上の ☰ ボタンで開閉するハンバーガー。
--}}
@php($active = $active ?? '')

{{-- スマホ用の開くボタン（PCでは非表示） --}}
<button class="menu-button" id="menuButton" aria-label="メニューを開く">☰</button>

{{-- メニューを開いた時の背景（スマホ用） --}}
<div class="overlay" id="overlay"></div>

<aside class="sidebar" id="sidebar">
    {{-- サイト名。クリックでホームへ --}}
    <a href="{{ url('/') }}" class="sidebar-logo" aria-label="ホームへ">
        <span class="logo-mark">学</span>
        <span class="logo-full">
            <span class="logo-title">学生支援.com</span>
            <span class="logo-sub">学校便利掲示板システム</span>
        </span>
    </a>

    <nav class="sidebar-menu">
        <a href="{{ url('/') }}" class="{{ $active === 'home' ? 'active' : '' }}">
            <span class="menu-icon">🏠</span><span class="label">ホーム</span>
        </a>
        <a href="{{ route('classroom.reservation') }}" class="{{ $active === 'reservation' ? 'active' : '' }}">
            <span class="menu-icon">🏫</span><span class="label">空き教室予約</span>
        </a>
        <a href="{{ route('forum.top') }}" class="{{ $active === 'forum' ? 'active' : '' }}">
            <span class="menu-icon">📋</span><span class="label">掲示板</span>
        </a>
        <a href="{{ route('gakunai.qna') }}" class="{{ $active === 'qna' ? 'active' : '' }}">
            <span class="menu-icon">💬</span><span class="label">学内Q＆A</span>
        </a>
        <a href="{{ route('event.calendar') }}" class="{{ $active === 'calendar' ? 'active' : '' }}">
            <span class="menu-icon">📅</span><span class="label">イベント・締切カレンダー</span>
        </a>
        <a href="{{ route('notification') }}" class="{{ $active === 'notification' ? 'active' : '' }}">
            <span class="menu-icon">📝</span><span class="label">欠席・遅刻届</span>
        </a>
        <a href="{{ route('recent.news') }}" class="{{ $active === 'news' ? 'active' : '' }}">
            <span class="menu-icon">📰</span><span class="label">時事ニュースまとめ</span>
        </a>
        <a href="{{ route('nearby.shop') }}" class="{{ $active === 'shop' ? 'active' : '' }}">
            <span class="menu-icon">📍</span><span class="label">近辺店舗情報マップ</span>
        </a>
    </nav>

    <div class="logout">
        @auth
            {{-- ログイン中のID表示 --}}
            <div class="current-user" title="ログイン中: {{ Auth::user()->login_id }}">
                <span class="menu-icon">👤</span><span class="label">{{ Auth::user()->login_id }}</span>
            </div>
        @endauth

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <span class="menu-icon">🚪</span><span class="label">ログアウト</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // スマホ用ハンバーガーの開閉。PCはCSSのホバーだけで動くのでJSは不要。
    (function () {
        const menuButton = document.getElementById('menuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuButton?.addEventListener('click', () => {
            sidebar?.classList.add('open');
            overlay?.classList.add('show');
        });

        overlay?.addEventListener('click', () => {
            sidebar?.classList.remove('open');
            overlay?.classList.remove('show');
        });
    })();
</script>
