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
                    <h2 style="font-size:1rem;font-weight:700;margin-bottom:0.75rem;">返信</h2>
                    <form method="POST" action="{{ route('forum.reply.store', $post) }}" style="display:flex;flex-direction:column;gap:0.75rem;">
                        @csrf
                        <textarea name="content" rows="4" placeholder="返信を入力してください" required style="width:100%;border:1px solid #d1d5db;border-radius:0.5rem;padding:0.75rem;"></textarea>
                        <button type="submit" class="submit-button" style="width:auto;">返信する</button>
                    </form>
                </section>

                @if ($post->replies->count() > 0)
                    <section style="margin-top:1.5rem;">
                        <h2 style="font-size:1rem;font-weight:700;margin-bottom:0.75rem;">返信一覧</h2>
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            @foreach ($post->replies as $reply)
                                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.9rem;">
                                    <div style="font-size:0.85rem;color:#6b7280;margin-bottom:0.35rem;">
                                        {{ $reply->author_name ?? ($reply->user?->login_id ?? '匿名') }} · {{ $reply->created_at->format('Y-m-d H:i') }}
                                    </div>
                                    <div style="white-space:pre-wrap;line-height:1.6;">{{ $reply->content }}</div>
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
