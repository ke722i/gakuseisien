<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\AuthController;
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
Route::get('/classroom-reservation', function () {
    return view('reservation.home.teacher');
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
    return view('qna'); // qna.blade.php を呼び出す
})->name('gakunai.qna');


// 掲示板画面ルート設定(miyata)
Route::get('/forum-top', [ForumController::class, 'index'])->name('forum.top');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    return view('qna.qna'); // qna.blade.php を呼び出す
})->name('gakunai.qna');

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

// 時事ニュースページのルート設定
Route::get('/recentnews', function() { //担当者へ、ファイル名違ったら修正してください
    return view('welcome'); // recentNews.blade.php を呼び出す 
})->name('recent.news');

// 近辺店舗ページのルート設定
Route::get('/nearby-shop', function () { //担当者へ、ファイル名違ったら修正してください
    return view('nearbyShop'); //nearbyShop.blade.phpを呼び出す
})->name('nearby.shop');
