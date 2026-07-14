<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗詳細</title>
    @vite(['resources/css/store/more.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'nearby-shop'])

    <!-- メイン -->
    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">← 一覧へ戻る</a>
          <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>
        <h1>ラーメン〇〇（店舗ID：{{ $id }}）</h1>

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
