<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>教室一括予約</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <select id="roomSelect">
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

        <!-- conflict modal -->
        <div class="conflict-modal" id="conflictModal" aria-hidden="true">
            <div class="conflict-backdrop"></div>
            <div class="conflict-box" role="dialog" aria-modal="true">
                <button id="conflictClose" aria-label="閉じる" style="position:absolute;right:12px;top:8px;border:none;background:none;font-size:18px;cursor:pointer;">×</button>
                <h3>予約の重複が見つかりました。</h3>
                <p>指定した教室・時間帯には、すでに学生の予約が入っています。上書きすると既存の予約はキャンセルされます。よろしいですか？</p>
                <div class="conflict-list" id="conflictList"></div>
                <div class="conflict-actions">
                    <button class="conflict-btn cancel" id="conflictCancel">戻る</button>
                    <button class="conflict-btn overwrite" id="conflictOverwrite">上書きして登録する</button>
                </div>
            </div>
        </div>

        <script>
            const existingReservations = [
                {
                room: "402c",
                date: "2026/07/15", // テストする際は、この日付が含まれる期間を指定してください
                period: "1限 9:15-10:45",
                userName: "山田 太郎",
                userType: "学生"
                }           
            ];

            document.addEventListener('DOMContentLoaded', function () {
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

                addToListBtn.addEventListener('click', () => {
                    const room = document.getElementById('roomSelect').value;
                    const usage = document.getElementById('usageInput').value.trim();
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;
                    const selectedPeriods = Array.from(periodBtns).filter(p => p.classList.contains('active')).map(p => p.dataset.label);
                    const selectedDays = Array.from(weekdayBtns).filter(d => d.classList.contains('active')).map(d => d.dataset.day);

                    if (!usage) { alert('授業名を入力してください'); return; }
                    if (selectedPeriods.length === 0) { alert('時限を1つ以上選択してください'); return; }
                    if (!fromDate || !toDate) { alert('予約期間を入力してください'); return; }
                    if (selectedDays.length === 0) { alert('曜日を1つ以上選択してください'); return; }

                    const tr = document.createElement('tr');
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

                function parseDuration(durationText) {
                    // expected formats: yyyy-mm-dd〜yyyy-mm-dd or yyyy/mm/dd〜yyyy/mm/dd
                    const parts = durationText.split('〜');
                    if (parts.length < 2) return [null, null];
                    const from = parts[0].trim().replace(/\//g, '-');
                    const to = parts[1].split('\n')[0].trim().replace(/\//g, '-');
                    return [from, to];
                }

                function dateInRange(dateStr, fromStr, toStr) {
                    if (!dateStr || !fromStr || !toStr) return false;
                    const d = new Date(dateStr);
                    const f = new Date(fromStr);
                    const t = new Date(toStr);
                    return d >= f && d <= t;
                }

                function findConflicts(rows) {
                    const conflicts = [];
                    rows.forEach((r, idx) => {
                        const [from, to] = parseDuration(r.duration);
                        existingReservations.forEach(ex => {
                            if (ex.room !== r.room) return;
                            // if existing reservation date falls within requested range
                            if (dateInRange(ex.date.replace(/\//g,'-'), from, to)) {
                                // check period overlap by checking ex.period appears in r.periods
                                if (r.periods.indexOf(ex.period) !== -1 || r.periods.indexOf(ex.period + ' ') !== -1) {
                                    conflicts.push({ rowIndex: idx, request: r, existing: ex });
                                }
                            }
                        });
                    });
                    return conflicts;
                }

                bulkRegisterBtn.addEventListener('click', () => {
                    const rows = Array.from(reserveTbody.querySelectorAll('tr')).map(row => {
                        const cells = row.querySelectorAll('td');
                        return {
                            room: cells[0].textContent.trim(),
                            usage: cells[1].textContent.trim(),
                            periods: cells[2].innerHTML.replace(/<br>/g, ', ').trim(),
                            duration: cells[3].textContent.trim()
                        };
                    });
                    if (rows.length === 0) { alert('登録する項目がありません'); return; }

                    const conflicts = findConflicts(rows);
                    if (conflicts.length > 0) {
                        // populate and show conflict modal
                        const list = document.getElementById('conflictList');
                        list.innerHTML = '';
                        conflicts.forEach(c => {
                            const li = document.createElement('div');
                            li.className = 'conflict-item';
                            li.innerHTML = `<strong>${c.existing.room}教室 ・ ${c.existing.date} ・ ${c.existing.period}</strong><div class="conflict-note">既存予約： ${c.existing.userName}（${c.existing.userType}）</div>`;
                            list.appendChild(li);
                        });
                        // store for overwrite action
                        conflictModal.dataset.conflicts = JSON.stringify(conflicts);
                        conflictModal.classList.add('is-open');
                        return;
                    }

                    // no conflicts -> perform registration (demo)
                    // TODO: replace with fetch POST to backend
                    console.log('一括登録データ', rows);
                    alert('一括登録を完了しました（UIデモ）');
                    reserveTbody.innerHTML = '';
                    updateCount();
                });

                // conflict modal handling
                const conflictModal = document.getElementById('conflictModal');
                const conflictClose = document.getElementById('conflictClose');
                const conflictCancel = document.getElementById('conflictCancel');
                const conflictOverwrite = document.getElementById('conflictOverwrite');

                conflictClose?.addEventListener('click', () => conflictModal.classList.remove('is-open'));
                conflictCancel?.addEventListener('click', () => conflictModal.classList.remove('is-open'));

                conflictOverwrite?.addEventListener('click', () => {
                    const data = JSON.parse(conflictModal.dataset.conflicts || '[]');
                    // simulate overwriting: remove conflicting existing reservations
                    data.forEach(c => {
                        const idx = existingReservations.findIndex(ex => ex.room === c.existing.room && ex.date === c.existing.date && ex.period === c.existing.period);
                        if (idx !== -1) existingReservations.splice(idx, 1);
                    });
                    // then register requested rows (demo: clear list)
                    reserveTbody.innerHTML = '';
                    updateCount();
                    conflictModal.classList.remove('is-open');
                    alert('一括登録（上書き）を完了しました（UIデモ）');
                });
            });
        </script>
    </div>
</body>
