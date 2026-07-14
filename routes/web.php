<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

// ホームページのルート設定
// 未ログインで開くとログイン画面へ。ログイン済みならホームダッシュボードを表示する。
Route::get('/', function () {
    $category = request('category', 'all');

    // 存在しないカテゴリーが来たら「すべて」に戻す
    if (!in_array($category, ['all', 'business', 'sports', 'politics', 'technology'])) {
        $category = 'all';
    }

    // GNewsのカテゴリー対応
    $categoryMap = [
        'business' => 'business',
        'sports' => 'sports',
        'politics' => 'nation',
        'technology' => 'technology',
    ];

    // 画面に表示するカテゴリー名
    $categoryLabelMap = [
        'business' => '経済',
        'sports' => 'スポーツ',
        'politics' => '政治',
        'technology' => 'IT',
    ];

    // top-headlinesで取得できなかった時の検索キーワード
    $keywordMap = [
        'business' => '経済',
        'sports' => 'スポーツ',
        'politics' => '政治',
        'technology' => '生成AI',
    ];

    // エンタメ系の記事を除外する関数
    $isEntertainment = function ($article) {
        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $source = $article['source']['name'] ?? '';
        $content = $title . ' ' . $description . ' ' . $source;

        $entertainmentWords = [
            'アニメ', '漫画', 'マンガ', '映画', 'ドラマ', '俳優', '女優',
            '声優', 'アイドル', '芸能', 'タレント', '歌手', '音楽',
            'ライブ', '舞台', 'キャスト', 'グッズ', '特装版', '付録',
            'CD', 'ブルーロック', 'ゲーム', 'Switch', 'PS5', 'XBOX',
            'PlayStation', '任天堂', 'ポケモン', 'ファミ通', 'Game',
            'Game*Spark', 'オリコン', 'ORICON', 'ちいかわ', 'コラボ限定',
            'リップ', 'スリーピングマスク', 'キャラクター'
        ];

        foreach ($entertainmentWords as $word) {
            if (mb_stripos($content, $word) !== false) {
                return true;
            }
        }

        return false;
    };

    // 記事内容からカテゴリーを判定する関数
    $detectCategory = function ($article) use ($isEntertainment) {
        if ($isEntertainment($article)) {
            return [null, null];
        }

        $title = $article['title'] ?? '';
        $description = $article['description'] ?? '';
        $source = $article['source']['name'] ?? '';
        $content = $title . ' ' . $description . ' ' . $source;

        // 政治系
        $politicsWords = [
            '政治', '政府', '国会', '選挙', '首相', '大臣',
            '知事', '法案', '政策', '与党', '野党', '議員',
            '自民', '立憲', '維新', '公明', '参院', '衆院',
            '自治体', '行政', '補助金', '制度', '内閣',
            '外交', '防衛', '予算', '条例', '皇室', '天皇'
        ];

        // 経済系
        $businessWords = [
            '経済', '企業', '株', '株価', '為替', '円安', '円高',
            '物価', '賃上げ', '決算', '市場', '投資', '銀行',
            '日経平均', '金利', '買収', '売上', '利益', '事業',
            'Amazon', 'PayPay', '価格', '値上げ', '消費', '雇用',
            '給付金', '税', '自動車', 'EV', '半導体', 'マクドナルド',
            'クレジットカード', '決済', '破産', '製造', '給与',
            '資産', '仮想通貨', '暗号資産', '好悪材料', '開示情報',
            '三菱', 'ソニー', 'ファミマ', 'コンビニ', 'インフレ'
        ];

        // IT系
        $technologyWords = [
            'IT', 'AI', '生成AI', '人工知能', 'テクノロジー',
            'アプリ', 'SNS', 'スマホ', 'iPhone', 'Android',
            'セキュリティ', 'クラウド', 'システム', 'ソフトウェア',
            'データ', 'ロボット', '半導体', '宇宙', 'ウェブ',
            'Web', 'Google', 'Microsoft', 'Meta', 'SEO',
            'マーケティング', 'AEO', 'スタートアップ', 'DX',
            'プログラム', 'デジタル'
        ];

        // スポーツ系
        $sportsWords = [
            '野球', 'サッカー', 'バスケット', 'バスケ', 'バレー',
            'バレーボール', 'テニス', 'ゴルフ', '五輪', 'オリンピック',
            '試合', '選手', '監督', '阪神', '巨人', '大谷',
            'ヤクルト', 'Jリーグ', 'W杯', '高校野球', '球団',
            '日本代表', 'リーグ', 'スポーツ', '決勝', '勝利',
            '敗戦', '得点', 'サーブ', 'ブラジル戦'
        ];

        foreach ($politicsWords as $word) {
            if (mb_stripos($content, $word) !== false) {
                return ['politics', '政治'];
            }
        }

        foreach ($businessWords as $word) {
            if (mb_stripos($content, $word) !== false) {
                return ['business', '経済'];
            }
        }

        foreach ($technologyWords as $word) {
            if (mb_stripos($content, $word) !== false) {
                return ['technology', 'IT'];
            }
        }

        foreach ($sportsWords as $word) {
            if (mb_stripos($content, $word) !== false) {
                return ['sports', 'スポーツ'];
            }
        }

        // どれにも分類できない記事は表示しない
        return [null, null];
    };

    // 並び替え用の関数
    $sortArticles = function (&$articles) {
        usort($articles, function ($a, $b) {
            $timeA = strtotime($a['publishedAt'] ?? '');
            $timeB = strtotime($b['publishedAt'] ?? '');

            $dateA = date('Y-m-d', $timeA);
            $dateB = date('Y-m-d', $timeB);

            // 日付が違う場合は、新しい日付を上にする
            if ($dateA !== $dateB) {
                return strtotime($dateB) <=> strtotime($dateA);
            }

            // 同じ日付なら、時間が遅い記事を上にする
            if ($timeA !== $timeB) {
                return $timeB <=> $timeA;
            }

            // 日付も時間も同じ場合は、タイトル順
            return strcmp($a['title'] ?? '', $b['title'] ?? '');
        });
    };

    // タイトル先頭10文字で重複削除する関数
    $removeDuplicateArticles = function ($articles) {
        return collect($articles)
            ->unique(function ($article) {
                $title = $article['title'] ?? '';

                // 半角・全角スペースを削除
                $titleKey = str_replace([' ', '　'], '', $title);

                // 先頭10文字を重複判定キーにする
                return mb_substr($titleKey, 0, 10);
            })
            ->values()
            ->all();
    };

    // GNewsから記事を取得する関数
    $fetchArticles = function ($categoryKey, $limit = 5) use ($categoryMap, $keywordMap) {
        $gnewsCategory = $categoryMap[$categoryKey] ?? 'general';

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

        // 取得できなかった場合だけ検索APIを使う
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

        return $articles;
    };

    // 指定日のニュースを取得・保存する関数
    $getDailyArticles = function ($date) use (
        $fetchArticles,
        $detectCategory,
        $removeDuplicateArticles,
        $sortArticles
    ) {
        $cacheKey = 'daily_news_' . $date;

        return Cache::remember($cacheKey, now()->addWeek(), function () use (
            $fetchArticles,
            $detectCategory,
            $removeDuplicateArticles,
            $sortArticles
        ) {
            $articles = [];

            // 4カテゴリーから取得
            foreach (['business', 'sports', 'politics', 'technology'] as $categoryKey) {
                // 各カテゴリー5件ずつ取得
                // GNews側の制限がある場合は、実際には指定数より少ない場合もある
                $categoryArticles = $fetchArticles($categoryKey, 5);

                foreach ($categoryArticles as $article) {
                    // 取得元カテゴリーではなく、記事内容で再分類する
                    [$detectedCategory, $detectedLabel] = $detectCategory($article);

                    if ($detectedCategory === null) {
                        continue;
                    }

                    $article['app_category'] = $detectedCategory;
                    $article['app_category_label'] = $detectedLabel;

                    $articles[] = $article;
                }
            }

            // 重複削除
            $articles = $removeDuplicateArticles($articles);

            // 最新順に並び替え
            $sortArticles($articles);

            // 1日分として最大10件保存
            return array_slice($articles, 0, 10);
        });
    };

    $today = now()->format('Y-m-d');

    // 今日のニュースを取得
    // 今日すでに保存済みならAPIは呼ばず、キャッシュから取る
    $todayArticles = $getDailyArticles($today);

    $articles = [];

    if ($category === 'all') {
        // すべて：今日保存したニュースだけ最大10件表示
        $articles = $todayArticles;
    } else {
        // カテゴリー別：今日から過去6日分、合計1週間分から同カテゴリーだけ集める
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $cacheKey = 'daily_news_' . $date;

            // 今日だけは、上で取得した $todayArticles を使う
            // 過去の日付は、保存済みキャッシュがあれば使う
            if ($date === $today) {
                $dailyArticles = $todayArticles;
            } else {
                $dailyArticles = Cache::get($cacheKey, []);
            }

            foreach ($dailyArticles as $article) {
                if (($article['app_category'] ?? '') === $category) {
                    $articles[] = $article;
                }
            }
        }

        // 重複削除
        $articles = $removeDuplicateArticles($articles);

        // 最新順に並び替え
        $sortArticles($articles);

        // カテゴリー別は過去1週間分から最大10件表示
        $articles = array_slice($articles, 0, 10);
    }

    return view('news.news', [
        'articles' => $articles,
        'currentCategory' => $category,
    ]);
});

// ホームダッシュボード
Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/history', function () {
    return view('news.history');
});

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
Route::get('/classroom-reservation/room', function () {
    return view('reservation.room.room-reservation');
})->name('classroom.reservation.room');
// 予約一覧ページのルート設定
Route::get('/classroom-reservation/list', function () {
    $reservations = [
        [
            'room' => '101c',
            'date' => '2026/06/26',
            'weekday' => '(金)',
            'period' => '1限',
            'time' => '(9:15〜10:45)',
            'status' => 'rejected',
            'status_label' => '承認拒否',
        ],
        [
            'room' => '101c',
            'date' => '2026/06/26',
            'weekday' => '(金)',
            'period' => '2限',
            'time' => '(11:00〜12:30)',
            'status' => 'approved',
            'status_label' => '承認済み',
        ],
        [
            'room' => '101c',
            'date' => '2026/06/30',
            'weekday' => '(火)',
            'period' => '3限',
            'time' => '(13:30〜15:00)',
            'status' => 'pending',
            'status_label' => '承認待ち',
        ],
    ];
    return view('reservation.room.reservation-list', compact('reservations'));
})->name('classroom.reservation.list');
// 予約管理ページのルート設定
Route::get('/classroom-reservation/manage', function () {
    if (!Auth::check() || !Auth::user()->isTeacher()) {
        return redirect()->route('classroom.reservation');
    }
    return view('reservation.room.reservation-management');
})->name('classroom.reservation.manage');
// 教室一覧予約ページのルート設定
Route::get('/classroom-reservation/bulk', function () {
    if (!Auth::check() || !Auth::user()->isTeacher()) {
        return redirect()->route('classroom.reservation');
    }
    return view('reservation.room.bulk-room-reservation');
})->name('classroom.reservation.bulk');

// 掲示板ページのルート設定
// TODO(掲示板担当): view名が未定のため一旦コメントアウト。
//   以前は '/' で登録されておりトップページ('/')を上書きして壊していたため、
//   URLは '/board' などに変更し、view('') に実ファイル名を入れて有効化してください。
// Route::get('/board', function () {
//     return view('board'); // 例: board.blade.php
// })->name('board');

// 掲示板画面ルート設定(miyata)
Route::get('/forum-top', [ForumController::class, 'index'])->name('forum.top');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');

// 学内Q&Aページのルート設定
use App\Http\Controllers\QnaController;

Route::delete('/gakunai-qna/{id}', [QnaController::class, 'destroy'])->name('qna.destroy');
Route::get('/gakunai-qna', [QnaController::class, 'index'])->name('gakunai.qna');
Route::get('/gakunai-qna/create', [QnaController::class, 'create'])->name('qna.create');
Route::post('/gakunai-qna/store', [QnaController::class, 'store'])->name('qna.store');
Route::get('/gakunai-qna/history', [QnaController::class, 'history'])->name('qna.history');
Route::get('/gakunai-qna/{id}', [QnaController::class, 'show'])->name('qna.detail');
Route::post('/gakunai-qna/{id}/answers', [QnaController::class, 'storeAnswer'])->name('qna.storeAnswer');

Route::get('/gakunai-qna/create', function () {
    return view('qna.create'); 
})->name('qna.create');

// イベント・締め切りカレンダーページのルート設定
Route::get('/event-calendar', [EventController::class, 'index'])->name('event.calendar');
Route::get('/event-calendar/day/{date}', [EventController::class, 'day'])->name('event.day');
Route::post('/event-calendar', [EventController::class, 'store'])->name('event.store');

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
