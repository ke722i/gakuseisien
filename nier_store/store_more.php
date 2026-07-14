<?php

$id = $_GET['id'];

?>

<h1>店舗詳細</h1>

<p>店舗ID：<?= $id ?></p>

<a href="storehome.php">戻る</a>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗詳細</title>
    <link rel="stylesheet" href="store_more.css">
</head>

<body>

<div class="container">

    <!-- サイドバー -->
    <aside class="sidebar">
        <h2>MENU</h2>

        <ul>
            <li><a href="index.html">ホーム</a></li>
            <li><a href="storehome.html">近辺店舗情報マップ</a></li>
            <li><a href="#">店舗申請</a></li>
            <li><a href="#">承認画面</a></li>
            <li><a href="#">ログアウト</a></li>
        </ul>
    </aside>

    <!-- メイン -->
    <main class="main-content">

        <a href="storehome.html" class="back-btn">← 一覧へ戻る</a>

        <h1>ラーメン〇〇</h1>

        <div class="detail-wrapper">

            <!-- 店舗情報 -->
            <section class="info-card">

                <h2>店舗情報</h2>

                <table>

                    <tr>
                        <th>評価</th>
                        <td>★★★★★ (4.8)</td>
                    </tr>

                    <tr>
                        <th>ジャンル</th>
                        <td>ラーメン</td>
                    </tr>

                    <tr>
                        <th>営業時間</th>
                        <td>11:00～22:00</td>
                    </tr>

                    <tr>
                        <th>住所</th>
                        <td>大阪市〇〇区〇〇</td>
                    </tr>

                    <tr>
                        <th>学校からの距離</th>
                        <td>徒歩3分</td>
                    </tr>

                    <tr>
                        <th>平均価格</th>
                        <td>800円</td>
                    </tr>

                    <tr>
                        <th>決済方法</th>
                        <td>現金・PayPay</td>
                    </tr>

                    <tr>
                        <th>公式URL</th>
                        <td>
                            <a href="#">https://sample.jp</a>
                        </td>
                    </tr>

                </table>

            </section>

            <!-- 地図 -->
            <section class="map-card">

                <h2>地図</h2>

                <div class="map-placeholder">
                    Google Map表示エリア
                </div>

            </section>

        </div>

        <!-- 口コミ -->

        <section class="review-card">

            <h2>口コミ</h2>

            <div class="review">

                <strong>★★★★★</strong>

                <p>学生でも入りやすく、量も多くて満足でした！</p>

            </div>

            <div class="review">

                <strong>★★★★☆</strong>

                <p>店員さんの対応が丁寧でした。</p>

            </div>

            <button class="review-btn">

                口コミを書く

            </button>

        </section>

    </main>

</div>

</body>
</html>