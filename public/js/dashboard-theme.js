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

        // Apply to <html> immediately (works even before <body> exists)
        document.documentElement.classList.toggle('dark-mode', isDark);
        document.documentElement.setAttribute('data-dashboard-theme', theme);

        // Apply to <body> if it exists
        if (document.body) {
            document.body.style.transition = `background-color ${TRANSITION_MS}ms ease, color ${TRANSITION_MS}ms ease`;
            document.body.classList.toggle('dark-mode', isDark);
        }

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

    // Apply saved theme IMMEDIATELY (prevents flash — works in <head> too via <html> class)
    const saved = localStorage.getItem(STORAGE_KEY) || 'light';
    applyTheme(saved);

    // Re-apply on DOM ready to catch <body> and update button state
    function onDomReady() {
        applyTheme(localStorage.getItem(STORAGE_KEY) || 'light');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onDomReady);
    } else {
        onDomReady();
    }

    // Use event delegation — stop duplicate handlers in legacy dashboard scripts
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#themeToggle');
        if (!btn) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        const next = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem(STORAGE_KEY, next);
        applyTheme(next);
    });

    window.SafeStepTheme = { applyTheme, STORAGE_KEY };
})();
