<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ログイン・新規登録画面を表示する。
     * $tab で初期表示タブ（'login' or 'register'）を切り替える。
     */
    public function show(string $tab = 'login')
    {
        // 既にログイン済みならホーム画面へ
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login', ['tab' => $tab]);
    }

    /**
     * ログイン処理。login_id と password で認証する。
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'login_id' => 'ID',
            'password' => 'パスワード',
        ]);

        // login_id と password が一致すればセッションを開始
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login_id' => 'IDまたはパスワードが正しくありません。',
            ]);
        }

        $request->session()->regenerate();

        // 初期パスワードのままなら、まずパスワード変更画面へ誘導する
        if (Auth::user()->must_change_password) {
            return redirect()->route('password.change');
        }

        // ログイン後はホーム画面へ（authミドルウェアで弾かれた場合は元のページへ戻す）
        return redirect()->intended('/home');
    }

    /**
     * ログアウト処理。
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
