<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー新規作成 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/admin/users.css'])
</head>

<body>
    <div class="app-layout">
        @include('partials.sidebar', ['active' => 'admin_users'])

        <main class="content">
            <div class="users-header">
                <h1>ユーザー新規作成</h1>
            </div>
            @include('admin.users._form', ['isEdit' => false, 'user' => null])
        </main>
    </div>
</body>

</html>
