<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>パスワードの変更｜学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/auth.css'])
    <style>
        .pw-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 18px;
        }
        .pw-logout {
            display: block;
            text-align: center;
            margin-top: 16px;
        }
        .pw-logout button {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 13px;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>

<body class="auth-body">
    <div class="auth-card" data-active="login">
        <div class="auth-logo">
            <h1>学生支援<span class="dot">.com</span></h1>
            <p>パスワードの変更</p>
        </div>

        @if ($isForced)
            <div class="pw-note">
                配布された初期パスワードでログインしています。<br>
                安全のため、自分だけがわかるパスワードに変更してください。
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" class="auth-form" data-form="login" style="display:block">
            @csrf

            <div class="field">
                <label for="current_password">現在のパスワード</label>
                <input type="password" id="current_password" name="current_password"
                    placeholder="配布されたパスワード" autocomplete="current-password">
                @error('current_password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password">新しいパスワード</label>
                <input type="password" id="password" name="password" placeholder="6文字以上" autocomplete="new-password">
                <p class="hint">英字と数字を使い、数字を1文字以上含めてください。</p>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">新しいパスワード（確認）</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="もう一度入力" autocomplete="new-password">
            </div>

            <button type="submit" class="auth-submit">変更する</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="pw-logout">
            @csrf
            <button type="submit">別のアカウントでログインする</button>
        </form>
    </div>

    @include('partials.toast')
</body>

</html>
