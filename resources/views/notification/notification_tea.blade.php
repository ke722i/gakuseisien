<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>遅刻・欠席届</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/notification.css', 'resources/js/app.js', 'resources/js/notification.js'])
</head>


<body>
    
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'notification'])

        <!-- メイン画面 -->
        <main class="content">
            <div class="notification-wrapper">
                <!-- 左側 情報サマリー -->
                <aside class="notification-summary">
                    <div class="summary-section">
                        <div class="summary-item">
                            <span class="summary-label">日付</span>
                            <span class="summary-value">yyyy/mm/dd（提出時の日付）</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">クラス</span>
                            <span class="summary-value">R4SA00（提出した生徒のクラス）</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">学生名</span>
                            <span class="summary-value">情報太郎</span>
                        </div>
                    </div>
                    <div class="summary-section highlight">
                        <div class="summary-item">
                            <span class="summary-label">ステータス</span>
                            <span class="summary-value">未処理</span>
                        </div>
                    </div>

                    <div class="submission-list">
                        <div class="submission-list-header">生徒提出一覧</div>
                        @if(isset($reports) && $reports->isNotEmpty())
                            @foreach($reports as $report)
                                @php
                                    $status = $report->attendance_type ?? '未処理';
                                    $statusClass = match ($status) {
                                        '受理' => 'status-accepted',
                                        '差し戻し' => 'status-rejected',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <div class="submission-item {{ $statusClass }}">
                                    <div class="summary-item">
                                        <span class="summary-label">日付</span>
                                        <span class="summary-value">{{ $report->submission_date }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">クラス</span>
                                        <span class="summary-value">{{ $report->class_number }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">学生名</span>
                                        <span class="summary-value">{{ $report->student_name }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">ステータス</span>
                                        <span class="summary-value">{{ $status }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="submission-empty">提出された届出はありません。</div>
                        @endif
                    </div>
                </aside>

                <!-- メインコンテンツ -->
                <div class="notification-content">
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
                            <div class="field"><label>日付</label><input type="date" value="" disabled></div>
                            <div class="field"><label>時限</label>
                                <div class="time-box">
                                    <label><input type="checkbox" disabled>1</label>
                                    <label><input type="checkbox" disabled>2</label>
                                    <label><input type="checkbox" disabled>3</label>
                                    <label><input type="checkbox" disabled>4</label>
                                </div>
                            </div>
                        </div>

                        <div class="panel-right">
                            <div class="field"><label>提出日</label><input type="date" value="" disabled></div>
                            <div class="field"><label>担任教師</label><input type="text" value="情報教師" disabled></div>
                            <div class="field"><label>科目教師</label>
                                <div class="subject-teacher-group">
                                    <div id="teacher-list" class="teacher-list teacher-list-static">
                                        <div class="teacher-row">
                                            <input type="text" value="情報教師" disabled>
                                        </div>
                                        <div class="teacher-row">
                                            <input type="text" value="情報教師" disabled>
                                        </div>
                                        <div class="teacher-row">
                                            <input type="text" value="情報教師" disabled>
                                        </div>
                                        <div class="teacher-row">
                                            <input type="text" value="情報教師" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-panel">
                        <div class="reason-header">
                            <label>理由</label>
                
                            <input class="reason-category" type="text" value="体調不良" disabled>
                        </div>
                        <textarea placeholder="テキストを入力" disabled></textarea>
                    </div>

                    <!-- 差し戻しコメント -->
                    <div class="comment-section">
                        <label class="comment-label">差し戻しコメント</label>
                        <textarea class="comment-box" placeholder="コメントを入力"></textarea>
                    </div>

                    <!-- 理由チェックボックス -->
                    <div class="reason-checkboxes">
                        <div class="checkbox-group">
                            <label><input type="radio" name="reason_approval" value="sick"> 遅刻</label>
                            <label><input type="radio" name="reason_approval" value="late"> 早退</label>
                            <label><input type="radio" name="reason_approval" value="absent">欠課</label>
                            <label><input type="radio" name="reason_approval" value="other"> その他</label>
                        </div>
                    </div>

                    <!-- アクションボタン -->
                    <div class="action-buttons">
                        <button class="btn-reject" type="button">差し戻し</button>
                        <button class="btn-approve" type="submit">受理</button>
                    </div>
                </form>
            </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>