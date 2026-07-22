<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $shopRequest->name }}｜申請内容</title>

    @vite([
        'resources/css/app.css',
        'resources/css/store/more.css'
    ])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main-content">

        <div class="top-actions">
            <a href="{{ route('store.admin') }}" class="back-btn">
                ← 店舗管理へ戻る
            </a>
        </div>

        <section class="shop-card">

            <span class="shop-genre">{{ $shopRequest->genre }}</span>

            <h1 class="shop-heading">{{ $shopRequest->name }}</h1>

            <p class="shop-address">
                {{ $shopRequest->address }}
            </p>

            <table class="shop-table">

                <tr>
                    <th>営業時間</th>
                    <td>{{ $shopRequest->business_hours }}</td>
                </tr>

                <tr>
                    <th>学校からの距離</th>
                    <td>{{ number_format($shopRequest->distance) }} m</td>
                </tr>

                <tr>
                    <th>平均価格</th>
                    <td>{{ number_format($shopRequest->budget) }} 円</td>
                </tr>

                <tr>
                    <th>決済方法</th>
                    {{-- 複数選択のため配列で保存されている。古いデータは文字列のこともある --}}
                    <td>
                        {{ is_array($shopRequest->payment_method)
                            ? implode('、', $shopRequest->payment_method)
                            : $shopRequest->payment_method }}
                    </td>
                </tr>

                <tr>
                    <th>公式サイト</th>
                    <td>
                        @if ($shopRequest->official_url)
                            <a
                                href="{{ $shopRequest->official_url }}"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ $shopRequest->official_url }}
                            </a>
                        @else
                            <span class="muted-text">登録なし</span>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>申請日時</th>
                    <td>{{ $shopRequest->created_at->format('Y年n月j日 H:i') }}</td>
                </tr>

            </table>

            <div class="request-actions">

                <form
                    action="{{ route('store.request.approve', $shopRequest->id) }}"
                    method="POST"
                    data-confirm="この申請を承認しますか？"
                >
                    @csrf

                    <button type="submit" class="action-button approve">
                        承認する
                    </button>
                </form>

                <form
                    action="{{ route('store.request.reject', $shopRequest->id) }}"
                    method="POST"
                    data-confirm="この申請を却下しますか？"
                >
                    @csrf

                    <button type="submit" class="action-button reject">
                        却下する
                    </button>
                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>
