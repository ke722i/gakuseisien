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

        /* メインの返信ボタン */
        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            padding: 0.4rem 0.6rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            /* ↓この1行を追加して横幅の広がりを抑え、左寄せにします */
            align-self: flex-start; 
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        /* サブボタン（返信一覧の中の返信・編集用） */
        .btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border-color: #d1d5db;
            /* 左右の余白を 0.6rem → 0.3rem にカットし、限界まで細く */
            padding: 0.25rem 0.3rem;
            font-size: 0.8rem;
        }
        .btn-secondary:hover {
            background-color: #f3f4f6;
        }

        /* 削除ボタン */
        .btn-danger {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            /* サブボタンと同じく左右の余白を 0.3rem にカット */
            padding: 0.25rem 0.3rem;
            font-size: 0.8rem;
        }
        .btn-danger:hover {
            background-color: #fecaca;
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
                            <time class="post-date">{{ $post->published_at?->format('Y-m-d H:i') }}</time>
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
                        <textarea name="content" rows="4" placeholder="返信を入力してください" required style="width:100%;border:1px solid #d1d5db;border-radius:0.5rem;padding:0.75rem;"></textarea>
                        <button type="submit" class="action-btn btn-primary">返信する</button>
                    </form>
                </section>

                @if ($post->replies->count() > 0)
                    <section style="margin-top:1.5rem;">
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            @foreach ($post->replies as $reply)
                                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.9rem;">
                                    <div style="font-size:0.85rem;color:#6b7280;margin-bottom:0.35rem;">
                                        {{ $reply->author_name ?? ($reply->user?->login_id ?? '匿名') }} · {{ $reply->created_at->format('Y-m-d H:i') }}
                                    </div>
                                    <div style="white-space:pre-wrap;line-height:1.6;">{{ $reply->content }}</div>

                                    <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                                        <form method="POST" action="{{ route('forum.reply.store', $post) }}" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <input type="text" name="content" placeholder="返信" required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;min-width:220px;">
                                            <button type="submit" class="action-btn btn-secondary">返信</button>
                                        </form>

                                        @auth
                                            @if (Auth::id() === $reply->user_id)
                                                <form method="POST" action="{{ route('forum.reply.update', $reply) }}" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="content" value="{{ old('content', $reply->content) }}" required style="border:1px solid #d1d5db;border-radius:0.4rem;padding:0.45rem 0.6rem;min-width:220px;">
                                                    <button type="submit" class="action-btn btn-secondary">編集</button>
                                                </form>
                                                <form method="POST" action="{{ route('forum.reply.destroy', $reply) }}" onsubmit="return confirm('この返信を削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn btn-danger">削除</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>

                                    @if ($reply->children->count() > 0)
                                        <div style="margin-top:0.75rem;padding-left:1rem;border-left:2px solid #e5e7eb;display:flex;flex-direction:column;gap:0.5rem;">
                                            @foreach ($reply->children as $child)
                                                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:0.4rem;padding:0.75rem;">
                                                    <div style="font-size:0.8rem;color:#6b7280;margin-bottom:0.3rem;">
                                                        {{ $child->author_name ?? ($child->user?->login_id ?? '匿名') }} · {{ $child->created_at->format('Y-m-d H:i') }}
                                                    </div>
                                                    <div style="white-space:pre-wrap;line-height:1.5;">{{ $child->content }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @auth
                    @if (Auth::id() === $post->user_id)
                        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
                            <a href="{{ route('forum.edit', $post) }}" class="submit-button" style="display:inline-block;text-decoration:none;">編集</a>
                            <form method="POST" action="{{ route('forum.destroy', $post) }}" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="submit-button" style="background:#dc2626;">削除</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            <aside class="post-detail-sidebar">
                @if ($relatedPosts->count() > 0)
                    <div class="related-posts-section">
                        <div class="related-posts-title">{{ $post->category }}の投稿</div>
                        <div>
                            @foreach ($relatedPosts as $related)
                                <div class="related-post-item">
                                    <a href="{{ route('forum.show', $related) }}" class="related-post-link">{{ $related->title }}</a>
                                    <div class="related-post-meta">{{ $related->published_at?->format('m/d H:i') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </main>
</div>
</body>
</html>
