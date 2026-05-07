/**
 * SafeStep SPA Navigation Controller
 * Unifies sidebar and page management across Admin, Parent, and Driver dashboards.
 */

(function() {
    'use strict';

    if (window.__spaNavigationInitialized) return;
    window.__spaNavigationInitialized = true;

    console.log('[SPA] Navigation Controller Initialized');

    const CONFIG = {
        activeClass: 'active',
        pageSelector: '.page',
        navLinkSelector: '.nav-link:not(.logout):not(.external-link)',
        pageTitleSelector: '#pageTitle',
        mobileBreakpoint: 768
    };

    let currentPageId = null;

    /**
     * Core navigation function
     * @param {string} pageId - The ID of the page to show
     */
    window.navigateTo = function(pageId) {
        if (!pageId) {
            pageId = 'dashboard';
        }

        const targetPage = document.getElementById(pageId);

        if (!targetPage) {
            console.warn(`[SPA] Target page not found: #${pageId}`);
            if (pageId !== 'dashboard') {
                return window.navigateTo('dashboard');
            }
            return;
        }

        const previousPageId = currentPageId;
        currentPageId = pageId;

        // 1. Update Page Visibility (Strict logic: only ONE active)
        document.querySelectorAll(CONFIG.pageSelector).forEach(p => {
            if (p.id === pageId) {
                p.classList.add(CONFIG.activeClass);
            } else {
                p.classList.remove(CONFIG.activeClass);
            }
        });

        // 2. Update Sidebar Active State
        document.querySelectorAll(CONFIG.navLinkSelector).forEach(l => {
            if (l.getAttribute('data-page') === pageId) {
                l.classList.add(CONFIG.activeClass);
                // 3. Update Page Title
                const titleEl = document.querySelector(CONFIG.pageTitleSelector);
                if (titleEl) {
                    const linkSpan = l.querySelector('span');
                    titleEl.textContent = linkSpan ? linkSpan.textContent : l.textContent.trim();
                }
            } else {
                l.classList.remove(CONFIG.activeClass);
            }
        });

        // 4. Mobile: Auto-close sidebar
        if (window.innerWidth <= CONFIG.mobileBreakpoint) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }

        // 5. Trigger custom event for page-specific initialization ONCE
        const event = new CustomEvent('spa:pageChanged', {
            detail: { 
                pageId: pageId, 
                pageElement: targetPage,
                previousPage: previousPageId
            }
        });
        document.dispatchEvent(event);

        console.log(`[SPA] Navigated to: ${pageId} (from: ${previousPageId})`);
    };

    let navigationAttached = false;

    function attachNavigation() {
        if (navigationAttached) return;
        navigationAttached = true;

        // Destroy previous handlers using cloneNode trick
        const links = document.querySelectorAll(CONFIG.navLinkSelector);
        links.forEach(oldLink => {
            const newLink = oldLink.cloneNode(true);
            if (oldLink.parentNode) {
                oldLink.parentNode.replaceChild(newLink, oldLink);
            }
        });

        // Use Event Delegation for clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest(CONFIG.navLinkSelector);
            if (!link) return;

            e.preventDefault();
            
            const pageId = link.getAttribute('data-page');
            if (!pageId) {
                console.warn('[SPA] Navigation link missing data-page attribute', link);
                window.navigateTo('dashboard');
                return;
            }

            window.navigateTo(pageId);
        });
    }

    function initSPA() {
        attachNavigation();

        const initialPage = window.__INITIAL_PAGE || 'dashboard';
        const activePage = document.querySelector(`${CONFIG.pageSelector}.${CONFIG.activeClass}`);
        
        if (activePage && !window.__INITIAL_PAGE) {
            window.navigateTo(activePage.id);
        } else {
            window.navigateTo(initialPage);
        }
    }

    // Handle initial page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSPA);
    } else {
        initSPA();
    }

})();
