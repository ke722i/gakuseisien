<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>空き教室予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/home/student.css', 'resources/css/reservation/home/student.css',])
</head>
<body>
    <div class="app-layout">

        <!-- 左サイドバー -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>学生支援.com</h2>
                <p>学校便利掲示板システム</p>
            </div>

            <nav class="sidebar-menu">
                <a href="#">ホーム</a>
                <a href="#">空き教室予約</a>
                <a href="#">掲示板</a>
                <a href="#">学内Q＆A</a>
                <a href="#">イベント・締切カレンダー</a>
                <a href="#">欠席・遅刻届</a>
                <a href="#" class="active">時事ニュースまとめ</a>
                <a href="#">近辺店舗情報マップ</a>
            </nav>

            <div class="logout">
                <a href="#">ログアウト</a>
            </div>
        </aside>

        <!-- メイン画面 -->
        <main class="content">
            <div class="center-panel">

                <a href="#" class="big-button">空き教室予約</a>

                <a href="#" class="big-button">予約一覧</a>
            </div>
        </main>
    </div>
</body>
</html>