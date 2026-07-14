<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>空き教室予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/reservation/room/room-reservation.css','resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <div class="app-layout">
        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'reservation'])

        <!-- メイン画面 -->
        <main class="content">
            <!-- ヘッダー -->
            <div class="header">
                <a href="javascript:history.back()" class="back-button">←</a>
                <h1>空き教室予約</h1>
            </div>

            <!-- フィルターセクション -->
            <div class="filter-section">
                <div class="filter-group">
                    <label for="date-input">日付</label>
                    <input type="date" id="date-input" value="2026-06-26">
                    
                </div>

                <div class="filter-group">
                    <label for="time-select">時間</label>
                    <select id="time-select">
                        <option value="1">1限</option>
                        <option value="2">2限</option>
                        <option value="3">3限</option>
                        <option value="4">4限</option>
                        <option value="5">5限</option>
                        <option value="6">6限</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="floor-select">階数</label>
                    <select id="floor-select">
                        <option value="1">1階</option>
                        <option value="2">2階</option>
                        <option value="3">3階</option>
                        <option value="4">4階</option>
                        <option value="5">5階</option>
                        <option value="6">6階</option>
                    </select>
                </div>

            
                </svg>
            </div>
        </main>
    </div>
</body>
</html>
