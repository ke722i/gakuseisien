<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿を編集</title>
    @vite(['resources/css/app.css', 'resources/css/forum/forum-top.css', 'resources/css/forum/forum-create.css'])
</head>
<body>
<div class="app-layout">
    @include('partials.sidebar', ['active' => 'forum'])

    <main class="content">
        <div class="top-header">
            <a href="{{ route('forum.show', $post) }}" class="back-button">←</a>
        </div>

        <section class="create-header">
            <h1>投稿を編集する</h1>
        </section>

        <form method="POST" action="{{ route('forum.update', $post) }}" class="forum-form">
            @csrf
            @method('PATCH')

            <div class="form-group category-group">
                <span class="group-label">カテゴリを選択</span>
                <label class="radio-label"><input type="radio" name="category" value="落とし物" {{ old('category', $post->category) === '落とし物' ? 'checked' : '' }}> 落とし物</label>
                <label class="radio-label"><input type="radio" name="category" value="サークル" {{ old('category', $post->category) === 'サークル' ? 'checked' : '' }}> サークル</label>
                <label class="radio-label"><input type="radio" name="category" value="教科書" {{ old('category', $post->category) === '教科書' ? 'checked' : '' }}> 教科書</label>
            </div>

            <div class="form-group">
                <label for="title">タイトル</label>
                <input id="title" name="title" type="text" maxlength="300" value="{{ old('title', $post->title) }}" required>
            </div>

            <div class="form-group">
                <label for="content">本文</label>
                <textarea id="content" name="content" rows="6">{{ old('content', $post->content) }}</textarea>
            </div>

            <button type="submit" class="submit-button">更新</button>
        </form>
    </main>
</div>
</body>
</html>
