<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>質問を投稿する - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite([
    'resources/css/app.css',
    'resources/css/qna/create.css',
    'resources/js/app.js',
    'resources/js/qna.js'
    ])
</head>

<body>
    @include('partials.sidebar', ['active' => 'qna'])

    <!-- 💡 class="qna-page" を除外し、余白のバグを防ぎます -->
    <main class="content">

        <div class="qna-header-container">
            <h1 class="qna-title">質問を投稿する</h1>
            <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">一覧に戻る</a>
        </div>

        <!-- 💡 余計な余白を作っていた "qna-card-list" コンテナを削除し、フォームを直接配置 -->
        <form action="{{ route('qna.store') }}" method="POST" enctype="multipart/form-data" class="qna-custom-card qna-form-padding">
            @csrf

            <div class="qna-form-group">
                <label for="category" class="qna-form-label">カテゴリ</label>
                <select id="category" name="category" class="qna-search-input qna-form-input-align" style="padding: 10px; background-color: #fff;" required>
                    <option value="" disabled selected>カテゴリを選択してください</option>
                    <option value="履修・授業">履修・授業</option>
                    <option value="学食・施設">学食・施設</option>
                    <option value="サークル・イベント">サークル・イベント</option>
                    <option value="生活・その他">生活・その他</option>
                </select>
            </div>

            <div class="qna-form-group">
                <label for="title" class="qna-form-label">タイトル</label>
                <input type="text" id="title" name="title" class="qna-search-input qna-form-input-align" placeholder="例：学食の発券機は新紙幣に対応していますか？" required value="{{ old('title') }}">
            </div>

            <div class="qna-form-group">
                <label for="body" class="qna-form-label">内容（詳細）</label>
                <textarea id="body" name="content" class="qna-search-input qna-form-input-align qna-form-textarea" rows="6" placeholder="詳しい質問内容や状況を入力してください..." required>{{ old('content') }}</textarea>
            </div>

            <div class="qna-form-group">
                <label class="qna-form-label">画像（任意）</label>
                <label for="image" class="qna-file-upload-box">
                    <span class="qna-upload-icon">📁</span>
                    <span class="qna-upload-text">画像を選択、またはドラッグ＆ドロップ</span>
                    <input type="file" id="image" name="image" style="display: none;" accept="image/*">
                </label>
            </div>

            <div class="qna-form-actions">
                <button type="submit" class="qna-history-btn qna-btn-submit">投稿する</button>
            </div>
        </form>
    </main>

    <!-- 💡 ここから追加：画像プレビューとドラッグ＆ドロップの処理 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('image');
            const uploadBox = document.querySelector('.qna-file-upload-box');
            const uploadText = document.querySelector('.qna-upload-text');
            const uploadIcon = document.querySelector('.qna-upload-icon');

            // ファイルが選択された時の処理
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    showPreview(file);
                }
            });

            // ドラッグ＆ドロップの視覚効果
            uploadBox.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadBox.style.backgroundColor = '#f0f0f0';
                uploadBox.style.borderColor = '#111111';
            });

            uploadBox.addEventListener('dragleave', function() {
                uploadBox.style.backgroundColor = '#ffffff';
                uploadBox.style.borderColor = '#111111';
            });

            uploadBox.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadBox.style.backgroundColor = '#ffffff';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files; // inputにファイルをセット
                    showPreview(files[0]);
                }
            });

            // プレースホルダーを画像プレビューに書き換える関数
            function showPreview(file) {
                if (!file.type.startsWith('image/')) {
                    showToast('画像ファイルを選択してください。', 'error');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // すでにプレビュー画像がある場合は削除しておく
                    const existingImg = uploadBox.querySelector('.preview-image');
                    if (existingImg) {
                        existingImg.remove();
                    }

                    // プレビュー用の画像要素を作成
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-image');
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '200px';
                    img.style.marginTop = '10px';
                    img.style.borderRadius = '4px';
                    img.style.objectFit = 'contain';

                    // テキストとアイコンを隠し、画像を表示する
                    uploadIcon.style.display = 'none';
                    uploadText.textContent = `選択済み: ${file.name}`;

                    uploadBox.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>