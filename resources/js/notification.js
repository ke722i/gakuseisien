// 共通ポップアップ（partials/toast.blade.php で定義）を使う。
// 読み込み順の問題で未定義のときは、ブラウザ標準の alert は使わずコンソールに残すだけにする
// （alert はページ操作を止めてしまうため、サイト内では使わない方針）。
const notify = (message, type = 'success') =>
    (window.showToast ? window.showToast(message, type) : console.warn('[notify]', type, message));

const teacherList = document.getElementById('teacher-list');
const addTeacherButton = document.querySelector('.teacher-btn.add');
const removeTeacherButton = document.querySelector('.teacher-btn.remove');

if (teacherList && addTeacherButton && removeTeacherButton) {
    const maxTeachers = 4;

    const updateTeacherButtons = () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        addTeacherButton.disabled = rows.length >= maxTeachers;
        removeTeacherButton.disabled = rows.length <= 1;
    };

    const updateTeacherNames = () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        rows.forEach((row, index) => {
            const select = row.querySelector('select');
            if (select) {
                select.name = `subject_teacher_${index + 1}`;
            }
        });
    };

    addTeacherButton.addEventListener('click', () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        if (rows.length >= maxTeachers) {
            return;
        }

        // 選択肢はサーバー側で教職員から生成しているため、1行目を複製して使う
        const templateSelect = teacherList.querySelector('.teacher-row select');
        if (!templateSelect) {
            return;
        }

        const row = document.createElement('div');
        row.className = 'teacher-row';

        const select = templateSelect.cloneNode(true);
        select.name = `subject_teacher_${rows.length + 1}`;
        select.selectedIndex = 0; // 追加した行は未選択から始める
        row.appendChild(select);

        teacherList.appendChild(row);
        updateTeacherNames();
        updateTeacherButtons();
    });

    removeTeacherButton.addEventListener('click', () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        if (rows.length <= 1) {
            return;
        }

        rows[rows.length - 1].remove();
        updateTeacherNames();
        updateTeacherButtons();
    });

    updateTeacherNames();
    updateTeacherButtons();
}

document.addEventListener('DOMContentLoaded', () => {
    // 左側のリストアイテムをすべて取得
    const submissionItems = document.querySelectorAll('.submission-item');

    submissionItems.forEach(item => {
        item.addEventListener('click', function() {
            // 埋め込んだJSONデータを取得してJavaScriptオブジェクトに変換
            const reportDataStr = this.getAttribute('data-report');
            if (!reportDataStr) return;
            
            const report = JSON.parse(reportDataStr);

            // 【追加】対象のIDをもとに、フォームの送信先URLを動的に設定する
            // ※URL（/teacher/notification/～）は実際のルーティング（web.php）に合わせて変更してください
            const detailForm = document.getElementById('detail_form');
            detailForm.action = `/teacher/notification/${report.id}/decide`;

            // 1. テキストボックス・日付への反映
            document.getElementById('detail_report_id').value = report.id || '';
            document.getElementById('detail_student_number').value = report.student_number || '';
            document.getElementById('detail_class_number').value = report.class_number || '';
            document.getElementById('detail_student_name').value = report.student_name || '';
            document.getElementById('detail_target_date').value = report.target_date || '';
            document.getElementById('detail_submission_date').value = report.submission_date || '';
            document.getElementById('detail_homeroom_teacher').value = report.homeroom_teacher || '';
            document.getElementById('detail_reason_category').value = report.reason_category || '';
            document.getElementById('detail_reason_detail').value = report.reason_detail || '';

            // 2. 時限チェックボックスの反映
            // データベースにはJSON文字列化された配列（例: '["1","3"]'）で保存されているため、再度パースする
            let periods = [];
            try {
                if (report.periods) {
                    periods = JSON.parse(report.periods);
                }
            } catch (e) {
                console.error('Periods parsing error', e);
            }
            
            // 1〜4のチェックボックスをループしてON/OFFを切り替える
            [1, 2, 3, 4].forEach(num => {
                const cb = document.getElementById(`detail_period_${num}`);
                if (cb) {
                    // 値が含まれていればチェックを入れる
                    cb.checked = periods.includes(String(num)) || periods.includes(num);
                }
            });

            // 3. 科目教師の反映
            const teacherList = document.getElementById('detail_teacher_list');
            if (teacherList) {
                teacherList.innerHTML = ''; // 一旦表示をクリア
                
                // 1〜4の担当教師を配列化してループ
                const teachers = [
                    report.subject_teacher_1,
                    report.subject_teacher_2,
                    report.subject_teacher_3,
                    report.subject_teacher_4
                ];
                
                teachers.forEach(teacher => {
                    if (teacher) {
                        const row = document.createElement('div');
                        row.className = 'teacher-row';
                        row.innerHTML = `<input type="text" value="${teacher}" disabled>`;
                        teacherList.appendChild(row);
                    }
                });
            }

            // 4. （おまけ）選択状態を視覚的に分かりやすくするためのクラス付け替え
            submissionItems.forEach(i => i.style.backgroundColor = ''); // 背景色をリセット
            this.style.backgroundColor = '#f0f4f8'; // 選択された項目に色を付ける
        });
    });

    // --- 【新規追加】アクションボタン押下時の処理 ---
    const btnReject = document.getElementById('btn_reject');
    const btnApprove = document.getElementById('btn_approve');
    const statusInput = document.getElementById('submit_report_status');
    const detailForm = document.getElementById('detail_form');

    if (btnReject && btnApprove && statusInput && detailForm) {
        // 「差し戻し」ボタンをクリックした時
        btnReject.addEventListener('click', function() {
            if (!detailForm.action || detailForm.action.endsWith('""')) {
                notify('対象の届出を左のリストから選択してください。', 'error');
                return;
            }
            statusInput.value = '差し戻し'; // 隠しフィールドに値をセット
            detailForm.submit();          // フォームを送信
        });

        // 「受理」ボタンをクリックした時
        btnApprove.addEventListener('click', function() {
            if (!detailForm.action || detailForm.action.endsWith('""')) {
                notify('対象の届出を左のリストから選択してください。', 'error');
                return;
            }
            statusInput.value = '受理';   // 隠しフィールドに値をセット
            detailForm.submit();        // フォームを送信
        });
    }
});