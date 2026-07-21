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

{{-- 確認・入力ダイアログ（ブラウザ標準の confirm/prompt の置き換え） --}}
<div id="appDialog" class="app-dialog" role="dialog" aria-modal="true" aria-labelledby="appDialogMessage" hidden>
    <div class="app-dialog-backdrop" data-dialog-cancel></div>

    <div class="app-dialog-panel">
        <p id="appDialogMessage" class="app-dialog-message"></p>

        <label id="appDialogInputWrap" class="app-dialog-input-wrap" hidden>
            <span class="app-dialog-input-label">理由</span>
            <textarea id="appDialogInput" class="app-dialog-input" rows="3"></textarea>
        </label>

        <div class="app-dialog-actions">
            <button type="button" class="app-dialog-btn cancel" data-dialog-cancel>キャンセル</button>
            <button type="button" class="app-dialog-btn ok" id="appDialogOk">OK</button>
        </div>
    </div>
</div>

<style>
    .app-dialog {
        position: fixed;
        inset: 0;
        z-index: 4000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .app-dialog[hidden] { display: none; }

    .app-dialog-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
    }

    .app-dialog-panel {
        position: relative;
        width: 100%;
        max-width: 420px;
        padding: 22px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.28);
    }

    .app-dialog-message {
        margin: 0 0 18px;
        font-size: 15px;
        line-height: 1.7;
        color: #111827;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .app-dialog-input-wrap { display: block; margin-bottom: 18px; }

    .app-dialog-input-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
    }

    .app-dialog-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
    }

    .app-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .app-dialog-btn {
        min-width: 96px;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .app-dialog-btn.cancel { background: #e5e7eb; color: #374151; }
    .app-dialog-btn.cancel:hover { background: #d1d5db; }
    .app-dialog-btn.ok { background: #dc2626; color: #ffffff; }
    .app-dialog-btn.ok:hover { background: #b91c1c; }

    @media (max-width: 700px) {
        .app-dialog-actions { flex-direction: column-reverse; }
        .app-dialog-btn { width: 100%; }
    }

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

    /*
     * 確認・入力ダイアログ。
     * ブラウザ標準の confirm()/prompt() は見た目が統一できないうえ、
     * 表示中はページ操作が完全に止まってしまうため使わない。
     *
     * 使い方:
     *   if (await confirmDialog('削除しますか？')) { ... }
     *   const reason = await promptDialog('通報理由を入力してください');
     *   // キャンセル時は confirmDialog=false / promptDialog=null
     *
     * フォームは属性だけで確認を付けられる:
     *   <form data-confirm="削除しますか？"> ... </form>
     *   <form data-confirm="..." data-confirm-input="reason"> ... </form>
     *     → 入力欄付き。入力値は name="reason" の hidden に入れて送信する。
     */
    (function () {
        const dialog = document.getElementById('appDialog');
        if (!dialog) return;

        const messageEl = dialog.querySelector('#appDialogMessage');
        const inputWrap = dialog.querySelector('#appDialogInputWrap');
        const inputEl = dialog.querySelector('#appDialogInput');
        const okBtn = dialog.querySelector('#appDialogOk');

        let resolveCurrent = null;

        function close(result) {
            dialog.hidden = true;
            const resolve = resolveCurrent;
            resolveCurrent = null;
            if (resolve) resolve(result);
        }

        function open({ message, withInput }) {
            messageEl.textContent = message;
            inputWrap.hidden = !withInput;
            inputEl.value = '';

            dialog.hidden = false;
            // 入力欄があるときはそこへ、なければOKへフォーカスする
            (withInput ? inputEl : okBtn).focus();

            return new Promise(resolve => { resolveCurrent = resolve; });
        }

        okBtn.addEventListener('click', () => {
            if (!inputWrap.hidden) {
                const value = inputEl.value.trim();
                if (value === '') {
                    window.showToast('理由を入力してください。', 'error');
                    return;
                }
                close(value);
                return;
            }
            close(true);
        });

        dialog.querySelectorAll('[data-dialog-cancel]').forEach(el => {
            el.addEventListener('click', () => close(inputWrap.hidden ? false : null));
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !dialog.hidden) {
                close(inputWrap.hidden ? false : null);
            }
        });

        /** 確認ダイアログ。OKなら true、キャンセルなら false を返す */
        window.confirmDialog = message => open({ message, withInput: false });

        /** 入力ダイアログ。入力値、キャンセルなら null を返す */
        window.promptDialog = message => open({ message, withInput: true });

        // data-confirm が付いたフォームの送信を横取りして確認を挟む
        document.addEventListener('submit', async e => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            const message = form.dataset.confirm;
            if (!message || form.dataset.confirmed === 'yes') return;

            e.preventDefault();

            const inputName = form.dataset.confirmInput;
            const answer = inputName
                ? await window.promptDialog(message)
                : await window.confirmDialog(message);

            if (answer === false || answer === null) return;

            // 入力値は hidden にして一緒に送る
            if (inputName) {
                let hidden = form.querySelector(`input[type="hidden"][name="${inputName}"]`);
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = inputName;
                    form.appendChild(hidden);
                }
                hidden.value = answer;
            }

            // 二重確認を避けてから本来の送信を行う
            form.dataset.confirmed = 'yes';
            form.submit();
        });
    })();
</script>
