<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * パスワード変更。
 * 名簿から発行した初期パスワード（ランダム）を、本人のパスワードに変更させる。
 * 初回ログイン時は EnsurePasswordChanged ミドルウェアによりこの画面へ誘導される。
 */
class PasswordChangeController extends Controller
{
    /** 変更フォーム */
    public function edit()
    {
        return view('auth.password-change', [
            'isForced' => (bool) Auth::user()->must_change_password,
        ]);
    }

    /** 変更処理 */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[0-9])[a-zA-Z0-9]+$/', 'confirmed'],
        ], [
            'password.min' => 'パスワードは6文字以上にしてください。',
            'password.regex' => 'パスワードは英字と数字を使い、数字を必ず1文字以上含めてください。',
            'password.confirmed' => '確認用パスワードが一致しません。',
        ], [
            'current_password' => '現在のパスワード',
            'password' => '新しいパスワード',
        ]);

        // 現在のパスワードが正しいか確認する
        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => '現在のパスワードが正しくありません。',
            ]);
        }

        // 初期パスワードと同じものへの変更は認めない
        if ($validated['current_password'] === $validated['password']) {
            throw ValidationException::withMessages([
                'password' => '現在と異なるパスワードを設定してください。',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return redirect()->route('home')->with('success', 'パスワードを変更しました。');
    }
}
