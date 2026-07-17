<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗詳細</title>
    @vite(['resources/css/app.css', 'resources/css/store/more.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <!-- メイン -->
    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">← 一覧へ戻る</a>
          <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>
        <h1>{{ $shop->name }}（店舗ID：{{ $shop->id }}）</h1>

        <div class="detail-wrapper">

            <!-- 店舗情報（shopsテーブルのデータを表示） -->
            <section class="info-card">

                <h2>店舗情報</h2>

                <table>

                    <tr>
                        <th>ジャンル</th>
                        <td>{{ $shop->genre }}</td>
                    </tr>

                    <tr>
                        <th>営業時間</th>
                        <td>{{ $shop->business_hours }}</td>
                    </tr>

                    <tr>
                        <th>住所</th>
                        <td>{{ $shop->address }}</td>
                    </tr>

                    <tr>
                        <th>学校からの距離</th>
                        <td>徒歩約{{ max(1, (int) ceil($shop->distance / 80)) }}分（{{ $shop->distance }}m）</td>
                    </tr>

                    <tr>
                        <th>平均価格</th>
                        <td>{{ number_format($shop->budget) }}円</td>
                    </tr>

                    <tr>
                        <th>決済方法</th>
                        <td>{{ $shop->payment_method }}</td>
                    </tr>

                </table>

            </section>

            <!-- 地図 -->
            <section class="map-card">

                <h2>地図</h2>

                <div class="map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=..."
                        width="100%"
                        height="400"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>

            </section>

        </div>

        <!-- 口コミ（レビュー機能は未実装のためダミー表示） -->

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
