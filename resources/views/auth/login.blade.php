<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログイン｜学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/auth.css'])
</head>

<body class="auth-body">
    {{-- $tab が 'register' なら新規登録タブを初期表示。バリデーションエラー時はそのタブを維持する。 --}}
    @php
        $activeTab = $errors->hasBag('register') || ($tab ?? 'login') === 'register' ? 'register' : 'login';
    @endphp

    <div class="auth-card" data-active="{{ $activeTab }}">
        <div class="auth-logo">
            <h1>学生支援<span class="dot">.com</span></h1>
            <p>学校便利掲示板システム</p>
        </div>

        {{-- タブ切り替え --}}
        <div class="auth-tabs">
            <button type="button" class="auth-tab" data-tab="login">ログイン</button>
            <button type="button" class="auth-tab" data-tab="register">新規登録</button>
        </div>

        {{-- ログインフォーム --}}
        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form" data-form="login">
            @csrf
            <div class="field">
                <label for="login_id_login">ID</label>
                <input type="text" id="login_id_login" name="login_id" placeholder="例: tanaka01"
                    value="{{ old('login_id') }}" autocomplete="username">
                <p class="hint">メールアドレスは不要です。好きな半角英数のIDを決めてください。</p>
                @error('login_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password_login">パスワード</label>
                <input type="password" id="password_login" name="password" placeholder="6文字以上"
                    autocomplete="current-password">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-submit">ログイン</button>
        </form>

        {{-- 新規登録フォーム --}}
        <form method="POST" action="{{ route('register.attempt') }}" class="auth-form" data-form="register">
            @csrf
            <div class="field">
                <label for="login_id_register">ID</label>
                <input type="text" id="login_id_register" name="login_id" placeholder="例: tanaka01"
                    value="{{ old('login_id') }}" autocomplete="username">
                <p class="hint">メールアドレスは不要です。好きな半角英数のIDを決めてください。</p>
                @error('login_id', 'register')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password_register">パスワード</label>
                <input type="password" id="password_register" name="password" placeholder="6文字以上"
                    autocomplete="new-password">
                @error('password', 'register')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-submit">登録してはじめる</button>
        </form>

        <p class="auth-note">先生用IDでログインすると、欠席・遅刻届を全員分閲覧できます。</p>
    </div>

    <script>
        const card = document.querySelector('.auth-card');
        const tabs = document.querySelectorAll('.auth-tab');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                card.dataset.active = tab.dataset.tab;
            });
        });
    </script>
</body>

</html>
