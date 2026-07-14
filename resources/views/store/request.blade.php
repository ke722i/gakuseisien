<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗申請</title>
    @vite(['resources/css/store/request.css'])
</head>

<body>

<div class="container">

    @include('partials.sidebar', ['active' => 'nearby-shop'])

    <!-- メイン -->
    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">← 一覧へ戻る</a>

        <h1>店舗申請</h1>
        <h2 style="color:red;">テスト表示</h2>

        <div class="form-card">

            <form action="{{ route('store.request.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>店舗名</label>
        <input type="text" name="name" placeholder="店舗名を入力">
    </div>

    <div class="form-group">
        <label>ジャンル</label>
        <select name="genre">
            <option value="">選択してください</option>
            <option>ラーメン</option>
            <option>カフェ</option>
            <option>定食</option>
            <option>居酒屋</option>
            <option>その他</option>
        </select>
    </div>

    <div class="form-group">
        <label>住所</label>
        <input type="text" name="address" placeholder="住所を入力">
    </div>

    <div class="form-group">
        <label>営業時間</label>
        <input type="text" name="business_hours" placeholder="11:00～22:00">
    </div>

    <div class="form-group">
        <label>平均価格</label>
        <input type="number" name="budget" placeholder="800">
    </div>

    <div class="form-group">
        <label>学校からの距離(m)</label>
        <input type="number" name="distance" placeholder="300">
    </div>

    <div class="form-group">
        <label>決済方法</label>
        <select name="payment_method">
            <option>現金</option>
            <option>クレジット</option>
            <option>PayPay</option>
            <option>電子マネー</option>
        </select>
    </div>

    <div class="form-group">
        <label>申請者</label>
        <input type="text" name="applicant" placeholder="学生名">
    </div>

    <div class="button-area">
        <button type="reset" class="reset-btn">リセット</button>
        <button type="submit" class="submit-btn">申請する</button>
    </div>
        
</form>
        </div>

    </main>

</div>

</body>

</html>
