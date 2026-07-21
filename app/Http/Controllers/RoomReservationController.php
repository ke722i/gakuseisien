<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomUnavailableSlot;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomReservationController extends Controller
{
    /** 時限ごとの時刻表示（list / manage で共用） */
    private const PERIOD_TIMES = [
        1 => '(9:15〜10:45)',
        2 => '(11:00〜12:30)',
        3 => '(13:30〜15:00)',
        4 => '(15:15〜16:45)',
        5 => '(17:00〜18:30)',
        6 => '(18:45〜20:15)',
    ];

    /** 曜日ラベル（例: (金)）を返す */
    private function weekdayLabel(\Illuminate\Support\Carbon $date): string
    {
        return '(' . ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek] . ')';
    }

    /**
     * フロアマップ表示（空き教室予約トップ）。
     * date/period/floor はクエリパラメータで指定し、指定がなければ本日・1限・1階を初期値にする。
     */
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $period = (int) $request->query('period', 1);
        $floor = (int) $request->query('floor', 1);

        $rooms = Room::where('floor', $floor)
            ->orderBy('display_order')
            ->get();

        if ($rooms->isNotEmpty()) {
            $reservedRoomIds = Reservation::whereIn('room_id', $rooms->pluck('id'))
                ->where('reservation_date', $date)
                ->where('period', $period)
                ->where('status', '!=', Reservation::STATUS_REJECTED)
                ->pluck('room_id')
                ->all();

            // 管理者が設定した利用不可時間帯も「予約不可」として扱う
            $unavailableRoomIds = RoomUnavailableSlot::whereIn('room_id', $rooms->pluck('id'))
                ->where('date', $date)
                ->where('period', $period)
                ->pluck('room_id')
                ->all();
        } else {
            $reservedRoomIds = [];
            $unavailableRoomIds = [];
        }

        return view('reservation.room.room-reservation', [
            'rooms' => $rooms,
            'reservedRoomIds' => $reservedRoomIds,
            'unavailableRoomIds' => $unavailableRoomIds,
            'date' => $date,
            'period' => $period,
            'floor' => $floor,
        ]);
    }

    /**
     * 予約確認モーダルからの予約作成。
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'reservation_date' => ['required', 'date'],
            'period' => ['required', 'integer', 'min:1', 'max:6'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'room_id' => '教室',
            'reservation_date' => '日付',
            'period' => '時限',
            'reason' => '予約理由',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        if (! $room->is_reservable) {
            abort(403, 'この部屋は予約できません。');
        }

        // 管理者が設定した利用不可時間帯は誰も予約できない
        $isUnavailable = RoomUnavailableSlot::where('room_id', $room->id)
            ->where('date', $validated['reservation_date'])
            ->where('period', $validated['period'])
            ->exists();
        if ($isUnavailable) {
            return back()
                ->withInput()
                ->with('reservation_error', 'この教室・日時は利用できない時間帯に設定されています。');
        }

        $isTeacher = Auth::user()->isTeacher();

        $conflicts = Reservation::where('room_id', $room->id)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('period', $validated['period'])
            ->where('status', '!=', Reservation::STATUS_REJECTED)
            ->with(['user', 'room'])
            ->get();

        // 既に教職員が押さえている枠は誰も上書きできない
        $teacherHeld = $conflicts->first(fn ($c) => $c->user && $c->user->isTeacher());
        // 学生は既存予約（学生・教職員問わず）がある枠は予約できない
        if ($teacherHeld || (! $isTeacher && $conflicts->isNotEmpty())) {
            return back()
                ->withInput()
                ->with('reservation_error', 'この教室・日時はすでに予約されています。');
        }

        // 優先予約制御：教職員予約は重複する学生予約を自動キャンセルし、即確定にする
        $cancelled = $isTeacher ? $this->cancelStudentConflicts($conflicts) : 0;

        Reservation::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'reservation_date' => $validated['reservation_date'],
            'period' => $validated['period'],
            'reason' => $validated['reason'] ?? null,
            'status' => $isTeacher ? Reservation::STATUS_APPROVED : Reservation::STATUS_PENDING,
        ]);

        $message = $isTeacher
            ? '教室を予約しました。' . ($cancelled > 0 ? "（重複する学生予約{$cancelled}件を自動キャンセルしました）" : '')
            : '予約を申請しました。教師の承認をお待ちください。';

        return redirect()
            ->route('classroom.reservation.room', ['date' => $validated['reservation_date'], 'period' => $validated['period'], 'floor' => $room->floor])
            ->with('reservation_success', $message);
    }

    /**
     * 自分の予約一覧。
     */
    public function list()
    {
        $statusLabels = [
            Reservation::STATUS_PENDING => '承認待ち',
            Reservation::STATUS_APPROVED => '承認済み',
            Reservation::STATUS_REJECTED => '承認拒否',
        ];

        $reservations = Reservation::where('user_id', Auth::id())
            ->with('room')
            ->orderByDesc('reservation_date')
            ->orderByDesc('period')
            ->get()
            ->map(function (Reservation $reservation) use ($statusLabels) {
                return [
                    'id' => $reservation->id,
                    'room' => $reservation->room?->name ?? '不明',
                    'room_id' => $reservation->room_id,
                    'date' => $reservation->reservation_date->format('Y/m/d'),
                    'raw_date' => $reservation->reservation_date->format('Y-m-d'),
                    'weekday' => $this->weekdayLabel($reservation->reservation_date),
                    'period' => $reservation->period . '限',
                    'raw_period' => $reservation->period,
                    'reason' => $reservation->reason,
                    'time' => self::PERIOD_TIMES[$reservation->period] ?? '',
                    'status' => $reservation->status,
                    'status_label' => $statusLabels[$reservation->status] ?? '承認待ち',
                ];
            });

        // 変更フォームの教室選択用（予約可能な教室のみ）
        $rooms = Room::where('is_reservable', true)
            ->orderBy('floor')
            ->orderBy('display_order')
            ->get(['id', 'name', 'floor']);

        return view('reservation.room.reservation-list', compact('reservations', 'rooms'));
    }

    /**
     * 自分の予約を変更する。
     * 学生が変更した場合は再承認が必要なため「承認待ち」に戻す。
     */
    public function update(Request $request, Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id() && ! Auth::user()->isTeacher(), 403);

        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'reservation_date' => ['required', 'date'],
            'period' => ['required', 'integer', 'min:1', 'max:6'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'room_id' => '教室',
            'reservation_date' => '日付',
            'period' => '時限',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        if (! $room->is_reservable) {
            return back()->with('reservation_error', 'この部屋は予約できません。');
        }

        // 利用不可時間帯チェック
        $isUnavailable = RoomUnavailableSlot::where('room_id', $room->id)
            ->where('date', $validated['reservation_date'])
            ->where('period', $validated['period'])
            ->exists();
        if ($isUnavailable) {
            return back()->with('reservation_error', 'この教室・日時は利用できない時間帯です。');
        }

        // 自分自身（今編集中の予約）以外の重複を確認
        $conflicts = Reservation::where('room_id', $room->id)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('period', $validated['period'])
            ->where('status', '!=', Reservation::STATUS_REJECTED)
            ->where('id', '!=', $reservation->id)
            ->with(['user', 'room'])
            ->get();

        $isTeacher = Auth::user()->isTeacher();
        $teacherHeld = $conflicts->first(fn ($c) => $c->user && $c->user->isTeacher());
        if ($teacherHeld || (! $isTeacher && $conflicts->isNotEmpty())) {
            return back()->with('reservation_error', '変更先の教室・日時はすでに予約されています。');
        }

        if ($isTeacher) {
            $this->cancelStudentConflicts($conflicts);
        }

        $reservation->update([
            'room_id' => $room->id,
            'reservation_date' => $validated['reservation_date'],
            'period' => $validated['period'],
            'reason' => $validated['reason'] ?? null,
            // 学生の変更は再承認が必要。教職員の変更は承認済みのまま。
            'status' => $isTeacher ? Reservation::STATUS_APPROVED : Reservation::STATUS_PENDING,
        ]);

        return redirect()->route('classroom.reservation.list')
            ->with('reservation_success', '予約を変更しました。' . ($isTeacher ? '' : '再度承認をお待ちください。'));
    }

    /**
     * 自分の予約を削除（キャンセル）する。
     */
    public function destroy(Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        $reservation->delete();

        return redirect()->route('classroom.reservation.list')->with('reservation_success', '予約を削除しました。');
    }

    /**
     * 教師用：承認待ち予約の一覧。
     */
    public function manage()
    {
        $reservations = Reservation::where('status', Reservation::STATUS_PENDING)
            ->with(['room', 'user'])
            ->orderBy('reservation_date')
            ->orderBy('period')
            ->get()
            ->map(function (Reservation $reservation) {
                return [
                    'id' => $reservation->id,
                    'room' => $reservation->room?->name ?? '不明',
                    'floor' => $reservation->room ? $reservation->room->floor . 'F' : '',
                    'date' => $reservation->reservation_date->format('Y/m/d'),
                    'weekday' => $this->weekdayLabel($reservation->reservation_date),
                    'period' => $reservation->period . '限',
                    'time' => self::PERIOD_TIMES[$reservation->period] ?? '',
                    'reason' => $reservation->reason,
                    'user_id' => $reservation->user?->student_number ?? $reservation->user?->login_id ?? '',
                    'user_name' => $reservation->user?->student_name ?? $reservation->user?->login_id ?? '不明',
                ];
            });

        return view('reservation.room.reservation-management', compact('reservations'));
    }

    /**
     * 教師用：予約を承認する。
     */
    public function approve(Reservation $reservation)
    {
        $reservation->update(['status' => Reservation::STATUS_APPROVED]);

        // 予約した学生に結果を通知する
        UserNotification::send(
            $reservation->user_id,
            '教室予約が承認されました',
            $this->reservationSummary($reservation),
            route('classroom.reservation.list', absolute: false)
        );

        return redirect()->route('classroom.reservation.manage')->with('reservation_success', '予約を承諾しました。');
    }

    /**
     * 教師用：予約を拒否する。
     */
    public function reject(Reservation $reservation)
    {
        $reservation->update(['status' => Reservation::STATUS_REJECTED]);

        // 予約した学生に結果を通知する
        UserNotification::send(
            $reservation->user_id,
            '教室予約が承認されませんでした',
            $this->reservationSummary($reservation),
            route('classroom.reservation.list', absolute: false)
        );

        return redirect()->route('classroom.reservation.manage')->with('reservation_success', '予約を拒否しました。');
    }

    /** 通知本文用に「101c 2026/07/24(金) 3限」形式の文字列を作る */
    private function reservationSummary(Reservation $reservation): string
    {
        return sprintf(
            '%s %s%s %d限',
            $reservation->room?->name ?? '教室不明',
            $reservation->reservation_date->format('Y/m/d'),
            $this->weekdayLabel($reservation->reservation_date),
            $reservation->period
        );
    }

    /**
     * 教職員が予約した枠に重複する「学生の予約」を自動キャンセルし、本人に通知する。
     * 渡されたコレクションのうち教職員予約は対象外。キャンセル件数を返す。
     */
    private function cancelStudentConflicts(\Illuminate\Support\Collection $conflicts): int
    {
        $count = 0;

        foreach ($conflicts as $conflict) {
            if ($conflict->user && $conflict->user->isTeacher()) {
                continue;
            }

            $userId = $conflict->user_id;
            $conflict->loadMissing('room');
            $summary = $this->reservationSummary($conflict);
            $conflict->delete();

            UserNotification::send(
                $userId,
                '教室予約がキャンセルされました',
                $summary . '／授業の予約が入ったため自動的にキャンセルされました。',
                route('classroom.reservation.list', absolute: false)
            );

            $count++;
        }

        return $count;
    }

    /** 一括予約画面の時限ラベル（画面のボタン表記に合わせる） */
    private const BULK_PERIOD_LABELS = [
        1 => '1限 9:15-10:45',
        2 => '2限 11:00-12:30',
        3 => '3限 13:30-15:00',
        4 => '4限 15:15-16:45',
        5 => '5限 17:00-18:30',
    ];

    /** 曜日ラベル→Carbonの曜日番号 */
    private const WEEKDAY_TO_DOW = ['日' => 0, '月' => 1, '火' => 2, '水' => 3, '木' => 4, '金' => 5, '土' => 6];

    /**
     * 教師用：一括予約画面。
     * クライアント側の重複プレチェック用に、今日以降の既存予約を渡す。
     */
    public function bulk()
    {
        $existing = Reservation::where('reservation_date', '>=', now()->toDateString())
            ->where('status', '!=', Reservation::STATUS_REJECTED)
            ->with(['room', 'user'])
            ->get()
            ->map(fn (Reservation $r) => [
                'room' => $r->room?->name ?? '',
                'date' => $r->reservation_date->format('Y/m/d'),
                'period' => self::BULK_PERIOD_LABELS[$r->period] ?? ($r->period . '限'),
                'userName' => $r->user?->student_name ?? $r->user?->teacher_name ?? $r->user?->login_id ?? '不明',
                'userType' => $r->user && $r->user->isTeacher() ? '教職員' : '学生',
            ])
            ->values();

        // 選択肢はDBの予約可能な教室から生成する（Bladeへの直書きだとDBと食い違うため）
        $reservableRooms = Room::where('is_reservable', true)
            ->orderBy('floor')
            ->orderBy('display_order')
            ->get(['name', 'floor']);

        return view('reservation.room.bulk-room-reservation', [
            'existingReservations' => $existing,
            'reservableRooms' => $reservableRooms,
        ]);
    }

    /**
     * 教師用：一括予約の登録。
     * 各行を「教室 × 期間内の該当曜日 × 選択時限」に展開して予約を作る。
     * 教職員予約は即確定（承認済み）とし、重複する学生予約は自動キャンセルする。
     */
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.room' => ['required', 'string'],
            'rows.*.usage' => ['required', 'string', 'max:255'],
            'rows.*.periods' => ['required', 'array', 'min:1'],
            'rows.*.fromDate' => ['required', 'date'],
            'rows.*.toDate' => ['required', 'date'],
            'rows.*.selectedDays' => ['required', 'array', 'min:1'],
        ]);

        $created = 0;
        $cancelled = 0;
        $skippedRooms = [];

        foreach ($data['rows'] as $row) {
            $room = Room::where('name', $row['room'])->first();
            if (! $room || ! $room->is_reservable) {
                $skippedRooms[] = $row['room'];
                continue;
            }

            $dows = collect($row['selectedDays'])
                ->map(fn ($d) => self::WEEKDAY_TO_DOW[$d] ?? -1)
                ->filter(fn ($d) => $d >= 0)
                ->all();

            $periods = collect($row['periods'])
                ->map(fn ($p) => (int) $p) // "1限 9:15-10:45" → 1
                ->filter(fn ($p) => $p >= 1 && $p <= 6)
                ->unique()
                ->values()
                ->all();

            $cursor = \Illuminate\Support\Carbon::parse($row['fromDate'])->startOfDay();
            $end = \Illuminate\Support\Carbon::parse($row['toDate'])->startOfDay();

            // 期間が異常に長い場合の暴走防止（最大1年）
            if ($cursor->diffInDays($end) > 366) {
                $end = $cursor->copy()->addDays(366);
            }

            while ($cursor <= $end) {
                if (in_array($cursor->dayOfWeek, $dows, true)) {
                    foreach ($periods as $period) {
                        $date = $cursor->toDateString();

                        // 利用不可時間帯に設定された枠は一括予約でもスキップ
                        $isUnavailable = RoomUnavailableSlot::where('room_id', $room->id)
                            ->where('date', $date)
                            ->where('period', $period)
                            ->exists();
                        if ($isUnavailable) {
                            continue;
                        }

                        $slotReservations = Reservation::where('room_id', $room->id)
                            ->where('reservation_date', $date)
                            ->where('period', $period)
                            ->where('status', '!=', Reservation::STATUS_REJECTED)
                            ->with(['user', 'room'])
                            ->get();

                        // 既に教職員が押さえている枠は二重登録しない
                        if ($slotReservations->first(fn ($c) => $c->user && $c->user->isTeacher())) {
                            continue;
                        }

                        // 重複する学生予約を自動キャンセル
                        $cancelled += $this->cancelStudentConflicts($slotReservations);

                        Reservation::create([
                            'room_id' => $room->id,
                            'user_id' => Auth::id(),
                            'reservation_date' => $date,
                            'period' => $period,
                            'reason' => $row['usage'],
                            'status' => Reservation::STATUS_APPROVED,
                        ]);
                        $created++;
                    }
                }
                $cursor->addDay();
            }
        }

        return response()->json([
            'created' => $created,
            'cancelled' => $cancelled,
            'skipped_rooms' => array_values(array_unique($skippedRooms)),
        ]);
    }
}
