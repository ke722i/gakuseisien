<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    @vite(['resources/css/app.css', 'resources/css/forum/forum-top.css'])
    <style>
        .post-detail-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
            margin-top: 1rem;
        }
        @media (max-width: 768px) {
            .post-detail-layout {
                grid-template-columns: 1fr;
            }
        }
        .post-detail-main {
            min-width: 0;
        }
        .post-detail-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .related-posts-section {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .related-posts-title {
            font-weight: bold;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            color: #333;
        }
        .related-post-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.9rem;
        }
        .related-post-item:last-child {
            border-bottom: none;
        }
        .related-post-link {
            color: #0066cc;
            text-decoration: none;
            display: block;
            line-height: 1.4;
        }
        .related-post-link:hover {
            text-decoration: underline;
        }
        .related-post-meta {
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.25rem;
        }

       .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            padding: 0.4rem 0.6rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            align-self: flex-start; 
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .btn-warning {
            background-color: #fffbeb;
            color: #d97706;
            border-color: #fcd34d;
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }
        .btn-warning:hover {
            background-color: #fef3c7;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border-color: #d1d5db;
            padding: 0.25rem 0.3rem;
            font-size: 0.8rem;
        }
        .btn-secondary:hover {
            background-color: #f3f4f6;
        }

        .btn-danger {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            padding: 0.25rem 0.3rem;
            font-size: 0.8rem;
        }
        .btn-danger:hover {
            background-color: #fecaca;
        }

        .btn-text {
            background: none;
            border: none;
            color: #4b5563;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: color 0.2s;
        }
        .btn-text:hover {
            color: #111827;
        }
        .btn-text-danger {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0;
            font-weight: 500;
            transition: color 0.2s;
        }
        .btn-text-danger:hover {
            color: #b91c1c;
        }

        .post-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        /* メインの返信カード */
        .reply-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .reply-card:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        /* 子コメントの返信カード（フラットなインデント用） */
        .child-reply-card {
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .child-reply-card:hover {
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>
<div class="app-layout">
    @include('partials.sidebar', ['active' => 'forum'])

    <main class="content">
        <div class="top-header">
            <a href="{{ route('forum.top') }}" class="back-button">←</a>
        </div>

        <div class="post-detail-layout">
            <div class="post-detail-main">
                <section class="post-card">
                    <div class="post-content">
                        <div class="post-author">
                            <span>{{ $post->posted_by ?? '投稿者不明' }}</span>
                        </div>
                        <div class="post-meta">
                            <span class="badge">{{ $post->category ?? 'その他' }}</span>
                            <time class="post-date">{{ optional($post->published_at)->setTimezone('Asia/Tokyo')->format('Y-m-d H:i') }}</time>
                        </div>
                        <h1 class="post-title">{{ $post->title }}</h1>
                        @if ($post->content)
                            <p style="margin-top:1rem;line-height:1.6;white-space:pre-wrap;">{{ $post->content }}</p>
                        @endif
                        <div style="margin-top:1.5rem;">
                            <img src="{{ $post->image_url ?? 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $post->title }}" style="max-width:100%;border-radius:0.5rem;">
                        </div>
                    </div>
                </section>

                <section style="margin-top:1.5rem;">
                    <form method="POST" action="{{ route('forum.reply.store', $post) }}" style="display:flex;flex-direction:column;gap:0.75rem;">
                        @csrf
                        <textarea name="content" rows="3" placeholder="返信を追加..." required style="width:100%;border:1px solid #d1d5db;border-radius:0.5rem;padding:0.75rem;font-family:inherit;"></textarea>
                        <button type="submit" class="action-btn btn-primary" style="border-radius:9999px; padding:0.5rem 1.25rem;">返信する</button>
                    </form>
                </section>

                @if ($post->replies->count() > 0)
                    <section style="margin-top:1.5rem;">
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            @foreach ($post->replies as $reply)
                                <!-- 一番上の親コメント -->
                                <div class="reply-card" onclick="toggleForm('reply', {{ $reply->id }}, event)">
                                    <div style="font-size:0.85rem;color:#6b7280;margin-bottom:0.35rem;">
                                        {{ $reply->author_name ?? ($reply->user?->login_id ?? '匿名') }} · {{ optional($reply->created_at)->setTimezone('Asia/Tokyo')->format('Y-m-d H:i') }}
                                    </div>
                                    <div style="white-space:pre-wrap;line-height:1.6;color:#111827;">{{ $reply->content }}</div>

                                    <div style="margin-top:0.75rem; display:flex; gap:1.25rem; align-items:center;" onclick="event.stopPropagation();">
                                        @auth
                                            @if (Auth::id() === $reply->user_id)
                                                <button type="button" class="btn-text" onclick="toggleForm('edit', {{ $reply->id }}, event)">編集</button>
                                                <!-- メッセージをフォーマルに修正 -->
                                                <form method="POST" action="{{ route('forum.reply.destroy', $reply) }}" onsubmit="return confirm('この返信を削除します。よろしいですか？\n※関連する返信もすべて削除されます。');" style="margin:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-text-danger">削除</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    <div id="reply-form-{{ $reply->id }}" style="display:none; margin-top:0.75rem;" onclick="event.stopPropagation();">
                                        <form method="POST" action="{{ route('forum.reply.store', $post) }}" style="display:flex; flex-direction:column; gap:0.5rem;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <textarea name="content" placeholder="返信を追加..." required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;width:100%;font-family:inherit;min-height:40px;"></textarea>
                                            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                                                <button type="button" class="btn-text" onclick="toggleForm('reply', {{ $reply->id }}, event)" style="padding:0.4rem 0.8rem;">キャンセル</button>
                                                <button type="submit" class="action-btn btn-primary" style="border-radius:9999px;">返信</button>
                                            </div>
                                        </form>
                                    </div>

                                    @auth
                                        @if (Auth::id() === $reply->user_id)
                                        <div id="edit-form-{{ $reply->id }}" style="display:none; margin-top:0.75rem;" onclick="event.stopPropagation();">
                                            <form method="POST" action="{{ route('forum.reply.update', $reply) }}" style="display:flex; flex-direction:column; gap:0.5rem;">
                                                @csrf
                                                @method('PATCH')
                                                <textarea name="content" required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;width:100%;font-family:inherit;min-height:40px;">{{ old('content', $reply->content) }}</textarea>
                                                <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                                                    <button type="button" class="btn-text" onclick="toggleForm('edit', {{ $reply->id }}, event)" style="padding:0.4rem 0.8rem;">キャンセル</button>
                                                    <button type="submit" class="action-btn btn-primary" style="border-radius:9999px;">保存</button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif
                                    @endauth

                                    <!-- サブ返信（子コメント）の表示エリア -->
                                    @if ($reply->children->count() > 0)
                                        <div style="margin-top:1rem;padding-left:1.5rem;display:flex;flex-direction:column;gap:0.5rem; border-left: 2px solid #e5e7eb;" onclick="event.stopPropagation();">
                                            @foreach ($reply->children as $child)
                                                <!-- 子コメントもクリック可能にする -->
                                                <div class="child-reply-card" onclick="toggleForm('reply', {{ $child->id }}, event)">
                                                    <div style="font-size:0.8rem;color:#6b7280;margin-bottom:0.2rem;">
                                                        {{ $child->author_name ?? ($child->user?->login_id ?? '匿名') }} · {{ optional($child->created_at)->setTimezone('Asia/Tokyo')->format('Y-m-d H:i') }}
                                                    </div>
                                                    <div style="white-space:pre-wrap;line-height:1.5;color:#374151;font-size:0.9rem;">{{ $child->content }}</div>

                                                    <div style="margin-top:0.5rem; display:flex; gap:1rem; align-items:center;" onclick="event.stopPropagation();">
                                                        @auth
                                                            @if (Auth::id() === $child->user_id)
                                                                <button type="button" class="btn-text" onclick="toggleForm('edit', {{ $child->id }}, event)">編集</button>
                                                                <!-- メッセージをフォーマルに修正 -->
                                                                <form method="POST" action="{{ route('forum.reply.destroy', $child) }}" onsubmit="return confirm('この返信を削除してもよろしいですか？');" style="margin:0;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn-text-danger">削除</button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </div>

                                                    <!-- 子コメントに対する返信フォーム -->
                                                    <div id="reply-form-{{ $child->id }}" style="display:none; margin-top:0.75rem;" onclick="event.stopPropagation();">
                                                        <form method="POST" action="{{ route('forum.reply.store', $post) }}" style="display:flex; flex-direction:column; gap:0.5rem;">
                                                            @csrf
                                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                                            <textarea name="content" placeholder="{{ $child->author_name ?? ($child->user?->login_id ?? '匿名') }} さんへ返信..." required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;width:100%;font-family:inherit;min-height:40px;"></textarea>
                                                            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                                                                <button type="button" class="btn-text" onclick="toggleForm('reply', {{ $child->id }}, event)" style="padding:0.4rem 0.8rem;">キャンセル</button>
                                                                <button type="submit" class="action-btn btn-primary" style="border-radius:9999px;">返信</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- 子コメントの編集フォーム -->
                                                    @auth
                                                        @if (Auth::id() === $child->user_id)
                                                        <div id="edit-form-{{ $child->id }}" style="display:none; margin-top:0.75rem;" onclick="event.stopPropagation();">
                                                            <form method="POST" action="{{ route('forum.reply.update', $child) }}" style="display:flex; flex-direction:column; gap:0.5rem;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <textarea name="content" required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;width:100%;font-family:inherit;min-height:40px;">{{ old('content', $child->content) }}</textarea>
                                                                <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                                                                    <button type="button" class="btn-text" onclick="toggleForm('edit', {{ $child->id }}, event)" style="padding:0.4rem 0.8rem;">キャンセル</button>
                                                                    <button type="submit" class="action-btn btn-primary" style="border-radius:9999px;">保存</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="post-detail-sidebar">
                @if ($relatedPosts->count() > 0)
                    <div class="related-posts-section">
                        <div class="related-posts-title">{{ $post->category }}の投稿</div>
                        <div>
                            @foreach ($relatedPosts as $related)
                                <div class="related-post-item">
                                    <a href="{{ route('forum.show', $related) }}" class="related-post-link">{{ $related->title }}</a>
                                    <div class="related-post-meta">{{ optional($related->published_at)->setTimezone('Asia/Tokyo')->format('m/d H:i') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </main>
</div>

<!-- 返信・編集フォームを開閉するためのJavaScript -->
<script>
    function toggleForm(type, id, event) {
        if (event) {
            event.stopPropagation();
        }

        const formToToggle = document.getElementById(type + '-form-' + id);
        const otherType = type === 'reply' ? 'edit' : 'reply';
        const formToHide = document.getElementById(otherType + '-form-' + id);

        if (formToHide) {
            formToHide.style.display = 'none';
        }

        if (formToToggle.style.display === 'none' || formToToggle.style.display === '') {
            formToToggle.style.display = 'block';
            formToToggle.querySelector('textarea').focus();
        } else {
            formToToggle.style.display = 'none';
        }
    }
</script>
</body>
</html>