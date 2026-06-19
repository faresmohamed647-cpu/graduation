(function () {
    const config = window.__ONBOARDING_POLL || null;
    if (!config || !config.endpoint) return;
    if (config.isDashboardUnlocked === true) return;

    let polling = false;

    async function checkApprovalStatus() {
        if (polling) return;
        polling = true;

        try {
            const token = window.__API_TOKEN || localStorage.getItem('safestep_token') || localStorage.getItem('token') || '';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const response = await fetch(config.endpoint, {
                headers: {
                    Accept: 'application/json',
                    Authorization: token ? `Bearer ${token}` : '',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const payload = await response.json().catch(() => ({}));
            if (payload.data?.is_dashboard_unlocked) {
                window.location.reload();
            }
        } catch (error) {
            console.warn('[OnboardingPoll] status check failed', error);
        } finally {
            polling = false;
        }
    }

    checkApprovalStatus();
    setInterval(checkApprovalStatus, 5000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            checkApprovalStatus();
        }
    });
})();
