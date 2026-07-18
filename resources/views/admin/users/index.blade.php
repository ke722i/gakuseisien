<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>アカウント管理 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/admin/users.css'])
</head>

<body>
    <div class="app-layout">
        @include('partials.sidebar', ['active' => 'admin_users'])

        <main class="content">
            <div class="users-header">
                <h1>アカウント管理</h1>
                <a href="{{ route('admin.users.create') }}" class="primary-btn">＋ 新規ユーザー</a>
            </div>

            @if (session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif
            @if (session('reset_password'))
                <div class="flash flash-password">
                    「{{ session('reset_password')['login_id'] }}」の新しいパスワード：
                    <code>{{ session('reset_password')['password'] }}</code>
                    <br>この画面を離れると再表示できません。本人にお伝えください。
                </div>
            @endif

            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>権限</th>
                            <th>氏名</th>
                            <th>学籍/教員番号</th>
                            <th>クラス</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->login_id }}</td>
                                <td>
                                    <span class="role-pill {{ $u->role === 'teacher' ? 'role-teacher' : 'role-student' }}">
                                        {{ $u->role === 'teacher' ? '教職員' : '学生' }}
                                    </span>
                                </td>
                                <td>{{ $u->role === 'teacher' ? ($u->teacher_name ?? '—') : ($u->student_name ?? '—') }}</td>
                                <td>{{ $u->role === 'teacher' ? ($u->teacher_number ?? '—') : ($u->student_number ?? '—') }}</td>
                                <td>{{ $u->class_number ?? '—' }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="act-edit">編集</a>
                                        <form method="POST" action="{{ route('admin.users.resetPassword', $u) }}" onsubmit="return confirm('{{ $u->login_id }} のパスワードを初期化しますか？');">
                                            @csrf
                                            <button type="submit" class="act-reset">PW初期化</button>
                                        </form>
                                        @if ($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('{{ $u->login_id }} を削除しますか？この操作は取り消せません。');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="act-delete">削除</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>
