<?php

$id = $_GET['id'];

?>

<h1>店舗編集</h1>

<p>編集する店舗ID：<?= $id ?></p>

<a href="storehome.php">戻る</a>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗管理</title>
    <link rel="stylesheet" href="management.css">
</head>

<body>

<div class="container">

    <!-- サイドバー -->
    <aside class="sidebar">

        <h2>MENU</h2>

        <ul>
            <li><a href="index.html">ホーム</a></li>
            <li><a href="storehome.html">近辺店舗情報マップ</a></li>
            <li><a href="store_request.html">店舗申請</a></li>
            <li><a href="management.html">店舗管理</a></li>
            <li><a href="#">ログアウト</a></li>
        </ul>

    </aside>

    <!-- メイン -->
    <main class="main-content">

        <h1>店舗管理</h1>

        <!-- 検索 -->
        <div class="search-area">

            <input type="text" placeholder="店舗名を検索">

            <button>検索</button>

        </div>

        <div class="management-area">

            <!-- 左 -->
            <section class="panel">

                <h2>登録済み店舗（12件）</h2>

                <div class="shop-card">

                    <h3>🍜 ラーメン○○</h3>

                    <p>公開中</p>

                    <div class="button-group">

                        <button class="edit">編集</button>

                        <button class="hide">非表示</button>

                        <button class="delete">削除</button>

                    </div>

                </div>

                <div class="shop-card">

                    <h3>☕ カフェ△△</h3>

                    <p>公開中</p>

                    <div class="button-group">

                        <button class="edit">編集</button>

                        <button class="hide">非表示</button>

                        <button class="delete">削除</button>

                    </div>

                </div>

            </section>

            <!-- 右 -->
            <section class="panel">

                <h2>店舗申請（3件）</h2>

                <div class="shop-card">

                    <h3>🍛 カレー□□</h3>

                    <p>申請者：山田太郎</p>

                    <div class="button-group">

                        <button class="approve">承認</button>

                        <button class="reject">却下</button>

                    </div>

                </div>

                <div class="shop-card">

                    <h3>🍔 バーガー☆☆</h3>

                    <p>申請者：佐藤花子</p>

                    <div class="button-group">

                        <button class="approve">承認</button>

                        <button class="reject">却下</button>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

</body>

</html>