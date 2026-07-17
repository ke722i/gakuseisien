<?php

use Illuminate\Support\Facades\Route;

// 時事ニュースの閲覧履歴ページ
// ※ /recentnews 本体は routes/web.php 側（キャッシュ付きの実装）に統一した
Route::get('/history', function () {
    return view('news.history');
})->name('news.history');
