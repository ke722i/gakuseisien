<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>店舗管理画面</title>

    @vite([
        'resources/css/app.css',
        'resources/css/store/admin.css'
    ])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main">

        <div class="page-header">
            <div>
                <a href="{{ route('nearby.shop') }}" class="back-link">
                    ← 店舗一覧へ戻る
                </a>

                <h1>店舗管理</h1>

                <p>
                    登録済み店舗と申請中の店舗を管理します。
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="admin-content">

            {{-- 登録済み店舗 --}}
            <section class="store-list">

                <div class="section-header">
                    <div>
                        <h2>登録済み店舗</h2>
                        <p>{{ $shops->count() }}件</p>
                    </div>
                </div>

                <div class="card-list">

                    @forelse ($shops as $shop)

                        <article class="card">

                            <div class="card-header">

                                <div>
                                    <h3>{{ $shop->name }}</h3>

                                    <span class="genre-label">
                                        {{ $shop->genre }}
                                    </span>
                                </div>

                                @if ($shop->is_visible)
                                    <span class="status-badge visible">
                                        公開中
                                    </span>
                                @else
                                    <span class="status-badge hidden">
                                        非公開
                                    </span>
                                @endif

                            </div>

                            <dl class="shop-information">

                                <div>
                                    <dt>営業時間</dt>
                                    <dd>{{ $shop->business_hours }}</dd>
                                </div>

                                <div>
                                    <dt>予算</dt>
                                    <dd>{{ number_format($shop->budget) }}円</dd>
                                </div>

                                <div>
                                    <dt>距離</dt>
                                    <dd>{{ number_format($shop->distance) }}m</dd>
                                </div>

                            </dl>

                            <div class="buttons">

                                <a
                                    href="{{ route('store.edit', $shop->id) }}"
                                    class="action-button edit"
                                >
                                    編集
                                </a>

                                <form
                                    action="{{ route('store.hide', $shop->id) }}"
                                    method="POST"
                                >
                                    @csrf

                                    @if ($shop->is_visible)
                                        <button
                                            type="submit"
                                            class="action-button hide"
                                        >
                                            非表示にする
                                        </button>
                                    @else
                                        <button
                                            type="submit"
                                            class="action-button show"
                                        >
                                            表示する
                                        </button>
                                    @endif
                                </form>

                                <form
                                    action="{{ route('store.destroy', $shop->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('この店舗を削除しますか？');"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="action-button delete"
                                    >
                                        削除
                                    </button>
                                </form>

                            </div>

                        </article>

                    @empty

                        <div class="empty-message">
                            登録されている店舗はありません。
                        </div>

                    @endforelse

                </div>

            </section>

            {{-- 店舗申請 --}}
            <section class="request-list">

                <div class="section-header">
                    <div>
                        <h2>店舗申請</h2>
                        <p>{{ $requests->count() }}件</p>
                    </div>
                </div>

                <div class="card-list">

                    @forelse ($requests as $request)

                        <article class="request-card">

                            <div class="request-card-header">
                                <div>
                                    <h3>{{ $request->name }}</h3>

                                    <span class="genre-label">
                                        {{ $request->genre }}
                                    </span>
                                </div>

                                <span class="status-badge pending">
                                    審査待ち
                                </span>
                            </div>

                            <div class="request-information">

                                <p>
                                    <strong>住所</strong>
                                    {{ $request->address }}
                                </p>

                                <p>
                                    <strong>予算</strong>
                                    {{ number_format($request->budget) }}円
                                </p>

                            </div>

                            <div class="request-buttons">

                                <a
                                    href="{{ route('store.request.more', $request->id) }}"
                                    class="action-button detail"
                                >
                                    詳細
                                </a>

                                <form
                                    action="{{ route('store.request.approve', $request->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('この申請を承認しますか？');"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="action-button approve"
                                    >
                                        承認
                                    </button>
                                </form>

                                <form
                                    action="{{ route('store.request.reject', $request->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('この申請を却下しますか？');"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="action-button reject"
                                    >
                                        却下
                                    </button>
                                </form>

                            </div>

                        </article>

                    @empty

                        <div class="empty-message">
                            現在、申請中の店舗はありません。
                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </main>

</div>

</body>
</html>