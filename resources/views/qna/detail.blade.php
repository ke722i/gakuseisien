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
            @if(request()->query('action') === 'select_best' && !$post->best_answer_id)
            <div class="qna-custom-card" style="background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; text-align: center;">
                💡 解決済みにするために、回答の中からふさわしいものを選んで「🌟 ベストアンサーに選ぶ」ボタンを押してください。
            </div>
            @endif

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

            <div id="comment-section" class="qna-comments-section" style="margin-top: 20px;">
                <h3 class="qna-comments-title">回答・コメント（{{ $post->answers->count() }}件）</h3>

                @forelse ($post->answers as $answer)
                <div class="qna-custom-card qna-comment-card" style="margin-bottom: 15px;">
                    <div class="qna-card-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div>
                            <span class="qna-comment-author">
                                @if($answer->user)
                                @if($answer->user->isTeacher())
                                👨‍🏫 教職員 ({{ $answer->user->login_id }})
                                @else
                                🎓 在学生 ({{ $answer->user->login_id }})
                                @endif
                                @else
                                👥 ゲストユーザー
                                @endif
                            </span>
                            <span class="qna-post-time">{{ $answer->created_at->format('Y/m/d H:i') }}</span>
                        </div>

                        <div style="margin-left: auto;">
                            @if(!$post->best_answer_id)
                            <form action="{{ route('qna.bestAnswer', ['id' => $post->id, 'answer_id' => $answer->id]) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="qna-history-btn" style="padding: 6px 12px; font-size: 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    🌟 ベストアンサーに選ぶ
                                </button>
                            </form>
                            @elseif($post->best_answer_id == $answer->id)
                            <span class="qna-badge qna-badge-resolved" style="background-color: #e0a800; color: #fff; padding: 4px 8px; border-radius: 4px; font-weight: bold;">🏆 ベストアンサー</span>
                            @endif
                        </div>
                    </div>
                    <div class="qna-comment-body" style="margin-top: 10px;">
                        <p>{!! nl2br(e($answer->content)) !!}</p>
                    </div>
                </div>
                @empty
                <div class="qna-custom-card qna-comment-card" style="text-align: center; color: #666666; padding: 20px;">
                    <p>まだコメントはありません。最初のコメントを書き込んでみましょう！</p>
                </div>
                @endforelse
            </div>

            @if(!$post->best_answer_id)
            <div class="qna-custom-card qna-comment-form-box" style="margin-top: 20px;">
                <h3 class="qna-comments-title" style="margin-top: 0;">コメントを書き込む</h3>
                <form action="{{ route('qna.storeAnswer', $post->id) }}" method="POST">
                    @csrf
                    <div class="qna-form-group">
                        <textarea id="body" name="comment" class="qna-search-input qna-form-input-align qna-form-textarea" rows="3" placeholder="コメントや回答を入力してください..." required></textarea>
                    </div>
                    <div class="qna-form-actions" style="margin-top: 10px; text-align: right;">
                        <button type="submit" class="qna-history-btn qna-btn-submit">コメントを送信</button>
                    </div>
                </form>
            </div>
            @else
            <div class="qna-custom-card qna-comment-form-box" style="margin-top: 20px; text-align: center; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; padding: 20px;">
                <p style="margin: 0; font-weight: bold;">🔒 この質問は解決済のため、コメントの受付を終了しました。</p>
            </div>
            @endif

        </div>

    </main>
</body>

</html>