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

            @forelse ($posts as $post)
            <div class="qna-custom-card">
                <div class="qna-card-header">
                    @if (!empty($post->best_answer_id))
                        <span class="qna-badge qna-badge-resolved">解決済</span>
                    @else
                        <span class="qna-badge qna-badge-unresolved">未解決</span>
                    @endif
                    <span class="qna-post-time">{{ $post->created_at->format('Y/m/d H:i') }}</span>
                </div>
                
                <h2 class="qna-card-title">{{ $post->title }}</h2>
                
                <p class="qna-card-body">
                    {{ Str::limit($post->content, 100, '...') }}
                </p>
                
                <div class="qna-card-footer">
                    <span class="qna-comment-count">💬 コメント 0件</span>
                    
                    <div class="qna-footer-right">
                        <button type="button" class="qna-delete-top-right" style="background: none; border: none; cursor: pointer; font-size: 14px; position: relative; z-index: 9999; color: #cc3333;" 
                                onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('global-delete-form').action = '/gakunai-qna/{{ $post->id }}'; document.getElementById('deleteModal').classList.add('is-open');">
                            🗑️ 削除
                        </button>
                        <a href="{{ route('qna.detail', $post->id) }}" class="qna-read-more" style="margin-left: auto;">詳細を見る</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="qna-custom-card" style="text-align: center; padding: 40px; color: #666666;">
                <p style="font-size: 16px; font-weight: bold;">まだ質問を投稿していません。</p>
            </div>
            @endforelse

        </div>

    </main>

    <div id="deleteModal" class="qna-modal-overlay" onclick="if(event.target === this) { this.classList.remove('is-open'); }">
        <div class="qna-custom-card qna-modal-box">
            <h3 class="qna-modal-title">投稿の削除</h3>
            <p class="qna-modal-text">
                この質問を削除してもよろしいですか？<br>
                <span style="color: #cc3333; font-size: 13px;">※この操作は取り消せません。</span>
            </p>
            <div class="qna-modal-actions">
                <button type="button" id="modalCancelBtn" class="qna-history-btn qna-modal-btn-cancel" onclick="event.preventDefault(); document.getElementById('deleteModal').classList.remove('is-open');">キャンセル</button>
                
                <form id="global-delete-form" action="" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="qna-history-btn qna-btn-submit qna-modal-btn-delete">削除する</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>