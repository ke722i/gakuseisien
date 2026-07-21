{{--
    共通のポップアップ（トースト）。
    ブラウザ標準の alert() を使わず、サイト内で統一した見た目で通知する。

    使い方（JS）:
        showToast('保存しました');                 // 成功（既定）
        showToast('入力に不備があります', 'error'); // エラー
        showToast('確認してください', 'info');      // お知らせ

    サーバー側のフラッシュメッセージ（session）も自動でポップアップ表示する。
    スタイルはこのファイル内で完結させ、どの画面（読み込むCSSが違っても）でも同じ見た目になるようにしている。
--}}

@php
    // サーバー側のフラッシュメッセージを種類ごとに集める
    $toastFlashes = [];

    $flashKeys = [
        'success' => 'success',
        'status' => 'success',
        'reservation_success' => 'success',
        'error' => 'error',
        'reservation_error' => 'error',
    ];

    foreach ($flashKeys as $key => $type) {
        $message = session($key);
        if (is_string($message) && $message !== '') {
            $toastFlashes[] = ['message' => $message, 'type' => $type];
        }
    }

    // バリデーションエラーも先頭の1件をポップアップで知らせる
    if (isset($errors) && $errors->any()) {
        $toastFlashes[] = ['message' => $errors->first(), 'type' => 'error'];
    }
@endphp

<div id="toastArea" class="toast-area" aria-live="polite" aria-atomic="true"></div>

<style>
    .toast-area {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 3000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .toast {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 260px;
        max-width: 360px;
        padding: 14px 16px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 5px solid #2563eb;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
        font-size: 14px;
        line-height: 1.6;
        color: #111827;
        pointer-events: auto;
        /* 常に可視。入場アニメーションは入れない
           （アニメーション/トランジションが進まない環境で
             opacity:0 のまま固まり、通知が見えなくなるのを防ぐ） */
        opacity: 1;
        transform: translateY(0);
    }

    .toast.is-leaving {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .toast-success { border-left-color: #16a34a; }
    .toast-error   { border-left-color: #dc2626; }
    .toast-info    { border-left-color: #2563eb; }

    .toast-icon {
        flex-shrink: 0;
        font-size: 16px;
        line-height: 1.4;
    }

    .toast-body { flex: 1; word-break: break-word; }

    .toast-close {
        flex-shrink: 0;
        border: none;
        background: transparent;
        color: #9ca3af;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 0 2px;
    }

    .toast-close:hover { color: #4b5563; }

    @media (max-width: 700px) {
        .toast-area {
            right: 12px;
            left: 12px;
            bottom: 12px;
        }

        .toast { min-width: 0; max-width: none; }
    }
</style>

<script>
    (function () {
        const area = document.getElementById('toastArea');

        const ICONS = { success: '✅', error: '⚠️', info: 'ℹ️' };

        /**
         * ポップアップを表示する。
         * @param {string} message 表示する文言
         * @param {'success'|'error'|'info'} type 種類
         * @param {number} duration 表示時間(ms)。0 で自動消去しない
         */
        window.showToast = function (message, type = 'success', duration = 6000) {
            if (!area || !message) return;

            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;
            toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

            const icon = document.createElement('span');
            icon.className = 'toast-icon';
            icon.textContent = ICONS[type] || ICONS.info;

            const body = document.createElement('div');
            body.className = 'toast-body';
            body.textContent = message; // textContent なのでHTMLは混入しない

            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'toast-close';
            close.setAttribute('aria-label', '閉じる');
            close.textContent = '×';

            toast.append(icon, body, close);
            area.appendChild(toast);

            let timer = null;
            const remove = () => {
                if (timer) clearTimeout(timer);
                toast.classList.add('is-leaving');
                setTimeout(() => toast.remove(), 220);
            };

            close.addEventListener('click', remove);
            if (duration > 0) timer = setTimeout(remove, duration);
        };

        // サーバー側のフラッシュメッセージを起動時にポップアップ表示する
        const flashes = @json($toastFlashes ?? []);
        flashes.forEach(f => window.showToast(f.message, f.type));
    })();
</script>
