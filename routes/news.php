<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/recentnews', function () {
    $category = request('category', 'all');

    $categoryMap = [
        'business' => 'business',
        'sports' => 'sports',
        'politics' => 'nation',
        'technology' => 'technology',
    ];

    $categoryLabelMap = [
        'business' => '経済',
        'sports' => 'スポーツ',
        'politics' => '政治',
        'technology' => 'IT',
    ];

    $keywordMap = [
        'business' => '経済',
        'sports' => '野球',
        'politics' => '政治',
        'technology' => '生成AI',
    ];

    $fetchArticles = function ($categoryKey, $limit = 5) use ($categoryMap, $categoryLabelMap, $keywordMap) {
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

        foreach ($articles as &$article) {
            $article['app_category'] = $categoryKey;
            $article['app_category_label'] = $categoryLabelMap[$categoryKey] ?? 'ニュース';
        }

        return $articles;
    };

    $articles = [];

    if ($category === 'all') {
        foreach (array_keys($categoryMap) as $categoryKey) {
            $articles = array_merge($articles, $fetchArticles($categoryKey, 3));
        }
    } else {
        $articles = $fetchArticles($category, 10);
    }

    return view('news.news', [
        'articles' => $articles,
        'currentCategory' => $category,
    ]);
})->name('recent.news');

Route::get('/history', function () {
    return view('news.history');
})->name('news.history');