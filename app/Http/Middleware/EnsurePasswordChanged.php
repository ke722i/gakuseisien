<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * 名簿から一括発行したアカウントは初期パスワードがランダムなため、
 * 本人のパスワードに変更するまで他の画面を使わせない。
 */
class EnsurePasswordChanged
{
    /** 変更前でもアクセスを許可するルート名 */
    private const ALLOWED_ROUTES = [
        'password.change',
        'password.change.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password && ! in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true)) {
            return redirect()->route('password.change')
                ->with('status', '初回ログインです。パスワードを変更してください。');
        }

        return $next($request);
    }
}
