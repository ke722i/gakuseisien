<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>時事ニュースまとめ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/news.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-layout">

        <!-- ハンバーガーメニューで開くサイドバー -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <h2>学生支援.com</h2>
                <p>学校便利掲示板システム</p>
            </div>

            <nav class="sidebar-menu">
                <a href="/">ホーム</a>
                <a href="#">空き教室予約</a>
                <a href="#">掲示板</a>
                <a href="#">学内Q＆A</a>
                <a href="#">イベント・締切カレンダー</a>
                <a href="#">欠席・遅刻届</a>
                <a href="/" class="active">時事ニュースまとめ</a>
                <a href="#">近辺店舗情報マップ</a>
            </nav>

            <div class="logout">
                <a href="#">ログアウト</a>
            </div>
        </aside>

        <!-- メニューを開いた時の背景 -->
        <div class="overlay" id="overlay"></div>

        <!-- メイン画面 -->
        <main class="content">

            <!-- 上のハンバーガーメニュー -->
            <div class="top-header">
                <button class="menu-button" id="menuButton">☰</button>
            </div>

            <!-- タイトル部分 -->
            <div class="content-header">
                <div>
                    <h1>時事ニュースまとめ</h1>
                    <p>学生に役立つ時事ニュースをまとめて掲載しています。</p>
                </div>

                <a href="/history" class="history-button">閲覧履歴</a>
            </div>

            <!-- カテゴリー -->
            <div class="category-tabs">
                <a class="{{ $currentCategory === 'all' ? 'selected' : '' }}" href="/">すべて</a>
                <a class="{{ $currentCategory === 'business' ? 'selected' : '' }}" href="/?category=business">経済</a>
                <a class="{{ $currentCategory === 'sports' ? 'selected' : '' }}" href="/?category=sports">スポーツ</a>
                <a class="{{ $currentCategory === 'politics' ? 'selected' : '' }}" href="/?category=politics">政治</a>
                <a class="{{ $currentCategory === 'technology' ? 'selected' : '' }}" href="/?category=technology">IT</a>
            </div>

            <!-- ニュース一覧 -->
            <section class="news-list">

                @forelse ($articles as $article)
                    <article class="news-card" data-title="{{ $article['title'] ?? 'タイトルなし' }}"
                        data-description="{{ $article['description'] ?? '概要はありません。' }}"
                        data-url="{{ $article['url'] ?? '#' }}" data-source="{{ $article['source']['name'] ?? '提供元不明' }}"
                        data-date="{{ str_replace('T', ' ', substr($article['publishedAt'] ?? '', 0, 16)) }}"
                        data-category="{{ $article['app_category'] ?? 'all' }}"
                        data-category-label="{{ $article['app_category_label'] ?? 'ニュース' }}">
                        <div class="news-top">
                            <div>
                                <div class="news-meta">
                                    <span class="tag {{ $article['app_category'] ?? 'all' }}">
                                        {{ $article['app_category_label'] ?? 'ニュース' }}
                                    </span>

                                    <span>
                                        {{ str_replace('T', ' ', substr($article['publishedAt'] ?? '', 0, 16)) }}
                                        ・
                                        {{ $article['source']['name'] ?? '提供元不明' }}
                                    </span>
                                </div>

                                <h2>
                                    {{ $article['title'] ?? 'タイトルなし' }}
                                </h2>
                            </div>

                            <button class="circle-button">⌄</button>
                        </div>

                        <div class="news-detail">
                            <p class="news-summary">
                                {{ $article['description'] ?? '概要はありません。' }}
                            </p>

                            <a href="{{ $article['url'] ?? '#' }}" target="_blank" class="read-more">
                                続きを読む
                            </a>
                        </div>
                    </article>
                @empty
                    <p>ニュースを取得できませんでした。</p>
                @endforelse

            </section>
        </main>
    </div>
</body>

</html>