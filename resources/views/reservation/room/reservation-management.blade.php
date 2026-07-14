<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>空き教室予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/reservation/room/reservation-management.css','resources/css/app.css','resources/js/app.js'])
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
                <h1>予約管理</h1>
            </div>

            <section class="reservation-selection">
                <p class="section-description">予約一覧から確認したい予約を選択してください。</p>

                <div class="reservation-card-list">
                    <button type="button" class="reservation-card-btn" data-room="101c" data-date="2026/06/26" data-weekday="(金)" data-period="1限" data-time="(9:15〜10:45)" data-floor="1F" data-reason="就職活動の面接" data-user-id="234000" data-user-name="情報太郎">
                        <div class="reservation-card-label">予約 1</div>
                        <div class="reservation-card-detail">教室 101c</div>
                        <div class="reservation-card-detail">2026/06/26 (金) 1限</div>
                    </button>

                    <button type="button" class="reservation-card-btn" data-room="101c" data-date="2026/06/26" data-weekday="(金)" data-period="2限" data-time="(11:00〜12:30)" data-floor="1F" data-reason="ゼミの発表練習" data-user-id="234001" data-user-name="情報野郎">
                        <div class="reservation-card-label">予約 2</div>
                        <div class="reservation-card-detail">教室 101c</div>
                        <div class="reservation-card-detail">2026/06/26 (金) 2限</div>
                    </button>
                </div>
            </section>

            <div class="reservation-modal" id="reservationModal" aria-hidden="true">
                <div class="reservation-modal-backdrop"></div>
                <div class="reservation-modal-content">
                    <button type="button" class="modal-close-btn" id="reservationModalClose" aria-label="閉じる">×</button>
                    <div class="modal-heading">
                        <h2>予約確認</h2>
                        <div class="modal-user-box">
                            <span class="modal-user-id">234000</span>
                            <span class="modal-user-name">情報太郎</span>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>教室番号</label>
                        <input type="text" id="modalRoom" readonly>
                    </div>
                    <div class="modal-form-row">
                        <label>日付</label>
                        <input type="text" id="modalDate" readonly>
                    </div>
                    <div class="modal-form-row horizontal-row">
                        <div>
                            <label>時限</label>
                            <input type="text" id="modalPeriod" readonly>
                        </div>
                        <div>
                            <label>階数</label>
                            <input type="text" id="modalFloor" readonly>
                        </div>
                    </div>
                    <div class="modal-form-row">
                        <label>予約理由</label>
                        <textarea id="modalReason" rows="4" readonly></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="modal-btn reject">拒否</button>
                        <button type="button" class="modal-btn approve">承諾する</button>
                    </div>
                </div>
            </div>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('reservationModal');
                const closeButton = document.getElementById('reservationModalClose');
                const selectButtons = document.querySelectorAll('.reservation-card-btn');
                const modalRoom = document.getElementById('modalRoom');
                const modalDate = document.getElementById('modalDate');
                const modalPeriod = document.getElementById('modalPeriod');
                const modalFloor = document.getElementById('modalFloor');
                const modalReason = document.getElementById('modalReason');
                const modalUserId = document.querySelector('.modal-user-id');
                const modalUserName = document.querySelector('.modal-user-name');
                let currentSelectedButton = null;
                const reservationCardList = document.querySelector('.reservation-card-list');
                const reservationSelectionSection = document.querySelector('.reservation-selection');

                function openModal(button) {
                    if (!modal) return;
                    modalRoom.value = button.dataset.room || '';
                    modalDate.value = `${button.dataset.date || ''} ${button.dataset.weekday || ''}`.trim();
                    modalPeriod.value = `${button.dataset.period || ''} ${button.dataset.time || ''}`.trim();
                    modalFloor.value = button.dataset.floor || '';
                    modalReason.value = button.dataset.reason || '';
                    modalUserId.textContent = button.dataset.userId || '';
                    modalUserName.textContent = button.dataset.userName || '';
                    // track which button opened the modal
                    currentSelectedButton = button;
                    // remove empty-state message while selecting
                    const existingEmpty = reservationSelectionSection.querySelector('.no-reservations');
                    if (existingEmpty) existingEmpty.remove();
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                }

                function closeModal() {
                    if (!modal) return;
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                }

                selectButtons.forEach(btn => {
                    btn.addEventListener('click', () => openModal(btn));
                });

                // approve / reject handlers: remove the selected card from the list
                const approveBtn = document.querySelector('.modal-btn.approve');
                const rejectBtn = document.querySelector('.modal-btn.reject');

                function removeCurrentSelection() {
                    if (currentSelectedButton && currentSelectedButton.parentNode) {
                        currentSelectedButton.remove();
                        currentSelectedButton = null;
                    }
                    // if no reservations remain, show empty state
                    if (reservationCardList && reservationCardList.querySelectorAll('.reservation-card-btn').length === 0) {
                        const empty = document.createElement('p');
                        empty.className = 'no-reservations';
                        empty.textContent = '現在予約している教室はありません';
                        reservationSelectionSection.appendChild(empty);
                    }
                }

                approveBtn?.addEventListener('click', () => {
                    // TODO: send approval to backend via fetch if desired
                    removeCurrentSelection();
                    closeModal();
                });

                rejectBtn?.addEventListener('click', () => {
                    // TODO: send rejection to backend via fetch if desired
                    removeCurrentSelection();
                    closeModal();
                });

                closeButton?.addEventListener('click', closeModal);

                modal?.addEventListener('click', event => {
                    if (event.target === modal || event.target.classList.contains('reservation-modal-backdrop')) {
                        closeModal();
                    }
                });
            });
        </script>
    </div>
</body>
</html>