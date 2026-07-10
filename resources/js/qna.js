document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================
       1. 投稿画面（create）の処理
       ========================================== */
    const titleInput = document.getElementById('title');
    const bodyTextarea = document.getElementById('body');
    const backBtn = document.querySelector('.qna-history-btn[href*="gakunai-qna"]');

    // 1. 文字数に合わせて高さを自動で広げる仕掛け（投稿画面用）
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

    /* ==========================================
       2. 詳細画面（detail）の処理（今後追加）
       ========================================== */

    /* ==========================================
       3. 削除ポップアップの処理（今後追加）
       ========================================== */
    const deleteButtons = document.querySelectorAll('.qna-delete-trigger-btn'); // 🗑️ボタン
    const deleteModal = document.getElementById('deleteModal'); // ポップアップ全体
    const cancelTextBtn = document.getElementById('modalCancelBtn'); // キャンセルボタン

    // ① 「🗑️ 削除」ボタンが押されたらポップアップを開く
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault(); // 誤動作防止
            if (deleteModal) {
                deleteModal.classList.add('is-open'); // 表示用クラスを付与
            }
        });
    });

    // ② 「キャンセル」ボタンが押されたらポップアップを閉じる
    if (cancelTextBtn && deleteModal) {
        cancelTextBtn.addEventListener('click', () => {
            deleteModal.classList.remove('is-open'); // 表示用クラスを外す
        });
    }

    // ③ ポップアップの外側の黒い背景部分をクリックしても閉じるようにする親切設計
    if (deleteModal) {
        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                deleteModal.classList.remove('is-open');
            }
        });
    }

});