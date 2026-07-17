<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>投稿履歴 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/qna/history.css', 'resources/js/app.js', 'resources/js/qna.js'])
</head>

<body>
    @include('partials.sidebar', ['active' => 'qna'])

    <main class="content qna-page">

        <div class="qna-header-container">
            <h1 class="qna-title">投稿履歴</h1>

            <div class="qna-header-actions">
                <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">一覧に戻る</a>
            </div>
        </div>

        <div class="qna-card-list">

            @forelse ($posts as $post)
            <div class="qna-custom-card" style="margin-bottom: 15px; @if(!empty($post->best_answer_id)) background-color: #fff9db; border-color: #ffe066; @endif">
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

                <!-- フッター部分 -->
                <div class="qna-card-footer" style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('qna.detail', $post->id) }}#comment-section" style="text-decoration: none;">
                        <button type="button" class="qna-icon-btn" title="comments" style="cursor: pointer; font-size: 13px;">
                            💬 コメント {{ $post->answers->count() }}件
                        </button>
                    </a>

                    <!-- 各種アクションボタン（通報ボタンを削除しました） -->
                    <button type="button" class="qna-history-btn" style="font-size: 12px; padding: 4px 8px; background-color: #6c757d; color: #fff; border: none; border-radius: 4px;" data-url="{{ route('qna.detail', $post->id) }}" onclick="handleShare(event, this)">
                        共有
                    </button>

                    @if(empty($post->best_answer_id))
                    <a href="{{ route('qna.detail', $post->id) }}?action=select_best" class="qna-history-btn" style="font-size: 12px; padding: 4px 8px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px; display: inline-block;">
                        解決する
                    </a>
                    @else
                    <button class="qna-history-btn" style="font-size: 12px; padding: 4px 8px; background-color: #6c757d; color: #fff; opacity: 0.5; cursor: not-allowed; border: none; border-radius: 4px;" disabled>
                        解決済
                    </button>
                    @endif

                    <!-- 右側のボタンエリアと「詳細を見る」 -->
                    <div class="qna-footer-right" style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
                        @auth
                        @if((string)Auth::id() === (string)$post->user_id || Auth::user()->isTeacher())
                        <button type="button" class="qna-delete-top-right" style="background: none; border: none; cursor: pointer; font-size: 16px; position: relative; z-index: 9999;"
                            onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('global-delete-form').action = '/gakunai-qna/{{ $post->id }}'; document.getElementById('deleteModal').classList.add('is-open');">
                            🗑️
                        </button>
                        @endif
                        @endauth

                        <a href="{{ route('qna.detail', $post->id) }}" class="qna-history-btn qna-btn-black" style="text-decoration: none; font-size: 12px; padding: 5px 12px; font-weight: bold;">
                            詳細を見る
                        </a>
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

    <!-- 削除確認モーダル -->
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

    <script>
        // 共有機能
        function handleShare(event, element) {
            event.preventDefault();
            event.stopPropagation();

            const url = element.dataset.url;

            navigator.clipboard.writeText(url).then(() => {
                alert('質問のURLをクリップボードにコピーしました！');
            }).catch(err => {
                alert('URLのコピーに失敗しました。');
            });
        }
    </script>
</body>

</html>