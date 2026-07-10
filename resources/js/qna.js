// resources/js/qna.js

// 💡 画面の解析が完全に終わった瞬間に確実に動かすため、関数を定義して即座に実行させます
function initQnaFeatures() {
    const titleInput = document.getElementById('title');
    const bodyTextarea = document.getElementById('body');
    const backBtn = document.querySelector('.qna-history-btn[href*="gakunai-qna"]');

    // 1. 文字数に合わせて高さを自動で広げる仕掛け（投稿・詳細画面用）
    if (bodyTextarea) {
        bodyTextarea.addEventListener('input', () => {
            bodyTextarea.style.height = 'auto';
            bodyTextarea.style.height = bodyTextarea.scrollHeight + 'px';
        });
    }

    // 2. 「一覧に戻る」ボタンを押したときの離脱警告（投稿画面用）
    if (backBtn && titleInput && bodyTextarea) {
        backBtn.addEventListener('click', (event) => {
            if (titleInput.value.trim() !== "" || bodyTextarea.value.trim() !== "") {
                const confirmLeave = confirm("入力中の内容は破棄されますが、一覧に戻ってもよろしいですか？");
                if (!confirmLeave) {
                    event.preventDefault();
                }
            }
        });
    }

    // 3. 削除ポップアップ（モーダル）の開閉処理（一覧・履歴画面共通）
    const deleteButtons = document.querySelectorAll('.qna-delete-trigger-btn'); // 🗑️ボタン
    const deleteModal = document.getElementById('deleteModal'); // ポップアップ全体
    const cancelTextBtn = document.getElementById('modalCancelBtn'); // キャンセルボタン
    const modalSubmitBtn = document.getElementById('modalSubmitBtn'); // 💡 モーダル内の「削除する」ボタン
    const deleteForm = document.getElementById('delete-form'); // 💡 隠しフォーム

    if (deleteButtons.length > 0 && deleteModal && deleteForm) {
        // ① 「🗑️」ボタンが押されたら、宛先URLを設定してポップアップを開く
        deleteButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                // 💡 ボタンから質問のIDを取得
                const questionId = button.getAttribute('data-id');
                // 💡 フォームの action 先を 「/gakunai-qna/ID」 に書き換える！
                deleteForm.action = `/gakunai-qna/${questionId}`;

                deleteModal.classList.add('is-open');
            });
        });

        // 💡 モーダル内の「削除する」が本当に押されたら、フォームを送信する
        if (modalSubmitBtn) {
            modalSubmitBtn.addEventListener('click', () => {
                deleteForm.submit();
            });
        }

        // ② 「キャンセル」ボタンが押されたらポップアップを閉じる
        if (cancelTextBtn) {
            cancelTextBtn.addEventListener('click', (event) => {
                event.preventDefault();
                deleteModal.classList.remove('is-open');
            });
        }

        // ③ ポップアップの外側の黒い背景部分をクリックしても閉じる
        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                deleteModal.classList.remove('is-open');
            }
        });
    }
}

// 💡 画面の読み込み状態に関わらず、確実に実行されるように2重で罠を張る
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQnaFeatures);
} else {
    initQnaFeatures();
}