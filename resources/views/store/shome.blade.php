<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>近辺店舗情報マップ</title>

    @vite([
        'resources/css/app.css',
        'resources/css/store/shome.css'
    ])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main-content">

        <header class="page-header">
            <h1>近辺店舗情報マップ</h1>
        </header>

        {{-- 検索 --}}
        <form method="GET" action="{{ route('nearby.shop') }}">

            <section class="search-area">

                <input
                    type="text"
                    name="keyword"
                    placeholder="店舗名・住所を検索"
                    value="{{ request('keyword') }}"
                >

                <button type="submit" class="keyword-search-btn">
                    検索
                </button>

                <a href="{{ route('nearby.shop') }}" class="all-btn">
                    すべて表示
                </a>

                <button
                    type="button"
                    class="detail-btn"
                    onclick="toggleSearch()"
                >
                    詳細検索 ▼
                </button>

            </section>

            <section
                id="detail-search"
                class="filter-area"
                style="{{ request()->hasAny([
                    'genre',
                    'budget',
                    'distance',
                    'payment_method'
                ]) ? 'display:flex;' : 'display:none;' }}"
            >

                <select name="genre">
                    <option value="">ジャンル</option>
                    <option value="ラーメン" @selected(request('genre') === 'ラーメン')>ラーメン</option>
                    <option value="カフェ" @selected(request('genre') === 'カフェ')>カフェ</option>
                    <option value="定食" @selected(request('genre') === '定食')>定食</option>
                    <option value="居酒屋" @selected(request('genre') === '居酒屋')>居酒屋</option>
                    <option value="中華" @selected(request('genre') === '中華')>中華</option>
                    <option value="寿司" @selected(request('genre') === '寿司')>寿司</option>
                    <option value="コンビニ" @selected(request('genre') === 'コンビニ')>コンビニ</option>
                    <option value="スイーツ" @selected(request('genre') === 'スイーツ')>スイーツ</option>
                    <option value="レストラン" @selected(request('genre') === 'レストラン')>レストラン</option>
                    <option value="その他" @selected(request('genre') === 'その他')>その他</option>
                </select>

                <select name="budget">
                    <option value="">価格</option>
                    <option value="500" @selected(request('budget') === '500')>500円以下</option>
                    <option value="1000" @selected(request('budget') === '1000')>1,000円以下</option>
                    <option value="1500" @selected(request('budget') === '1500')>1,500円以下</option>
                    <option value="2000" @selected(request('budget') === '2000')>2,000円以下</option>
                </select>

                <select name="distance">
                    <option value="">距離</option>
                    <option value="300" @selected(request('distance') === '300')>300m以内</option>
                    <option value="500" @selected(request('distance') === '500')>500m以内</option>
                    <option value="1000" @selected(request('distance') === '1000')>1,000m以内</option>
                    <option value="2000" @selected(request('distance') === '2000')>2,000m以内</option>
                </select>

                <select name="payment_method">
                    <option value="">決済方法</option>
                    <option value="現金" @selected(request('payment_method') === '現金')>現金</option>
                    <option value="クレジット" @selected(request('payment_method') === 'クレジット')>クレジット</option>
                    <option value="PayPay" @selected(request('payment_method') === 'PayPay')>PayPay</option>
                    <option value="電子マネー" @selected(request('payment_method') === '電子マネー')>電子マネー</option>
                    <option value="その他" @selected(request('payment_method') === 'その他')>その他</option>
                </select>

                <button type="submit" class="search-btn">
                    条件で検索
                </button>

            </section>

        </form>

        {{-- 管理・申請ボタン --}}
        <div class="menu-buttons">

            <a href="{{ route('store.admin') }}" class="action-card admin-btn">
                <span class="icon">⚙️</span>

                <span>
                    <strong>管理者画面</strong>
                    <small>店舗と申請を管理</small>
                </span>
            </a>

            <a href="{{ route('store.request') }}" class="action-card request-btn">
                <span class="icon">➕</span>

                <span>
                    <strong>店舗を申請する</strong>
                    <small>新しい店舗情報を登録</small>
                </span>
            </a>

        </div>

        {{-- 店舗一覧 --}}
        <div class="store-content">

            <section class="store-grid">

                @forelse($shops as $shop)

                    <article class="store-card">

                        <h3>{{ $shop->name }}</h3>

                        <p class="store-genre">
                            {{ $shop->genre }}
                        </p>

                        <dl class="store-info">
                            <div>
                                <dt>営業時間</dt>
                                <dd>{{ $shop->business_hours }}</dd>
                            </div>

                            <div>
                                <dt>予算</dt>
                                <dd>{{ number_format($shop->budget) }}円</dd>
                            </div>

                            <div>
                                <dt>学校から</dt>
                                <dd>{{ number_format($shop->distance) }}m</dd>
                            </div>
                        </dl>

                        <a
                            href="{{ route('store.more', $shop->id) }}"
                            class="detail-link"
                        >
                            詳細を見る
                        </a>

                    </article>

                @empty

                    <div class="empty-message">
                        条件に該当する店舗はありません。
                    </div>

                @endforelse

            </section>

        </div>

        {{-- ページネーション --}}
        @if ($shops->hasPages())
            <div class="pagination">
                {{ $shops->links() }}
            </div>
        @endif

    </main>

</div>

<script>
    function toggleSearch() {
        const area = document.getElementById('detail-search');

        area.style.display =
            area.style.display === 'none' ? 'flex' : 'none';
    }
</script>

</body>
</html>