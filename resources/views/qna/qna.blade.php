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
    @include('partials.sidebar', ['active' => 'qna'])

    <main class="content qna-page">

        <div class="qna-header-container">
            <h1 class="qna-title">学内Q&A</h1>

            <!-- 並べ替えリンク -->
            <div class="qna-sort-menu" style="margin: 15px 0; display: flex; gap: 10px;">
                <a href="{{ route('gakunai.qna', ['sort' => 'new', 'keyword' => $keyword ?? '']) }}"
                    class="qna-history-btn {{ ($sort ?? 'new') == 'new' ? 'qna-btn-black' : '' }}">最近順</a>
                <a href="{{ route('gakunai.qna', ['sort' => 'resolved', 'keyword' => $keyword ?? '']) }}"
                    class="qna-history-btn {{ ($sort ?? '') == 'resolved' ? 'qna-btn-black' : '' }}">解決済み順</a>
                <a href="{{ route('gakunai.qna', ['sort' => 'unresolved', 'keyword' => $keyword ?? '']) }}"
                    class="qna-history-btn {{ ($sort ?? '') == 'unresolved' ? 'qna-btn-black' : '' }}">未解決順</a>
            </div>

            <!-- 💡 検索機能をフォームに変更 -->
            <form action="{{ route('gakunai.qna') }}" method="GET" class="qna-search-box">
                <input type="hidden" name="sort" value="{{ $sort ?? 'new' }}">
                <input type="text" name="keyword" class="qna-search-input"
                    placeholder="キーワードで質問を検索..." value="{{ $keyword ?? '' }}">
                <button type="submit" style="display:none;"></button>
            </form>

            <div class="qna-header-actions">
                <a href="{{ route('qna.create') }}" class="qna-history-btn qna-btn-black">質問を投稿</a>
                <a href="{{ route('qna.history') }}" class="qna-history-btn">投稿履歴</a>
            </div>
        </div>

        <div class="qna-card-list">

            @forelse ($posts as $post)
            <article class="qna-custom-card">

                @auth
                @if(Auth::id() == $post->user_id || Auth::user()->isTeacher())
                <button type="button" class="qna-delete-top-right" style="background: none; border: none; cursor: pointer; font-size: 16px; position: relative; z-index: 9999;"
                    onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('global-delete-form').action = '/gakunai-qna/{{ $post->id }}'; document.getElementById('deleteModal').classList.add('is-open');">
                    🗑️
                </button>
                @endif
                @endauth

                <div class="qna-card-body">
                    <div class="qna-card-text">
                        <div style="margin-bottom: 8px; font-size: 13px; color: #666666; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span class="qna-badge" style="background-color: #111111; color: #ffffff; padding: 2px 8px; border-radius: 4px;">
                                {{ $post->category }}
                            </span>

                            @if($post->best_answer_id)
                            <span class="qna-badge qna-badge-resolved" style="background-color: #e0a800; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: bold;">解決済</span>
                            @else
                            <span class="qna-badge qna-badge-unresolved" style="background-color: #007bff; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: bold;">未解決</span>
                            @endif

                            <span>{{ $post->created_at->format('Y/m/d H:i') }}</span>
                        </div>

                        <h2 class="qna-card-title">{{ $post->title }}</h2>
                        <p class="qna-card-desc">{{ Str::limit($post->content, 100, '...') }}</p>

                        @if($post->image)
                        <div class="qna-card-image-container" style="margin-top: 12px; text-align: left;">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像"
                                class="qna-card-image"
                                style="max-width: 100%; max-height: 180px; border-radius: 6px; object-fit: contain; cursor: pointer; border: 1px solid #eee;"
                                onclick="event.preventDefault(); event.stopPropagation(); openImagePopup(this.src)">
                        </div>
                        @endif

                    </div>
                </div>

                <div class="qna-card-footer" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; width: 100%;">
                    <a href="{{ route('qna.detail', $post->id) }}#comment-section" style="text-decoration: none;">
                        <button type="button" class="qna-icon-btn" title="comments" style="cursor: pointer;">
                            💬 コメント {{ $post->answers->count() }}件
                        </button>
                    </a>
                    <button type="button" class="qna-icon-btn" title="URLをコピー" data-url="{{ route('qna.detail', $post->id) }}" onclick="handleShare(event, this)">共有</button>
                    @if(Auth::id() !== $post->user_id)
                    <button type="button" class="qna-icon-btn" title="通報する" data-id="{{ $post->id }}" onclick="handleReport(event, this)">通報</button>
                    <form id="report-form-{{ $post->id }}" action="{{ route('qna.report', $post->id) }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="reason" id="report-reason-{{ $post->id }}">
                    </form>
                    @endif
                    <form id="report-form-{{ $post->id }}" action="{{ route('qna.report', $post->id) }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="reason" id="report-reason-{{ $post->id }}">
                    </form>

                    @if(!$post->best_answer_id)
                    @auth
                    @if(Auth::id() == $post->user_id)
                    <a href="{{ route('qna.detail', $post->id) }}?action=select_best" class="qna-icon-btn" title="解決にする" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        解決する
                    </a>
                    @endif
                    @endauth
                    @else
                    <button class="qna-icon-btn" title="解決済みです" style="opacity: 0.4; cursor: not-allowed;" disabled>
                        解決済
                    </button>
                    @endif

                    <a href="{{ route('qna.detail', $post->id) }}" class="qna-history-btn qna-btn-black" style="margin-left: auto; text-decoration: none; font-size: 13px; padding: 6px 14px; text-align: center; font-weight: bold;">
                        詳細を見る
                    </a>
                </div>
            </article>
            @empty
            @endforelse

        </div>
    </main>

    <!-- 画像拡大ポップアップモーダル -->
    <div id="imagePopupModal" class="qna-image-popup-overlay" onclick="closeImagePopup()">
        <div class="qna-image-popup-content" onclick="event.stopPropagation()">
            <span class="qna-image-popup-close" onclick="closeImagePopup()">&times;</span>
            <img id="popupTargetImage" src="" alt="拡大画像" class="qna-image-popup-img">
        </div>
    </div>

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
        function openImagePopup(src) {
            const modal = document.getElementById('imagePopupModal');
            const popupImg = document.getElementById('popupTargetImage');

            popupImg.src = src;
            modal.classList.add('is-active');
            document.addEventListener('keydown', handleEscClose);
        }

        function closeImagePopup() {
            const modal = document.getElementById('imagePopupModal');
            modal.classList.remove('is-active');
            document.removeEventListener('keydown', handleEscClose);
        }

        function handleEscClose(event) {
            if (event.key === 'Escape') {
                closeImagePopup();
            }
        }

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

        function handleReport(event, element) {
            event.preventDefault();
            event.stopPropagation();
            const id = element.dataset.id;
            const reason = prompt("通報する理由を入力してください（スパム、嫌がらせ、公序良俗に反する投稿など）：");
            if (reason === null) return;
            if (reason.trim() === "") {
                alert("通報理由は必須入力です。");
                return;
            }
            document.getElementById('report-reason-' + id).value = reason;
            document.getElementById('report-form-' + id).submit();
        }
    </script>
</body>

</html>