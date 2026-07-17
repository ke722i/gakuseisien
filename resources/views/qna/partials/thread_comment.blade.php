@php
$hasReplies = $answer->replies && $answer->replies->count() > 0;
$isTeacher = Auth::check() && Auth::user()->isTeacher();
$canDelete = Auth::check() && (Auth::id() == $answer->user_id || Auth::user()->isTeacher());

// 明確な判定
$isApproved = (bool)$answer->is_approved; // booleanに強制変換
$isBest = ($post->best_answer_id == $answer->id);
$isSpecial = $isBest || $isApproved;
@endphp

<div class="qna-thread-wrapper {{ $isRoot ? 'root-thread' : 'child-thread' }}">
    <div class="qna-thread-item {{ $isSpecial ? 'is-special-answer' : '' }}" id="answer-{{ $answer->id }}">

        <!-- 💡 ラベルを個別に表示（重要） -->
        @if($isSpecial)
        <div class="qna-special-labels" style="margin-bottom: 10px;">
            @if($isBest)
            <span class="special-answer-label label-best">🌟 ベストアンサー</span>
            @endif
            @if($isApproved)
            <span class="special-answer-label label-approved">✅ 教職員承認済み</span>
            @endif
        </div>
        @endif

        <!-- ヘッダー -->
        <div class="qna-thread-header">
            <div class="qna-thread-meta">
                <button type="button" class="qna-minimize-btn" onclick="toggleMinimizeThread(event, this)" title="スレッドを折りたたむ">[-]</button>
                <span class="qna-user-avatar">🟢</span>
                <span class="qna-comment-author">
                    @if($answer->user)
                    @if($answer->user->isTeacher()) 👨‍🏫 教職員 @else 🎓 在学生 @endif
                    ({{ $answer->user->login_id }})
                    @else
                    👥 ゲスト
                    @endif
                </span>
                <span class="qna-post-time">{{ $answer->created_at->format('m/d H:i') }}</span>
            </div>

            <div class="qna-thread-header-actions">
                @if($isRoot && Auth::id() == $post->user_id && !$post->best_answer_id)
                <form action="{{ route('qna.bestAnswer', ['id' => $post->id, 'answer_id' => $answer->id]) }}" method="POST" style="margin: 0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="qna-action-btn" title="ベストアンサーに選ぶ">📌 ベストアンサーに選ぶ</button>
                </form>
                @endif

                @if($canDelete)
                <button type="button" class="qna-delete-btn"
                    data-delete-url="{{ route('qna.destroyAnswer', $answer->id) }}"
                    onclick="event.preventDefault(); document.getElementById('global-delete-form').action = this.dataset.deleteUrl; document.getElementById('deleteModal').classList.add('is-open');"
                    title="削除">🗑️</button>
                @endif
            </div>
        </div>

        <!-- コメント本体 -->
        <div class="qna-thread-content-body">
            <div class="qna-comment-text">
                {!! nl2br(e($answer->content)) !!}
            </div>

            <div class="qna-thread-footer-actions">
                <button type="button" class="qna-action-btn upvote-btn {{ Auth::check() && $answer->isUpvotedBy(Auth::user()) ? 'active' : '' }}"
                    data-url="{{ route('qna.answers.upvote', $answer->id) }}" onclick="handleUpvote(event, this)">
                    ▲ <span class="upvote-count">{{ $answer->upvote_count }}</span>
                </button>

                @if(!$post->best_answer_id)
                <button type="button" class="qna-action-btn" onclick="toggleReplyForm(event, '{{ $answer->id }}')">
                    💬 リプライ ({{ $answer->replies ? $answer->replies->count() : 0 }})
                </button>
                @else
                <span class="qna-action-btn" style="opacity:0.6; cursor:default;">🔒 解決済</span>
                @endif

                @if(Auth::id() !== $answer->user_id)
                <button type="button" class="qna-action-btn" onclick="handleReport(event, '{{ $answer->id }}')">🏳️ 通報</button>
                @endif

                @if($isTeacher && !$answer->is_approved && !$post->best_answer_id)
                <form action="{{ route('qna.answers.approve', $answer->id) }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="qna-action-btn approve-btn" style="background-color: #2196f3; color: white;">✅ 承認する</button>
                </form>
                @endif
            </div>

            @if(!$post->best_answer_id)
            <div id="reply-form-wrapper-{{ $answer->id }}" class="qna-sub-reply-form" style="display: none;">
                <form action="{{ route('qna.storeAnswer', $post->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $answer->id }}">
                    <textarea name="comment" class="qna-form-textarea" placeholder="リプライを入力..." required></textarea>
                    <div style="margin-top:8px; text-align:right;">
                        <button type="button" class="qna-cancel-btn" onclick="document.getElementById('reply-form-wrapper-{{ $answer->id }}').style.display='none'">キャンセル</button>
                        <button type="submit" class="qna-btn-submit">返信</button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>

    @if($hasReplies)
    <div class="qna-replies-list">
        @foreach($answer->replies as $reply)
        @include('qna.partials.thread_comment', ['answer' => $reply, 'isRoot' => false])
        @endforeach
    </div>
    @endif
</div>