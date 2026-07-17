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

            @if (session('reservation_success'))
                <p class="flash-message flash-success">{{ session('reservation_success') }}</p>
            @endif
            @if (session('reservation_error'))
                <p class="flash-message flash-error">{{ session('reservation_error') }}</p>
            @endif
            @if ($errors->any())
                <ul class="flash-message flash-error">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <!-- フィルターセクション -->
            <form class="filter-section" method="GET" action="{{ route('classroom.reservation.room') }}">
                <div class="filter-group">
                    <label for="date-input">日付</label>
                    <input type="date" id="date-input" name="date" value="{{ $date }}">
                </div>

                <div class="filter-group">
                    <label for="time-select">時間</label>
                    <select id="time-select" name="period">
                        @foreach ([1 => '1限', 2 => '2限', 3 => '3限', 4 => '4限', 5 => '5限', 6 => '6限'] as $value => $label)
                            <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="floor-select">階数</label>
                    <select id="floor-select" name="floor">
                        @foreach ([1, 2, 3, 4, 5, 6] as $value)
                            <option value="{{ $value }}" {{ $floor === $value ? 'selected' : '' }}>{{ $value }}階</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="filter-button" aria-label="表示を更新">🔍</button>
            </form>

            @php
                // 当日は黄/赤、翌日以降は緑/灰の配色にする
                $isFutureDate = $date > now()->toDateString();
            @endphp

            <!-- フロアマップ -->
            <div class="room-layout {{ $isFutureDate ? 'palette-future' : '' }}">
                @if ($rooms->isEmpty())
                    <div class="room-layout-empty">
                        <p>{{ $floor }}階の情報は準備中です。</p>
                    </div>
                @else
                    <div class="floor-legend">
                        <span class="legend-item"><span class="legend-box legend-box--available"></span>＝予約可能</span>
                        <span class="legend-item"><span class="legend-box legend-box--reserved"></span>＝予約不可</span>
                    </div>
                    @php
                        // 添付画像のレイアウトに合わせた各階の部屋の表示位置
                        $roomShapesByFloor = [
                            1 => [
                                '101c' => ['x' => 100, 'y' => 40, 'w' => 260, 'h' => 330],
                                '会議室1' => ['x' => 460, 'y' => 40, 'w' => 180, 'h' => 140],
                                '会議室2' => ['x' => 650, 'y' => 40, 'w' => 180, 'h' => 140],
                                '職員室' => ['x' => 460, 'y' => 220, 'w' => 280, 'h' => 230],
                                '物置' => ['x' => 100, 'y' => 390, 'w' => 120, 'h' => 110],
                                'EV' => ['x' => 230, 'y' => 390, 'w' => 90, 'h' => 110],
                                '階段' => ['x' => 330, 'y' => 390, 'w' => 110, 'h' => 110],
                            ],
                            2 => [
                                '202c' => ['x' => 100, 'y' => 40, 'w' => 250, 'h' => 115],
                                '203c' => ['x' => 100, 'y' => 155, 'w' => 250, 'h' => 115],
                                '理事長室' => ['x' => 100, 'y' => 270, 'w' => 250, 'h' => 140],
                                '201c' => ['x' => 390, 'y' => 40, 'w' => 220, 'h' => 250],
                                '事務室' => ['x' => 630, 'y' => 40, 'w' => 170, 'h' => 140],
                                '保健室' => ['x' => 630, 'y' => 180, 'w' => 110, 'h' => 45],
                                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                                '女子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
                            ],
                            3 => [
                                '301' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                                '302' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                                '303' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                                '304c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                                '305' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
                            ],
                            4 => [
                                '403c' => ['x' => 110, 'y' => 40, 'w' => 235, 'h' => 250],
                                '402c' => ['x' => 345, 'y' => 40, 'w' => 225, 'h' => 220],
                                '401c' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 185],
                                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 58, 'h' => 120],
                                '女子トイレ' => ['x' => 615, 'y' => 380, 'w' => 58, 'h' => 120],
                            ],
                            5 => [
                                '501' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                                '502' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                                '503' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                                '504c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                                '505' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                                '女子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
                            ],
                            6 => [
                                '601' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                                '602' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                                '603' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                                '604c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                                '605' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
                            ],
                        ];
                        $roomShapes = $roomShapesByFloor[$floor] ?? [];
                    @endphp
                    <svg viewBox="0 0 900 560" xmlns="http://www.w3.org/2000/svg" id="floorMapSvg">
                        @foreach ($rooms as $room)
                            @php
                                $shape = $roomShapes[$room->room_code] ?? ['x' => 0, 'y' => 0, 'w' => 100, 'h' => 100];
                                $isReserved = in_array($room->id, $reservedRoomIds, true);
                                $stateClass = ! $room->is_reservable
                                    ? 'floor-room--utility'
                                    : ($isReserved ? 'floor-room--reserved' : 'floor-room--available');
                            @endphp
                            <g class="floor-room {{ $stateClass }}"
                                @if ($room->is_reservable && ! $isReserved)
                                    data-room-id="{{ $room->id }}"
                                    data-room-name="{{ $room->name }}"
                                    tabindex="0"
                                    role="button"
                                @endif
                            >
                                @php
                                    // 幅が狭く縦長の部屋（PS・トイレなど）はラベルを縦書きにする
                                    $isVertical = $shape['w'] < 65 && $shape['h'] > $shape['w'];
                                @endphp
                                <rect x="{{ $shape['x'] }}" y="{{ $shape['y'] }}" width="{{ $shape['w'] }}" height="{{ $shape['h'] }}" rx="6"></rect>
                                <text x="{{ $shape['x'] + $shape['w'] / 2 }}" y="{{ $shape['y'] + $shape['h'] / 2 }}" text-anchor="middle" dominant-baseline="middle" @if ($isVertical) class="label-vertical" @endif>{{ $room->name }}</text>
                            </g>
                        @endforeach
                    </svg>

                @endif
            </div>
        </main>

        <!-- 予約確認モーダル -->
        <div class="reservation-modal" id="bookingModal" aria-hidden="true">
            <div class="reservation-modal-backdrop"></div>
            <div class="reservation-modal-content">
                <h2>予約確認</h2>

                <form method="POST" action="{{ route('classroom.reservation.store') }}">
                    @csrf
                    <input type="hidden" name="room_id" id="bookingRoomId">
                    <input type="hidden" name="reservation_date" id="bookingDateInput" value="{{ $date }}">
                    <input type="hidden" name="period" id="bookingPeriodInput" value="{{ $period }}">

                    <div class="modal-form-row">
                        <label>教室番号</label>
                        <input type="text" id="bookingRoomLabel" readonly>
                    </div>
                    <div class="modal-form-row">
                        <label>日付</label>
                        <input type="text" id="bookingDateLabel" value="{{ $date }}" readonly>
                    </div>
                    <div class="modal-form-row horizontal-row">
                        <div>
                            <label>時限</label>
                            <input type="text" id="bookingPeriodLabel" value="{{ $period }}限" readonly>
                        </div>
                        <div>
                            <label>階数</label>
                            <input type="text" value="{{ $floor }}F" readonly>
                        </div>
                    </div>
                    <div class="modal-form-row">
                        <label>予約理由</label>
                        <textarea name="reason" rows="4" placeholder="例：就職活動の面接"></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="modal-btn cancel" id="bookingModalCancel">キャンセル</button>
                        <button type="submit" class="modal-btn submit">予約する</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('bookingModal');
                const roomIdInput = document.getElementById('bookingRoomId');
                const roomLabel = document.getElementById('bookingRoomLabel');
                const cancelBtn = document.getElementById('bookingModalCancel');

                function openModal(roomId, roomName) {
                    roomIdInput.value = roomId;
                    roomLabel.value = roomName;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                }

                function closeModal() {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                }

                document.querySelectorAll('.floor-room[data-room-id]').forEach(function (el) {
                    el.addEventListener('click', function () {
                        openModal(el.dataset.roomId, el.dataset.roomName);
                    });
                    el.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            openModal(el.dataset.roomId, el.dataset.roomName);
                        }
                    });
                });

                cancelBtn?.addEventListener('click', closeModal);
                modal?.addEventListener('click', function (event) {
                    if (event.target === modal || event.target.classList.contains('reservation-modal-backdrop')) {
                        closeModal();
                    }
                });

                // フィルター（日付・時限・階数）を変更したら自動で再表示
                ['date-input', 'time-select', 'floor-select'].forEach(function (id) {
                    document.getElementById(id)?.addEventListener('change', function () {
                        this.form.submit();
                    });
                });

                @if ($errors->any())
                    // バリデーションエラーで戻ってきた場合はモーダルを開き直す
                    openModal('{{ old('room_id') }}', '');
                @endif
            });
        </script>
    </div>
</body>
</html>
