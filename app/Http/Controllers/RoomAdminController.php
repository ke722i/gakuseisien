<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomUnavailableSlot;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * 教職員向けの空き教室設定。
 * 予約可能な教室の登録・編集・削除と、利用不可時間帯の設定を行う。
 * ルート側で teacher ミドルウェアを掛けている前提。
 */
class RoomAdminController extends Controller
{
    /** 教室設定トップ（フロア別の教室一覧＋利用不可時間帯） */
    public function index(Request $request)
    {
        $floor = (int) $request->query('floor', 1);

        $rooms = Room::where('floor', $floor)
            ->orderBy('display_order')
            ->get();

        // 今日以降の利用不可時間帯（設定済み一覧）
        $unavailableSlots = RoomUnavailableSlot::with('room')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('period')
            ->get();

        $reservableRooms = Room::where('is_reservable', true)
            ->orderBy('floor')
            ->orderBy('display_order')
            ->get();

        return view('admin.rooms.index', compact('floor', 'rooms', 'unavailableSlots', 'reservableRooms'));
    }

    /** 教室を新規登録 */
    public function store(Request $request)
    {
        $validated = $this->validateRoom($request, null);

        // room_type は NOT NULL のため未入力時は既定値を入れる
        $validated['room_type'] = $validated['room_type'] ?? 'classroom';
        // 表示順は同じ階の末尾に追加する
        $validated['display_order'] = (Room::where('floor', $validated['floor'])->max('display_order') ?? 0) + 1;

        Room::create($validated);

        return redirect()->route('admin.rooms.index', ['floor' => $validated['floor']])
            ->with('success', '教室を登録しました。');
    }

    /** 教室を更新 */
    public function update(Request $request, Room $room)
    {
        $validated = $this->validateRoom($request, $room->id);

        $room->update($validated);

        return redirect()->route('admin.rooms.index', ['floor' => $validated['floor']])
            ->with('success', '教室情報を更新しました。');
    }

    /** 教室を削除 */
    public function destroy(Room $room)
    {
        $floor = $room->floor;
        $room->delete();

        return redirect()->route('admin.rooms.index', ['floor' => $floor])
            ->with('success', '教室を削除しました。');
    }

    /** 利用不可時間帯を追加 */
    public function storeUnavailable(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            // 過ぎた日付を利用不可にしても意味がないため当日以降のみ
            'date' => ['required', 'date', 'after_or_equal:today'],
            'period' => ['required', 'integer', 'min:1', 'max:6'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'date.after_or_equal' => '過去の日付は設定できません。',
        ], [
            'room_id' => '教室',
            'date' => '日付',
            'period' => '時限',
        ]);

        // 同じ枠が既にあれば理由だけ更新（unique制約に配慮）
        RoomUnavailableSlot::updateOrCreate(
            [
                'room_id' => $validated['room_id'],
                'date' => $validated['date'],
                'period' => $validated['period'],
            ],
            ['reason' => $validated['reason'] ?? null],
        );

        return redirect()->route('admin.rooms.index')
            ->with('success', '利用不可時間帯を設定しました。');
    }

    /** 利用不可時間帯を解除 */
    public function destroyUnavailable(RoomUnavailableSlot $slot)
    {
        $slot->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', '利用不可時間帯を解除しました。');
    }

    /** 教室の入力バリデーション */
    private function validateRoom(Request $request, ?int $ignoreId): array
    {
        $validated = $request->validate([
            'floor' => ['required', 'integer', 'min:1', 'max:20'],
            'room_code' => [
                'required', 'string', 'max:50',
                // 同一フロア内で room_code は重複させない
                Rule::unique('rooms', 'room_code')
                    ->where(fn ($q) => $q->where('floor', $request->input('floor')))
                    ->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'room_type' => ['nullable', 'string', 'max:50'],
            'pos_x' => ['required', 'integer', 'min:0', 'max:900'],
            'pos_y' => ['required', 'integer', 'min:0', 'max:560'],
            'width' => ['required', 'integer', 'min:20', 'max:900'],
            'height' => ['required', 'integer', 'min:20', 'max:560'],
        ], [
            'room_code.unique' => 'この教室コードは同じ階で既に使われています。',
        ], [
            'floor' => '階', 'room_code' => '教室コード', 'name' => '教室名',
            'pos_x' => 'X位置', 'pos_y' => 'Y位置', 'width' => '幅', 'height' => '高さ',
        ]);

        // チェックボックスは未チェックだと送信されないため boolean() で明示的に確定させる
        $validated['is_reservable'] = $request->boolean('is_reservable');

        return $validated;
    }
}
