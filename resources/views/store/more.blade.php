<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $shop->name }}｜店舗詳細</title>

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
            <a href="{{ route('nearby.shop') }}" class="back-btn">
                ← 一覧へ戻る
            </a>

            <a href="{{ route('store.request') }}" class="request-btn">
                店舗を申請する
            </a>
        </div>

        {{-- 完了メッセージは共通ポップアップ（partials/toast）で表示する --}}

        @if ($errors->any())
            <div class="error-message">
                <p>入力内容を確認してください。</p>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="shop-card">

            <div class="shop-heading">
                <p class="shop-genre">
                    {{ $shop->genre }}
                </p>

                <h1>{{ $shop->name }}</h1>

                <p class="shop-address">
                    {{ $shop->address }}
                </p>

                @auth
                @php
                    $isFav = $shop->isFavoritedBy(Auth::user());
                @endphp
                <form method="POST" action="{{ route('store.favorite.toggle', $shop) }}" class="fav-detail-form">
                    @csrf
                    <button type="submit" class="fav-detail-btn {{ $isFav ? 'is-fav' : '' }}">
                        {{ $isFav ? '★ お気に入り登録済み' : '☆ お気に入りに追加' }}
                    </button>
                </form>
                @endauth
            </div>

            <table class="shop-table">

                <tr>
                    <th>営業時間</th>
                    <td>{{ $shop->business_hours }}</td>
                </tr>

                <tr>
                    <th>学校からの距離</th>
                    <td>{{ number_format($shop->distance) }} m</td>
                </tr>

                <tr>
                    <th>平均価格</th>
                    <td>{{ number_format($shop->budget) }} 円</td>
                </tr>

                <tr>
                    <th>決済方法</th>
                    {{-- 複数選択のため配列で保存されている。古いデータは文字列のこともある --}}
                    <td>
                        {{ is_array($shop->payment_method)
                            ? implode('、', $shop->payment_method)
                            : $shop->payment_method }}
                    </td>
                </tr>

                <tr>
                    <th>公式サイト</th>
                    <td>
                        @if ($shop->official_url)
                            <a
                                href="{{ $shop->official_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                公式サイトを見る
                            </a>
                        @else
                            <span class="muted-text">
                                未登録
                            </span>
                        @endif
                    </td>
                </tr>

            </table>

        </section>

        <div class="detail-grid">

            <section class="map-card">

    <div class="section-header">
        <h2>地図</h2>

        <a
            href="{{ $googleMapsUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="google-map-link"
        >
            Google Mapsで開く
        </a>
    </div>

    @if ($googleEmbedEnabled && $mapEmbedUrl)

        <div class="map">
            <iframe
                src="{{ $mapEmbedUrl }}"
                title="{{ $shop->name }}の地図"
                loading="lazy"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin">
            </iframe>
        </div>

    @else

        <div class="google-placeholder">
            <p class="placeholder-title">
                Google Maps連携準備中
            </p>

            <p>
                APIキーを設定すると地図が表示されます。
            </p>

            <a
                href="{{ $googleMapsUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="google-map-button"
            >
                Google Mapsで検索する
            </a>
        </div>

    @endif

</section>

            <section class="rating-card">

                <h2>店舗評価</h2>

                <div class="rating-box">

    <h3>Google Maps評価</h3>

    @if (!$googlePlacesEnabled)

        <p class="rating-message">
            APIキー設定後にGoogle評価が表示されます。
        </p>

    @elseif ($googleRating !== null)

        <div class="rating-score-row">
            <strong class="rating-number">
                {{ number_format($googleRating, 1) }}
            </strong>

            <span class="rating-stars">
                @for ($i = 1; $i <= 5; $i++)
                    {{ $i <= round($googleRating) ? '★' : '☆' }}
                @endfor
            </span>
        </div>

        <p class="rating-count">
            {{ number_format($googleReviewCount ?? 0) }}件の評価
        </p>

    @else

        <p class="rating-message">
            Google評価を取得できませんでした。
        </p>

    @endif

    <a
        href="{{ $googleMapsUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="google-map-button"
    >
        Google Mapsで見る
    </a>

</div>
                <div class="rating-divider"></div>

                <div class="rating-box">

                    <h3>学生支援.com評価</h3>

                    @if ($reviewCount > 0)

                        <div class="rating-score-row">
                            <strong class="rating-number">
                                {{ number_format($studentRating, 1) }}
                            </strong>

                            <span class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    {{ $i <= round($studentRating) ? '★' : '☆' }}
                                @endfor
                            </span>
                        </div>

                        <p class="rating-count">
                            {{ number_format($reviewCount) }}件の口コミ
                        </p>

                    @else

                        <p class="rating-message">
                            まだ評価はありません。
                        </p>

                    @endif

                </div>

            </section>

        </div>

        <div class="review-grid">

            <section class="review-card review-list-card">

                <div class="section-header">
                    <h2>学生の口コミ</h2>

                    <span class="review-count">
                        {{ $reviewCount }}件
                    </span>
                </div>

                <div class="review-list">

                    @forelse ($shop->reviews as $review)

    @php
        $editableReviews = session('editable_reviews', []);

        $canEditReview =
            isset($editableReviews[$review->id]) &&
            !empty($review->edit_token) &&
            hash_equals(
                $review->edit_token,
                $editableReviews[$review->id]
            );
    @endphp

    <article class="review">

        <div class="review-header">

            <div>
                <span class="anonymous-name">
                    匿名の学生
                </span>

                <strong class="review-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        {{ $i <= $review->rating ? '★' : '☆' }}
                    @endfor
                </strong>
            </div>

            <div class="review-header-right">

                <time
                    datetime="{{ $review->created_at->toDateString() }}"
                    class="review-date"
                >
                    {{ $review->created_at->format('Y年n月j日') }}
                </time>

                @if ($canEditReview)
                    <div class="review-menu">

                        <button
                            type="button"
                            class="review-menu-button"
                            aria-label="口コミの操作メニュー"
                            onclick="toggleReviewMenu({{ $review->id }})"
                        >
                            ・・・
                        </button>

                        <div
                            id="review-menu-{{ $review->id }}"
                            class="review-menu-list"
                        >
                            <a href="{{ route('reviews.edit', $review->id) }}">
                                編集
                            </a>

                            <form
                                action="{{ route('reviews.destroy', $review->id) }}"
                                method="POST"
                                data-confirm="この口コミを削除しますか？"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">
                                    削除
                                </button>
                            </form>
                        </div>

                    </div>
                @endif

            </div>

        </div>

        <p class="review-comment">
            {{ $review->comment }}
        </p>

    </article>

                    @empty

                        <div class="no-review">
                            <p>まだ口コミはありません。</p>
                            <p>最初の口コミを投稿してみましょう。</p>
                        </div>

                    @endforelse

                </div>

            </section>

            <section class="review-card review-form-card">

                <h2>口コミを投稿</h2>

                <p class="form-description">
                    口コミは匿名で公開されます。
                </p>

                <form
                    action="{{ route('reviews.store', $shop->id) }}"
                    method="POST"
                    class="review-form"
                >
                    @csrf

                    <div class="form-group">
                        <label for="rating">
                            評価
                            <span class="required">必須</span>
                        </label>

                        <select id="rating" name="rating" required>
                            <option value="">選択してください</option>

                            <option value="5" @selected(old('rating') == 5)>
                                ★★★★★ 5
                            </option>

                            <option value="4" @selected(old('rating') == 4)>
                                ★★★★☆ 4
                            </option>

                            <option value="3" @selected(old('rating') == 3)>
                                ★★★☆☆ 3
                            </option>

                            <option value="2" @selected(old('rating') == 2)>
                                ★★☆☆☆ 2
                            </option>

                            <option value="1" @selected(old('rating') == 1)>
                                ★☆☆☆☆ 1
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comment">
                            口コミ内容
                            <span class="required">必須</span>
                        </label>

                        <textarea
                            id="comment"
                            name="comment"
                            rows="7"
                            maxlength="1000"
                            placeholder="店舗の雰囲気、料理、価格、混雑状況などを入力してください"
                            required
                        >{{ old('comment') }}</textarea>
                    </div>

                    <p class="review-note">
                        個人を特定する情報や、誹謗中傷にあたる内容は投稿しないでください。
                    </p>

                    <button type="submit" class="review-btn">
                        匿名で投稿する
                    </button>

                </form>

            </section>

        </div>

    </main>

        </div>

    </main>

</div>

<script>
    function toggleReviewMenu(reviewId) {
        const targetMenu = document.getElementById(
            `review-menu-${reviewId}`
        );

        document
            .querySelectorAll('.review-menu-list')
            .forEach(menu => {
                if (menu !== targetMenu) {
                    menu.classList.remove('is-open');
                }
            });

        targetMenu.classList.toggle('is-open');
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.review-menu')) {
            document
                .querySelectorAll('.review-menu-list')
                .forEach(menu => {
                    menu.classList.remove('is-open');
                });
        }
    });
</script>

</body>
</html>

</body>

</html>