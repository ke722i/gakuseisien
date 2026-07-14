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
        <section class="search-area">

            <input type="text" placeholder="店舗名を検索">

            <button>検索</button>

        </section>

    <div class="menu-buttons">

    <a href="{{ route('store.request') }}" class="request-btn">
        店舗を申請する
    </a>

    <a href="{{ route('store.admin') }}" class="admin-btn">
        管理者画面
    </a>

</div>

    
</a>

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
                    <button onclick="location.href='{{ route('store.more', ['id' => 1]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 2]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 3]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 4]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 5]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 6]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 7]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 8]) }}'">詳細を見る</button>
                </div>

                <div class="store-card">
                    <h3>☕ カフェ△△</h3>
                    <p>★★★★☆</p>
                    <p>営業時間：9:00～18:00</p>
                    <p>予算：700円</p>
                    <p>徒歩5分</p>
                    <button onclick="location.href='{{ route('store.more', ['id' => 9]) }}'">詳細を見る</button>
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
