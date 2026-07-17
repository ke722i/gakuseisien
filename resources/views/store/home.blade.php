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
        <section class="search-area">

            <input type="text" placeholder="店舗名を検索">

            <button>検索</button>

        </section>

    <div class="menu-buttons">

    <a href="{{ route('store.admin') }}" class="admin-btn">
        管理者画面
    </a>

    <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>

</div>

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

        <div class="store-content">

            <!-- 店舗一覧（shopsテーブルのデータを表示） -->
            <section class="store-grid">

                @forelse ($shops as $shop)
                    @php
                        // ジャンルに応じた表示用アイコン
                        $genreIcon = match ($shop->genre) {
                            'ラーメン' => '🍜',
                            'カフェ' => '☕',
                            '定食' => '🍛',
                            '寿司' => '🍣',
                            'コンビニ' => '🏪',
                            default => '🍽️',
                        };
                    @endphp
                    <div class="store-card">
                        <h3>{{ $genreIcon }} {{ $shop->name }}</h3>
                        <p>ジャンル：{{ $shop->genre }}</p>
                        <p>営業時間：{{ $shop->business_hours }}</p>
                        <p>予算：{{ number_format($shop->budget) }}円</p>
                        <p>徒歩約{{ max(1, (int) ceil($shop->distance / 80)) }}分（{{ $shop->distance }}m）</p>
                        <button onclick="location.href='{{ route('store.more', ['id' => $shop->id]) }}'">詳細を見る</button>
                    </div>
                @empty
                    <p>表示できる店舗がありません。</p>
                @endforelse
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
