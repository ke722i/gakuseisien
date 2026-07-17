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

            @if (session('reservation_success'))
                <p class="flash-message flash-success">{{ session('reservation_success') }}</p>
            @endif

            <section class="reservation-selection">
                <p class="section-description">予約一覧から確認したい予約を選択してください。</p>

                <div class="reservation-card-list">
                    @forelse ($reservations as $index => $reservation)
                        <button type="button" class="reservation-card-btn"
                            data-id="{{ $reservation['id'] }}"
                            data-room="{{ $reservation['room'] }}"
                            data-date="{{ $reservation['date'] }}"
                            data-weekday="{{ $reservation['weekday'] }}"
                            data-period="{{ $reservation['period'] }}"
                            data-time="{{ $reservation['time'] }}"
                            data-floor="{{ $reservation['floor'] }}"
                            data-reason="{{ $reservation['reason'] }}"
                            data-user-id="{{ $reservation['user_id'] }}"
                            data-user-name="{{ $reservation['user_name'] }}">
                            <div class="reservation-card-label">予約 {{ $index + 1 }}</div>
                            <div class="reservation-card-detail">教室 {{ $reservation['room'] }}</div>
                            <div class="reservation-card-detail">{{ $reservation['date'] }} {{ $reservation['weekday'] }} {{ $reservation['period'] }}</div>
                        </button>
                    @empty
                        <p class="no-reservations">現在承認待ちの予約はありません</p>
                    @endforelse
                </div>
            </section>

            <div class="reservation-modal" id="reservationModal" aria-hidden="true">
                <div class="reservation-modal-backdrop"></div>
                <div class="reservation-modal-content">
                    <button type="button" class="modal-close-btn" id="reservationModalClose" aria-label="閉じる">×</button>
                    <div class="modal-heading">
                        <h2>予約確認</h2>
                        <div class="modal-user-box">
                            <span class="modal-user-id"></span>
                            <span class="modal-user-name"></span>
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
                        <form method="POST" id="rejectForm" action="">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="modal-btn reject">拒否</button>
                        </form>
                        <form method="POST" id="approveForm" action="">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="modal-btn approve">承諾する</button>
                        </form>
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
                const approveForm = document.getElementById('approveForm');
                const rejectForm = document.getElementById('rejectForm');

                function openModal(button) {
                    if (!modal) return;
                    modalRoom.value = button.dataset.room || '';
                    modalDate.value = `${button.dataset.date || ''} ${button.dataset.weekday || ''}`.trim();
                    modalPeriod.value = `${button.dataset.period || ''} ${button.dataset.time || ''}`.trim();
                    modalFloor.value = button.dataset.floor || '';
                    modalReason.value = button.dataset.reason || '';
                    modalUserId.textContent = button.dataset.userId || '';
                    modalUserName.textContent = button.dataset.userName || '';
                    // 承諾/拒否フォームの送信先を選択した予約IDに合わせる
                    approveForm.action = `{{ url('/classroom-reservation') }}/${button.dataset.id}/approve`;
                    rejectForm.action = `{{ url('/classroom-reservation') }}/${button.dataset.id}/reject`;
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