<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ホーム - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/home.css'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'home'])

        <!-- メイン画面 -->
        <main class="content">
            <h1 class="home-title">ホーム</h1>

            {{-- 右列は右上ウィジェット（内容未定）用にスペースを確保している --}}
            <div class="home-grid">
                <div class="home-main">

                    <!-- 本日の予定 -->
                    <section class="card today-card">
                        <div class="today-left">
                            <p class="today-date" id="todayDate">2026/06/30</p>
                            <p class="today-greeting" id="greeting">こんにちは！</p>
                            <p class="today-clock" id="clock">11:48</p>
                        </div>
                        <div class="today-right">
                            <h2 class="card-title center">本日の予定</h2>
                            <ul class="schedule-list">
                                <li><span class="time">09:15</span> 総合演習 <span class="end">終了10:45</span></li>
                                <li><span class="time">11:00</span> 総合演習 <span class="end">終了12:30</span></li>
                                <li><span class="time">12:30</span> お昼休み <span class="end">終了13:30</span></li>
                                <li><span class="time">13:30</span> 大学研究 <span class="end">終了15:30</span></li>
                                <li><span class="time">15:15</span> 大学研究 <span class="end">終了16:45</span></li>
                            </ul>
                        </div>
                    </section>

                    <!-- 直近 イベント・締め切り -->
                    <section class="card events-card">
                        <h2 class="card-title center">直近　イベント・締め切り</h2>
                        <div class="events-cols">
                            <ul>
                                <li><span class="date">2026/06/30</span> <span class="time">23:59</span> 提出物</li>
                                <li><span class="date">2026/07/25</span> <span class="time">23:59</span> 提出物</li>
                                <li><span class="date">2026/07/25</span> <span class="time">23:59</span> 提出物</li>
                                <li><span class="date">2026/07/25</span> <span class="time">23:59</span> 提出物</li>
                            </ul>
                            <ul>
                                <li><span class="date">2026/07/29</span> <span class="time">14:00</span> 提出物</li>
                                <li><span class="date">2026/08/01</span> <span class="time">23:59</span> 提出物</li>
                            </ul>
                        </div>
                    </section>

                    <!-- 下段：空き教室予約 ／ お知らせ -->
                    <div class="home-bottom">
                        <section class="card reserve-card">
                            <h2 class="card-title">空き教室予約</h2>
                            <ul class="reserve-list">
                                <li><span class="date">2026/07/08</span> <span class="time">13:30</span> <span class="room">404</span></li>
                                <li><span class="date">2026/07/29</span> <span class="time">15:30</span> <span class="room">201</span></li>
                                <li><span class="date">2026/07/29</span> <span class="time">15:30</span> <span class="room">306</span></li>
                                <li><span class="date">2026/07/29</span> <span class="time">15:30</span> <span class="room">506</span></li>
                            </ul>
                        </section>

                        <section class="card notice-card">
                            <h2 class="card-title">お知らせ</h2>
                            <p>
                                アップデートのお知らせ。<br>
                                教室の予約が確定しました。
                            </p>
                        </section>
                    </div>
                </div>

                <!-- 右上ウィジェット（内容未定）用の予約スペース -->
                <aside class="home-side" aria-hidden="true"></aside>
            </div>
        </main>
    </div>

    <script>
        // 日付・時計・あいさつを現在時刻で更新する
        (function () {
            const pad = (n) => String(n).padStart(2, '0');

            function update() {
                const now = new Date();

                const dateEl = document.getElementById('todayDate');
                if (dateEl) {
                    dateEl.textContent = `${now.getFullYear()}/${pad(now.getMonth() + 1)}/${pad(now.getDate())}`;
                }

                const clockEl = document.getElementById('clock');
                if (clockEl) {
                    clockEl.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
                }

                const greetingEl = document.getElementById('greeting');
                if (greetingEl) {
                    const h = now.getHours();
                    greetingEl.textContent = h < 11 ? 'おはようございます！' : (h < 17 ? 'こんにちは！' : 'こんばんは！');
                }
            }

            update();
            setInterval(update, 1000);
        })();
    </script>
</body>

</html>
