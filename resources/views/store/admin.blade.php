<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗管理画面</title>

    <link rel="stylesheet" href="{{ asset('css/store/admin.css') }}">
    @vite(['resources/css/store/admin.css'])
</head>
<body>

<div class="container">

    <!-- サイドメニュー -->
    @include('partials.sidebar', ['active' => 'nearby-shop'])

    <!-- メイン -->
    <main class="main">

        <h1>店舗管理</h1>

        <!-- 検索 -->
        <div class="search-box">

            <input
                type="text"
                placeholder="店舗名を検索">

            <button>検索</button>

        </div>

        <div class="content">

            <!-- 店舗管理 -->
            <section class="store-list">

                <h2>店舗一覧</h2>

                <div class="card">

                    <h3>🍜 ラーメン〇〇</h3>

                    <p>営業中</p>

                    <div class="buttons">

                        <button class="edit">
                            編集
                        </button>

                        <button class="hide">
                            非表示
                        </button>

                        <button class="delete">
                            削除
                        </button>

                    </div>

                </div>

                <div class="card">

                    <h3>☕ カフェ△△</h3>

                    <p>営業中</p>

                    <div class="buttons">

                        <button class="edit">
                            編集
                        </button>

                        <button class="hide">
                            非表示
                        </button>

                        <button class="delete">
                            削除
                        </button>

                    </div>

                </div>

            </section>

            <!-- 申請一覧 -->
            <section class="request-list">

                <h2>店舗申請一覧</h2>

                <div class="request-card">

                    <h3>🍛 カレー□□</h3>

                    <p>ジャンル：カレー</p>

                    <p>申請者：学生A</p>

                    <div class="buttons">

                        <button class="approve">
                            承認
                        </button>

                        <button class="reject">
                            却下
                        </button>

                    </div>

                </div>

                <div class="request-card">

                    <h3>🍖 焼肉☆☆</h3>

                    <p>ジャンル：焼肉</p>

                    <p>申請者：学生B</p>

                    <div class="buttons">

                        <button class="approve">
                            承認
                        </button>

                        <button class="reject">
                            却下
                        </button>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

</body>
</html>