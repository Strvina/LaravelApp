import './bootstrap';
import './pages/todo/todoDnD';
import './pages/todo/todoRecurring';
import './pages/expenses/expensesChart';
import './pages/products/filter';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const syncThemeLabel = () => {
    const label = document.querySelector('[data-theme-label]');
    if (!label) return;

    label.textContent = document.documentElement.classList.contains('dark') ? 'Light mode' : 'Dark mode';
};

const toggleTheme = () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('opsdesk-theme', isDark ? 'dark' : 'light');
    syncThemeLabel();
};

document.addEventListener('DOMContentLoaded', () => {
    syncThemeLabel();

    const toggleButton = document.querySelector('[data-theme-toggle]');
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleTheme);
    }
});
