<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>閲覧履歴</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <div class="content-header">
                <div>
                    <h1>閲覧履歴</h1>
                    <p>直近6日間で閲覧したニュースをまとめて掲載しています。</p>
                </div>

                <a href="/" class="history-button">戻る</a>
            </div>

            <!-- カテゴリー -->
            <div class="category-tabs">
                <a class="selected" href="/history">すべて</a>
                <a href="#">経済</a>
                <a href="#">スポーツ</a>
                <a href="#">政治</a>
                <a href="#">IT</a>
            </div>

            <!-- 履歴一覧 -->
            <section class="news-list" id="historyList">
                <p>閲覧履歴を読み込み中です。</p>
            </section>

        </main>
    </div>
</body>
</html>