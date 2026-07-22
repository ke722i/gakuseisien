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

        {{-- 検索（絞り込みは search() が担当するため store.search へ送る） --}}
        <form method="GET" action="{{ route('store.search') }}">

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

                {{-- 申請フォームの選択肢と一致させる。ずれていると検索しても常に0件になる --}}
                <select name="payment_method">
                    <option value="">決済方法</option>
                    @foreach (['現金', 'クレジットカード', '交通系IC', 'QRコード決済', '電子マネー'] as $payment)
                        <option value="{{ $payment }}" @selected(request('payment_method') === $payment)>
                            {{ $payment }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="search-btn">
                    条件で検索
                </button>

            </section>

        </form>

        {{-- 管理・申請ボタン --}}
        <div class="menu-buttons">

            @if (Auth::user()?->isTeacher())
                <a href="{{ route('store.admin') }}" class="action-card admin-btn">
                    <span class="icon">⚙️</span>

                    <span>
                        <strong>管理者画面</strong>
                        <small>店舗と申請を管理</small>
                    </span>
                </a>
            @endif

            <a href="{{ route('store.request') }}" class="action-card request-btn">
                <span class="icon">➕</span>

                <span>
                    <strong>店舗を申請する</strong>
                    <small>新しい店舗情報を登録</small>
                </span>
            </a>

        </div>

        @auth
            {{-- お気に入りのみ表示の切り替え --}}
            <a
                href="{{ ($onlyFavorites ?? false) ? route('nearby.shop') : route('nearby.shop', ['favorites' => 1]) }}"
                class="favorite-filter-btn {{ ($onlyFavorites ?? false) ? 'is-active' : '' }}"
            >
                {{ ($onlyFavorites ?? false) ? '★ お気に入りのみ表示中' : '☆ お気に入りのみ' }}
            </a>
        @endauth

        {{-- 店舗一覧 --}}
        <div class="store-content">

            <section class="store-grid">

                @forelse($shops as $shop)

                    <article class="store-card">

                        @auth
                            {{-- お気に入りボタン（CSS側で カード右上に絶対配置される） --}}
                            @php($isFav = $shop->isFavoritedBy(Auth::user()))
                            <form
                                method="POST"
                                action="{{ route('store.favorite.toggle', $shop) }}"
                                class="fav-form"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="fav-btn {{ $isFav ? 'is-fav' : '' }}"
                                    title="{{ $isFav ? 'お気に入りを解除' : 'お気に入りに追加' }}"
                                    aria-label="{{ $isFav ? 'お気に入りを解除' : 'お気に入りに追加' }}"
                                >{{ $isFav ? '★' : '☆' }}</button>
                            </form>
                        @endauth

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