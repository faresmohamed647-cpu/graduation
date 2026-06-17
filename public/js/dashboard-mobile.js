/**
 * SafeStep — Unified Mobile Dashboard Controller
 * Consistent sidebar overlay + hamburger behavior for all 4 role dashboards.
 */
(function () {
    'use strict';

    if (window.__dashboardMobileInitialized) return;
    window.__dashboardMobileInitialized = true;

    const MOBILE_BREAKPOINT = 768;
    let sidebarOverlay = null;

    function isMobileView() {
        return window.innerWidth <= MOBILE_BREAKPOINT;
    }

    function getSidebar() {
        return document.querySelector('.sidebar');
    }

    function getMenuToggle() {
        return document.getElementById('menuToggle');
    }

    function ensureOverlay() {
        if (sidebarOverlay && document.body.contains(sidebarOverlay)) {
            return sidebarOverlay;
        }

        sidebarOverlay = document.querySelector('.sidebar-overlay');
        if (!sidebarOverlay) {
            sidebarOverlay = document.createElement('div');
            sidebarOverlay.className = 'sidebar-overlay';
            sidebarOverlay.setAttribute('aria-hidden', 'true');
            document.body.appendChild(sidebarOverlay);
        }

        if (sidebarOverlay.dataset.bound !== '1') {
            sidebarOverlay.dataset.bound = '1';
            sidebarOverlay.addEventListener('click', () => window.DashboardMobile.setOpen(false));
        }

        return sidebarOverlay;
    }

    function setMenuToggleIcon(isOpen) {
        const menuToggle = getMenuToggle();
        if (!menuToggle) return;

        const icon = menuToggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars', !isOpen);
            icon.classList.toggle('fa-times', isOpen);
        }

        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function syncSidebarClasses(isOpen) {
        const sidebar = getSidebar();
        if (!sidebar) return;

        if (isMobileView()) {
            sidebar.classList.toggle('active', isOpen);
            sidebar.classList.toggle('hidden', !isOpen);
        } else {
            sidebar.classList.remove('active');
            if (!sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
            }
        }
    }

    function setOpen(isOpen) {
        const overlay = ensureOverlay();
        const sidebar = getSidebar();

        if (!sidebar) return;

        if (isMobileView()) {
            document.body.classList.toggle('sidebar-open', isOpen);
            overlay.classList.toggle('active', isOpen);
            overlay.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            syncSidebarClasses(isOpen);
            setMenuToggleIcon(isOpen);
            return;
        }

        document.body.classList.remove('sidebar-open');
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        sidebar.classList.remove('active');
        setMenuToggleIcon(false);
    }

    function toggle() {
        setOpen(!document.body.classList.contains('sidebar-open'));
    }

    function isOpen() {
        return document.body.classList.contains('sidebar-open');
    }

    function bindMenuToggle() {
        const menuToggle = getMenuToggle();
        if (!menuToggle || menuToggle.dataset.mobileBound === '1') return;

        menuToggle.dataset.mobileBound = '1';
        menuToggle.addEventListener('click', (event) => {
            event.preventDefault();
            toggle();
        });
    }

    function init() {
        ensureOverlay();
        bindMenuToggle();
        setOpen(false);

        window.addEventListener('resize', () => {
            if (!isMobileView()) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && isOpen()) {
                setOpen(false);
            }
        });

        document.addEventListener('spa:pageChanged', () => {
            if (isMobileView() && isOpen()) {
                setOpen(false);
            }
        });
    }

    window.DashboardMobile = {
        init,
        setOpen,
        toggle,
        isOpen,
        isMobileView,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
