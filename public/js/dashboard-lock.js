(function () {
    'use strict';

    let initialized = false;

    function getConfig() {
        return window.__DASHBOARD_LOCK;
    }

    function shouldLockPage(pageId) {
        const config = getConfig();
        if (!config || pageId === 'dashboard') return false;

        if (typeof config.shouldLockPage === 'function') {
            return config.shouldLockPage(pageId) === true;
        }

        return config.isReady?.() !== true;
    }

    function getMessage(pageId) {
        const config = getConfig();
        if (typeof config.getMessage === 'function') {
            return config.getMessage(pageId);
        }
        return null;
    }

    function clearPageLock(pageEl) {
        if (!pageEl) return;
        const lock = pageEl.querySelector('.dashboard-page-lock');
        if (lock) lock.remove();
    }

    function showPageLock(pageEl, pageId) {
        if (!pageEl) {
            return;
        }

        pageId = pageId || pageEl.id;

        if (!shouldLockPage(pageId)) {
            clearPageLock(pageEl);
            return;
        }

        const msg = getMessage(pageId);
        if (!msg) {
            clearPageLock(pageEl);
            return;
        }

        let lock = pageEl.querySelector('.dashboard-page-lock');
        if (!lock) {
            lock = document.createElement('div');
            lock.className = 'dashboard-page-lock';
            lock.setAttribute('role', 'alert');
            lock.setAttribute('aria-live', 'polite');
            pageEl.appendChild(lock);
        }

        const dashboardBtn = msg.showDashboardBtn !== false
            ? `<button type="button" class="btn-primary" style="margin-top:8px;" onclick="window.navigateTo('dashboard')"><i class="fas fa-chart-line"></i> ${msg.dashboardBtn || 'Back to Dashboard'}</button>`
            : '';

        lock.innerHTML = `
            <div class="dashboard-page-lock-card">
                <div class="dashboard-page-lock-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>${msg.title || 'Section Locked'}</h3>
                <p>${msg.body || ''}</p>
                ${msg.sub ? `<p class="lock-sub">${msg.sub}</p>` : ''}
                ${dashboardBtn}
            </div>`;
    }

    function updateNavLockState() {
        const config = getConfig();
        document.querySelectorAll('.nav-link[data-page]').forEach((link) => {
            const page = link.getAttribute('data-page');
            if (!page || page === 'dashboard') {
                link.classList.remove('nav-locked');
                return;
            }

            const locked = shouldLockPage(page);
            link.classList.toggle('nav-locked', locked);
        });

        if (typeof config?.onNavLockUpdate === 'function') {
            config.onNavLockUpdate();
        }
    }

    function handlePageChange(pageId) {
        updateNavLockState();

        document.querySelectorAll('.page').forEach((pageEl) => {
            if (pageEl.id === pageId) {
                showPageLock(pageEl, pageId);
            } else {
                clearPageLock(pageEl);
            }
        });
    }

    function bootstrap() {
        const config = getConfig();
        if (!config || typeof config.isReady !== 'function') return;

        if (!initialized) {
            document.addEventListener('spa:pageChanged', (event) => {
                handlePageChange(event.detail?.pageId);
            });
            initialized = true;
        }

        updateNavLockState();
        const activePage = document.querySelector('.page.active');
        if (activePage) {
            handlePageChange(activePage.id);
        }
    }

    window.DashboardLock = {
        refresh: bootstrap,
        showPageLock,
        clearPageLock,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();
