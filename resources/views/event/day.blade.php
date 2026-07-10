<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>今日の予定 - 学生支援.com</title>
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

            <div class="day-header">
                <div>
                    <span class="day-label">今日の予定</span>
                    <span class="day-date">{{ $date->month }}月{{ $date->day }}日（{{ $weekdays[$date->dayOfWeek] }}）</span>
                </div>
                <a href="{{ route('event.calendar', ['year' => $date->year, 'month' => $date->month]) }}" class="day-back">戻る</a>
            </div>

            <section class="day-list">
                @forelse ($events as $event)
                    <article class="day-event {{ $event->categoryClass() }}">
                        <h2 class="day-event-title">{{ $event->title }}</h2>
                        <p class="day-event-meta">
                            <span class="day-event-cat">{{ $event->category }}</span>
                            @if ($event->all_day)
                                ・終日
                            @else
                                ・{{ $event->start_at->format('H:i') }}@if ($event->end_at)〜{{ $event->end_at->format('H:i') }}@endif
                            @endif
                            @if ($event->location)
                                ・{{ $event->location }}
                            @endif
                        </p>
                        @if ($event->description)
                            <p class="day-event-desc">{{ $event->description }}</p>
                        @endif
                    </article>
                @empty
                    <p class="day-empty">予定なし</p>
                @endforelse
            </section>
        </main>
    </div>
</body>

</html>
