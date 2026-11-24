const menuToggle = document.querySelector('.menu-toggle');
const siteNav = document.querySelector('.site-nav');

menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    menuToggle.querySelector('span').textContent = isOpen ? '+' : '−';
    siteNav?.classList.toggle('is-open', !isOpen);
});

siteNav?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        const menuIcon = menuToggle?.querySelector('span');

        menuToggle?.setAttribute('aria-expanded', 'false');
        if (menuIcon) menuIcon.textContent = '+';
        siteNav.classList.remove('is-open');
    });
});

document.querySelectorAll('.billing-option').forEach((button) => {
    button.addEventListener('click', () => {
        const isAnnual = button.dataset.period === 'annual';

        document.querySelectorAll('.billing-option').forEach((option) => {
            option.classList.toggle('is-active', option === button);
        });

        document.querySelectorAll('[data-price-monthly]').forEach((price) => {
            const value = isAnnual ? price.dataset.priceAnnual : price.dataset.priceMonthly;
            price.textContent = new Intl.NumberFormat('ro-RO').format(Number(value));
            price.closest('.plan-card').querySelector('[data-billing-note]').textContent = isAnnual ? 'anual' : 'lunar';
        });
    });
});


const themeToggle = document.querySelector('.theme-toggle');
const savedTheme = localStorage.getItem('eloquence-theme');
const initialTheme = savedTheme === 'dark' || savedTheme === 'light'
    ? savedTheme
    : 'light';

const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    document.querySelectorAll('.theme-toggle').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.querySelector('span').textContent = theme === 'dark' ? '☼' : '◐';
    });
};

applyTheme(initialTheme);
themeToggle?.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('eloquence-theme', nextTheme);
    applyTheme(nextTheme);
});
