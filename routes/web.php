<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ホームページのルート設定
// 未ログインで開くとログイン画面へ。ログイン済みならホームダッシュボードを表示する。
Route::get('/', function () {
    return view('home');
})->middleware('auth');

// ログイン・新規登録画面のルート設定
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', fn () => app(AuthController::class)->show('register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 空き教室予約ページのルート設定
// 先生は先生用、学生（未ログイン含む）は学生用ページへ遷移する
Route::get('/classroom-reservation', function () {
    $view = Auth::user()?->isTeacher() ? 'reservation.home.teacher' : 'reservation.home.student';
    return view($view);
})->name('classroom.reservation');

// 空き教室予約・詳細ページのルート設定
Route::get('/classroom-reservation/bulk', function () {
    return view('reservation.room.bulk-reservation');
})->name('classroom.reservation.bulk');

// 掲示板ページのルート設定
// TODO(掲示板担当): view名が未定のため一旦コメントアウト。
//   以前は '/' で登録されておりトップページ('/')を上書きして壊していたため、
//   URLは '/board' などに変更し、view('') に実ファイル名を入れて有効化してください。
// Route::get('/board', function () {
//     return view('board'); // 例: board.blade.php
// })->name('board');

// 学内Q&Aページのルート設定
Route::get('/gakunai-qna', function () {
    return view('qna.qna'); // qna/qna.blade.php を呼び出す
})->name('gakunai.qna');

// 掲示板画面ルート設定(miyata)
Route::get('/forum-top', [ForumController::class, 'index'])->name('forum.top');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');

Route::get('/gakunai-qna/create', function () {
    return view('qna.create'); 
})->name('qna.create');

Route::get('/gakunai-qna/detail', function () {
    return view('qna.detail'); 
})->name('qna.detail');

Route::get('/gakunai-qna/history', function () {
    return view('qna.history'); 
})->name('qna.history');

// イベント・締め切りカレンダーページのルート設定
Route::get('/event-calendar', function () {
    return view('eventCalendar'); //eventCalendar.blade.php を呼び出す
})->name('event.calendar');

// 欠席・遅刻届ページのルート設定
Route::get('/notification', function () {
    return view('notification.notification_tea'); //notification.blade.php を呼び出す
})->name('notification');

// 時事ニュースページは routes/news.php に定義（GNews API で取得）。
// 以前ここにあった暫定ルート（view('welcome')）は news.php と重複するため無効化。

// 近辺店舗ページのルート設定
// 近辺店舗情報マップ（一覧 / 詳細 / 申請）
Route::get('/nearby-shop', function () {
    return view('store.home');
})->name('nearby.shop');

Route::get('/nearby-shop/store/{id}', function ($id) {
    return view('store.more', ['id' => $id]);
})->name('store.more');

Route::get('/nearby-shop/request', function () {
    return view('store.request');
})->name('store.request');

Route::get('/nearby-shop/admin', function () {
    return view('store.admin');
})->name('store.admin');







// 時事ニュース関連のルート（/recentnews, /history）を読み込む
require __DIR__.'/news.php';
