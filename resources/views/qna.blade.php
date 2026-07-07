<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内Q&A - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/qna.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'qna'])

        <main class="content qna-page">

            <div class="qna-header-container">
                <h1 class="qna-title">学内Q&A</h1>

                <div class="qna-search-box">
                    <input type="text" placeholder="キーワードで質問を検索..." class="qna-search-input">
                </div>

                <button class="qna-profile-btn"></button>
            </div>

            <div class="qna-card-list">

                <article class="qna-custom-card">
                    <button class="qna-delete-icon">🗑️</button>

                    <div class="qna-card-body">
                        <div class="qna-card-text">
                            <h2 class="qna-card-title">履修登録の変更期間はいつまでですか？</h2>
                            <p class="qna-card-desc">詳細（一部）。投稿をクリックしたら「さらに表示」ができる...</p>
                        </div>
                        <div class="qna-thumb-box">URLのサムネ</div>
                    </div>

                    <div class="qna-card-footer">
                        <button class="qna-icon-btn">💬 99</button>
                        <button class="qna-icon-btn">↪️</button>
                        <button class="qna-icon-btn">🏳️</button>
                        <button class="qna-icon-btn">🔄</button>
                    </div>
                </article>

                <article class="qna-custom-card">
                    <button class="qna-delete-icon">🗑️</button>

                    <div class="qna-card-body">
                        <div class="qna-card-text">
                            <h2 class="qna-card-title">学食の発券機は新紙幣に対応していますか？</h2>
                            <p class="qna-card-desc">詳細（一部）。投稿をクリックしたら「さらに表示」ができる...</p>
                        </div>
                    </div>

                    <div class="qna-card-footer">
                        <button class="qna-icon-btn">💬 99</button>
                        <button class="qna-icon-btn">↪️</button>
                        <button class="qna-icon-btn">🏳️</button>
                        <button class="qna-icon-btn">🔄</button>
                    </div>
                </article>

                <article class="qna-custom-card">
                    <button class="qna-delete-icon">🗑️</button>

                    <div class="qna-card-body">
                        <div class="qna-card-text">
                            <h2 class="qna-card-title">3号館の入り口で見落とし物（鍵）がありました</h2>
                            <p class="qna-card-desc">詳細（一部）。投稿をクリックしたら「さらに表示」ができる。ここに詳しい説明文がすべて表示されます。</p>
                        </div>
                    </div>

                    <div class="qna-image-large-box">
                        画像
                    </div>

                    <div class="qna-card-footer">
                        <button class="qna-icon-btn">💬 99</button>
                        <button class="qna-icon-btn">↪️</button>
                        <button class="qna-icon-btn">🏳️</button>
                        <button class="qna-icon-btn">🔄</button>
                    </div>
                </article>

            </div>
        </main>

    </div>
</body>

</html>