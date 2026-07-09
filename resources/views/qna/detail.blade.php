<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>質問の詳細 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/qna/detail.css', 'resources/js/app.js', 'resources/js/qna.js'])
</head>

<body>
    <main class="content qna-page">


        <div class="qna-header-container">
            <h1 class="qna-title">質問詳細</h1>
            <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">一覧に戻る</a>
        </div>

        <div class="app-layout">
            @include('partials.sidebar', ['active' => 'qna'])
        </div>

        <div class="qna-card-list">
            
            <div class="qna-custom-card qna-detail-main">
                <div class="qna-card-header">
                    <span class="qna-badge qna-badge-unresolved">未解決</span>
                    <span class="qna-post-time">2026/07/08 10:00</span>
                </div>
                
                <h2 class="qna-detail-title">学食の発券機は新紙幣に対応していますか？</h2>
                
                <div class="qna-detail-body">
                    <p>
                        お昼休みに学食を利用したいのですが、最近発行された新一万円札や新千円札は券売機でそのまま使えますでしょうか？<br>
                        もし使えない場合、事前に両替できる場所などが学内にあるか教えていただけると助かります。よろしくお願いいたします。
                    </p>
                </div>

                <div class="qna-detail-image-box">
                    <div class="qna-image-placeholder">【添付画像（サンプル表示枠）】</div>
                </div>
            </div>

            <div class="qna-comments-section">
                <h3 class="qna-comments-title">回答・コメント（2件）</h3>

                <div class="qna-custom-card qna-comment-card">
                    <div class="qna-card-header">
                        <span class="qna-comment-author">在学生 Aさん</span>
                        <span class="qna-post-time">2026/07/08 10:15</span>
                    </div>
                    <div class="qna-comment-body">
                        <p>昨日利用しましたが、新千円札は問題なく使えましたよ！五千円札と一万円札は試していないので分かりませんが、千円札なら大丈夫です。</p>
                    </div>
                </div>

                <div class="qna-custom-card qna-comment-card">
                    <div class="qna-card-header">
                        <span class="qna-comment-author">学生課 スタッフ</span>
                        <span class="qna-post-time">2026/07/08 11:00</span>
                    </div>
                    <div class="qna-comment-body">
                        <p>質問ありがとうございます。学食の券売機は、現在すべての金種（新紙幣）に対応しております。万が一読み込みエラー等が発生した場合は、隣の購買カウンターにて両替対応いたしますのでお声がけください。</p>
                    </div>
                </div>
            </div>

            <div class="qna-custom-card qna-comment-form-box">
                <h3 class="qna-comments-title" style="margin-top: 0;">コメントを書き込む</h3>
                <form action="#" method="POST">
                    @csrf
                    <div class="qna-form-group">
                        <textarea id="body" name="comment" class="qna-search-input qna-form-input-align qna-form-textarea" rows="3" placeholder="コメントや回答を入力してください..." required></textarea>
                    </div>
                    <div class="qna-form-actions" style="margin-top: 10px;">
                        <button type="submit" class="qna-history-btn qna-btn-submit">コメントを送信</button>
                    </div>
                </form>
            </div>

        </div>

    </main>
</body>

</html>