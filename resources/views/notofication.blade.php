<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>遅刻・欠席届</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/notofication.css'])
</head>

<?php
    $today = date("Y-m-d");

?>

<body>
    
    <div class="app-layout">

        <!-- 左サイドバー -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>学生支援.com</h2>
                <p>学校便利掲示板システム</p>
            </div>

            <nav class="sidebar-menu">
                <a href="#">ホーム</a>
                <a href="#">空き教室予約</a>
                <a href="#">掲示板</a>
                <a href="{{ route('gakunai.qna') }}">学内Q＆A</a>
                <a href="#">イベント・締切カレンダー</a>
                <a href="{{ route('notofication') }}" class="active">欠席・遅刻届</a>
                <a href="#">時事ニュースまとめ</a>
                <a href="#">近辺店舗情報マップ</a>
            </nav>

            <div class="logout">
                <a href="#">ログアウト</a>
            </div>
        </aside>

        <!-- メイン画面 -->
        <main class="content">
            <div class="content-header">
                <h1>欠席・遅刻届</h1>
            </div>

            <div class="content-inner">
                <form method="post" action="#">
                    @csrf
                    <div class="grid-2">
                        <div class="panel-left">
                            <div class="field"><label>学籍番号</label><input type="text" value="234000" disabled></div>
                            <div class="field"><label>クラス番号</label><input type="text" value="R4SA00" disabled></div>
                            <div class="field"><label>名前</label><input type="text" value="情報太郎" disabled></div>
                            <div class="field"><label>日付</label><input type="date" value="<?= $today ?>"></div>
                            <div class="field"><label>時限</label>
                                <div class="time-box">
                                    <label><input type="checkbox">1</label>
                                    <label><input type="checkbox">2</label>
                                    <label><input type="checkbox">3</label>
                                    <label><input type="checkbox">4</label>
                                </div>
                            </div>
                        </div>

                        <div class="panel-right">
                            <div class="field"><label>提出日</label><input type="text" value="<?= $today ?>" disabled></div>
                            <div class="field"><label>担任教師</label><input type="text" value="情報教師" disabled></div>
                            <div class="field"><label>科目教師</label>
                                <select>
                                    <option>科目教師1</option>
                                    <option>担当教師2</option>
                                    <option>担当教師3</option>
                                    <option>担当教師4</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="reason-panel">
                        <div class="reason-header">
                            <label>理由</label>
                            <select class="reason-select"><option>体調不良</option><option>就活</option><option>家庭の事情</option><option>その他</option></select>
                        </div>
                        <textarea placeholder="テキストを入力"></textarea>
                    </div>

                    <div class="submit-wrap">
                        <button class="btn-submit" type="submit">提出</button>
                    </div>

                    <div class="log-section">
                        <div class="log-wrap">
                            <label class="log-label">ログ</label>
                            <div class="log-box">
                                <div>提出:yyyy/mm/dd 日付:yyyy/mm/dd 理由:就活</div>
                                <div>提出:yyyy/mm/dd 日付:yyyy/mm/dd 理由:遅延</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>