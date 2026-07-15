<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckTeacher
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 💡 ログインしているユーザーを取得
        $user = Auth::user();
        if (!$user || !($user instanceof User) || !$user->isTeacher()) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
