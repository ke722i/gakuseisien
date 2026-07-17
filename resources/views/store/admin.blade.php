<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗管理画面</title>

    @vite(['resources/css/store/admin.css'])
</head>
<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main">

        <h1>店舗管理</h1>

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

            <a href="{{ route('store.edit', $shop->id) }}">
                <button class="edit">編集</button>
            </a>

            <form action="{{ route('store.hide', $shop->id) }}" method="POST">
    @csrf

    @if($shop->is_visible)
        <button class="hide">非表示</button>
    @else
        <button class="show">表示</button>
    @endif

</form>

            <form action="{{ route('store.destroy', $shop->id) }}" method="POST">
                @csrf
                <button class="delete">削除</button>
            </form>

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

                    <div class="buttons">

                        <a href="{{ route('store.request.more', $request->id) }}">
                            <button type="button" class="detail">詳細を見る</button>
                        </a>

                        <form action="{{ route('store.request.approve', $request->id) }}" method="POST">
                            @csrf
                            <button class="approve">承認</button>
                        </form>

                        <form action="{{ route('store.request.reject', $request->id) }}" method="POST">
                            @csrf
                            <button class="reject">却下</button>
                        </form>

                    </div>

                </div>

                @endforeach

            </section>

        </div>

    </main>

</div>

</body>
</html>