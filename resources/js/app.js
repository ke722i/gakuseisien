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