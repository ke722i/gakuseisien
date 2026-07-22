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

            {{-- 完了メッセージは共通ポップアップ（partials/toast）で表示する。
                 発行したパスワードだけは読み取れるよう画面にも残す。 --}}
            @if (session('reset_password'))
                <div class="flash flash-password">
                    「{{ session('reset_password')['login_id'] }}」の新しいパスワード：
                    <code>{{ session('reset_password')['password'] }}</code>
                    <br>この画面を離れると再表示できません。本人にお伝えください。
                </div>
            @endif

            {{-- 名簿CSVからの一括発行 --}}
            <section class="import-card">
                <h2>名簿CSVから一括登録</h2>
                <p class="import-desc">
                    列の順番は <code>学籍番号 , 氏名 , クラス番号 , 担任教師</code>（1行目の見出しは自動で読み飛ばします）。<br>
                    ログインIDは学籍番号、初期パスワードはランダムに発行され、本人の初回ログイン時に変更を求めます。
                </p>

                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" class="import-form">
                    @csrf
                    <input type="file" name="roster" accept=".csv,text/csv" required>
                    <button type="submit" class="primary-btn">取り込む</button>
                </form>

                @if (session('import_result'))
                    @php($result = session('import_result'))
                    <div class="import-result">
                        @if (count($result['created']) > 0)
                            <p class="import-result-head">
                                {{ count($result['created']) }}件のアカウントを発行しました。
                                <a href="{{ route('admin.users.import.result') }}" class="primary-btn" style="padding:6px 14px;font-size:13px;margin-left:8px">
                                    初期パスワード一覧をダウンロード
                                </a>
                            </p>
                            <p class="import-note">
                                ※ パスワードは暗号化して保存されるため、この画面を離れると再表示できません。
                                必ずダウンロードして配布してください。
                            </p>
                            <div class="users-table-wrap" style="margin-top:10px">
                                <table class="users-table">
                                    <thead><tr><th>ログインID</th><th>氏名</th><th>クラス</th><th>初期パスワード</th></tr></thead>
                                    <tbody>
                                        @foreach ($result['created'] as $row)
                                            <tr>
                                                <td>{{ $row['login_id'] }}</td>
                                                <td>{{ $row['student_name'] }}</td>
                                                <td>{{ $row['class_number'] ?: '—' }}</td>
                                                <td><code>{{ $row['password'] }}</code></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if (count($result['skipped']) > 0)
                            <p class="import-skipped">既に登録済みのためスキップ：{{ implode('、', $result['skipped']) }}</p>
                        @endif

                        @if (count($result['errors']) > 0)
                            <div class="import-errors">
                                <p>取り込めなかった行：</p>
                                @foreach ($result['errors'] as $e)<div>{{ $e }}</div>@endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </section>

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
                                        <form method="POST" action="{{ route('admin.users.resetPassword', $u) }}" data-confirm="{{ $u->login_id }} のパスワードを初期化しますか？">
                                            @csrf
                                            <button type="submit" class="act-reset">PW初期化</button>
                                        </form>
                                        @if ($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="{{ $u->login_id }} を削除しますか？この操作は取り消せません。">
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
