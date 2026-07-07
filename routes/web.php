<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

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