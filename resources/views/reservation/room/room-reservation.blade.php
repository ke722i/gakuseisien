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

            {{-- 完了メッセージは共通ポップアップ（partials/toast）で表示する --}}
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
                    <svg viewBox="0 0 900 560" xmlns="http://www.w3.org/2000/svg" id="floorMapSvg">
                        @foreach ($rooms as $room)
                            @php
                                // 位置は DB の列（pos_x/pos_y/width/height）を使う
                                $shape = ['x' => $room->pos_x, 'y' => $room->pos_y, 'w' => $room->width, 'h' => $room->height];
                                $isReserved = in_array($room->id, $reservedRoomIds, true);
                                $isUnavailable = in_array($room->id, $unavailableRoomIds ?? [], true);
                                $blocked = $isReserved || $isUnavailable;
                                $stateClass = ! $room->is_reservable
                                    ? 'floor-room--utility'
                                    : ($blocked ? 'floor-room--reserved' : 'floor-room--available');
                            @endphp
                            <g class="floor-room {{ $stateClass }}"
                                @if ($room->is_reservable && ! $blocked)
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
                                <text x="{{ $shape['x'] + $shape['w'] / 2 }}" y="{{ $shape['y'] + $shape['h'] / 2 }}" text-anchor="middle" dominant-baseline="middle" @if ($isVertical) class="label-vertical" @endif>{{ $room->name }}@if ($isUnavailable) <tspan class="unavailable-mark">（利用不可）</tspan>@endif</text>
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
