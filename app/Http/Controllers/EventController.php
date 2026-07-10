<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * 月間カレンダー表示。?year=&month= で月移動。
     */
    public function index(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);

        // 不正値でも落ちないように補正
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $current = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // カレンダーグリッド（日曜始まり）の範囲
        $gridStart = $current->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $current->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        // 表示範囲の予定を日付(Y-m-d)ごとにまとめる（複数日にまたがる予定は各日に配置）
        $eventsByDate = [];
        foreach (Event::orderBy('start_at')->get() as $event) {
            $start = $event->start_at->copy()->startOfDay();
            $end = ($event->end_at ?? $event->start_at)->copy()->startOfDay();

            if ($end->lt($gridStart) || $start->gt($gridEnd)) {
                continue; // 表示範囲外
            }

            $from = $start->lt($gridStart) ? $gridStart->copy() : $start;
            $to = $end->gt($gridEnd) ? $gridEnd->copy() : $end;

            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $eventsByDate[$d->format('Y-m-d')][] = $event;
            }
        }

        // グリッドの日付一覧
        $days = [];
        for ($d = $gridStart->copy(); $d->lte($gridEnd); $d->addDay()) {
            $days[] = $d->copy();
        }

        // JS用: 日付ごとの予定データ（クリックで「今日の予定」パネル更新に使う）
        $eventsData = [];
        foreach ($eventsByDate as $date => $list) {
            $eventsData[$date] = array_map(fn (Event $e) => [
                'title' => $e->title,
                'time' => $e->all_day
                    ? '終日'
                    : $e->start_at->format('H:i') . ($e->end_at ? '〜' . $e->end_at->format('H:i') : ''),
                'location' => $e->location,
                'cat' => $e->categoryClass(),
            ], $list);
        }

        // 「今日の予定」パネル用（表示中の月に関係なく today）
        $today = Carbon::today();
        $todayEvents = Event::orderBy('start_at')->get()->filter(function (Event $e) use ($today) {
            $s = $e->start_at->copy()->startOfDay();
            $en = ($e->end_at ?? $e->start_at)->copy()->startOfDay();

            return $today->gte($s) && $today->lte($en);
        })->values();

        return view('event.calendar', [
            'current' => $current,
            'days' => $days,
            'eventsByDate' => $eventsByDate,
            'eventsData' => $eventsData,
            'today' => $today,
            'todayEvents' => $todayEvents,
            'prev' => $current->copy()->subMonthNoOverflow(),
            'next' => $current->copy()->addMonthNoOverflow(),
            'categories' => array_keys(Event::CATEGORY_CLASSES),
        ]);
    }

    /**
     * 指定日の予定一覧。
     */
    public function day(string $date)
    {
        $target = Carbon::parse($date)->startOfDay();

        $events = Event::orderBy('start_at')->get()->filter(function (Event $event) use ($target) {
            $start = $event->start_at->copy()->startOfDay();
            $end = ($event->end_at ?? $event->start_at)->copy()->startOfDay();

            return $target->gte($start) && $target->lte($end);
        })->values();

        return view('event.day', [
            'date' => $target,
            'events' => $events,
        ]);
    }

    /**
     * 予定の追加。
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'タイトルを入力してください。',
            'start_at.required' => '開始日時を入力してください。',
            'end_at.after_or_equal' => '終了日時は開始日時以降にしてください。',
        ]);

        Event::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'all_day' => $request->boolean('all_day'),
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        // 追加した予定の月へ戻る
        $d = Carbon::parse($validated['start_at']);

        return redirect()->route('event.calendar', ['year' => $d->year, 'month' => $d->month]);
    }
}
