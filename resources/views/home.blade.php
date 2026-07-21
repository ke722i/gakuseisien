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

            <div class="home-grid">
                <div class="home-main">

                    <!-- 上段：日付・時計 ＋ お知らせ（学生）／対応待ち（先生） -->
                    <section class="card today-card">
                        <div class="today-left">
                            <p class="today-date" id="todayDate"></p>
                            <p class="today-greeting" id="greeting">こんにちは！</p>
                            <p class="today-clock" id="clock"></p>
                        </div>
                        <div class="today-right">
                            @if ($isTeacher)
                                {{-- 先生：承認・確認待ちの件数一覧 --}}
                                <h2 class="card-title center">対応待ち</h2>
                                <ul class="inbox-list">
                                    <li>
                                        <a href="{{ route('classroom.reservation.manage') }}">
                                            教室予約の承認待ち
                                            <span class="count-badge {{ $pendingReservations > 0 ? 'has-items' : '' }}">{{ $pendingReservations }}件</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('store.admin') }}">
                                            店舗申請の承認待ち
                                            <span class="count-badge {{ $pendingShopRequests > 0 ? 'has-items' : '' }}">{{ $pendingShopRequests }}件</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('notification') }}">
                                            欠席・遅刻届の未処理
                                            <span class="count-badge {{ $pendingAttendance > 0 ? 'has-items' : '' }}">{{ $pendingAttendance }}件</span>
                                        </a>
                                    </li>
                                </ul>
                            @else
                                {{-- 学生：お知らせ（先生からの返信に気づける場所） --}}
                                <div class="notice-header">
                                    <h2 class="card-title">
                                        お知らせ
                                        @if ($unreadCount > 0)
                                            <span class="unread-badge">未読{{ $unreadCount }}件</span>
                                        @endif
                                    </h2>
                                    @if ($unreadCount > 0)
                                        <form method="POST" action="{{ route('home.notifications.read') }}">
                                            @csrf
                                            <button type="submit" class="read-all-btn">すべて既読にする</button>
                                        </form>
                                    @endif
                                </div>
                                <ul class="notice-list">
                                    @forelse ($notifications as $n)
                                        <li class="{{ $n->read_at ? '' : 'is-unread' }}">
                                            <a href="{{ $n->link_url ?? '#' }}">
                                                <span class="notice-title">
                                                    @unless ($n->read_at)<span class="unread-dot" aria-hidden="true"></span>@endunless
                                                    {{ $n->title }}
                                                </span>
                                                @if ($n->body)
                                                    <span class="notice-body">{{ $n->body }}</span>
                                                @endif
                                                <span class="notice-time">{{ $n->created_at->format('n/j H:i') }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="notice-empty">新しいお知らせはありません。</li>
                                    @endforelse
                                </ul>
                            @endif
                        </div>
                    </section>

                    <!-- 中段：直近のイベント・締め切り（今日以降のみ） -->
                    <section class="card events-card">
                        <h2 class="card-title center">直近のイベント・締め切り</h2>
                        @if ($upcomingEvents->isEmpty())
                            <p class="list-empty">直近の予定はありません。</p>
                        @else
                            <ul class="events-list">
                                @foreach ($upcomingEvents as $event)
                                    <li>
                                        <span class="date">{{ $event['date'] }}({{ $event['weekday'] }})</span>
                                        <span class="time">{{ $event['time'] }}</span>
                                        <span class="event-title">{{ $event['title'] }}</span>
                                        @if ($event['is_new'])
                                            <span class="new-badge">New</span>
                                        @endif
                                        @if ($event['days_left'] <= 3)
                                            <span class="deadline-badge">{{ $event['days_left'] === 0 ? '今日' : 'あと' . $event['days_left'] . '日' }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a class="card-more-link" href="{{ route('event.calendar') }}">カレンダーを見る →</a>
                    </section>

                    @unless ($isTeacher)
                    <!-- 下段：自分の教室予約 ／ 欠席・遅刻届の状態 -->
                    <div class="home-bottom">
                        <section class="card reserve-card">
                            <h2 class="card-title">自分の教室予約</h2>
                            @if ($myReservations->isEmpty())
                                <p class="list-empty">予約はありません。</p>
                            @else
                                <ul class="reserve-list">
                                    @foreach ($myReservations as $r)
                                        <li>
                                            <span class="date">{{ $r['date'] }}</span>
                                            <span class="time">{{ $r['period'] }}</span>
                                            <span class="room">{{ $r['room'] }}</span>
                                            <span class="status-badge status-{{ $r['status'] }}">{{ $r['status_label'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a class="card-more-link" href="{{ route('classroom.reservation.list') }}">予約一覧を見る →</a>
                        </section>

                        <section class="card attendance-card">
                            <h2 class="card-title">欠席・遅刻届</h2>
                            @if ($myAttendance->isEmpty())
                                <p class="list-empty">提出中の届はありません。</p>
                            @else
                                <ul class="reserve-list">
                                    @foreach ($myAttendance as $a)
                                        <li>
                                            <span class="date">{{ \Illuminate\Support\Carbon::parse($a->target_date)->format('n/j') }}分</span>
                                            <span class="status-badge status-att-{{ $a->report_status === '受理' ? 'accepted' : ($a->report_status === '差し戻し' ? 'returned' : 'pending') }}">{{ $a->report_status }}</span>
                                            @if ($a->report_status === '差し戻し' && $a->return_comment)
                                                <span class="return-comment">{{ $a->return_comment }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a class="card-more-link" href="{{ route('notification') }}">届を提出する →</a>
                        </section>
                    </div>

                    <!-- 最下段：よく使う機能への近道（スマホではサイドバーが隠れるため） -->
                    <section class="quick-grid">
                        <a href="{{ route('classroom.reservation') }}" class="quick-item">🏫<span>教室を予約</span></a>
                        <a href="{{ route('notification') }}" class="quick-item">📝<span>届を提出</span></a>
                        <a href="{{ route('gakunai.qna') }}" class="quick-item">💬<span>質問する</span></a>
                        <a href="{{ route('nearby.shop') }}" class="quick-item">📍<span>近くのお店</span></a>
                    </section>
                    @endunless
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
