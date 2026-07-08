<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// ホームページのルート設定
// 未ログインで開くとログイン画面へ。ログイン済みならホーム(welcome)を表示する。
Route::get('/', function () {
    $category = request('category', 'all');

    // 画面側のカテゴリー
    $categoryMap = [
        'business' => 'business',
        'sports' => 'sports',
        'politics' => 'nation',
        'technology' => 'technology',
    ];

    // 表示する日本語カテゴリー名
    $categoryLabelMap = [
        'business' => '経済',
        'sports' => 'スポーツ',
        'politics' => '政治',
        'technology' => 'IT',
    ];

    // top-headlinesで取れなかった時の予備検索キーワード
    $keywordMap = [
        'business' => '経済',
        'sports' => '野球',
        'politics' => '政治',
        'technology' => '生成AI',
    ];

    // ニュース取得用の関数
    $fetchArticles = function ($categoryKey, $limit = 5) use ($categoryMap, $categoryLabelMap, $keywordMap) {
        $gnewsCategory = $categoryMap[$categoryKey] ?? 'general';

        // ① まずカテゴリーで取得
        $response = Http::withoutVerifying()
            ->get('https://gnews.io/api/v4/top-headlines', [
                'category' => $gnewsCategory,
                'lang' => 'ja',
                'country' => 'jp',
                'max' => $limit,
                'apikey' => trim(env('GNEWS_API_KEY')),
            ]);

        $articles = [];

        if ($response->successful()) {
            $articles = $response->json('articles') ?? [];
        }

        // ② カテゴリーで0件ならキーワード検索
        if (count($articles) === 0) {
            $searchResponse = Http::withoutVerifying()
                ->get('https://gnews.io/api/v4/search', [
                    'q' => $keywordMap[$categoryKey] ?? '日本',
                    'lang' => 'ja',
                    'country' => 'jp',
                    'max' => $limit,
                    'apikey' => trim(env('GNEWS_API_KEY')),
                ]);

            if ($searchResponse->successful()) {
                $articles = $searchResponse->json('articles') ?? [];
            }
        }

        // アプリ側で使うカテゴリー情報を追加
        foreach ($articles as &$article) {
            $article['app_category'] = $categoryKey;
            $article['app_category_label'] = $categoryLabelMap[$categoryKey] ?? 'ニュース';
        }

        return $articles;
    };

    $articles = [];

    // すべての場合：各カテゴリーから取得して混ぜる
    if ($category === 'all') {
        foreach (array_keys($categoryMap) as $categoryKey) {
            $categoryArticles = $fetchArticles($categoryKey, 3);
            $articles = array_merge($articles, $categoryArticles);
        }
    } else {
        // 個別カテゴリーの場合
        $articles = $fetchArticles($category, 10);
    }

    return view('news.news', [
        'articles' => $articles,
        'currentCategory' => $category,
    ]);
});

Route::get('/history', function () {
    return view('news.history');
});
    return view('welcome');
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
