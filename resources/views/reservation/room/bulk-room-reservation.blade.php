<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>教室一括予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/reservation/room/bulk-room-reservation.css','resources/css/app.css','resources/js/app.js'])
</head>

<body>
    <div class="app-layout">
        @include('partials.sidebar', ['active' => 'reservation'])

        <main class="content">
            <div class="header">
                <a href="javascript:history.back()" class="back-button">←</a>
                <h1>教室一括予約</h1>
            </div>

            <section class="bulk-form">
                <div class="form-row">
                    <div class="form-item">
                        <label>教室番号・施設名の入力</label>
                        {{-- 予約可能な教室はDBから生成する（直書きするとDBと食い違い、選んでも登録されない） --}}
                        <select id="roomSelect">
                            @foreach ($reservableRooms as $room)
                                <option value="{{ $room->name }}">{{ $room->floor }}階 {{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-item">
                        <label>授業名（必須）</label>
                        <input type="text" id="usageInput" placeholder="総合演習">
                    </div>
                </div>

                <div class="periods">
                    <label>コマ（時間）を選択</label>
                    <div class="period-list">
                        <button type="button" class="period-btn" data-label="1限 9:15-10:45">1限<br><span>9:15-10:45</span></button>
                        <button type="button" class="period-btn" data-label="2限 11:00-12:30">2限<br><span>11:00-12:30</span></button>
                        <button type="button" class="period-btn" data-label="3限 13:30-15:00">3限<br><span>13:30-15:00</span></button>
                        <button type="button" class="period-btn" data-label="4限 15:15-16:45">4限<br><span>15:15-16:45</span></button>
                        <button type="button" class="period-btn" data-label="5限 17:00-18:30">5限<br><span>17:00-18:30</span></button>
                    </div>
                </div>

                <div class="weekday-range">
                    <div class="weekday-toggle">
                        <label>曜日・繰返し指定</label>
                        <div class="weekday-list">
                            <button type="button" class="weekday-btn" data-day="月">月</button>
                            <button type="button" class="weekday-btn" data-day="火">火</button>
                            <button type="button" class="weekday-btn" data-day="水">水</button>
                            <button type="button" class="weekday-btn" data-day="木">木</button>
                            <button type="button" class="weekday-btn" data-day="金">金</button>
                            <button type="button" class="weekday-btn" data-day="土">土</button>
                            <button type="button" class="weekday-btn" data-day="日">日</button>
                        </div>
                    </div>

                    <div class="date-range">
                        <label>予約期間</label>
                        <div class="date-inputs">
                            <input type="date" id="fromDate">
                            <span class="tilde">〜</span>
                            <input type="date" id="toDate">
                            <button type="button" id="addToList" class="primary">一覧に追加</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="list-review">
                <div class="list-header">
                    <h2>登録内容の確認 <span id="itemCount">0件</span></h2>
                </div>

                <div class="table-wrap">
                    <table class="reserve-table">
                        <thead>
                            <tr>
                                <th>教室</th>
                                <th>授業名</th>
                                <th>時限</th>
                                <th>期間・曜日</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="reserveTbody">
                            <!-- rows inserted here -->
                        </tbody>
                    </table>
                </div>

                <div class="list-actions">
                    <button type="button" id="clearList">クリア</button>
                    <button type="button" id="bulkRegister" class="primary">一括登録する</button>
                </div>
            </section>
        </main>

        </main>

        <!-- 登録確認モーダル -->
        <div class="conflict-modal success-modal" id="confirmModal" aria-hidden="true">
            <div class="conflict-backdrop"></div>

            <div class="conflict-box" role="dialog" aria-modal="true">
                <div class="success-header">
                    <div class="success-icon" aria-hidden="true">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11" fill="#2ecc71"/>
                            <path d="M7.5 12.5L10.2 15.2L16.5 8.9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="success-title">登録内容を確認してください。</h3>
                </div>

                <div class="success-body">
                    <p class="muted">指定した教室・時間帯はすべて空いています。<br>重複する予約はありません。</p>
                    <p>以下の内容で登録しますか？</p>
                </div>

                <div class="conflict-actions">
                    <button id="confirmCancel" class="conflict-btn cancel">戻る</button>
                    <button id="confirmRegister" class="conflict-btn overwrite">登録する</button>
                </div>
            </div>
        </div>

        <!-- 重複確認モーダル -->
        <div class="conflict-modal" id="conflictModal" aria-hidden="true">
            <div class="conflict-backdrop"></div>

            <div class="conflict-box" role="dialog" aria-modal="true">
                <button
                    id="conflictClose"
                    aria-label="閉じる"
                    style="position:absolute;right:12px;top:8px;border:none;background:none;font-size:18px;cursor:pointer;">
                </button>
                <h3>予約の重複が見つかりました。</h3>
                <p>
                    指定した教室・時間帯には、すでに学生の予約が入っています。<br>
                    上書きすると既存の予約はキャンセルされます。よろしいですか？
                </p>

                <div class="conflict-list" id="conflictList"></div>

                <div class="conflict-actions">
                    <button class="conflict-btn cancel" id="conflictCancel">
                        戻る
                    </button>

                    <button class="conflict-btn overwrite" id="conflictOverwrite">
                        上書きして登録する
                    </button>
                </div>
            </div>
        </div>

        <script>
            // 重複プレチェック用の既存予約（今日以降・却下以外）をサーバーから受け取る
            const existingReservations = @json($existingReservations ?? []);

            document.addEventListener('DOMContentLoaded', function() {
                const periodBtns = document.querySelectorAll('.period-btn');
                const weekdayBtns = document.querySelectorAll('.weekday-btn');
                const addToListBtn = document.getElementById('addToList');
                const reserveTbody = document.getElementById('reserveTbody');
                const itemCount = document.getElementById('itemCount');
                const clearListBtn = document.getElementById('clearList');
                const bulkRegisterBtn = document.getElementById('bulkRegister');

                function updateCount() {
                    const count = reserveTbody.querySelectorAll('tr').length;
                    itemCount.textContent = count + '件';
                }

                periodBtns.forEach(b => b.addEventListener('click', () => b.classList.toggle('active')));
                weekdayBtns.forEach(b => b.addEventListener('click', () => b.classList.toggle('active')));

                function normalizeDate(value) {
                    return value.replace(/\//g, '-').split('T')[0];
                }

                function getRequestedDates(fromDate, toDate, selectedDays) {
                    if (!fromDate || !toDate || selectedDays.length === 0) return [];

                    const start = new Date(normalizeDate(fromDate));
                    const end = new Date(normalizeDate(toDate));
                    const dayNames = ['日', '月', '火', '水', '木', '金', '土'];
                    const selectedSet = new Set(selectedDays);
                    const dates = [];
                    const current = new Date(start);

                    while (current <= end) {
                        const year = current.getFullYear();
                        const month = String(current.getMonth() + 1).padStart(2, '0');
                        const day = String(current.getDate()).padStart(2, '0');
                        const dateKey = `${year}-${month}-${day}`;

                        if (selectedSet.has(dayNames[current.getDay()])) {
                            dates.push(dateKey);
                        }

                        current.setDate(current.getDate() + 1);
                    }

                    return dates;
                }

                function collectRowData(row) {
                    return {
                        room: row.dataset.room || row.querySelector('td')?.textContent.trim() || '',
                        usage: row.dataset.usage || row.querySelectorAll('td')[1]?.textContent.trim() || '',
                        periods: JSON.parse(row.dataset.periods || '[]'),
                        fromDate: row.dataset.fromDate || '',
                        toDate: row.dataset.toDate || '',
                        selectedDays: JSON.parse(row.dataset.selectedDays || '[]')
                    };
                }

                function buildConflictDetails(candidate, existingRows = []) {
                    const requestedDates = getRequestedDates(candidate.fromDate, candidate.toDate, candidate.selectedDays);
                    const conflicts = [];
                    const seen = new Set();

                    existingReservations.forEach(ex => {
                        if (ex.room !== candidate.room) return;
                        if (!requestedDates.includes(normalizeDate(ex.date))) return;
                        if (!candidate.periods.includes(ex.period)) return;

                        const key = `${candidate.room}|${normalizeDate(ex.date)}|${ex.period}`;
                        if (seen.has(key)) return;

                        seen.add(key);
                        conflicts.push({
                            room: candidate.room,
                            date: normalizeDate(ex.date),
                            period: ex.period,
                            request: candidate,
                            existing: ex
                        });
                    });

                    existingRows.forEach(existingRow => {
                        if (existingRow.room !== candidate.room) return;

                        const existingDates = getRequestedDates(existingRow.fromDate, existingRow.toDate, existingRow.selectedDays);
                        const overlapDates = existingDates.filter(date => requestedDates.includes(date));
                        if (overlapDates.length === 0) return;

                        const overlapPeriods = existingRow.periods.filter(period => candidate.periods.includes(period));
                        if (overlapPeriods.length === 0) return;

                        overlapDates.forEach(date => {
                            overlapPeriods.forEach(period => {
                                const key = `${candidate.room}|${date}|${period}`;
                                if (seen.has(key)) return;

                                seen.add(key);
                                conflicts.push({
                                    room: candidate.room,
                                    date,
                                    period,
                                    request: candidate,
                                    existing: {
                                        room: existingRow.room,
                                        date,
                                        period,
                                        userName: existingRow.usage,
                                        userType: '登録済み'
                                    }
                                });
                            });
                        });
                    });

                    return conflicts;
                }

                function hasConflict(candidate, existingRows = []) {
                    return buildConflictDetails(candidate, existingRows).length > 0;
                }

                addToListBtn.addEventListener('click', () => {
                    const room = document.getElementById('roomSelect').value;
                    const usage = document.getElementById('usageInput').value.trim();
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;
                    const selectedPeriods = Array.from(periodBtns).filter(p => p.classList.contains('active')).map(p => p.dataset.label);
                    const selectedDays = Array.from(weekdayBtns).filter(d => d.classList.contains('active')).map(d => d.dataset.day);

                    if (!usage) {
                        showToast('授業名を入力してください', 'error');
                        return;
                    }
                    if (selectedPeriods.length === 0) {
                        showToast('時限を1つ以上選択してください', 'error');
                        return;
                    }
                    if (!fromDate || !toDate) {
                        showToast('予約期間を入力してください', 'error');
                        return;
                    }
                    if (selectedDays.length === 0) {
                        showToast('曜日を1つ以上選択してください', 'error');
                        return;
                    }

                    const candidate = {
                        room,
                        usage,
                        periods: selectedPeriods,
                        fromDate,
                        toDate,
                        selectedDays
                    };

                    const tr = document.createElement('tr');
                    tr.dataset.room = room;
                    tr.dataset.usage = usage;
                    tr.dataset.fromDate = fromDate;
                    tr.dataset.toDate = toDate;
                    tr.dataset.periods = JSON.stringify(selectedPeriods);
                    tr.dataset.selectedDays = JSON.stringify(selectedDays);
                    tr.innerHTML = `
                        <td>${room}</td>
                        <td>${usage}</td>
                        <td>${selectedPeriods.join('<br>')}</td>
                        <td>${fromDate}〜${toDate} <br> ${selectedDays.join('・')}</td>
                        <td><button type="button" class="row-delete">削除</button></td>
                    `;
                    reserveTbody.appendChild(tr);
                    updateCount();

                    tr.querySelector('.row-delete').addEventListener('click', () => {
                        tr.remove();
                        updateCount();
                    });
                });

                clearListBtn.addEventListener('click', () => {
                    reserveTbody.innerHTML = '';
                    updateCount();
                });

                function findConflicts(rows) {
                    const conflicts = [];
                    const seen = new Set();

                    rows.forEach((r, idx) => {
                        const detailConflicts = buildConflictDetails(r, rows.filter((_, otherIdx) => otherIdx !== idx));

                        detailConflicts.forEach(detail => {
                            const key = `${idx}|${detail.date}|${detail.period}|${detail.existing.userName || ''}`;
                            if (seen.has(key)) return;

                            seen.add(key);
                            conflicts.push({
                                rowIndex: idx,
                                request: r,
                                existing: detail.existing
                            });
                        });
                    });

                    return conflicts;
                }

                bulkRegisterBtn.addEventListener('click', () => {
                    const rows = Array.from(reserveTbody.querySelectorAll('tr')).map(collectRowData);

                    if (rows.length === 0) {
                        showToast('登録する項目がありません', 'error');
                        return;
                    }

                    const conflicts = findConflicts(rows);

                    // 重複あり
                    if (conflicts.length > 0) {

                        const list = document.getElementById('conflictList');
                        list.innerHTML = '';

                        conflicts.forEach(c => {
                            const li = document.createElement('div');
                            li.className = 'conflict-item';
                            li.innerHTML = `
                <strong>${c.existing.room}教室 ・ ${c.existing.date} ・ ${c.existing.period}</strong>
                <div class="conflict-note">
                    既存予約：${c.existing.userName}（${c.existing.userType}）
                </div>
            `;
                            list.appendChild(li);
                        });

                        conflictModal.dataset.conflicts = JSON.stringify(conflicts);
                        conflictModal.classList.add('is-open');
                        return;
                    }

                    // 重複なし
                    console.log('一括登録データ', rows);

                    // 登録データを保持
                    confirmModal.dataset.rows = JSON.stringify(rows);

                    // 登録確認モーダル表示
                    confirmModal.classList.add('is-open');
                });


                //======================
                // モーダル関係
                //======================

                // 登録確認モーダル
                const confirmModal = document.getElementById("confirmModal");
                const confirmCancel = document.getElementById("confirmCancel");
                const confirmRegister = document.getElementById("confirmRegister");

                // 重複確認モーダル
                const conflictModal = document.getElementById('conflictModal');
                const conflictClose = document.getElementById('conflictClose');
                const conflictCancel = document.getElementById('conflictCancel');
                const conflictOverwrite = document.getElementById('conflictOverwrite');


                // 登録確認モーダル
                confirmCancel?.addEventListener("click", () => {
                    confirmModal.classList.remove("is-open");
                });

                confirmRegister?.addEventListener("click", async () => {

                    const rows = JSON.parse(confirmModal.dataset.rows || "[]");
                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    confirmRegister.disabled = true;

                    try {
                        const res = await fetch("{{ route('classroom.reservation.bulk.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": token,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ rows })
                        });

                        const data = await res.json();
                        confirmModal.classList.remove("is-open");

                        if (res.ok) {
                            reserveTbody.innerHTML = '';
                            updateCount();
                            let msg = `一括登録を完了しました（登録 ${data.created} 件`;
                            if (data.cancelled > 0) msg += `、重複する学生予約 ${data.cancelled} 件を自動キャンセル`;
                            if (data.skipped_rooms && data.skipped_rooms.length) msg += `、未登録の教室: ${data.skipped_rooms.join('・')}`;
                            msg += "）";
                            showToast(msg, 'success');
                            // 登録内容を反映するため再読み込み
                            window.location.reload();
                        } else {
                            showToast("登録に失敗しました：" + (data.message || res.status), 'error');
                        }
                    } catch (e) {
                        confirmModal.classList.remove("is-open");
                        showToast("通信エラーが発生しました：" + e.message, 'error');
                    } finally {
                        confirmRegister.disabled = false;
                    }
                });


                // 重複確認モーダル
                conflictClose?.addEventListener('click', () => {
                    conflictModal.classList.remove('is-open');
                });

                conflictCancel?.addEventListener('click', () => {
                    conflictModal.classList.remove('is-open');
                });

                conflictOverwrite?.addEventListener('click', async () => {
                    // 上書き = そのままサーバーへ送信（サーバー側が重複する学生予約を自動キャンセルする）
                    const rows = Array.from(reserveTbody.querySelectorAll('tr')).map(collectRowData);
                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    conflictOverwrite.disabled = true;

                    try {
                        const res = await fetch("{{ route('classroom.reservation.bulk.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": token,
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ rows })
                        });
                        const data = await res.json();
                        conflictModal.classList.remove('is-open');

                        if (res.ok) {
                            let msg = `一括登録を完了しました（登録 ${data.created} 件`;
                            if (data.cancelled > 0) msg += `、重複する学生予約 ${data.cancelled} 件を自動キャンセル`;
                            msg += "）";
                            showToast(msg, 'success');
                            window.location.reload();
                        } else {
                            showToast("登録に失敗しました：" + (data.message || res.status), 'error');
                        }
                    } catch (e) {
                        conflictModal.classList.remove('is-open');
                        showToast("通信エラーが発生しました：" + e.message, 'error');
                    } finally {
                        conflictOverwrite.disabled = false;
                    }
                });
            });
        </script>
    </div>
</body>