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
                    <label for="room-select">教室番号</label>
                    <select id="room-select">
                        <option value="101c">101c</option>
                        <option value="201c">201c</option>
                        <option value="202c">202c</option>
                        <option value="203c">203c</option>
                        <option value="301">301</option>
                        <option value="302">302</option>
                        <option value="303">303</option>
                        <option value="304c">304c</option>
                        <option value="305">305</option>
                        <option value="401c">401c</option>
                        <option value="402c">402c</option>
                        <option value="403c">403c</option>
                        <option value="501">501</option>
                        <option value="502">502</option>
                        <option value="503c">503c</option>
                        <option value="504c">504c</option>
                        <option value="505">505</option>
                        <option value="601">601</option>
                        <option value="602">602</option>
                        <option value="603c">603c</option>
                        <option value="604c">604c</option>
                    </select>
                </div>

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
            </div>

            <div class="bottom-actions">
                <button type="button" id="reserveButton" class="primary">登録</button>
            </div>
        </main>
    </div>

    <div class="reservation-modal" id="reservationConfirmModal" aria-hidden="true">
        <div class="modal-backdrop"></div>
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="reservationModalTitle">
            <h2 id="reservationModalTitle">予約確認</h2>

            <div class="modal-field">
                <label>教室番号</label>
                <input type="text" id="confirmRoom" readonly>
            </div>
            <div class="modal-field">
                <label>日付</label>
                <input type="text" id="confirmDate" readonly>
            </div>
            <div class="modal-field">
                <label>時間</label>
                <input type="text" id="confirmTime" readonly>
            </div>
            <div class="modal-field">
                <label>階数</label>
                <input type="text" id="confirmFloor" readonly>
            </div>
            <div class="modal-field">
                <label for="reservationReason">予約理由</label>
                <textarea id="reservationReason" rows="4" placeholder="予約理由を入力してください"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" id="cancelReserve" class="cancel">キャンセル</button>
                <button type="button" id="confirmReserve" class="confirm">予約する</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reserveButton = document.getElementById('reserveButton');
            const confirmModal = document.getElementById('reservationConfirmModal');
            const cancelReserve = document.getElementById('cancelReserve');
            const confirmReserve = document.getElementById('confirmReserve');

            const dateInput = document.getElementById('date-input');
            const timeSelect = document.getElementById('time-select');
            const floorSelect = document.getElementById('floor-select');
            const roomSelect = document.getElementById('room-select');

            const confirmRoom = document.getElementById('confirmRoom');
            const confirmDate = document.getElementById('confirmDate');
            const confirmTime = document.getElementById('confirmTime');
            const confirmFloor = document.getElementById('confirmFloor');
            const reservationReason = document.getElementById('reservationReason');

            function openConfirmModal() {
                confirmRoom.value = roomSelect?.value || '101c';
                confirmDate.value = dateInput.value || '';
                confirmTime.value = timeSelect.options[timeSelect.selectedIndex]?.text || '';
                confirmFloor.value = floorSelect.options[floorSelect.selectedIndex]?.text || '';
                reservationReason.value = '';

                confirmModal.classList.add('open');
                confirmModal.setAttribute('aria-hidden', 'false');
            }

            function closeConfirmModal() {
                confirmModal.classList.remove('open');
                confirmModal.setAttribute('aria-hidden', 'true');
            }

            reserveButton.addEventListener('click', function() {
                if (!dateInput.value) {
                    alert('日付を入力してください');
                    return;
                }
                if (timeSelect.selectedIndex < 0) {
                    alert('時間を入力してください');
                    return;
                }
                if (floorSelect.selectedIndex < 0) {
                    alert('階数を入力してください');
                    return;
                }
                openConfirmModal();
            });

            cancelReserve.addEventListener('click', closeConfirmModal);

            confirmReserve.addEventListener('click', function() {
                if (!reservationReason.value.trim()) {
                    alert('予約理由を入力してください');
                    reservationReason.focus();
                    return;
                }

                console.log('予約確定', {
                    room: confirmRoom.value,
                    date: confirmDate.value,
                    time: confirmTime.value,
                    floor: confirmFloor.value,
                    reason: reservationReason.value.trim()
                });

                closeConfirmModal();
                alert('予約を送信しました');
            });
        });
    </script>
</body>
</html>
