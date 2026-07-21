<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗申請</title>
    @vite(['resources/css/app.css', 'resources/css/store/request.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'shop'])

    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">
            ← 一覧へ戻る
        </a>

        <h1>店舗申請</h1>

        <div class="form-card">

            {{-- 完了メッセージは共通ポップアップ（partials/toast）で表示する --}}

            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('store.request.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">店舗名</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="店舗名を入力"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="genre">ジャンル</label>

                    <select id="genre" name="genre" required>
                        <option value="">選択してください</option>
                        <option value="ラーメン" @selected(old('genre') === 'ラーメン')>
                            ラーメン
                        </option>
                        <option value="カフェ" @selected(old('genre') === 'カフェ')>
                            カフェ
                        </option>
                        <option value="定食" @selected(old('genre') === '定食')>
                            定食
                        </option>
                        <option value="居酒屋" @selected(old('genre') === '居酒屋')>
                            居酒屋
                        </option>
                        <option value="中華" @selected(old('genre') === '中華')>
                            中華
                        </option>
                        <option value="寿司" @selected(old('genre') === '寿司')>
                            寿司
                        </option>
                        <option value="コンビニ" @selected(old('genre') === 'コンビニ')>
                            コンビニ
                        </option>
                        <option value="スイーツ" @selected(old('genre') === 'スイーツ')>
                            スイーツ
                        </option>
                        <option value="レストラン" @selected(old('genre') === 'レストラン')>
                            レストラン
                        </option>
                        <option value="その他" @selected(old('genre') === 'その他')>
                            その他
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="address">住所</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="住所を入力"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="business_hours">営業時間</label>
                    <input
                        type="text"
                        id="business_hours"
                        name="business_hours"
                        value="{{ old('business_hours') }}"
                        placeholder="11:00～22:00"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="budget">平均価格</label>
                    <input
                        type="number"
                        id="budget"
                        name="budget"
                        value="{{ old('budget') }}"
                        placeholder="800"
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
                        value="{{ old('distance') }}"
                        placeholder="300"
                        min="0"
                        required
                    >
                </div>

                <div class="form-group">
    <label>決済方法</label>

    @php
        $selectedPayments = old('payment_method', []);
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
</div>

    @error('payment_method')
        <p class="error-message">{{ $message }}</p>
    @enderror
</div>

                <div class="form-group optional">
    <label for="official_url">公式URL</label>
    <input
        type="url"
        id="official_url"
        name="official_url"
        value="{{ old('official_url') }}"
        placeholder="https://example.com"
    >
</div>

                <div class="button-area">
                    <button type="reset" class="reset-btn">クリア</button>
                    <button type="submit" class="submit-btn">申請する</button>
                </div>

            </form>

        </div>

    </main>

</div>

</body>

</html>