<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>投稿履歴 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/qna/history.css', 'resources/js/app.js', 'resources/js/qna.js'])
</head>

<body>
    <main class="content qna-page">

        <div class="top-header">
            <button class="menu-button" id="menuButton">☰</button>
        </div>
        <div class="overlay" id="overlay"></div>

        <div class="qna-header-container">
            <h1 class="qna-title">投稿履歴</h1>

            <div class="qna-header-actions">
                <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">一覧に戻る</a>
            </div>
        </div>

        <div class="app-layout">
            @include('partials.sidebar', ['active' => 'qna'])
        </div>

        <div class="qna-card-list">

            <div class="qna-custom-card">
                <div class="qna-card-header">
                    <span class="qna-badge qna-badge-unresolved">未解決</span>
                    <span class="qna-post-time">2026/07/08 10:00</span>
                </div>
                <h2 class="qna-card-title">学食の発券機は新紙幣に対応していますか？</h2>
                <p class="qna-card-body">
                    お昼休みに学食を利用したいのですが、最近発行された新一万円札や新千円札は券売機でそのまま使えますでしょうか？...
                </p>
                <div class="qna-card-footer">
                    <span class="qna-comment-count">💬 コメント 2件</span>
                    <div class="qna-footer-right">
                        <button class="qna-delete-trigger-btn">🗑️ 削除</button>
                        <a href="{{ route('qna.detail') }}" class="qna-read-more">詳細を見る</a>
                    </div>
                </div>
            </div>

            <div class="qna-custom-card">
                <div class="qna-card-header">
                    <span class="qna-badge qna-badge-resolved">解決済</span>
                    <span class="qna-post-time">2026/04/15 14:20</span>
                </div>
                <h2 class="qna-card-title">新入生向けのMacBook相談会はどこで行われますか？</h2>
                <p class="qna-card-body">
                    大学推奨のMacの初期設定や、必須アプリのインストールについて聞きたいのですが、今週相談会などはありますか？...
                </p>
                <div class="qna-card-footer">
                    <span class="qna-comment-count">💬 コメント 5件</span>
                    <div class="qna-footer-right">
                        <button class="qna-delete-trigger-btn">🗑️ 削除</button>
                        <a href="{{ route('qna.detail') }}" class="qna-read-more">詳細を見る</a>
                    </div>
                </div>
            </div>

        </div>

    </main>
    <div id="deleteModal" class="qna-modal-overlay">
        <div class="qna-custom-card qna-modal-box">
            <h3 class="qna-modal-title">投稿の削除</h3>
            <p class="qna-modal-text">
                この質問を削除してもよろしいですか？<br>
                <span style="color: #cc3333; font-size: 13px;">※この操作は取り消せません。</span>
            </p>
            <div class="qna-modal-actions">
                <button id="modalCancelBtn" class="qna-history-btn qna-modal-btn-cancel">キャンセル</button>
                <form action="#" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="qna-history-btn qna-btn-submit qna-modal-btn-delete">削除する</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>