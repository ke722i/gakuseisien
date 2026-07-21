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

            {{-- 完了メッセージは共通ポップアップ（partials/toast）で表示する --}}

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
                                <button type="button" class="change-button"
                                    onclick='openReservationEdit(@json($reservation))'>変更</button>
                                <form method="POST" action="{{ route('classroom.reservation.destroy', $reservation['id']) }}" data-confirm="この予約を削除しますか？">
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

    <!-- 予約変更モーダル -->
    <div class="resv-modal" id="resvEditModal">
        <div class="resv-modal-box">
            <div class="resv-modal-head">
                <h2>予約の変更</h2>
                <button type="button" class="resv-modal-close" onclick="closeReservationEdit()">×</button>
            </div>
            <form method="POST" id="resvEditForm">
                @csrf
                @method('PATCH')
                <div class="resv-field">
                    <label>教室</label>
                    <select name="room_id" id="resvRoom" required>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->floor }}階 {{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="resv-field">
                    <label>日付</label>
                    <input type="date" name="reservation_date" id="resvDate" min="{{ now()->toDateString() }}" required>
                </div>
                <div class="resv-field">
                    <label>時限</label>
                    <select name="period" id="resvPeriod" required>
                        @for ($p = 1; $p <= 6; $p++)<option value="{{ $p }}">{{ $p }}限</option>@endfor
                    </select>
                </div>
                <div class="resv-field">
                    <label>予約理由</label>
                    <input type="text" name="reason" id="resvReason" placeholder="任意">
                </div>
                <p class="resv-note">※ 変更すると再度、教師の承認待ちになります。</p>
                <div class="resv-modal-actions">
                    <button type="button" class="resv-btn-cancel" onclick="closeReservationEdit()">閉じる</button>
                    <button type="submit" class="resv-btn-submit">変更する</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const resvBaseUrl = "{{ url('classroom-reservation') }}";
        function openReservationEdit(r) {
            const form = document.getElementById('resvEditForm');
            form.action = resvBaseUrl + '/' + r.id;
            document.getElementById('resvRoom').value = r.room_id;
            document.getElementById('resvDate').value = r.raw_date;
            document.getElementById('resvPeriod').value = r.raw_period;
            document.getElementById('resvReason').value = r.reason || '';
            document.getElementById('resvEditModal').classList.add('open');
        }
        function closeReservationEdit() {
            document.getElementById('resvEditModal').classList.remove('open');
        }
        document.getElementById('resvEditModal').addEventListener('click', (e) => {
            if (e.target.id === 'resvEditModal') closeReservationEdit();
        });
    </script>
</body>
</html>
