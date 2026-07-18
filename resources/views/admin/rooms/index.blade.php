<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>空き教室設定 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/admin/users.css'])
    <style>
        .floor-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .floor-tab { padding: 8px 16px; border: 1px solid #cbd5e1; border-radius: 8px; text-decoration: none; color: #334155; font-weight: 700; font-size: 14px; }
        .floor-tab.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .section-block { margin-bottom: 40px; }
        .section-block h2 { font-size: 18px; margin: 0 0 12px; }
        .compact-input { width: 64px; padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
        .text-input { padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
        .inline-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .add-room-card, .add-slot-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-top: 12px; }
        .add-room-card h3, .add-slot-card h3 { margin: 0 0 12px; font-size: 15px; }
        .grid-inputs { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 12px; }
        .grid-inputs label { font-size: 12px; color: #475569; font-weight: 700; display: block; margin-bottom: 4px; }
        .grid-inputs input, .grid-inputs select { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        .check-line { display: flex; align-items: center; gap: 6px; font-size: 13px; }
        .room-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 12px; }
        .room-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px; }
        .room-card .rc-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; flex-wrap: wrap; }
        .room-card .rc-row label { font-size: 12px; color: #475569; font-weight: 700; min-width: 60px; }
        .room-card input[type=text], .room-card input[type=number] { padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
        .room-card .rc-actions { display: flex; gap: 8px; margin-top: 10px; }
    </style>
</head>

<body>
    <div class="app-layout">
        @include('partials.sidebar', ['active' => 'admin_rooms'])

        <main class="content">
            <div class="users-header">
                <h1>空き教室設定</h1>
            </div>

            @if (session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-error">
                    @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            {{-- ===== 教室の登録・編集 ===== --}}
            <div class="section-block">
                <h2>教室の登録・編集</h2>

                <div class="floor-tabs">
                    @for ($f = 1; $f <= 6; $f++)
                        <a href="{{ route('admin.rooms.index', ['floor' => $f]) }}"
                           class="floor-tab {{ $floor === $f ? 'active' : '' }}">{{ $f }}階</a>
                    @endfor
                </div>

                @if ($rooms->isEmpty())
                    <p class="notice-empty" style="list-style:none">この階の教室はまだありません。</p>
                @else
                    <div class="room-cards">
                        @foreach ($rooms as $room)
                            <div class="room-card">
                                <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="floor" value="{{ $room->floor }}">
                                    <input type="hidden" name="room_type" value="{{ $room->room_type }}">
                                    <div class="rc-row">
                                        <label>教室名</label>
                                        <input type="text" name="name" value="{{ $room->name }}" style="flex:1">
                                    </div>
                                    <div class="rc-row">
                                        <label>コード</label>
                                        <input type="text" name="room_code" value="{{ $room->room_code }}" style="flex:1">
                                    </div>
                                    <div class="rc-row">
                                        <label>位置</label>
                                        X<input type="number" name="pos_x" value="{{ $room->pos_x }}" style="width:60px">
                                        Y<input type="number" name="pos_y" value="{{ $room->pos_y }}" style="width:60px">
                                    </div>
                                    <div class="rc-row">
                                        <label>サイズ</label>
                                        W<input type="number" name="width" value="{{ $room->width }}" style="width:60px">
                                        H<input type="number" name="height" value="{{ $room->height }}" style="width:60px">
                                    </div>
                                    <label class="check-line"><input type="checkbox" name="is_reservable" value="1" @checked($room->is_reservable)> 予約可能</label>
                                    <div class="rc-actions">
                                        <button type="submit" class="primary-btn" style="padding:8px 16px">保存</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('{{ $room->name }} を削除しますか？');" style="margin-top:8px">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cancel-btn" style="padding:8px 16px;color:#dc2626;border-color:#fca5a5">この教室を削除</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="add-room-card">
                    <h3>＋ {{ $floor }}階に教室を追加</h3>
                    <form method="POST" action="{{ route('admin.rooms.store') }}">
                        @csrf
                        <input type="hidden" name="floor" value="{{ $floor }}">
                        <div class="grid-inputs">
                            <div><label>教室名</label><input name="name" required placeholder="例: 206c"></div>
                            <div><label>教室コード</label><input name="room_code" required placeholder="例: 206c"></div>
                            <div><label>種別（任意）</label><input name="room_type" placeholder="classroom"></div>
                            <div><label>X位置</label><input type="number" name="pos_x" value="100" required></div>
                            <div><label>Y位置</label><input type="number" name="pos_y" value="40" required></div>
                            <div><label>幅</label><input type="number" name="width" value="200" required></div>
                            <div><label>高さ</label><input type="number" name="height" value="120" required></div>
                        </div>
                        <label class="check-line"><input type="checkbox" name="is_reservable" value="1" checked> 予約可能な教室にする</label>
                        <div style="margin-top:12px"><button type="submit" class="primary-btn">追加する</button></div>
                    </form>
                </div>
            </div>

            {{-- ===== 利用不可時間帯 ===== --}}
            <div class="section-block">
                <h2>利用不可時間帯の設定</h2>

                <div class="add-slot-card">
                    <h3>＋ 利用不可の枠を追加</h3>
                    <form method="POST" action="{{ route('admin.rooms.unavailable.store') }}">
                        @csrf
                        <div class="grid-inputs">
                            <div>
                                <label>教室</label>
                                <select name="room_id" required>
                                    @foreach ($reservableRooms as $r)
                                        <option value="{{ $r->id }}">{{ $r->floor }}階 {{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div><label>日付</label><input type="date" name="date" required></div>
                            <div>
                                <label>時限</label>
                                <select name="period" required>
                                    @for ($p = 1; $p <= 6; $p++)<option value="{{ $p }}">{{ $p }}限</option>@endfor
                                </select>
                            </div>
                            <div><label>理由（任意）</label><input name="reason" placeholder="メンテナンス"></div>
                        </div>
                        <button type="submit" class="primary-btn">設定する</button>
                    </form>
                </div>

                <div class="users-table-wrap" style="margin-top:16px">
                    <table class="users-table">
                        <thead>
                            <tr><th>教室</th><th>日付</th><th>時限</th><th>理由</th><th>操作</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($unavailableSlots as $slot)
                                <tr>
                                    <td>{{ $slot->room?->name ?? '（削除済み）' }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($slot->date)->format('Y/m/d') }}</td>
                                    <td>{{ $slot->period }}限</td>
                                    <td>{{ $slot->reason ?? '—' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.rooms.unavailable.destroy', $slot) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="act-delete" style="border:1px solid #cbd5e1;background:#fff;border-radius:6px;padding:5px 10px;font-size:12px;font-weight:700;cursor:pointer">解除</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">設定中の利用不可時間帯はありません。</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
