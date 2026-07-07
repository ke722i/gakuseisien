<?php

use Illuminate\Support\Facades\Route;

// ホームページのルート設定
Route::get('/', function () {
    return view('welcome');
});

// 空き教室予約ページのルート設定
Route::get('/classroom-reservation', function () {
    return view('reservation.home.teacher');
})->name('classroom.reservation');

// 掲示板ページのルート設定 
Route::get('/bulletin-board', function () { //担当者へ、ファイル名違ったら修正してください
    return view('bulletinBoard');
})->name('bulletin.board');

// 学内Q&Aページのルート設定
Route::get('/gakunai-qna', function () {
    return view('qna'); // qna.blade.php を呼び出す
})->name('gakunai.qna');

// イベント・締め切りカレンダーページのルート設定
Route::get('/event-calendar', function () {
    return view('eventCalendar'); //eventCalendar.blade.php を呼び出す
})->name('event.calendar');

// 欠席・遅刻届ページのルート設定
Route::get('/notification', function () {
    return view('notofication'); //notofication.blade.php を呼び出す
})->name('notification');

// 時事ニュースページのルート設定
Route::get('/recentnews', function() { //担当者へ、ファイル名違ったら修正してください
    return view('welcome'); // recentNews.blade.php を呼び出す 
})->name('recent.news');

// 近辺店舗ページのルート設定
Route::get('/nearby-shop', function () { //担当者へ、ファイル名違ったら修正してください
    return view('nearbyShop'); //nearbyShop.blade.phpを呼び出す
})->name('nearby.shop');