<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗管理</title>
</head>
<body>

<h1>店舗管理画面</h1>

<button onclick="location.href='store_more.php?id=1'">
詳細
</button>

<button onclick="location.href='manegement.php?id=1'">
編集
</button>

<button onclick="location.href='store_request.php'">
店舗申請
</button>

<button onclick="location.href='favorite.php'">
お気に入り
</button>

</body>
</html>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>近辺店舗情報マップ</title>

    <link rel="stylesheet" href="store_home.css">
</head>

<body>

<div class="container">

    <!-- サイドバー -->
    <aside class="sidebar">

        <h2>MENU</h2>

        <nav>
            <ul>
                <li><a href="#">ホーム</a></li>
                <li><a href="#">近辺店舗情報マップ</a></li>
                <li><a href="#">店舗申請</a></li>
                <li><a href="#">承認画面</a></li>
                <li><a href="#">ログアウト</a></li>
            </ul>
        </nav>

    </aside>

    <!-- メイン -->
    <main class="main-content">

        <header>
            <h1>近辺店舗情報マップ</h1>
        </header>

        <!-- 検索 -->
        <section class="search-area">

            <input type="text" placeholder="店舗名を検索">

            <button>検索</button>

        </section>

        <!-- フィルター -->
        <section class="filter-area">

            <select>
                <option>価格</option>
            </select>

            <select>
                <option>距離</option>
            </select>

            <select>
                <option>ジャンル</option>
            </select>

            <select>
                <option>営業時間</option>
            </select>

            <select>
                <option>決済方法</option>
            </select>

        </section>

        <div class="content">

            <!-- 店舗一覧 -->
            <section class="store-grid">

                <div class="store-card">
                    <h3>🍜 ラーメン○○</h3>
                    <p>★★★★★</p>
                    <p>営業時間：11:00～22:00</p>
                    <p>予算：800円</p>
                    <p>徒歩3分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <!-- あとは同じように9件まで追加 -->

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button>詳細を見る</button>
                </div>
            </section>

            <!-- お気に入り -->
            <aside class="favorite-area">

                <h2>お気に入り</h2>

                <div class="favorite-card">🍜 ラーメン○○</div>

                <div class="favorite-card">☕ カフェ△△</div>

                <div class="favorite-card">🍛 カレー□□</div>

            </aside>

        </div>

        <!-- ページネーション -->
        <section class="pagination">

            <button>＜</button>

            <button class="active">1</button>

            <button>2</button>

            <button>3</button>

            <button>＞</button>

        </section>

    </main>

</div>

</body>

</html>