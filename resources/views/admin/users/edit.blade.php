<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー編集 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/admin/users.css'])
</head>

<body>
    <div class="app-layout">
        @include('partials.sidebar', ['active' => 'admin_users'])

        <main class="content">
            <div class="users-header">
                <h1>ユーザー編集：{{ $user->login_id }}</h1>
            </div>
            @include('admin.users._form', ['isEdit' => true, 'user' => $user])
        </main>
    </div>
</body>

</html>
