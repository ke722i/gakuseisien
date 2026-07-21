<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>近辺店舗情報マップ</title>

    @vite(['resources/css/app.css', 'resources/css/store/home.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

        <!-- メイン -->
    <main class="main-content">

        <header>
            <h1>近辺店舗情報マップ</h1>
        </header>

        <!-- 検索 -->
        <form method="GET" action="{{ route('store.search') }}">

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

    @if (Auth::user()?->isTeacher())
    <a href="{{ route('store.admin') }}" class="admin-btn">
        管理者画面
    </a>
    @endif

    <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>

    @auth
    {{-- お気に入りのみ表示の切り替え --}}
    <a href="{{ ($onlyFavorites ?? false) ? route('nearby.shop') : route('nearby.shop', ['favorites' => 1]) }}"
       class="favorite-filter-btn {{ ($onlyFavorites ?? false) ? 'is-active' : '' }}">
        {{ ($onlyFavorites ?? false) ? '★ お気に入りのみ表示中' : '☆ お気に入りのみ' }}
    </a>
    @endauth

</div>

            <!-- 店舗一覧 -->
            <section class="store-grid">

@foreach($shops as $shop)

<div class="store-card">

    @auth
    @php($isFav = $shop->isFavoritedBy(Auth::user()))
    <form method="POST" action="{{ route('store.favorite.toggle', $shop) }}" class="fav-form">
        @csrf
        <button type="submit" class="fav-btn {{ $isFav ? 'is-fav' : '' }}"
                title="{{ $isFav ? 'お気に入りを解除' : 'お気に入りに追加' }}"
                aria-label="{{ $isFav ? 'お気に入りを解除' : 'お気に入りに追加' }}">{{ $isFav ? '★' : '☆' }}</button>
    </form>
    @endauth

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

        <!-- ページネーション（2ページ以上あるときだけ表示） -->
        @if ($shops->hasPages())
        <section class="pagination">

            @if ($shops->onFirstPage())
                <button disabled>＜</button>
            @else
                <a href="{{ $shops->previousPageUrl() }}"><button>＜</button></a>
            @endif

            @foreach ($shops->getUrlRange(1, $shops->lastPage()) as $page => $url)
                @if ($page == $shops->currentPage())
                    <button class="active">{{ $page }}</button>
                @else
                    <a href="{{ $url }}"><button>{{ $page }}</button></a>
                @endif
            @endforeach

            @if ($shops->hasMorePages())
                <a href="{{ $shops->nextPageUrl() }}"><button>＞</button></a>
            @else
                <button disabled>＞</button>
            @endif

        </section>
        @endif

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
