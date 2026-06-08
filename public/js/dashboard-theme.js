/**
 * Unified SafeStep dashboard dark-mode (body.dark-mode + localStorage persistence).
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'safestep-theme';
    const TRANSITION_MS = 280;

    function getToggleButton() {
        return document.getElementById('themeToggle');
    }

    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.body.style.transition = `background-color ${TRANSITION_MS}ms ease, color ${TRANSITION_MS}ms ease`;
        document.body.classList.toggle('dark-mode', isDark);
        document.documentElement.setAttribute('data-dashboard-theme', theme);

        const btn = getToggleButton();
        if (btn) {
            btn.innerHTML = isDark
                ? '<i class="fas fa-sun"></i><span>Light</span>'
                : '<i class="fas fa-moon"></i><span>Dark</span>';
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        }

        if (window.Chart) {
            document.querySelectorAll('canvas').forEach((canvas) => {
                const chart = Chart.getChart(canvas);
                if (chart) {
                    chart.options.plugins = chart.options.plugins || {};
                    chart.options.plugins.legend = chart.options.plugins.legend || {};
                    chart.options.plugins.legend.labels = chart.options.plugins.legend.labels || {};
                    chart.options.plugins.legend.labels.color = isDark ? '#e2e8f0' : '#334155';
                    chart.update('none');
                }
            });
        }
    }

    function initDashboardTheme() {
        const saved = localStorage.getItem(STORAGE_KEY) || 'light';
        applyTheme(saved);

        const btn = getToggleButton();
        if (!btn || btn.dataset.themeBound === '1') {
            return;
        }

        btn.dataset.themeBound = '1';
        btn.addEventListener('click', () => {
            const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem(STORAGE_KEY, next);
            applyTheme(next);
        });
    }

    window.SafeStepTheme = { applyTheme, initDashboardTheme, STORAGE_KEY };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardTheme);
    } else {
        initDashboardTheme();
    }
})();
