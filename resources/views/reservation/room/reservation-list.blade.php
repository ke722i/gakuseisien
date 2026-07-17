<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>空き教室予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/reservation/room/reservation-list.css','resources/css/app.css','resources/js/app.js'])
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
                <h1>予約一覧</h1>
            </div>

            @if (session('reservation_success'))
                <p class="flash-message flash-success">{{ session('reservation_success') }}</p>
            @endif

            <section class="reservation-list">
                @php($reservations = $reservations ?? [])

                @forelse($reservations as $reservation)
                    <article class="reservation-card status-{{ $reservation['status'] ?? 'pending' }}">
                        <div class="card-top">
                            <div>
                                <div class="room-title">教室 <strong>{{ $reservation['room'] ?? '未定' }}</strong></div>
                                <div class="reservation-meta">
                                    <p><span>日付</span> {{ $reservation['date'] ?? '----/--/--' }} {{ $reservation['weekday'] ?? '' }}</p>
                                    <p><span>時刻</span> {{ $reservation['period'] ?? '' }} {{ $reservation['time'] ?? '' }}</p>
                                </div>
                            </div>
                            <div class="card-controls">
                                <div class="reservation-status">{{ $reservation['status_label'] ?? '承認待ち' }}</div>
                                <form method="POST" action="{{ route('classroom.reservation.destroy', $reservation['id']) }}" onsubmit="return confirm('この予約を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button">削除</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <p>現在予約している教室はありません</p>
                    </div>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
