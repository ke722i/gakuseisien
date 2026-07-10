<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }} - 学生支援.com</title>
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
                    @if($post->best_answer_id)
                    <span class="qna-badge qna-badge-resolved">解決済</span>
                    @else
                    <span class="qna-badge qna-badge-unresolved">未解決</span>
                    @endif
                    <span class="qna-post-time">{{ $post->created_at->format('Y/m/d H:i') }}</span>
                </div>

                <h2 class="qna-detail-title">{{ $post->title }}</h2>

                <div class="qna-detail-body">
                    <p>{!! nl2br(e($post->content)) !!}</p>
                </div>

                <div class="qna-detail-image-box">
                    <div class="qna-image-placeholder">【添付画像（サンプル表示枠）】</div>
                </div>
            </div>

            <div class="qna-comments-section">
                <h3 class="qna-comments-title">回答・コメント（{{ $post->answers->count() }}件）</h3>

                @forelse ($post->answers as $answer)
                <div class="qna-custom-card qna-comment-card">
                    <div class="qna-card-header">
                        <span class="qna-comment-author">在学生さん</span>
                        <span class="qna-post-time">{{ $answer->created_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div class="qna-comment-body">
                        <p>{!! nl2br(e($answer->content)) !!}</p>
                    </div>
                </div>
                @empty
                <div class="qna-custom-card qna-comment-card" style="text-align: center; color: #666666; padding: 20px;">
                    <p>まだコメントはありません。最初のコメントを書き込んでみましょう！</p>
                </div>
                @endforelse
            </div>

            <div class="qna-custom-card qna-comment-form-box">
                <h3 class="qna-comments-title" style="margin-top: 0;">コメントを書き込む</h3>

                <form action="{{ route('qna.storeAnswer', $post->id) }}" method="POST">
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