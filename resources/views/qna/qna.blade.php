<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学内Q&A - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite([
    'resources/css/app.css',
    'resources/css/qna/qna.css',
    'resources/js/app.js',
    'resources/js/qna.js'
    ])
</head>

<body>
    <main class="content qna-page">

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
            @include('partials.sidebar', ['active' => 'qna'])
        </div>

        <div class="qna-card-list">

            @forelse ($posts as $post)
            <article class="qna-custom-card">
                <button type="button" class="qna-delete-top-right" style="background: none; border: none; cursor: pointer; font-size: 16px; position: relative; z-index: 9999;"
                    onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('global-delete-form').action = '/gakunai-qna/{{ $post->id }}'; document.getElementById('deleteModal').classList.add('is-open');">
                    🗑️
                </button>

                <div class="qna-card-body">
                    <div class="qna-card-text">
                        <div style="margin-bottom: 8px; font-size: 13px; color: #666666;">
                            <span class="qna-badge" style="background-color: #111111; color: #ffffff; padding: 2px 8px; border-radius: 4px; margin-right: 8px;">
                                {{ $post->category }}
                            </span>
                            <span>{{ $post->created_at->format('Y/m/d H:i') }}</span>
                        </div>

                        <h2 class="qna-card-title">{{ $post->title }}</h2>

                        <p class="qna-card-desc">{{ Str::limit($post->content, 100, '...') }}</p>
                    </div>
                </div>

                <div class="qna-card-footer">
                    <button class="qna-icon-btn">💬 0</button>
                    <button class="qna-icon-btn">↪️</button>
                    <button class="qna-icon-btn">🏳️</button>
                    <button class="qna-icon-btn">🔄</button>

                    <a href="{{ route('qna.detail', $post->id) }}" class="qna-read-more" style="margin-left: auto;">詳細を見る</a>
                </div>
            </article>
            @empty
            <div class="qna-custom-card" style="text-align: center; padding: 40px; color: #666666;">
                <p style="font-size: 16px; font-weight: bold;">まだ質問が投稿されていません。</p>
                <p style="font-size: 14px; margin-top: 5px;">最初の質問を投稿してみよう！</p>
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