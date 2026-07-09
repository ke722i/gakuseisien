<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>空き教室予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/reservation/home/teacher.css','resources/css/app.css','resources/js/app.js'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'reservation'])

        <!-- メイン画面 -->
        <main class="content">
            <div class="center-panel">

                <a href="{{ route('classroom.reservation.bulk') }}" class="big-button">空き教室予約</a>

                <a href="#" class="big-button">予約一覧</a>

                <a href="#" class="big-button">予約管理</a>

                <a href="#" class="big-button">教室一覧予約</a>
            </div>
        </main>
    </div>
</body>
</html>