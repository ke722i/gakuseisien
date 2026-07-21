<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>店舗編集</title>

    @vite([
        'resources/css/app.css',
        'resources/css/store/request.css'
    ])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main-content">

        <a href="{{ route('store.admin') }}" class="back-btn">
            ← 管理画面へ戻る
        </a>

        <h1>店舗編集</h1>

        <p class="page-description">
            登録されている店舗情報を編集します。
        </p>

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

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-card">

            <form action="{{ route('store.update', $shop->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">店舗名</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $shop->name) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="genre">ジャンル</label>

                    <select id="genre" name="genre" required>
                        @php
                            $genres = [
                                'ラーメン',
                                'カフェ',
                                '定食',
                                '居酒屋',
                                '中華',
                                '寿司',
                                'コンビニ',
                                'スイーツ',
                                'レストラン',
                                'その他'
                            ];
                        @endphp

                        @foreach ($genres as $genre)
                            <option
                                value="{{ $genre }}"
                                {{ old('genre', $shop->genre) === $genre ? 'selected' : '' }}
                            >
                                {{ $genre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="address">住所</label>

                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address', $shop->address) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="business_hours">営業時間</label>

                    <input
                        type="text"
                        id="business_hours"
                        name="business_hours"
                        value="{{ old('business_hours', $shop->business_hours) }}"
                        placeholder="例：11:00～22:00"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="budget">平均価格</label>

                    <input
                        type="number"
                        id="budget"
                        name="budget"
                        value="{{ old('budget', $shop->budget) }}"
                        min="0"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="distance">学校からの距離（m）</label>

                    <input
                        type="number"
                        id="distance"
                        name="distance"
                        value="{{ old('distance', $shop->distance) }}"
                        min="0"
                        required
                    >
                </div>

                <div class="form-group">
    <label>決済方法</label>

    @php
        $selectedPayments = old(
            'payment_method',
            is_array($shop->payment_method)
                ? $shop->payment_method
                : []
        );
    @endphp

    <div class="payment-checkbox-group">
        @foreach ([
            '現金',
            'クレジットカード',
            '交通系IC',
            'QRコード決済',
            '電子マネー'
        ] as $payment)
            <label class="payment-checkbox">
                <input
                    type="checkbox"
                    name="payment_method[]"
                    value="{{ $payment }}"
                    @checked(in_array($payment, $selectedPayments))
                >

                <span>{{ $payment }}</span>
            </label>
        @endforeach
    </div>

    @error('payment_method')
        <p class="error-message">{{ $message }}</p>
    @enderror

    @error('payment_method.*')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>
                <div class="form-group optional">
                    <label for="official_url">公式URL</label>

                    <input
                        type="url"
                        id="official_url"
                        name="official_url"
                        value="{{ old('official_url', $shop->official_url) }}"
                        placeholder="https://example.com"
                    >
                </div>

                <div class="button-area">

                    <a href="{{ route('store.admin') }}" class="reset-btn">
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