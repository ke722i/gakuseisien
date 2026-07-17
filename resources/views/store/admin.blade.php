<!DOCTYPE html> <html lang="ja">
     <head> 
        <meta charset="UTF-8"> 
        <title>店舗管理画面</title> 
        <link rel="stylesheet" href="{{ asset('css/store/admin.css') }}"
        > @vite(['resources/css/store/admin.css']) 
    </head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'nearby-shop'])

    <main class="main">

        <h1>店舗管理</h1>

        <!-- 検索 -->
        <div class="search-box">
            <input type="text" placeholder="店舗名を検索">
            <button>検索</button>
        </div>

        <div class="content">

            <!-- 店舗一覧 -->
            <section class="store-list">

                <h2>店舗一覧</h2>

                @foreach ($shops as $shop)

                <div class="card">

                    <h3>{{ $shop->name }}</h3>

                    <p>{{ $shop->genre }}</p>

                    <p>{{ $shop->business_hours }}</p>

                    <div class="buttons">

                        <button class="edit">編集</button>

                        <button class="hide">非表示</button>

                        <button class="delete">削除</button>

                    </div>

                </div>

                @endforeach

            </section>

            <!-- 申請一覧 -->
            <section class="request-list">

                <h2>店舗申請一覧</h2>

                @foreach ($requests as $request)

                <div class="request-card">

                    <h3>{{ $request->name }}</h3>

                    <p>ジャンル：{{ $request->genre }}</p>

                    <p>申請者：{{ $request->applicant }}</p>

                    <div class="buttons">

                        <button class="approve">承認</button>

                        <button class="reject">却下</button>

                    </div>

                </div>

                @endforeach

            </section>

        </div>

    </main>

</div>

</body>