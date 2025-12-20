// ThemeToggle.js
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    const currentTheme = localStorage.getItem('theme') || 'dark'; // По умолчанию 'dark'

    // Список доступных тем
    const themes = ['dark', 'light', 'classic'];
    let themeIndex = themes.indexOf(currentTheme);
    if (themeIndex === -1) {
        themeIndex = 0; // Начать с 'dark', если тема неизвестна
    }

    /**
     * Устанавливает тему и сохраняет её в localStorage.
     * @param {string} theme - Название темы ('dark', 'light', 'classic').
     */
    function setTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
        updateIcon(theme);
    }

    /**
     * Обновляет иконку кнопки в зависимости от текущей темы.
     * @param {string} theme - Название темы.
     */
    function updateIcon(theme) {
        // Предполагаем, что иконки существуют в DOM
        const sun = toggleButton.querySelector('.bi-sun');
        const moon = toggleButton.querySelector('.bi-moon-fill');
        const code = toggleButton.querySelector('.bi-code');

        // Скрываем все иконки
        [sun, moon, code].forEach(icon => {
            if (icon) icon.classList.add('d-none');
        });

        // Показываем нужную иконку
        if (theme === 'dark') {
            // На темной теме показываем солнце (предлагаем Light)
            if (sun) sun.classList.remove('d-none');
            toggleButton.title = "Сменить на светлую тему";
        } else if (theme === 'light') {
            // На светлой теме показываем код (предлагаем Classic)
            if (code) code.classList.remove('d-none');
            toggleButton.title = "Сменить на классическую тему";
        } else if (theme === 'classic') {
            // На классической теме показываем луну (предлагаем Dark)
            if (moon) moon.classList.remove('d-none');
            toggleButton.title = "Сменить на темную тему";
        }
    }

    /**
     * Переключает на следующую тему в цикле.
     */
    function cycleThemes() {
        themeIndex = (themeIndex + 1) % themes.length;
        const nextTheme = themes[themeIndex];
        setTheme(nextTheme);
    }

    // Инициализация: устанавливаем тему, сохраненную в localStorage, или 'dark'
    setTheme(currentTheme);

    // Добавляем обработчик клика
    toggleButton.addEventListener('click', cycleThemes);
});