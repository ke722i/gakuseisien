<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    @vite(['resources/css/app.css', 'resources/css/forum/forum-top.css', 'resources/js/app.js'])
</head>
<body>

    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'forum'])

        <main class="content">
            <header class="header">
                <h1 class="header-title">掲示板</h1>
            </header>

            <nav class="category-nav">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('forum.top', ['category' => $category !== 'すべて' ? $category : null, 'q' => $searchQuery]) }}"
                        class="category-btn {{ $selectedCategory === $category ? 'active' : '' }}"
                    >
                        {{ $category }}
                    </a>
                @endforeach
            </nav>

            <div class="search-container">
                <form method="GET" action="{{ route('forum.top') }}">
                    <input type="hidden" name="category" value="{{ $selectedCategory !== 'すべて' ? $selectedCategory : '' }}">
                    <div class="search-box">
                        <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="q" class="search-input" placeholder="キーワード検索" value="{{ $searchQuery }}">
                    </div>
                </form>
            </div>

            <section class="post-list">
                @forelse ($posts as $post)
                    <article class="post-card">
                        <div class="post-thumbnail">
                            <img src="{{ $post->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $post->title }}">
                        </div>
                        <div class="post-content">
                            <div class="post-author">
                                <svg class="icon-user" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>{{ $post->posted_by ?? '投稿者不明' }}</span>
                            </div>
                            <div class="post-meta">
                                <span class="badge {{ $post->category === '落とし物' ? 'badge-lost' : ($post->category === 'サークル' ? 'badge-circle' : ($post->category === '教科書' ? 'badge-book' : 'badge-circle')) }}">{{ $post->category ?? 'その他' }}</span>
                                <time class="post-date">{{ $post->published_at?->format('Y-m-d H:i') ?? $post->created_at->format('Y-m-d H:i') }}</time>
                            </div>
                            <h2 class="post-title">{{ $post->title }}</h2>
                            @if ($post->location)
                                <div class="post-location">
                                    <svg class="icon-location" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>{{ $post->location }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="post-empty">
                        <p>投稿が見つかりませんでした。キーワードやカテゴリを変更してみてください。</p>
                    </div>
                @endforelse
            </section>

            <a href="{{ route('forum.create') }}" class="fab-button" aria-label="投稿を作成する">
                <svg class="fab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                </svg>
            </a>

        </main>

    </div>

</body>
</html>