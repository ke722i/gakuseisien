// サイドバーの開閉は resources/views/partials/sidebar.blade.php 内のスクリプトで処理する。

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
        } else {
            button.textContent = '⌄';
        }
    });
});