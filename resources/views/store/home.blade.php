<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>近辺店舗情報マップ</title>

    @vite(['resources/css/store/home.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'nearby-shop'])

        <!-- メイン -->
    <main class="main-content">

        <header>
            <h1>近辺店舗情報マップ</h1>
        </header>

        <!-- 検索 -->
        <form method="GET" action="{{ route('nearby.shop') }}">

    <section class="search-area">

        <input
            type="text"
            name="keyword"
            placeholder="店舗名を検索"
            value="{{ request('keyword') }}">

        <button type="submit">検索</button>

        <a href="{{ route('nearby.shop') }}" class="all-btn">
            すべて表示
        </a>

        <button type="button" class="detail-btn" onclick="toggleSearch()">
            詳細検索 ▼
        </button>

    </section>

    <section id="detail-search" class="filter-area" style="display:none;">

        <select name="genre">
            <option value="">ジャンル</option>
            <option value="ラーメン">ラーメン</option>
            <option value="カフェ">カフェ</option>
            <option value="定食">定食</option>
            <option value="居酒屋">居酒屋</option>
            <option value="中華">中華</option>
            <option value="寿司">寿司</option>
            <option value="コンビニ">コンビニ</option>
            <option value="スイーツ">スイーツ</option>
            <option value="レストラン">レストラン</option>
            <option value="その他">その他</option>
        </select>

        <select name="budget">
            <option value="">価格</option>
            <option value="500">500円以下</option>
            <option value="1000">1000円以下</option>
            <option value="1500">1500円以下</option>
            <option value="2000">2000円以下</option>
        </select>

        <select name="distance">
            <option value="">距離</option>
            <option value="300">300m以内</option>
            <option value="500">500m以内</option>
            <option value="1000">1000m以内</option>
            <option value="2000">2000m以内</option>
        </select>

        <select name="payment_method">
            <option value="">決済方法</option>
            <option value="現金">現金</option>
            <option value="クレジット">クレジット</option>
            <option value="PayPay">PayPay</option>
            <option value="電子マネー">電子マネー</option>
            <option value="その他">その他</option>
        </select>

        <button type="submit" class="search-btn">
            条件で検索
        </button>

    </section>

</form>

    <div class="menu-buttons">

    <a href="{{ route('store.admin') }}" class="admin-btn">
        管理者画面
    </a>

    <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>


        <div class="content">

            <!-- 店舗一覧 -->
            <section class="store-grid">

@foreach($shops as $shop)

<div class="store-card">

    <h3>{{ $shop->name }}</h3>

    <p>{{ $shop->genre }}</p>

    <p>営業時間：{{ $shop->business_hours }}</p>

    <p>予算：{{ $shop->budget }}円</p>

    <p>徒歩 {{ $shop->distance }}m</p>

    <a href="{{ route('store.more',$shop->id) }}">
        <button>詳細を見る</button>
    </a>

</div>

@endforeach

</section>

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

    <script>
        function toggleSearch(){

        const area = document.getElementById("detail-search");

        if(area.style.display === "none"){
            area.style.display = "flex";
        }else{
            area.style.display = "none";
        }

}
    </script>
</body>

</html>
