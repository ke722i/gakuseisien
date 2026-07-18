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
        // 英字6文字 + 数字2桁（登録時のパスワード規則に沿う形式）
        $temp = Str::lower(Str::random(6)) . random_int(10, 99);

        $user->update(['password' => Hash::make($temp)]);

        return back()->with('reset_password', [
            'login_id' => $user->login_id,
            'password' => $temp,
        ]);
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
