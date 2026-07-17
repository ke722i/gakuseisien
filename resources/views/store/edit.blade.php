<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗編集</title>
    @vite(['resources/css/store/request.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'nearby-shop'])

    <main class="main-content">

        <a href="{{ route('store.admin') }}" class="back-btn">← 管理画面へ戻る</a>

        <h1>店舗編集</h1>

        <div class="form-card">

            <form action="{{ route('store.update', $shop->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>店舗名</label>
                    <input type="text" name="name" value="{{ $shop->name }}">
                </div>

                <div class="form-group">
                    <label>ジャンル</label>

                    <select name="genre">
                        <option value="ラーメン" {{ $shop->genre == 'ラーメン' ? 'selected' : '' }}>ラーメン</option>
                        <option value="カフェ" {{ $shop->genre == 'カフェ' ? 'selected' : '' }}>カフェ</option>
                        <option value="定食" {{ $shop->genre == '定食' ? 'selected' : '' }}>定食</option>
                        <option value="居酒屋" {{ $shop->genre == '居酒屋' ? 'selected' : '' }}>居酒屋</option>
                        <option value="中華" {{ $shop->genre == '中華' ? 'selected' : '' }}>中華</option>
                        <option value="寿司" {{ $shop->genre == '寿司' ? 'selected' : '' }}>寿司</option>
                        <option value="コンビニ" {{ $shop->genre == 'コンビニ' ? 'selected' : '' }}>コンビニ</option>
                        <option value="スイーツ" {{ $shop->genre == 'スイーツ' ? 'selected' : '' }}>スイーツ</option>
                        <option value="レストラン" {{ $shop->genre == 'レストラン' ? 'selected' : '' }}>レストラン</option>
                        <option value="その他" {{ $shop->genre == 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>住所</label>
                    <input type="text" name="address" value="{{ $shop->address }}">
                </div>

                <div class="form-group">
                    <label>営業時間</label>
                    <input type="text" name="business_hours" value="{{ $shop->business_hours }}">
                </div>

                <div class="form-group">
                    <label>平均価格</label>
                    <input type="number" name="budget" value="{{ $shop->budget }}">
                </div>

                <div class="form-group">
                    <label>学校からの距離(m)</label>
                    <input type="number" name="distance" value="{{ $shop->distance }}">
                </div>

                <div class="form-group">
                    <label>決済方法</label>

                    <select name="payment_method">
                        <option value="現金" {{ $shop->payment_method == '現金' ? 'selected' : '' }}>現金</option>
                        <option value="クレジット" {{ $shop->payment_method == 'クレジット' ? 'selected' : '' }}>クレジット</option>
                        <option value="PayPay" {{ $shop->payment_method == 'PayPay' ? 'selected' : '' }}>PayPay</option>
                        <option value="電子マネー" {{ $shop->payment_method == '電子マネー' ? 'selected' : '' }}>電子マネー</option>
                        <option value="その他" {{ $shop->payment_method == 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>

                <div class="button-area">
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