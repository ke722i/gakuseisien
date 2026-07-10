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
@endphp

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'calendar'])

        <!-- メイン画面 -->
        <main class="content">

            <div class="cal-header">
                <h1>イベント・締め切りカレンダー</h1>
                <button type="button" class="cal-add-btn" onclick="openEventModal()">＋ 新しい予定の追加</button>
            </div>

            <!-- 月移動 -->
            <div class="cal-nav">
                <a class="cal-nav-btn" href="{{ route('event.calendar', ['year' => $prev->year, 'month' => $prev->month]) }}">‹</a>
                <span class="cal-nav-title">{{ $current->year }}年{{ $current->month }}月</span>
                <a class="cal-nav-btn" href="{{ route('event.calendar', ['year' => $next->year, 'month' => $next->month]) }}">›</a>
            </div>

            <!-- カレンダー本体 -->
            <div class="cal-grid">
                @foreach ($weekdays as $i => $w)
                    <div class="cal-weekday {{ $i === 0 ? 'sun' : ($i === 6 ? 'sat' : '') }}">{{ $w }}</div>
                @endforeach

                @foreach ($days as $day)
                    @php $key = $day->format('Y-m-d'); @endphp
                    <a href="{{ route('event.day', $key) }}"
                       class="cal-cell {{ $day->month === $current->month ? '' : 'other-month' }} {{ $day->isToday() ? 'today' : '' }}">
                        <span class="cal-date {{ $day->dayOfWeek === 0 ? 'sun' : ($day->dayOfWeek === 6 ? 'sat' : '') }}">{{ $day->day }}</span>
                        <span class="cal-dots">
                            @foreach (($eventsByDate[$key] ?? []) as $event)
                                <span class="cal-dot {{ $event->categoryClass() }}" title="{{ $event->title }}"></span>
                            @endforeach
                        </span>
                    </a>
                @endforeach
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
        function openEventModal() {
            document.getElementById('eventModal').classList.add('open');
        }
        function closeEventModal() {
            document.getElementById('eventModal').classList.remove('open');
        }
        // 背景クリックで閉じる
        document.getElementById('eventModal').addEventListener('click', (e) => {
            if (e.target.id === 'eventModal') closeEventModal();
        });
    </script>
</body>

</html>
