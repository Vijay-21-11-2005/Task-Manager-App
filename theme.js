document.addEventListener('DOMContentLoaded', () => {
    const themeIcon = document.getElementById('theme-icon');
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
    }
});

function toggleTheme() {
    const themeIcon = document.getElementById('theme-icon');
    document.body.classList.toggle('dark-mode');
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
        if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
    } else {
        localStorage.setItem('theme', 'light');
        if (themeIcon) themeIcon.classList.replace('fa-sun', 'fa-moon');
    }
}