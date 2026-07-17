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

    <!-- メイン -->
    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">← 一覧へ戻る</a>

        <h1>店舗申請</h1>

        {{-- 申請完了メッセージ --}}
        @if (session('success'))
            <p style="color:#1f8a4c;font-weight:bold;margin-bottom:20px;">{{ session('success') }}</p>
        @endif

        {{-- バリデーションエラー --}}
        @if ($errors->any())
            <ul style="color:#c0392b;margin-bottom:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="form-card">

            <form action="{{ route('store.request.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>店舗名</label>
        <input type="text" name="name" placeholder="店舗名を入力" value="{{ old('name') }}">
    </div>

    <div class="form-group">
        <label>ジャンル</label>
        <select name="genre">
            <option value="">選択してください</option>
            @foreach (['ラーメン', 'カフェ', '定食', '居酒屋', '中華', '寿司', 'コンビニ', 'スイーツ', 'レストラン', 'その他'] as $genre)
                <option value="{{ $genre }}" {{ old('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>住所</label>
        <input type="text" name="address" placeholder="住所を入力" value="{{ old('address') }}">
    </div>

    <div class="form-group">
        <label>営業時間</label>
        <input type="text" name="business_hours" placeholder="11:00～22:00" value="{{ old('business_hours') }}">
    </div>

    <div class="form-group">
        <label>平均価格</label>
        <input type="number" name="budget" placeholder="800" value="{{ old('budget') }}">
    </div>

    <div class="form-group">
        <label>学校からの距離(m)</label>
        <input type="number" name="distance" placeholder="300" value="{{ old('distance') }}">
    </div>

    <div class="form-group">
        <label>決済方法</label>
            @foreach (['現金', 'クレジット', 'paypay', '電子マネー', 'その他'] as $method)
                <input type="checkbox" name="payment_method[]" value="{{ $method }}"
                    {{ in_array($method, old('payment_method', []), true) ? 'checked' : '' }}>　{{ $method }}
            @endforeach
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
