<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        // 件数が増えても重くならないよう、表示するマスに重なる予定だけを取り出す
        $eventsByDate = [];
        foreach ($this->eventsOverlapping($gridStart, $gridEnd) as $event) {
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
        $todayEvents = $this->eventsOverlapping($today, $today);

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
     * 指定期間に重なる予定を取り出す。
     *
     * 終了日が未設定の予定は開始日だけの1日予定として扱うため、
     * 期間の判定には「終了日、無ければ開始日」を使う。
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Event>
     */
    private function eventsOverlapping(Carbon $from, Carbon $to)
    {
        return Event::query()
            ->whereDate('start_at', '<=', $to->toDateString())
            ->whereRaw('date(coalesce(end_at, start_at)) >= ?', [$from->toDateString()])
            ->orderBy('start_at')
            ->get();
    }

    /**
     * 指定日の予定一覧。
     */
    public function day(string $date)
    {
        $target = Carbon::parse($date)->startOfDay();

        $events = $this->eventsOverlapping($target, $target);

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
            // 画面の選択肢以外の値を直接送られても受け付けない
            'category' => ['required', 'string', Rule::in(array_keys(Event::CATEGORY_CLASSES))],
            // 新規登録は過去日を受け付けない（編集は過去の予定も直せるよう制限しない）
            'start_at' => ['required', 'date', 'after_or_equal:today'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'タイトルを入力してください。',
            'start_at.required' => '開始日時を入力してください。',
            'start_at.after_or_equal' => '開始日時に過去の日付は指定できません。',
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

    /**
     * 予定の編集（教職員のみ）。
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // 画面の選択肢以外の値を直接送られても受け付けない
            'category' => ['required', 'string', Rule::in(array_keys(Event::CATEGORY_CLASSES))],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'タイトルを入力してください。',
            'start_at.required' => '開始日時を入力してください。',
            'end_at.after_or_equal' => '終了日時は開始日時以降にしてください。',
        ]);

        $event->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'all_day' => $request->boolean('all_day'),
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        $d = Carbon::parse($validated['start_at']);

        return redirect()->route('event.calendar', ['year' => $d->year, 'month' => $d->month])
            ->with('success', '予定を更新しました。');
    }

    /**
     * 予定の削除（教職員のみ）。
     */
    public function destroy(Event $event): RedirectResponse
    {
        $d = $event->start_at->copy();
        $event->delete();

        return redirect()->route('event.calendar', ['year' => $d->year, 'month' => $d->month])
            ->with('success', '予定を削除しました。');
    }
}
