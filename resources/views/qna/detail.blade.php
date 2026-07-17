<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }} - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/css/qna/qna.css', 'resources/css/qna/detail.css', 'resources/js/app.js', 'resources/js/qna.js'])
</head>

<body>
    @include('partials.sidebar', ['active' => 'qna'])

    <main class="content qna-page">
        <div class="qna-header-container">
            <h1 class="qna-title">質問詳細</h1>
            <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">一覧に戻る</a>
        </div>

        <div class="qna-card-list">
            <!-- 質問本文カード -->
            <div class="qna-custom-card qna-detail-main">
                <div class="qna-card-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="font-weight: bold; color: #333;">
                            {{ $post->user ? $post->user->login_id : 'ゲストユーザー' }}
                        </span>
                        <span class="qna-post-time" style="color: #666;">
                            {{ $post->created_at->format('Y/m/d H:i') }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" class="qna-icon-btn" title="URLをコピー" data-url="{{ url()->current() }}" onclick="handleShare(event, this)">共有</button>
                        @auth
                        @if(Auth::id() !== $post->user_id)
                        <button type="button" class="qna-icon-btn" title="通報する" data-id="{{ $post->id }}" onclick="handleReport(event, this)">通報</button>
                        @endif
                        @endauth
                    </div>
                </div>

                <!-- 💡 承認済み通知バナー -->
                @if($post->answers()->where('is_approved', true)->exists())
                <div style="background-color: #e3f2fd; color: #0277bd; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #b3e5fc; font-weight: bold; text-align: center;">
                    ✅ この質問は教職員により承認されました。
                </div>
                @endif

                <h2 class="qna-detail-title" style="margin-top: 15px;">{{ $post->title }}</h2>
                <div class="qna-detail-body" style="margin-top: 15px; line-height: 1.6;">
                    <p>{!! nl2br(e($post->content)) !!}</p>
                </div>

                @if($post->image)
                <div class="qna-detail-image-box">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像" onclick="openImagePopup(this.src)">
                </div>
                @endif
            </div>

            <!-- 回答スレッドエリア -->
            <div id="comment-section" class="qna-custom-card qna-combined-comment-box" style="margin-top: 20px; padding: 25px !important;">
                <h3 style="margin-bottom: 20px;">回答・コメント（{{ $post->answers()->count() }}件）</h3>

                <div class="qna-thread-container">
                    @forelse ($post->answers as $answer)
                    @include('qna.partials.thread_comment', ['answer' => $answer, 'isRoot' => true])
                    @empty
                    <div style="text-align: center; color: #666; padding: 20px;">まだ回答はありません。</div>
                    @endforelse
                </div>

                <!-- フォーム -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
                    @if($post->best_answer_id || $post->answers()->where('is_approved', true)->exists())
                    <div style="text-align:center; padding: 20px; background: #f8f9fa; border-radius: 6px;">
                        🔒 この質問は解決済のため、コメント受付を終了しました。
                    </div>
                    @else
                    <h3>回答する</h3>
                    <form action="{{ route('qna.storeAnswer', $post->id) }}" method="POST">
                        @csrf
                        <textarea name="comment" class="qna-form-textarea" placeholder="回答を入力してください..." required style="width:100%; height:80px;"></textarea>
                        <div style="text-align: right; margin-top: 10px;">
                            <button type="submit" class="qna-history-btn">送信する</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- 画像拡大ポップアップモーダル -->
    <div id="imagePopupModal" class="qna-image-popup-overlay" onclick="closeImagePopup()">
        <div class="qna-image-popup-content" onclick="event.stopPropagation()">
            <span class="qna-image-popup-close" onclick="closeImagePopup()">&times;</span>
            <img id="popupTargetImage" src="" alt="拡大画像" class="qna-image-popup-img">
        </div>
    </div>

    <script>
        // スレッドの折りたたみ制御（Reddit風Minimize）
        function toggleMinimizeThread(event, button) {
            event.stopPropagation();
            const threadItem = button.closest('.qna-thread-item');
            const threadWrapper = button.closest('.qna-thread-wrapper');

            if (threadWrapper.classList.contains('is-collapsed')) {
                threadWrapper.classList.remove('is-collapsed');
                button.innerText = '[-]';
                button.title = "スレッドを折りたたむ";
            } else {
                threadWrapper.classList.add('is-collapsed');
                button.innerText = '[+]';
                button.title = "スレッドを展開する";
            }
        }

        // リプライ入力欄のトグル表示
        function toggleReplyForm(event, answerId) {
            event.preventDefault();
            const form = document.getElementById(`reply-form-wrapper-${answerId}`);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                form.querySelector('textarea').focus();
            } else {
                form.style.display = 'none';
            }
        }

        // 教員承認マークのアクティブ切り替え（教員アカウントでのみ機能）
        function handleTeacherApprove(event, answerId) {
            event.preventDefault();
            const button = event.currentTarget;
            // 本来はLaravelのサーバーAPIにAjaxリクエストを送る箇所
            if (button.classList.contains('approved')) {
                button.classList.remove('approved');
                alert('回答の教員承認を解除しました。');
            } else {
                button.classList.add('approved');
                alert('回答を承認しました。！');
            }
        }

        // コメント・リプライの削除
        function handleDeleteComment(event, answerId) {
            event.preventDefault();
            if (confirm('このコメント（および配下のリプライ）を本当に削除しますか？')) {
                document.getElementById(`delete-form-${answerId}`).submit();
            }
        }

        // 画像ポップアップ制御
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

        // 共有
        function handleShare(event, element) {
            event.preventDefault();
            event.stopPropagation();
            const url = element.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                alert('URLをコピーしました！');
            }).catch(err => {
                alert('コピーに失敗しました。');
            });
        }

        // 通報
        function handleReport(event, element) {
            event.preventDefault();
            event.stopPropagation();
            const id = element.dataset.id;
            const reason = prompt("通報する理由を入力してください：");
            if (reason === null) return;
            if (reason.trim() === "") {
                alert("理由は必須入力です。");
                return;
            }
            document.getElementById('report-reason-' + id).value = reason;
            document.getElementById('report-form-' + id).submit();
        }

        // 投票（Upvote）ボタンの非同期通信処理（Ajax/Fetch）
        function handleUpvote(event, button) {
            event.preventDefault();

            const url = button.dataset.url;
            if (!url) {
                alert("投票するにはログインが必要です。");
                return;
            }

            // LaravelのCSRFトークンを取得
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                '{{ csrf_token() }}';

            // サーバーに非同期(POST)リクエストを投げる
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("投票するにはログインしてください。");
                        throw new Error("Unauthorized");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const countSpan = button.querySelector('.upvote-count');
                        countSpan.textContent = data.upvote_count;

                        if (data.upvoted) {
                            button.classList.add('active');
                        } else {
                            button.classList.remove('active');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>