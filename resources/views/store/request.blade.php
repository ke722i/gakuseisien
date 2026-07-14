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

    <!-- サイドバー -->
    <aside class="sidebar">

        <h2>MENU</h2>

        <ul>
            <li><a href="{{ url('/') }}">ホーム</a></li>
            <li><a href="{{ route('nearby.shop') }}">近辺店舗情報マップ</a></li>
            <li><a href="{{ route('store.request') }}">店舗申請</a></li>
            <li><a href="#">承認画面</a></li>
            <li><a href="#">ログアウト</a></li>
        </ul>

    </aside>

    <!-- メイン -->
    <main class="main-content">

        <a href="{{ route('nearby.shop') }}" class="back-btn">← 一覧へ戻る</a>

        <h1>店舗申請</h1>

        <div class="form-card">

            <form>

                <div class="form-group">
                    <label>店舗名</label>
                    <input type="text" placeholder="店舗名を入力">
                </div>

                <div class="form-group">
                    <label>ジャンル</label>
                    <select>
                        <option>選択してください</option>
                        <option>ラーメン</option>
                        <option>カフェ</option>
                        <option>定食</option>
                        <option>居酒屋</option>
                        <option>その他</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>住所</label>
                    <input type="text" placeholder="住所を入力">
                </div>

                <div class="form-group">
                    <label>営業時間</label>
                    <input type="text" placeholder="例：11:00～22:00">
                </div>

                <div class="form-group">
                    <label>定休日</label>
                    <input type="text" placeholder="例：水曜日">
                </div>

                <div class="form-group">
                    <label>平均価格</label>
                    <input type="text" placeholder="例：800円">
                </div>

                <div class="form-group">
                    <label>決済方法</label>

                    <div class="checkbox-group">
                        <label><input type="checkbox"> 現金</label>
                        <label><input type="checkbox"> クレジット</label>
                        <label><input type="checkbox"> PayPay</label>
                        <label><input type="checkbox"> 電子マネー</label>
                    </div>

                </div>

                <div class="form-group">
                    <label>公式サイト</label>
                    <input type="url" placeholder="https://">
                </div>

                <div class="form-group">
                    <label>店舗紹介</label>
                    <textarea rows="5" placeholder="店舗の特徴など"></textarea>
                </div>

                <div class="button-area">

                    <button type="reset" class="reset-btn">
                        リセット
                    </button>

                    <button type="submit" class="submit-btn">
                        申請する
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

</body>

</html>
