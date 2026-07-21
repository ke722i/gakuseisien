<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>口コミ編集</title>

    @vite([
        'resources/css/app.css',
        'resources/css/store/request.css'
    ])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main-content">

        <a
            href="{{ route('store.more', $review->shop_id) }}"
            class="back-btn"
        >
            ← 店舗詳細へ戻る
        </a>

        <h1>口コミ編集</h1>

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

        <div class="form-card">

            <form
                action="{{ route('reviews.update', $review->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="rating">評価</label>

                    <select id="rating" name="rating" required>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option
                                value="{{ $rating }}"
                                @selected(
                                    old('rating', $review->rating) == $rating
                                )
                            >
                                {{ str_repeat('★', $rating) }}
                                {{ str_repeat('☆', 5 - $rating) }}
                                {{ $rating }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">口コミ内容</label>

                    <textarea
                        id="comment"
                        name="comment"
                        rows="7"
                        maxlength="1000"
                        required
                    >{{ old('comment', $review->comment) }}</textarea>
                </div>

                <div class="button-area">

                    <a
                        href="{{ route('store.more', $review->shop_id) }}"
                        class="reset-btn"
                    >
                        キャンセル
                    </a>

                    <button type="submit" class="submit-btn">
                        更新する
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>