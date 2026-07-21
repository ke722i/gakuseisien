<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規投稿</title>
    @vite(['resources/css/app.css', 'resources/css/forum/forum-top.css', 'resources/css/forum/forum-create.css'])
</head>
<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'forum'])

        <main class="content">
            <div class="top-header">
                <a href="{{ route('forum.top') }}" class="back-button">←</a>
            </div>

            <section class="create-header">
                <h1>投稿を作成する</h1>
            </section>

            <form method="POST" action="{{ route('forum.store') }}" enctype="multipart/form-data" class="forum-form">
                @csrf

                @if ($errors->any())
                    <div class="alert-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group category-group">
                    <span class="group-label">カテゴリを選択</span>
                    @if (Auth::check() && Auth::user()->isTeacher())
                        <label class="radio-label"><input type="radio" name="category" value="落とし物" {{ old('category', '落とし物') === '落とし物' ? 'checked' : '' }}> 落とし物</label>
                    @endif
                    <label class="radio-label"><input type="radio" name="category" value="サークル" {{ old('category') === 'サークル' ? 'checked' : '' }}> サークル</label>
                    <label class="radio-label"><input type="radio" name="category" value="教科書" {{ old('category') === '教科書' ? 'checked' : '' }}> 教科書</label>
                </div>

                <div class="form-group">
                    <label for="title">タイトル</label>
                    <input id="title" name="title" type="text" maxlength="300" placeholder="タイトル" value="{{ old('title') }}" required>
                    <!-- JSから操作できるように id="title-counter" を追加し、PHPのカウントを mb_strlen に変更 -->
                    <div class="hint" id="title-counter">{{ mb_strlen(old('title', '')) }}/300</div>
                </div>

                <div class="form-group">
                    <label for="content">本文（任意）</label>
                    <textarea id="content" name="content" rows="6" placeholder="本文（任意）">{{ old('content') }}</textarea>
                </div>

                <div class="form-group file-upload">
                    <label for="media">画像・動画</label>
                    <input id="media" type="file" name="media[]" multiple>
                    <p>メディアをドラッグアンドドロップするか、アップロードしてください</p>
                </div>

                <button type="submit" class="submit-button">投稿</button>
            </form>
        </main>
    </div>

    <!-- リアルタイム文字数カウント用のJavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('title');
            const titleCounter = document.getElementById('title-counter');

            if (titleInput && titleCounter) {
                // 入力（キー入力、コピペなど）があるたびに実行
                titleInput.addEventListener('input', function() {
                    const currentLength = this.value.length;
                    titleCounter.textContent = currentLength + '/300';
                });
            }
        });
    </script>
</body>
</html>