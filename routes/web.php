<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\AttendanceNotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RoomReservationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

// ホームページのルート設定
// 未ログインで開くとログイン画面へ。ログイン済みならホームダッシュボードへ。
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// 時事ニュースまとめ（GNews連携・キャッシュ付き）
Route::get('/recentnews', function () {
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
            'アニメ',
            '漫画',
            'マンガ',
            '映画',
            'ドラマ',
            '俳優',
            '女優',
            '声優',
            'アイドル',
            '芸能',
            'タレント',
            '歌手',
            '音楽',
            'ライブ',
            '舞台',
            'キャスト',
            'グッズ',
            '特装版',
            '付録',
            'CD',
            'ブルーロック',
            'ゲーム',
            'Switch',
            'PS5',
            'XBOX',
            'PlayStation',
            '任天堂',
            'ポケモン',
            'ファミ通',
            'Game',
            'Game*Spark',
            'オリコン',
            'ORICON',
            'ちいかわ',
            'コラボ限定',
            'リップ',
            'スリーピングマスク',
            'キャラクター'
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
            '政治',
            '政府',
            '国会',
            '選挙',
            '首相',
            '大臣',
            '知事',
            '法案',
            '政策',
            '与党',
            '野党',
            '議員',
            '自民',
            '立憲',
            '維新',
            '公明',
            '参院',
            '衆院',
            '自治体',
            '行政',
            '補助金',
            '制度',
            '内閣',
            '外交',
            '防衛',
            '予算',
            '条例',
            '皇室',
            '天皇'
        ];

        // 経済系
        $businessWords = [
            '経済',
            '企業',
            '株',
            '株価',
            '為替',
            '円安',
            '円高',
            '物価',
            '賃上げ',
            '決算',
            '市場',
            '投資',
            '銀行',
            '日経平均',
            '金利',
            '買収',
            '売上',
            '利益',
            '事業',
            'Amazon',
            'PayPay',
            '価格',
            '値上げ',
            '消費',
            '雇用',
            '給付金',
            '税',
            '自動車',
            'EV',
            '半導体',
            'マクドナルド',
            'クレジットカード',
            '決済',
            '破産',
            '製造',
            '給与',
            '資産',
            '仮想通貨',
            '暗号資産',
            '好悪材料',
            '開示情報',
            '三菱',
            'ソニー',
            'ファミマ',
            'コンビニ',
            'インフレ'
        ];

        // IT系
        $technologyWords = [
            'IT',
            'AI',
            '生成AI',
            '人工知能',
            'テクノロジー',
            'アプリ',
            'SNS',
            'スマホ',
            'iPhone',
            'Android',
            'セキュリティ',
            'クラウド',
            'システム',
            'ソフトウェア',
            'データ',
            'ロボット',
            '半導体',
            '宇宙',
            'ウェブ',
            'Web',
            'Google',
            'Microsoft',
            'Meta',
            'SEO',
            'マーケティング',
            'AEO',
            'スタートアップ',
            'DX',
            'プログラム',
            'デジタル'
        ];

        // スポーツ系
        $sportsWords = [
            '野球',
            'サッカー',
            'バスケット',
            'バスケ',
            'バレー',
            'バレーボール',
            'テニス',
            'ゴルフ',
            '五輪',
            'オリンピック',
            '試合',
            '選手',
            '監督',
            '阪神',
            '巨人',
            '大谷',
            'ヤクルト',
            'Jリーグ',
            'W杯',
            '高校野球',
            '球団',
            '日本代表',
            'リーグ',
            'スポーツ',
            '決勝',
            '勝利',
            '敗戦',
            '得点',
            'サーブ',
            'ブラジル戦'
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
                'apikey' => trim(config('services.gnews.key') ?? ''),
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
                    'apikey' => trim(config('services.gnews.key') ?? ''),
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
})->name('recent.news');

// ホームダッシュボード（要ログイン）
// 学生: 自分の申請状況・お知らせ・直近イベントを表示
// 先生: 承認待ち件数のインボックスを表示
Route::get('/home', function () {
    $user = Auth::user();
    $today = Carbon::today();

    // 直近のイベント・締め切り（今日以降のみ・開始日時順に5件）
    // 7日以内に追加された予定には New バッジを付けて変更に気づけるようにする
    $upcomingEvents = App\Models\Event::where('start_at', '>=', $today)
        ->orderBy('start_at')
        ->limit(5)
        ->get()
        ->map(fn($e) => [
            'date' => $e->start_at->format('n/j'),
            'weekday' => ['日', '月', '火', '水', '木', '金', '土'][$e->start_at->dayOfWeek],
            'time' => $e->all_day ? '終日' : $e->start_at->format('H:i'),
            'title' => $e->title,
            'category' => $e->category,
            'is_new' => $e->created_at->gt(now()->subDays(7)),
            'days_left' => (int) $today->diffInDays($e->start_at->copy()->startOfDay()),
        ]);

    if ($user->isTeacher()) {
        // 先生: 未対応の件数を集めたインボックス
        $classPrefix = substr($user->class_number ?? '', 0, 4);

        return view('home', [
            'isTeacher' => true,
            'upcomingEvents' => $upcomingEvents,
            'pendingReservations' => App\Models\Reservation::where('status', App\Models\Reservation::STATUS_PENDING)->count(),
            'pendingShopRequests' => App\Models\ShopRequest::where('status', 'pending')->count(),
            'pendingAttendance' => $classPrefix === ''
                ? 0
                : DB::table('attendance_reports')
                    ->whereRaw('left(class_number, 4) = ?', [$classPrefix])
                    ->where('report_status', '未処理')
                    ->count(),
        ]);
    }

    // 学生: 自分の申請状況
    $statusLabels = [
        App\Models\Reservation::STATUS_PENDING => '承認待ち',
        App\Models\Reservation::STATUS_APPROVED => '承認済み',
        App\Models\Reservation::STATUS_REJECTED => '承認拒否',
    ];

    $myReservations = App\Models\Reservation::where('user_id', $user->id)
        ->where('reservation_date', '>=', $today)
        ->with('room')
        ->orderBy('reservation_date')
        ->orderBy('period')
        ->limit(3)
        ->get()
        ->map(fn($r) => [
            'room' => $r->room?->name ?? '不明',
            'date' => $r->reservation_date->format('n/j'),
            'period' => $r->period . '限',
            'status' => $r->status,
            'status_label' => $statusLabels[$r->status] ?? '承認待ち',
        ]);

    // 欠席届: 自分の学籍番号で出した直近の届（学籍番号未設定なら空）
    $myAttendance = $user->student_number
        ? DB::table('attendance_reports')
            ->where('student_number', $user->student_number)
            ->orderByDesc('created_at')
            ->limit(2)
            ->get()
        : collect();

    // 自分のお知らせ（最新5件 + 未読数）
    $notifications = App\Models\UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->limit(5)
        ->get();
    $unreadCount = App\Models\UserNotification::where('user_id', $user->id)->unread()->count();

    return view('home', [
        'isTeacher' => false,
        'upcomingEvents' => $upcomingEvents,
        'myReservations' => $myReservations,
        'myAttendance' => $myAttendance,
        'notifications' => $notifications,
        'unreadCount' => $unreadCount,
    ]);
})->name('home')->middleware('auth');

// お知らせを全件既読にする（学生がホームで「すべて既読にする」を押したとき）
Route::post('/home/notifications/read', function () {
    App\Models\UserNotification::where('user_id', Auth::id())
        ->unread()
        ->update(['read_at' => now()]);

    return redirect()->route('home');
})->name('home.notifications.read')->middleware('auth');

// ログイン画面のルート設定
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// アカウントは教職員が名簿（CSV）から発行する運用のため、学生のセルフ登録は行わない。
// 旧URLを踏んでもエラーにならないよう、ログイン画面へ案内する。
Route::get('/register', fn() => redirect()->route('login')
    ->with('error', 'アカウントは学校から配布されます。配布されたIDとパスワードでログインしてください。'))
    ->name('register');

// パスワード変更（初回ログイン時は必須。それ以降も任意で変更可能）
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'edit'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.change.update');
});

// アカウント管理（教職員のみ）：ユーザーのCRUD・権限付与・パスワード初期化
Route::middleware('teacher')->group(function () {
    Route::get('/admin/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [App\Http\Controllers\AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [App\Http\Controllers\AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [App\Http\Controllers\AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{user}/reset-password', [App\Http\Controllers\AdminUserController::class, 'resetPassword'])->name('admin.users.resetPassword');

    // 名簿CSVからの一括アカウント発行と、発行結果（初期パスワード付き）のダウンロード
    Route::post('/admin/users/import', [App\Http\Controllers\AdminUserController::class, 'importCsv'])->name('admin.users.import');
    Route::get('/admin/users/import/result', [App\Http\Controllers\AdminUserController::class, 'downloadImportResult'])->name('admin.users.import.result');

    // 空き教室設定：教室の登録・編集・削除、利用不可時間帯の設定
    Route::get('/admin/rooms', [App\Http\Controllers\RoomAdminController::class, 'index'])->name('admin.rooms.index');
    Route::post('/admin/rooms', [App\Http\Controllers\RoomAdminController::class, 'store'])->name('admin.rooms.store');
    Route::patch('/admin/rooms/{room}', [App\Http\Controllers\RoomAdminController::class, 'update'])->name('admin.rooms.update');
    Route::delete('/admin/rooms/{room}', [App\Http\Controllers\RoomAdminController::class, 'destroy'])->name('admin.rooms.destroy');
    Route::post('/admin/rooms/unavailable', [App\Http\Controllers\RoomAdminController::class, 'storeUnavailable'])->name('admin.rooms.unavailable.store');
    Route::delete('/admin/rooms/unavailable/{slot}', [App\Http\Controllers\RoomAdminController::class, 'destroyUnavailable'])->name('admin.rooms.unavailable.destroy');

    // 通報管理（掲示板・学内Q&A の両方）
    Route::get('/admin/reports', [App\Http\Controllers\ReportAdminController::class, 'index'])->name('adminReports');
    Route::delete('/admin/reports/{report}', [App\Http\Controllers\ReportAdminController::class, 'destroy'])->name('admin.reports.destroy');
});

// 空き教室予約ページのルート設定
// 先生は先生用、学生（未ログイン含む）は学生用ページへ遷移する
Route::get('/classroom-reservation', function () {
    $view = Auth::user()?->isTeacher() ? 'reservation.home.teacher' : 'reservation.home.student';
    return view($view);
})->name('classroom.reservation');

// 空き教室予約・詳細ページのルート設定（フロアマップ表示・予約作成）
Route::get('/classroom-reservation/room', [RoomReservationController::class, 'index'])->name('classroom.reservation.room');
Route::post('/classroom-reservation/room', [RoomReservationController::class, 'store'])
    ->name('classroom.reservation.store')
    ->middleware('auth');

// 予約一覧ページのルート設定（自分の予約のみ）
Route::get('/classroom-reservation/list', [RoomReservationController::class, 'list'])
    ->name('classroom.reservation.list')
    ->middleware('auth');
Route::patch('/classroom-reservation/{reservation}', [RoomReservationController::class, 'update'])
    ->name('classroom.reservation.update')
    ->middleware('auth');
Route::delete('/classroom-reservation/{reservation}', [RoomReservationController::class, 'destroy'])
    ->name('classroom.reservation.destroy')
    ->middleware('auth');

// 予約管理ページのルート設定（先生のみ・承認/拒否）
Route::get('/classroom-reservation/manage', [RoomReservationController::class, 'manage'])
    ->name('classroom.reservation.manage')
    ->middleware('teacher');
Route::patch('/classroom-reservation/{reservation}/approve', [RoomReservationController::class, 'approve'])
    ->name('classroom.reservation.approve')
    ->middleware('teacher');
Route::patch('/classroom-reservation/{reservation}/reject', [RoomReservationController::class, 'reject'])
    ->name('classroom.reservation.reject')
    ->middleware('teacher');
// 教室一括予約ページのルート設定（教職員のみ）
Route::middleware('teacher')->group(function () {
    Route::get('/classroom-reservation/bulk', [RoomReservationController::class, 'bulk'])
        ->name('classroom.reservation.bulk');
    Route::post('/classroom-reservation/bulk', [RoomReservationController::class, 'bulkStore'])
        ->name('classroom.reservation.bulk.store');
});

// 掲示板ページのルート設定
// TODO(掲示板担当): view名が未定のため一旦コメントアウト。
//   以前は '/' で登録されておりトップページ('/')を上書きして壊していたため、
//   URLは '/board' などに変更し、view('') に実ファイル名を入れて有効化してください。
// Route::get('/board', function () {
//     return view('board'); // 例: board.blade.php
// })->name('board');

// 掲示板画面ルート設定(miyata)
// 要件「学外の一般ユーザーによる閲覧・投稿は対象外」に合わせ、掲示板は
// 閲覧・投稿・返信すべてログイン必須とする（本人チェックはコントローラー側）。
Route::middleware('auth')->group(function () {
    Route::get('/forum-top', [ForumController::class, 'index'])->name('forum.top');
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{post}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('/forum/{post}/edit', [ForumController::class, 'edit'])->name('forum.edit');
    Route::patch('/forum/{post}', [ForumController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/{post}/reply', [ForumController::class, 'storeReply'])->name('forum.reply.store');
    Route::patch('/forum/replies/{reply}', [ForumController::class, 'updateReply'])->name('forum.reply.update');
    Route::delete('/forum/replies/{reply}', [ForumController::class, 'destroyReply'])->name('forum.reply.destroy');
    Route::post('/forum/{post}/report', [ForumController::class, 'reportPost'])->name('forum.report');
});

// 学内Q&Aページのルート設定
use App\Http\Controllers\QnaController;

// 固定のURL
Route::get('/gakunai-qna', [QnaController::class, 'index'])->name('gakunai.qna');
Route::get('/gakunai-qna/create', [QnaController::class, 'create'])->name('qna.create')->middleware('auth');
Route::post('/gakunai-qna/store', [QnaController::class, 'store'])->name('qna.store')->middleware('auth');
Route::get('/gakunai-qna/history', [QnaController::class, 'history'])->name('qna.history');
// 通報管理は掲示板とQ&Aの両方を扱うため、専用ルートに集約している（下部の /admin/reports を参照）

// 2. 動的なURL
Route::delete('/gakunai-qna/{id}', [QnaController::class, 'destroy'])->name('qna.destroy')->middleware('auth');
Route::get('/gakunai-qna/{id}', [QnaController::class, 'show'])->name('qna.detail');
Route::post('/gakunai-qna/{id}/answers', [QnaController::class, 'storeAnswer'])->name('qna.storeAnswer')->middleware('auth');
Route::patch('/gakunai-qna/{id}/best-answer/{answer_id}', [QnaController::class, 'selectBestAnswer'])->name('qna.bestAnswer')->middleware('auth');
Route::post('/gakunai-qna/{id}/report', [QnaController::class, 'reportQuestion'])->name('qna.report');
Route::post('/qna/answers/{answer}/upvote', [App\Http\Controllers\QnaController::class, 'toggleUpvote'])
    ->name('qna.answers.upvote')
    ->middleware('auth');
Route::delete('/qna/answers/{answer}', [App\Http\Controllers\QnaController::class, 'destroyAnswer'])
    ->name('qna.destroyAnswer')
    ->middleware('auth');
Route::post('/qna/answers/{id}/approve', [QnaController::class, 'approveAnswer'])->name('qna.answers.approve');

// イベント・締め切りカレンダーページのルート設定
Route::get('/event-calendar', [EventController::class, 'index'])->name('event.calendar');
Route::get('/event-calendar/day/{date}', [EventController::class, 'day'])->name('event.day');
Route::post('/event-calendar', [EventController::class, 'store'])->name('event.store')->middleware('teacher');
Route::patch('/event-calendar/{event}', [EventController::class, 'update'])->name('event.update')->middleware('teacher');
Route::delete('/event-calendar/{event}', [EventController::class, 'destroy'])->name('event.destroy')->middleware('teacher');

// 欠席・遅刻届ページのルート設定
Route::get('/notification', function () {
    $user = Auth::user();

    if ($user?->isTeacher()) {
        $classPrefix = substr($user->class_number ?? '', 0, 4);
        $reports = DB::table('attendance_reports')
            ->whereRaw('left(class_number, 4) = ?', [$classPrefix])
            ->orderBy('submission_date', 'desc')
            ->get();

        return view('notification.notification_tea', compact('reports', 'classPrefix'));
    }

    // 学生：自分の学籍番号で提出した届の履歴をログ欄に表示する
    $myReports = $user?->student_number
        ? DB::table('attendance_reports')
            ->where('student_number', $user->student_number)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
        : collect();

    // 学籍番号・クラス・氏名・担任はアカウント情報から自動入力する（$user）
    // 提出日・対象日の初期値は本日（$today）
    return view('notification.notification_stu', [
        'myReports' => $myReports,
        'user' => $user,
        'today' => now()->toDateString(),
    ]);
})->name('notification');

// 欠席・遅刻届フォームの送信（POSTリクエスト）を受け付けるURLとコントローラーの紐付け
Route::post('/notification/store', [AttendanceNotificationController::class, 'storeNotification'])
    ->name('notification.store');

// 教師が「受理」または「差し戻し」の処理を行うためのURL
Route::post('/teacher/notification/{id}/decide', [AttendanceNotificationController::class, 'decideNotificationType'])
    ->name('notification.decide')
    ->middleware('teacher');

// 近辺店舗ページのルート設定
// 近辺店舗情報マップ（一覧 / 詳細 / 申請）
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ReviewController;

// 一般公開（誰でも閲覧・検索・申請できる）
Route::get('/nearby-shop', [ShopController::class, 'index'])
    ->name('nearby.shop');

Route::get('/nearby-shop/search', [ShopController::class, 'search'])
    ->name('store.search');

// 店舗のお気に入り登録・解除（ログイン必須）
Route::post('/nearby-shop/{shop}/favorite', [ShopController::class, 'toggleFavorite'])
    ->name('store.favorite.toggle')
    ->middleware('auth');

Route::get('/nearby-shop/store/{id}', [ShopController::class, 'show'])
    ->name('store.more');

Route::get('/nearby-shop/request', [ShopController::class, 'request'])
    ->name('store.request');

// 店舗申請を保存
Route::post('/nearby-shop/request', [ShopController::class, 'storeRequest'])
    ->name('store.request.store');

// 店舗管理（先生アカウント専用）
Route::middleware('teacher')->group(function () {
    Route::get('/nearby-shop/admin', [ShopController::class, 'admin'])
        ->name('store.admin');

    Route::get('/nearby-shop/request/{id}', [ShopController::class, 'requestMore'])
        ->name('store.request.more');

    // 承認
    Route::post('/nearby-shop/request/{id}/approve', [ShopController::class, 'approve'])
        ->name('store.request.approve');

    // 却下
    Route::post('/nearby-shop/request/{id}/reject', [ShopController::class, 'reject'])
        ->name('store.request.reject');

    Route::get('/nearby-shop/admin/edit/{id}', [ShopController::class, 'edit'])
        ->name('store.edit');

    Route::post('/nearby-shop/admin/update/{id}', [ShopController::class, 'update'])
        ->name('store.update');

    Route::post('/nearby-shop/admin/hide/{id}', [ShopController::class, 'hide'])
        ->name('store.hide');

    Route::post('/nearby-shop/admin/delete/{id}', [ShopController::class, 'destroy'])
        ->name('store.destroy');
});

// 口コミ投稿
Route::post('/nearby-shop/store/{id}/review', [ReviewController::class, 'store'])
    ->name('reviews.store');

Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->name('reviews.edit');

Route::put('/reviews/{review}', [ReviewController::class, 'update'])
    ->name('reviews.update');

Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->name('reviews.destroy');


// 時事ニュース関連のルート（/recentnews, /history）を読み込む
require __DIR__ . '/news.php';
