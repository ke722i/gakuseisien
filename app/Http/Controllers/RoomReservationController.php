<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomReservationController extends Controller
{
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
        } else {
            $reservedRoomIds = [];
        }

        return view('reservation.room.room-reservation', [
            'rooms' => $rooms,
            'reservedRoomIds' => $reservedRoomIds,
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

        $alreadyReserved = Reservation::where('room_id', $room->id)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('period', $validated['period'])
            ->where('status', '!=', Reservation::STATUS_REJECTED)
            ->exists();

        if ($alreadyReserved) {
            return back()
                ->withInput()
                ->with('reservation_error', 'この教室・日時はすでに予約されています。');
        }

        Reservation::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'reservation_date' => $validated['reservation_date'],
            'period' => $validated['period'],
            'reason' => $validated['reason'] ?? null,
            'status' => Reservation::STATUS_PENDING,
        ]);

        return redirect()
            ->route('classroom.reservation.room', ['date' => $validated['reservation_date'], 'period' => $validated['period'], 'floor' => $room->floor])
            ->with('reservation_success', '予約を申請しました。教師の承認をお待ちください。');
    }

    /**
     * 自分の予約一覧。
     */
    public function list()
    {
        $periodTimes = [
            1 => '(9:15〜10:45)',
            2 => '(11:00〜12:30)',
            3 => '(13:30〜15:00)',
            4 => '(15:15〜16:45)',
            5 => '(17:00〜18:30)',
            6 => '(18:45〜20:15)',
        ];
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
            ->map(function (Reservation $reservation) use ($periodTimes, $statusLabels) {
                return [
                    'id' => $reservation->id,
                    'room' => $reservation->room?->name ?? '不明',
                    'date' => $reservation->reservation_date->format('Y/m/d'),
                    'weekday' => '(' . ['日', '月', '火', '水', '木', '金', '土'][$reservation->reservation_date->dayOfWeek] . ')',
                    'period' => $reservation->period . '限',
                    'time' => $periodTimes[$reservation->period] ?? '',
                    'status' => $reservation->status,
                    'status_label' => $statusLabels[$reservation->status] ?? '承認待ち',
                ];
            });

        return view('reservation.room.reservation-list', compact('reservations'));
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
        $periodTimes = [
            1 => '(9:15〜10:45)',
            2 => '(11:00〜12:30)',
            3 => '(13:30〜15:00)',
            4 => '(15:15〜16:45)',
            5 => '(17:00〜18:30)',
            6 => '(18:45〜20:15)',
        ];

        $reservations = Reservation::where('status', Reservation::STATUS_PENDING)
            ->with(['room', 'user'])
            ->orderBy('reservation_date')
            ->orderBy('period')
            ->get()
            ->map(function (Reservation $reservation) use ($periodTimes) {
                return [
                    'id' => $reservation->id,
                    'room' => $reservation->room?->name ?? '不明',
                    'floor' => $reservation->room ? $reservation->room->floor . 'F' : '',
                    'date' => $reservation->reservation_date->format('Y/m/d'),
                    'weekday' => '(' . ['日', '月', '火', '水', '木', '金', '土'][$reservation->reservation_date->dayOfWeek] . ')',
                    'period' => $reservation->period . '限',
                    'time' => $periodTimes[$reservation->period] ?? '',
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

        return redirect()->route('classroom.reservation.manage')->with('reservation_success', '予約を承諾しました。');
    }

    /**
     * 教師用：予約を拒否する。
     */
    public function reject(Reservation $reservation)
    {
        $reservation->update(['status' => Reservation::STATUS_REJECTED]);

        return redirect()->route('classroom.reservation.manage')->with('reservation_success', '予約を拒否しました。');
    }
}
