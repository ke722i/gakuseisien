<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>時事ニュースまとめ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/news.css', 'resources/js/news.js'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'news'])

        <!-- メイン画面 -->
        <main class="content">

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
                <a class="{{ $currentCategory === 'all' ? 'selected' : '' }}" href="/recentnews">すべて</a>
                <a class="{{ $currentCategory === 'business' ? 'selected' : '' }}" href="/recentnews?category=business">経済</a>
                <a class="{{ $currentCategory === 'sports' ? 'selected' : '' }}" href="/recentnews?category=sports">スポーツ</a>
                <a class="{{ $currentCategory === 'politics' ? 'selected' : '' }}" href="/recentnews?category=politics">政治</a>
                <a class="{{ $currentCategory === 'technology' ? 'selected' : '' }}" href="/recentnews?category=technology">IT</a>
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

                            <button class="circle-button">▼</button>
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