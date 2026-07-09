<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内Q&A - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(
    ['resources/css/app.css',
    'resources/css/qna/qna.css',
    'resources/js/app.js'])
</head>

<body>
    <main class="content qna-page">

        <div class="top-header">
            <button class="menu-button" id="menuButton">☰</button>
        </div>
        <div class="overlay" id="overlay"></div>

        <div class="qna-header-container">
            <h1 class="qna-title">学内Q&A</h1>

            <div class="qna-search-box">
                <input type="text" class="qna-search-input" placeholder="キーワードで質問を検索...">
            </div>

            <div class="qna-header-actions">
                <a href="{{ route('qna.create') }}" class="qna-history-btn qna-btn-black">質問を投稿</a>

                <a href="{{ route('qna.history') }}" class="qna-history-btn">投稿履歴</a>
            </div>
        </div>

        <div class="app-layout">
            <!-- 左サイドバー（共通部品） -->
            @include('partials.sidebar', ['active' => 'qna'])
        </div>

        <div class="qna-card-list">

            <article class="qna-custom-card">
                <button class="qna-delete-trigger-btn qna-delete-top-right">🗑️</button>

                <div class="qna-card-body">
                    <div class="qna-card-text">
                        <h2 class="qna-card-title">学食の発券機は新紙幣に対応していますか？</h2>
                        <p class="qna-card-desc">お昼休みに学食を利用したいのですが、最近発行された新一万円札や新千円札は券売機でそのまま使えますでしょうか？...</p>
                    </div>
                </div>

                <div class="qna-card-footer">
                    <button class="qna-icon-btn">💬 99</button>
                    <button class="qna-icon-btn">↪️</button>
                    <button class="qna-icon-btn">🏳️</button>
                    <button class="qna-icon-btn">🔄</button>

                    <a href="{{ route('qna.detail') }}" class="qna-read-more" style="margin-left: auto;">詳細を見る</a>
                </div>
            </article>

            <article class="qna-custom-card">
                <button class="qna-delete-trigger-btn qna-delete-top-right">🗑️</button>

                <div class="qna-card-body">
                    <div class="qna-card-text">
                        <h2 class="qna-card-title">3号館の入り口で見落とし物（鍵）がありました</h2>
                        <p class="qna-card-desc">3号館の自動ドアを入ってすぐの床に、黒いキーホルダーのついた鍵が落ちていました。心当たりのある方は学生課に届けてあるので確認してみてください。</p>
                    </div>
                </div>

                <div class="qna-image-large-box" style="margin: 0 20px 15px 20px; border: 2px dashed #111111; padding: 20px; text-align: center; background: #fafafa; border-radius: 4px; font-weight: bold;">
                    【添付画像（サンプル表示枠）】
                </div>

                <div class="qna-card-footer">
                    <button class="qna-icon-btn">💬 5</button>
                    <button class="qna-icon-btn">↪️</button>
                    <button class="qna-icon-btn">🏳️</button>
                    <button class="qna-icon-btn">🔄</button>

                    <a href="{{ route('qna.detail') }}" class="qna-read-more" style="margin-left: auto;">詳細を見る</a>
                </div>
            </article>

        </div>
    </main>
</body>

</html>