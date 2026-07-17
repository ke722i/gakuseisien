<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>遅刻・欠席届</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/css/notification.css', 'resources/js/app.js', 'resources/js/notification.js'])
</head>

<?php
    $today = date("Y-m-d");
    $user = Auth::user();
?>

<body>
    
    <div class="app-layout">

        <!-- 左サイドバー（共通部品） -->
        @include('partials.sidebar', ['active' => 'notification'])

        <!-- メイン画面 -->
        <main class="content">
            <div class="content-header">
                <h1>欠席・遅刻届（生徒用）</h1>
            </div>

            <div class="content-inner">
                <form method="post" action="{{ route('notification.store') }}">
                    @csrf
                    <div class="grid-2">
                        <div class="panel-left">
                            <div class="field"><label>学籍番号</label><input type="text" name="student_number" value="{{ old('student_number', $user?->student_number ?? '') }}" readonly></div>
                            <div class="field"><label>クラス番号</label><input type="text" name="class_number" value="{{ old('class_number', $user?->class_number ?? '') }}" readonly></div>
                            <div class="field"><label>名前</label><input type="text" name="student_name" value="{{ old('student_name', $user?->student_name ?? '') }}" readonly></div>
                            <div class="field"><label>日付</label><input type="date" name="target_date" value="{{ old('target_date', $today) }}"></div>
                            <div class="field"><label>時限</label>
                                <div class="time-box">
                                    <label><input type="checkbox" name="periods[]" value="1">1</label>
                                    <label><input type="checkbox" name="periods[]" value="2">2</label>
                                    <label><input type="checkbox" name="periods[]" value="3">3</label>
                                    <label><input type="checkbox" name="periods[]" value="4">4</label>
                                </div>
                            </div>
                        </div>

                        <div class="panel-right">
                            <div class="field"><label>提出日</label><input type="text" name="submission_date" value="{{ old('submission_date', $today) }}" readonly></div>
                            <div class="field"><label>担任教師</label><input type="text" name="homeroom_teacher" value="{{ old('homeroom_teacher', $user?->homeroom_teacher ?? '') }}" readonly></div>
                            <div class="field"><label>科目教師</label>
                                <div class="subject-teacher-group">
                                    <div id="teacher-list" class="teacher-list">
                                        <div class="teacher-row">
                                            <select name="subject_teacher_1">
                                                <option>科目教師1</option>
                                                <option>担当教師2</option>
                                                <option>担当教師3</option>
                                                <option>担当教師4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="teacher-controls">
                                        <button type="button" class="teacher-btn add" data-action="add">＋</button>
                                        <button type="button" class="teacher-btn remove" data-action="remove">−</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-panel">
                        <div class="reason-header">
                            <label>理由</label>
                            <select class="reason-select" name="reason_category">
                                <option value="体調不良" {{ old('reason_category') === '体調不良' ? 'selected' : '' }}>体調不良</option>
                                <option value="就活" {{ old('reason_category') === '就活' ? 'selected' : '' }}>就活</option>
                                <option value="家庭の事情" {{ old('reason_category') === '家庭の事情' ? 'selected' : '' }}>家庭の事情</option>
                                <option value="その他" {{ old('reason_category') === 'その他' ? 'selected' : '' }}>その他</option>
                            </select>
                        </div>
                        <textarea name="reason_detail" placeholder="テキストを入力">{{ old('reason_detail') }}</textarea>
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