<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>遅刻・欠席届（教師用）</title>
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

                    <div class="submission-list">
                        <div class="submission-list-header">生徒提出一覧</div>
                        @if(isset($reports) && $reports->isNotEmpty())
                            @foreach($reports as $report)
                                @php
                                    $status = $report->report_status ?? '未処理';
                                    $statusClass = match ($status) {
                                        '受理' => 'status-accepted',
                                        '差し戻し' => 'status-rejected',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <div class="submission-item {{ $statusClass }}" data-report="{{ json_encode($report) }}" style="cursor: pointer;">
                                    {{-- 提出日と欠席日は別物なので、取り違えないよう両方出す --}}
                                    <div class="summary-item">
                                        <span class="summary-label">欠席日</span>
                                        <span class="summary-value">{{ $report->target_date }}</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">提出日</span>
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
                <!-- ▼▼▼ エラーメッセージ表示用のコードを追加 ▼▼▼ -->
                @if ($errors->any())
                    <div style="color: red; background-color: #fee; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- ▲▲▲ ここまで ▲▲▲ -->
                <form id="detail_form" method="post" action="">
                    @csrf
                    <!-- 受理または差し戻し用にIDを保持 -->
                    <input type="hidden" name="report_id" id="detail_report_id" value="">

                    <!-- ★追加：ステータス送信用の隠しフィールド -->
                    <input type="hidden" name="report_status" id="submit_report_status" value="">  

                    <div class="grid-2">
                        <div class="panel-left">
                            <div class="field"><label>学籍番号</label><input type="text" id="detail_student_number" disabled></div>
                            <div class="field"><label>クラス番号</label><input type="text" id="detail_class_number" disabled></div>
                            <div class="field"><label>名前</label><input type="text" id="detail_student_name" disabled></div>
                            <div class="field"><label>欠席日</label><input type="date" id="detail_target_date" disabled></div>
                            <div class="field"><label>時限</label>
                                <div class="time-box">
                                    <label><input type="checkbox" id="detail_period_1" disabled>1</label>
                                    <label><input type="checkbox" id="detail_period_2" disabled>2</label>
                                    <label><input type="checkbox" id="detail_period_3" disabled>3</label>
                                    <label><input type="checkbox" id="detail_period_4" disabled>4</label>
                                </div>
                            </div>
                        </div>

                        <div class="panel-right">
                            <div class="field"><label>提出日</label><input type="date" id="detail_submission_date" disabled></div>
                            <div class="field"><label>担任教師</label><input type="text" id="detail_homeroom_teacher" disabled></div>
                            <div class="field"><label>科目教師</label>
                                <div class="subject-teacher-group">
                                    <!-- 中身はJavaScriptで動的に生成するため空にしてidを付与 -->
                                    <div id="detail_teacher_list" class="teacher-list teacher-list-static">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-panel">
                        <div class="reason-header">
                            <label>理由</label>
                            <input class="reason-category" type="text" id="detail_reason_category"disabled>
                        </div>
                        <textarea id="detail_reason_detail" placeholder="テキストを入力" disabled></textarea>
                    </div>

                    <!-- 差し戻しコメント（name がないと送信されないので注意） -->
                    <div class="comment-section">
                        <label class="comment-label">差し戻しコメント</label>
                        <textarea class="comment-box" name="return_comment" placeholder="コメントを入力"></textarea>
                    </div>

                    <!-- 理由チェックボックス -->
                    <div class="reason-checkboxes">
                        <div class="checkbox-group">
                            <!-- nameを「attendance_type」に変更し、valueを日本語に変更 -->
                            <label><input type="radio" name="attendance_type" value="遅刻"> 遅刻</label>
                            <label><input type="radio" name="attendance_type" value="病気"> 病気</label>
                            <label><input type="radio" name="attendance_type" value="欠席"> 欠席</label>
                            <label><input type="radio" name="attendance_type" value="その他"> その他</label>
                        </div>
                    </div>

                    <!-- アクションボタン -->
                    <div class="action-buttons">
                        <button id="btn_reject" class="btn-reject" type="button">差し戻し</button>
                        <button id="btn_approve" class="btn-approve" type="button">受理</button>
                    </div>
                </form>
            </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>