<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 学内Q&Aページのルート設定
Route::get('/gakunai-qna', function () {
    return view('qna'); // qna.blade.php を呼び出す
})->name('gakunai.qna');