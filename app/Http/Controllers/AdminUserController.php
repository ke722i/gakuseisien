<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * 管理者（教職員）向けのアカウント管理。
 * ユーザーの登録・編集・削除・権限付与・パスワード初期化を行う。
 * ルート側で teacher ミドルウェアを掛けている前提。
 */
class AdminUserController extends Controller
{
    /** ユーザー一覧 */
    public function index()
    {
        $users = User::orderByRaw("CASE WHEN role = 'teacher' THEN 0 ELSE 1 END")
            ->orderBy('login_id')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /** 新規作成フォーム */
    public function create()
    {
        return view('admin.users.create');
    }

    /** 新規作成 */
    public function store(Request $request)
    {
        $validated = $this->validateUser($request, null, passwordRequired: true);

        $data = $this->profileData($validated);
        $data['login_id'] = $validated['login_id'];
        $data['role'] = $validated['role'];
        $data['password'] = Hash::make($validated['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'ユーザーを作成しました。');
    }

    /** 編集フォーム */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /** 編集（プロフィール・権限。パスワードは入力があった場合のみ変更） */
    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user->id, passwordRequired: false);

        $data = $this->profileData($validated);
        $data['login_id'] = $validated['login_id'];
        $data['role'] = $validated['role'];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'ユーザー情報を更新しました。');
    }

    /** 削除（自分自身は削除不可） */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', '自分自身のアカウントは削除できません。');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'ユーザーを削除しました。');
    }

    /**
     * パスワード初期化。
     * ランダムな一時パスワードを発行し、本人へ手渡しで伝える運用（メール基盤が無いため）。
     * 発行した平文は画面に1度だけ表示する。
     */
    public function resetPassword(User $user)
    {
        $temp = $this->generateInitialPassword();

        $user->update([
            'password' => Hash::make($temp),
            // 初期化後は本人にパスワードを変更させる
            'must_change_password' => true,
        ]);

        return back()->with('reset_password', [
            'login_id' => $user->login_id,
            'password' => $temp,
        ]);
    }

    /**
     * 名簿CSVから学生アカウントを一括発行する。
     *
     * CSVの列（1行目はヘッダーとして読み飛ばす）:
     *   学籍番号, 氏名, クラス番号, 担任教師
     *
     * ログインIDは学籍番号、初期パスワードはランダム。
     * 発行結果はセッションに入れ、直後に1度だけCSVでダウンロードできるようにする。
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'roster' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'roster.required' => 'CSVファイルを選択してください。',
            'roster.mimes' => 'CSV形式のファイルを選択してください。',
        ], ['roster' => '名簿ファイル']);

        $path = $request->file('roster')->getRealPath();
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->with('error', 'ファイルを読み込めませんでした。');
        }

        $created = [];   // 新規発行できたアカウント（平文パスワード付き）
        $skipped = [];   // 既に存在していた学籍番号
        $errors = [];    // 形式不正の行
        $rowNumber = 0;

        // PHP8.4以降は $escape の既定値が変わる警告が出るため明示する
        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $rowNumber++;

            // 1行目のヘッダー行は読み飛ばす（学籍番号列が数値でない場合をヘッダーとみなす）
            if ($rowNumber === 1 && ! preg_match('/^[0-9A-Za-z\-]+$/', trim((string) ($row[0] ?? '')))) {
                continue;
            }

            // Excelで保存したCSVはBOMやSJISが混じることがあるため整える
            $row = array_map(fn ($v) => trim($this->toUtf8((string) $v)), $row);

            [$studentNumber, $studentName, $classNumber, $homeroomTeacher] = array_pad($row, 4, null);

            if ($studentNumber === null || $studentNumber === '' || $studentName === null || $studentName === '') {
                $errors[] = "{$rowNumber}行目: 学籍番号または氏名が空です。";
                continue;
            }

            if (! preg_match('/^[a-zA-Z0-9]{4,20}$/', $studentNumber)) {
                $errors[] = "{$rowNumber}行目: 学籍番号「{$studentNumber}」は英数字4〜20文字で入力してください。";
                continue;
            }

            // 学籍番号 = ログインID。既にあるならスキップ（上書きしない）
            if (User::where('login_id', $studentNumber)->orWhere('student_number', $studentNumber)->exists()) {
                $skipped[] = $studentNumber;
                continue;
            }

            $password = $this->generateInitialPassword();

            User::create([
                'login_id' => $studentNumber,
                'password' => Hash::make($password),
                'role' => 'student',
                'must_change_password' => true,
                'student_number' => $studentNumber,
                'student_name' => $studentName,
                'class_number' => $classNumber ?: null,
                'homeroom_teacher' => $homeroomTeacher ?: null,
            ]);

            $created[] = [
                'login_id' => $studentNumber,
                'student_name' => $studentName,
                'class_number' => $classNumber,
                'password' => $password,
            ];
        }

        fclose($handle);

        $message = sprintf('%d件のアカウントを発行しました。', count($created));
        if ($skipped) {
            $message .= sprintf('（既存のため%d件はスキップ）', count($skipped));
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message)
            ->with('import_result', [
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
            ]);
    }

    /**
     * 直前の一括発行結果をCSVでダウンロードする（配布用）。
     * 平文パスワードを含むため、セッションに残っている間だけ取得できる。
     */
    public function downloadImportResult(Request $request)
    {
        $result = $request->session()->get('import_result');

        if (! $result || empty($result['created'])) {
            return redirect()->route('admin.users.index')
                ->with('error', 'ダウンロードできる発行結果がありません。もう一度CSVを取り込んでください。');
        }

        // 次の画面では取得できないよう、取り出したら消す
        $request->session()->forget('import_result');

        $filename = 'accounts_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($result) {
            $out = fopen('php://output', 'w');
            // Excelで文字化けしないようBOMを付ける
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ログインID(学籍番号)', '氏名', 'クラス番号', '初期パスワード']);
            foreach ($result['created'] as $row) {
                fputcsv($out, [$row['login_id'], $row['student_name'], $row['class_number'], $row['password']]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * 初期パスワードを生成する。
     * 推測されないようランダムにし、既存のパスワード規則（英数字・数字を含む6文字以上）に合わせる。
     */
    private function generateInitialPassword(): string
    {
        return Str::lower(Str::random(6)) . random_int(10, 99);
    }

    /** SJISで保存されたCSVでも読めるようUTF-8へ変換する */
    private function toUtf8(string $value): string
    {
        // BOMを除去
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

        if (! mb_check_encoding($value, 'UTF-8')) {
            return mb_convert_encoding($value, 'UTF-8', 'SJIS-win, CP932, EUC-JP, UTF-8');
        }

        return $value;
    }

    /** 共通バリデーション */
    private function validateUser(Request $request, ?int $ignoreId, bool $passwordRequired): array
    {
        $passwordRule = $passwordRequired ? ['required'] : ['nullable'];
        $passwordRule = array_merge($passwordRule, ['string', 'min:6', 'regex:/^(?=.*[0-9])[a-zA-Z0-9]+$/']);

        return $request->validate([
            'login_id' => [
                'required', 'string', 'regex:/^[a-zA-Z0-9]{6,10}$/',
                Rule::unique('users', 'login_id')->ignore($ignoreId),
            ],
            'role' => ['required', Rule::in(['student', 'teacher'])],
            'password' => $passwordRule,
            'student_number' => ['nullable', 'string', 'max:50'],
            'class_number' => ['nullable', 'string', 'max:50'],
            'student_name' => ['nullable', 'string', 'max:100'],
            'homeroom_teacher' => ['nullable', 'string', 'max:100'],
            'teacher_number' => ['nullable', 'string', 'max:50'],
            'teacher_name' => ['nullable', 'string', 'max:100'],
        ], [
            'login_id.regex' => 'IDは英字と数字で6文字以上10文字以下にしてください。',
            'login_id.unique' => 'このIDは既に使われています。',
            'password.regex' => 'パスワードは英字と数字を使い、数字を必ず1文字以上含めてください。',
            'password.min' => 'パスワードは6文字以上にしてください。',
        ], [
            'login_id' => 'ID',
            'password' => 'パスワード',
            'role' => '権限',
        ]);
    }

    /** プロフィール列だけを抜き出す */
    private function profileData(array $validated): array
    {
        return [
            'student_number' => $validated['student_number'] ?? null,
            'class_number' => $validated['class_number'] ?? null,
            'student_name' => $validated['student_name'] ?? null,
            'homeroom_teacher' => $validated['homeroom_teacher'] ?? null,
            'teacher_number' => $validated['teacher_number'] ?? null,
            'teacher_name' => $validated['teacher_name'] ?? null,
        ];
    }
}
