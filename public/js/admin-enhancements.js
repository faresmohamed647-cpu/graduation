/**
 * Admin dashboard API enhancements — activity logs, system health, school performance.
 */
(function () {
    'use strict';

    let activityLogsCache = [];

    async function loadActivityLogsFromApi() {
        try {
            const res = await safestepApi('/api/admin/activity-logs?per_page=100');
            activityLogsCache = (res.data || []).map((log) => ({
                id: log.id,
                timestamp: log.created_at,
                user: log.user?.name || 'System',
                action: log.action,
                module: log.entity_type || 'System',
                description: log.action,
                ipAddress: log.ip_address || '-',
            }));
            if (typeof renderActivityLogs === 'function') {
                renderActivityLogs();
            }
        } catch (e) {
            console.warn('[AdminEnhancements] activity logs:', e.message);
        }
    }

    async function injectSystemHealth() {
        try {
            const [health, schools, emergencies] = await Promise.all([
                safestepApi('/api/admin/system/health'),
                safestepApi('/api/admin/system/school-performance'),
                safestepApi('/api/admin/system/emergency-overview'),
            ]);

            const grid = document.querySelector('#dashboard .dashboard-grid');
            if (!grid || document.getElementById('systemHealthCard')) return;

            const card = document.createElement('div');
            card.className = 'card';
            card.id = 'systemHealthCard';
            card.innerHTML = `
                <div class="card-header"><h3>System Health</h3></div>
                <div class="card-content" style="padding:16px 20px;font-size:13px;">
                    <p><strong>Database:</strong> ${health.data?.database || 'unknown'}</p>
                    <p><strong>Cache:</strong> ${health.data?.cache || 'unknown'}</p>
                    <p><strong>Failed Jobs:</strong> ${health.data?.failed_jobs ?? 0}</p>
                    <p><strong>Open Emergencies:</strong> ${emergencies.data?.open ?? 0}</p>
                    <p><strong>Schools:</strong> ${schools.data?.length ?? 0}</p>
                </div>`;
            grid.appendChild(card);
        } catch (e) {
            console.warn('[AdminEnhancements] system health:', e.message);
        }
    }

    const origNavigate = window.navigateTo;
    if (typeof origNavigate === 'function') {
        window.navigateTo = function (pageId) {
            origNavigate(pageId);
            if (pageId === 'activity-logs') loadActivityLogsFromApi();
            if (pageId === 'dashboard') injectSystemHealth();
        };
    }

    const origRender = window.renderActivityLogs;
    if (typeof origRender === 'function') {
        window.renderActivityLogs = function () {
            if (activityLogsCache.length) {
                const backup = window.activityLogsData;
                window.activityLogsData = activityLogsCache;
                origRender();
                window.activityLogsData = backup;
                return;
            }
            origRender();
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadActivityLogsFromApi();
        injectSystemHealth();
    });
})();
