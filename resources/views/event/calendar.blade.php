<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>イベント・締切カレンダー - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/event/calendar.css'])
</head>

@php
    $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
    $miniWeekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
@endphp

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'calendar'])

        <!-- メイン画面 -->
        <main class="content cal-page">

            <h1 class="cal-page-title">イベント・締め切りカレンダー</h1>

            <!-- 検索 -->
            <div class="cal-searchbar">
                <input type="text" id="calSearch" placeholder="予定を検索">
            </div>

            <div class="cal-layout">

                <!-- 凡例 / フィルタ -->
                <aside class="cal-legend">
                    <label class="leg-item"><input type="checkbox" id="legAll" checked><span class="leg-box all"></span>すべて表示</label>
                    <label class="leg-item"><input type="checkbox" class="leg-cat" data-cat="event" checked><span class="leg-box event"></span>イベント</label>
                    <label class="leg-item"><input type="checkbox" class="leg-cat" data-cat="gyoji" checked><span class="leg-box gyoji"></span>行事</label>
                    <label class="leg-item"><input type="checkbox" class="leg-cat" data-cat="kyuko" checked><span class="leg-box kyuko"></span>休校</label>
                    <label class="leg-item"><input type="checkbox" class="leg-cat" data-cat="exam" checked><span class="leg-box exam"></span>試験日</label>
                    <label class="leg-item"><input type="checkbox" class="leg-cat" data-cat="other" checked><span class="leg-box other"></span>その他</label>

                    <button type="button" class="cal-add-btn" onclick="openEventModal()">＋ 予定追加</button>
                </aside>

                <!-- メインカレンダー -->
                <section class="cal-main">
                    <div class="cal-nav">
                        <a class="cal-nav-btn" href="{{ route('event.calendar', ['year' => $prev->year, 'month' => $prev->month]) }}">‹</a>
                        <span class="cal-nav-title">{{ $current->year }}年 {{ $current->month }}月</span>
                        <a class="cal-nav-btn" href="{{ route('event.calendar', ['year' => $next->year, 'month' => $next->month]) }}">›</a>
                    </div>

                    <div class="cal-grid">
                        @foreach ($weekdays as $i => $w)
                            <div class="cal-weekday {{ $i === 0 ? 'sun' : ($i === 6 ? 'sat' : '') }}">{{ $w }}</div>
                        @endforeach

                        @foreach ($days as $day)
                            @php $key = $day->format('Y-m-d'); @endphp
                            <div class="cal-cell {{ $day->month === $current->month ? '' : 'other-month' }} {{ $day->isToday() ? 'today' : '' }}" data-date="{{ $key }}">
                                <span class="cal-date {{ $day->dayOfWeek === 0 ? 'sun' : ($day->dayOfWeek === 6 ? 'sat' : '') }}">{{ $day->day }}</span>
                                <div class="cal-events">
                                    @foreach (($eventsByDate[$key] ?? []) as $event)
                                        <div class="cal-event {{ $event->categoryClass() }}" data-cat="{{ $event->categoryClass() }}" data-title="{{ $event->title }}">
                                            <span class="cal-event-title">{{ $event->title }}</span>
                                            @unless ($event->all_day)
                                                <span class="cal-event-time">({{ $event->start_at->format('H:i') }}@if ($event->end_at)〜{{ $event->end_at->format('H:i') }}@endif)</span>
                                            @endunless
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- 右カラム -->
                <aside class="cal-right">

                    <!-- ミニカレンダー -->
                    <div class="mini-cal">
                        <div class="mini-head">
                            <a href="{{ route('event.calendar', ['year' => $prev->year, 'month' => $prev->month]) }}">‹</a>
                            <span>{{ $current->format('M') }} {{ $current->year }}</span>
                            <a href="{{ route('event.calendar', ['year' => $next->year, 'month' => $next->month]) }}">›</a>
                        </div>
                        <div class="mini-grid">
                            @foreach ($miniWeekdays as $w)
                                <span class="mini-wd">{{ $w }}</span>
                            @endforeach
                            @foreach ($days as $day)
                                <span class="mini-day {{ $day->month === $current->month ? '' : 'other-month' }} {{ $day->isToday() ? 'today' : '' }}" data-date="{{ $day->format('Y-m-d') }}">{{ $day->day }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- 今日の予定 -->
                    <div class="today-panel">
                        <div class="today-panel-head" id="todayPanelHead">今日の予定</div>
                        <div class="today-panel-body" id="todayPanelBody">
                            @forelse ($todayEvents as $event)
                                <div class="tp-item">
                                    <div class="tp-title"><span class="tp-dot {{ $event->categoryClass() }}"></span>{{ $event->title }}</div>
                                    <div class="tp-time">@if ($event->all_day)終日 @else({{ $event->start_at->format('H:i') }}@if ($event->end_at)〜{{ $event->end_at->format('H:i') }}@endif)@endif</div>
                                    @if ($event->location)
                                        <div class="tp-loc">{{ $event->location }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="tp-empty">予定なし</p>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <!-- 予定追加モーダル -->
    <div class="cal-modal {{ $errors->any() ? 'open' : '' }}" id="eventModal">
        <div class="cal-modal-box">
            <div class="cal-modal-head">
                <h2>新しい予定の追加</h2>
                <button type="button" class="cal-modal-close" onclick="closeEventModal()">×</button>
            </div>

            <form method="POST" action="{{ route('event.store') }}" class="cal-form">
                @csrf

                @if ($errors->any())
                    <div class="cal-errors">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="cal-field">
                    <label for="title">タイトル</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="例: タイピング技術検定">
                </div>

                <div class="cal-field">
                    <label for="category">カテゴリ</label>
                    <select id="category" name="category">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cal-field-row">
                    <div class="cal-field">
                        <label for="start_at">開始</label>
                        <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}">
                    </div>
                    <div class="cal-field">
                        <label for="end_at">終了</label>
                        <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}">
                    </div>
                </div>

                <div class="cal-field cal-check">
                    <label><input type="checkbox" name="all_day" value="1" {{ old('all_day') ? 'checked' : '' }}> 終日</label>
                </div>

                <div class="cal-field">
                    <label for="location">場所</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="例: 402教室">
                </div>

                <div class="cal-field">
                    <label for="description">詳細な説明</label>
                    <textarea id="description" name="description" rows="4" placeholder="詳細な説明">{{ old('description') }}</textarea>
                </div>

                <div class="cal-modal-actions">
                    <button type="button" class="cal-btn-cancel" onclick="closeEventModal()">閉じる</button>
                    <button type="submit" class="cal-btn-submit">追加</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.eventsData = @json($eventsData);

        function escapeHtml(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        // モーダル
        function openEventModal() { document.getElementById('eventModal').classList.add('open'); }
        function closeEventModal() { document.getElementById('eventModal').classList.remove('open'); }
        document.getElementById('eventModal').addEventListener('click', (e) => {
            if (e.target.id === 'eventModal') closeEventModal();
        });

        // カテゴリ絞り込み + 検索
        function checkedCats() {
            return Array.from(document.querySelectorAll('.leg-cat:checked')).map(c => c.dataset.cat);
        }
        function applyFilters() {
            const cats = checkedCats();
            const q = (document.getElementById('calSearch').value || '').trim();
            document.querySelectorAll('.cal-event').forEach(ev => {
                const catOk = cats.includes(ev.dataset.cat);
                const qOk = !q || (ev.dataset.title || '').includes(q);
                ev.style.display = (catOk && qOk) ? '' : 'none';
            });
        }
        document.getElementById('legAll').addEventListener('change', (e) => {
            document.querySelectorAll('.leg-cat').forEach(c => c.checked = e.target.checked);
            applyFilters();
        });
        document.querySelectorAll('.leg-cat').forEach(c => c.addEventListener('change', () => {
            const total = document.querySelectorAll('.leg-cat').length;
            const checked = document.querySelectorAll('.leg-cat:checked').length;
            document.getElementById('legAll').checked = (total === checked);
            applyFilters();
        }));
        document.getElementById('calSearch').addEventListener('input', applyFilters);

        // 日クリックで「今日の予定」パネルを更新
        function renderPanel(dateStr) {
            const body = document.getElementById('todayPanelBody');
            const head = document.getElementById('todayPanelHead');
            const [y, m, d] = dateStr.split('-').map(Number);
            head.textContent = `${m}月${d}日の予定`;
            const list = (window.eventsData && window.eventsData[dateStr]) || [];
            if (list.length === 0) {
                body.innerHTML = '<p class="tp-empty">予定なし</p>';
                return;
            }
            body.innerHTML = list.map(ev => `
                <div class="tp-item">
                    <div class="tp-title"><span class="tp-dot ${ev.cat}"></span>${escapeHtml(ev.title)}</div>
                    <div class="tp-time">(${escapeHtml(ev.time)})</div>
                    ${ev.location ? `<div class="tp-loc">${escapeHtml(ev.location)}</div>` : ''}
                </div>`).join('');
        }
        document.querySelectorAll('.cal-cell, .mini-day').forEach(el => {
            el.addEventListener('click', () => {
                if (el.dataset.date) renderPanel(el.dataset.date);
            });
        });
    </script>
</body>

</html>
