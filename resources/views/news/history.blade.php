<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>閲覧履歴</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/news.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'news'])

        <!-- メイン画面 -->
        <main class="content">

            <div class="content-header">
                <div>
                    <h1>閲覧履歴</h1>
                    <p>直近6日間で閲覧したニュースをまとめて掲載しています。</p>
                </div>

                <a href="/recentnews" class="history-button">戻る</a>
            </div>


            <!-- 履歴一覧 -->
            <section class="news-list" id="historyList">
                <p>閲覧履歴を読み込み中です。</p>
            </section>

        </main>
    </div>
</body>

</html>