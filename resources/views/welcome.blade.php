<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>時事ニュースまとめ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'news'])

        <!-- メイン画面 -->
        <main class="content">

            <div class="content-header">
                <div>
                    <h1>時事ニュースまとめ</h1>
                    <p>学生に役立つ時事ニュースをまとめて掲載しています。</p>
                </div>

                <button class="history-button">閲覧履歴</button>
            </div>

            <!-- カテゴリーボタン -->
            <div class="category-tabs">
                <button class="selected">すべて</button>
                <button>経済</button>
                <button>スポーツ</button>
                <button>政治</button>
                <button>IT</button>
                <button>その他</button>
            </div>

            <!-- ニュース一覧 -->
            <section class="news-list">

                <article class="news-card open">
                    <div class="news-top">
                        <div>
                            <div class="news-meta">
                                <span class="tag sports">スポーツ</span>
                                <span>2024-05-16・スポーツニッポン</span>
                            </div>
                            <h2>パリ五輪日本代表選手団の構成発表</h2>
                        </div>

                        <button class="circle-button">⌃</button>
                    </div>

                    <div class="news-detail">
                        <p class="news-summary">
                            今夏開催のパリ五輪に向け、日本代表選手団の最終選考結果が発表された。
                            陸上・水泳・体操など各競技から計200名超が選出された。
                        </p>

                        <a href="https://example.com" target="_blank" class="read-more">
                            続きを読む
                        </a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-top">
                        <div>
                            <div class="news-meta">
                                <span class="tag economy">経済</span>
                                <span>2024-05-16・NHK国際放送</span>
                            </div>
                            <h2>G7サミット、気候変動対策で合意文書</h2>
                        </div>

                        <button class="circle-button">⌄</button>
                    </div>

                    <div class="news-detail">
                        <p class="news-summary">
                            G7サミットで、各国は気候変動対策を強化するための合意文書を発表しました。
                            再生可能エネルギーの活用や温室効果ガスの削減が重要なテーマとなっています。
                        </p>

                        <a href="https://example.com" target="_blank" class="read-more">
                            続きを読む
                        </a>
                    </div>
                </article>

            </section>
        </main>
    </div>
</body>

</html>