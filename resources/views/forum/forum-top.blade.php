<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    @vite(['resources/css/app.css', 'resources/css/forum/forum-top.css', 'resources/js/app.js'])
    <style>
        /* --- ドロップダウンメニュー用UI --- */
        .post-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: #4b5563;
            padding: 0.4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }
        .dropdown-toggle:hover {
            background-color: #f3f4f6;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #ffffff;
            min-width: 180px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            z-index: 50;
            /* 枠からはみ出るようにoverflow設定を上書き */
            overflow: visible; 
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.6rem 1rem;
            font-size: 0.875rem;
            color: #374151;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .dropdown-item:hover {
            background-color: #f9fafb;
        }
        .dropdown-item.text-danger {
            color: #dc2626;
        }
        .dropdown-item.text-warning {
            color: #d97706;
        }
    </style>
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
                    <!-- z-index をループごとに下げていき、下のカードより上に表示させる。overflow:visibleを追加 -->
                    <article class="post-card" style="width:100%; display:flex; position:relative; overflow:visible !important; z-index: {{ 100 - $loop->index }};">
                        <!-- カード全体をリンク化 -->
                        <a href="{{ route('forum.show', $post) }}" style="position: absolute; inset: 0; z-index: 1;"></a>
                        
                        <div class="post-thumbnail">
                            <img src="{{ $post->image_url ?? 'https://via.placeholder.com/150' }}" alt="{{ $post->title }}">
                        </div>
                        
                        <div class="post-content" style="width: 100%;">
                            <div class="post-header-top">
                                <div>
                                    <div class="post-author">
                                        <svg class="icon-user" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $post->posted_by ?? '投稿者不明' }}</span>
                                    </div>
                                    <div class="post-meta">
                                        <span class="badge {{ $post->category === '落とし物' ? 'badge-lost' : ($post->category === 'サークル' ? 'badge-circle' : ($post->category === '教科書' ? 'badge-book' : 'badge-circle')) }}">{{ $post->category ?? 'その他' }}</span>
                                        <time class="post-date">{{ optional($post->published_at)->setTimezone('Asia/Tokyo')->format('Y-m-d H:i') ?? optional($post->created_at)->setTimezone('Asia/Tokyo')->format('Y-m-d H:i') }}</time>
                                    </div>
                                </div>

                                @auth
                                <div class="dropdown" style="position: relative; z-index: 10;">
                                    <button class="dropdown-toggle" onclick="toggleDropdown(event, 'dropdown-{{ $post->id }}')" aria-label="メニューを開く">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="1.5"></circle>
                                            <circle cx="19" cy="12" r="1.5"></circle>
                                            <circle cx="5" cy="12" r="1.5"></circle>
                                        </svg>
                                    </button>
                                    
                                    <div class="dropdown-menu" id="dropdown-{{ $post->id }}">
                                        @if (Auth::user()->isTeacher())
                                            <form method="POST" action="{{ route('forum.destroy', $post) }}" data-confirm="この投稿を削除しますか？" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="position: relative; z-index: 11;">全員分削除</button>
                                            </form>
                                        @elseif (Auth::id() === $post->user_id)
                                            <a href="{{ route('forum.edit', $post) }}" class="dropdown-item" style="position: relative; z-index: 11;">編集する</a>
                                            <form method="POST" action="{{ route('forum.destroy', $post) }}" data-confirm="削除しますか？" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="position: relative; z-index: 11;">削除する</button>
                                            </form>
                                        @endif

                                        @if (!Auth::user()->isTeacher())
                                            {{-- 通報理由は自由記述（data-confirm-input で入力欄付きダイアログを出す） --}}
                                            <form method="POST" action="{{ route('forum.report', $post) }}"
                                                  data-confirm="この投稿を通報します。理由を入力してください（スパム、嫌がらせ、公序良俗に反する投稿など）"
                                                  data-confirm-input="reason" style="margin:0;">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning" style="position: relative; z-index: 11;">通報する</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                @endauth
                            </div>
                            
                            <h2 class="post-title" style="margin-top: 0.5rem;">{{ $post->title }}</h2>
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

            <div style="margin-top:1rem;">
                {{ $posts->links() }}
            </div>

            <a href="{{ route('forum.create') }}" class="fab-button" aria-label="投稿を作成する">
                <svg class="fab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                </svg>
            </a>

        </main>

    </div>

    <!-- 三点リーダーを開閉するためのスクリプト -->
    <script>
        function toggleDropdown(event, dropdownId) {
            // 親のリンクがクリックされるのを防ぐ
            event.preventDefault();
            event.stopPropagation();
            
            // 他の開いているメニューをすべて閉じる
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].id !== dropdownId) {
                    dropdowns[i].classList.remove('show');
                }
            }
            
            // クリックしたメニューの表示を切り替える
            document.getElementById(dropdownId).classList.toggle('show');
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

</body>
</html>