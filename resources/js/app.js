// =======================
// ハンバーガーメニュー
// =======================
const menuButton = document.getElementById('menuButton');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

menuButton?.addEventListener('click', () => {
    sidebar?.classList.add('open');
    overlay?.classList.add('show');
});

overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('show');
});
// サイドバーの開閉は resources/views/partials/sidebar.blade.php 内のスクリプトで処理する。


// =======================
// 6日以内の履歴だけ残す
// =======================
function getValidHistories() {
    const histories = JSON.parse(localStorage.getItem('newsHistories')) || [];

    const now = new Date();
    const sixDaysAgo = new Date();
    sixDaysAgo.setDate(now.getDate() - 6);

    const validHistories = histories.filter((item) => {
        if (!item.viewedAt) {
            return false;
        }

        const viewedDate = new Date(item.viewedAt);
        return viewedDate >= sixDaysAgo;
    });

    localStorage.setItem('newsHistories', JSON.stringify(validHistories));

    return validHistories;
}


// =======================
// 閲覧履歴保存
// =======================
function saveHistory(newsCard) {
    const title = newsCard.dataset.title;
    const description = newsCard.dataset.description;
    const url = newsCard.dataset.url;
    const source = newsCard.dataset.source;
    const date = newsCard.dataset.date;
    const category = newsCard.dataset.category;
    const categoryLabel = newsCard.dataset.categoryLabel;

    if (!title || !url) {
        return;
    }

    const newHistory = {
        title,
        description,
        url,
        source,
        date,
        category,
        categoryLabel,
        viewedAt: new Date().toISOString(),
    };

    const histories = getValidHistories();

    // 同じURLの記事は重複保存しない
    const filteredHistories = histories.filter((item) => item.url !== url);

    // 新しく見た記事を先頭に追加
    filteredHistories.unshift(newHistory);

    // 最大20件まで保存
    const limitedHistories = filteredHistories.slice(0, 20);

    localStorage.setItem('newsHistories', JSON.stringify(limitedHistories));
}


// =======================
// アコーディオン開閉
// =======================
function setAccordionEvents() {
    const circleButtons = document.querySelectorAll('.circle-button');

    circleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const newsCard = button.closest('.news-card');

            if (!newsCard) {
                return;
            }

            newsCard.classList.toggle('open');

            if (newsCard.classList.contains('open')) {
                button.textContent = '⌃';

                // トップ画面の記事だけ閲覧履歴に保存
                if (!document.getElementById('historyList')) {
                    saveHistory(newsCard);
                }
            } else {
                button.textContent = '⌄';
            }
        });
    });
}

setAccordionEvents();


// =======================
// 閲覧履歴画面に表示
// =======================
const historyList = document.getElementById('historyList');

if (historyList) {
    const histories = getValidHistories();

    if (histories.length === 0) {
        historyList.innerHTML = '<p>閲覧履歴はまだありません。</p>';
    } else {
        historyList.innerHTML = histories.map((item) => {
            return `
                <article
                    class="news-card"
                    data-title="${escapeHtml(item.title)}"
                    data-description="${escapeHtml(item.description)}"
                    data-url="${escapeHtml(item.url)}"
                    data-source="${escapeHtml(item.source)}"
                    data-date="${escapeHtml(item.date)}"
                    data-category="${escapeHtml(item.category)}"
                    data-category-label="${escapeHtml(item.categoryLabel)}"
                >
                    <div class="news-top">
                        <div>
                            <div class="news-meta">
                                <span class="tag ${escapeHtml(item.category)}">
                                    ${escapeHtml(item.categoryLabel)}
                                </span>

                                <span>
                                    ${escapeHtml(item.date)}
                                    ・
                                    ${escapeHtml(item.source)}
                                </span>
                            </div>

                            <h2>${escapeHtml(item.title)}</h2>
                        </div>

                        <button class="circle-button">⌄</button>
                    </div>

                    <div class="news-detail">
                        <p class="news-summary">
                            ${escapeHtml(item.description)}
                        </p>

                        <a href="${escapeHtml(item.url)}" target="_blank" class="read-more">
                            続きを読む
                        </a>
                    </div>
                </article>
            `;
        }).join('');

        setAccordionEvents();
    }
}


// =======================
// HTML特殊文字の変換
// =======================
function escapeHtml(text) {
    if (!text) {
        return '';
    }

    return String(text)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}