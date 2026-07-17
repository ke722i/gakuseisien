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

        // ログイン後はホーム画面へ（authミドルウェアで弾かれた場合は元のページへ戻す）
        return redirect()->intended('/home');
    }

    /**
     * 新規登録処理。
     * ID: 英数字6〜10文字 / パスワード: 英数字かつ数字を必ず含む6文字以上。
     */
    public function register(Request $request): RedirectResponse
    {
        // エラーは 'register' バッグに入れ、画面側で新規登録タブを開いたまま表示する
        $validated = $request->validateWithBag('register', [
            'login_id' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{6,10}$/', 'unique:users,login_id'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[0-9])[a-zA-Z0-9]+$/'],
        ], [
            'login_id.regex' => 'IDは英字と数字で6文字以上10文字以下にしてください。',
            'login_id.unique' => 'このIDは既に使われています。別のIDにしてください。',
            'password.min' => 'パスワードは6文字以上にしてください。',
            'password.regex' => 'パスワードは英字と数字を使い、数字を必ず1文字以上含めてください。',
        ], [
            'login_id' => 'ID',
            'password' => 'パスワード',
        ]);

        $user = User::create([
            'login_id' => $validated['login_id'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        // 登録後はそのままログイン状態にしてホーム画面へ
        Auth::login($user);
        $request->session()->regenerate();

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
