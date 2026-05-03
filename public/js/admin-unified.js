/* ═══════════════════════════════════════════════════════════════
   SafeStep Unified Admin Dashboard — JavaScript
   ═══════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    const API   = '/api/admin';
    const TOKEN = window.__API_TOKEN || localStorage.getItem('safestep_token') || localStorage.getItem('token') || '';
    const CSRF  = document.querySelector('meta[name="csrf-token"]')?.content || '';

    /* ── helpers ──────────────────────────────────────────────── */
    function hdr(json) {
        const h = { 'Accept': 'application/json' };
        if (TOKEN) {
            h['Authorization'] = 'Bearer ' + TOKEN;
        } else {
            // Fallback: use session cookie auth with CSRF header
            h['X-CSRF-TOKEN'] = CSRF;
            h['X-Requested-With'] = 'XMLHttpRequest';
        }
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }

    async function api(path, opts = {}) {
        try {
            const r = await fetch(path, { headers: hdr(!!opts.body), ...opts });
            const d = await r.json();
            if (!r.ok) throw d;
            return d;
        } catch (e) {
            if (e.status === 401) showToast('error', 'Session expired — please log in again.');
            throw e;
        }
    }

    function showToast(type, msg) {
        const c = document.getElementById('toastContainer');
        const t = document.createElement('div');
        t.className = 'toast ' + type;
        t.textContent = msg;
        c.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3000);
    }
    window.showToast = showToast;

    function escHtml(s) { const d = document.createElement('div'); d.textContent = s || '—'; return d.innerHTML; }

    /* ── section navigation ──────────────────────────────────── */
    function switchSection(name) {
        document.querySelectorAll('.section-page').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-items a[data-section]').forEach(a => a.classList.remove('active'));
        const page = document.getElementById('section-' + name);
        const link = document.querySelector(`.nav-items a[data-section="${name}"]`);
        if (page) page.classList.add('active');
        if (link) link.classList.add('active');
        window.location.hash = name;

        // lazy-load data on first visit
        if (name === 'overview' && !loaded.overview) loadOverview();
        if (name === 'drivers'  && !loaded.drivers)  loadDrivers();
        if (name === 'parents'  && !loaded.parents)   loadParents();
        if (name === 'notifications' && !loaded.notifications) loadNotifications();
    }
    window.switchSection = switchSection;

    const loaded = { overview: false, drivers: false, parents: false, notifications: false };

    /* ════════════════════════════════════════════════════════════
       OVERVIEW SECTION
       ════════════════════════════════════════════════════════════ */
    async function loadOverview() {
        const el = document.getElementById('overviewContent');
        el.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Loading dashboard…</p></div>';
        try {
            const res = await api(API + '/dashboard/stats');
            const s = res.data || res;
            el.innerHTML = `
            <div class="stats-row">
                <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-users"></i></div><div><h3>${s.total_parents ?? 0}</h3><span>Parents</span></div></div>
                <div class="stat-card"><div class="stat-icon green"><i class="fas fa-id-card"></i></div><div><h3>${s.total_drivers ?? 0}</h3><span>Drivers</span></div></div>
                <div class="stat-card"><div class="stat-icon amber"><i class="fas fa-user-graduate"></i></div><div><h3>${s.total_students ?? 0}</h3><span>Students</span></div></div>
                <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-bus"></i></div><div><h3>${s.total_buses ?? 0}</h3><span>Buses (${s.active_buses ?? 0} active)</span></div></div>
                <div class="stat-card"><div class="stat-icon red"><i class="fas fa-route"></i></div><div><h3>${s.today_trips ?? 0}</h3><span>Trips Today (${s.active_trips ?? 0} active)</span></div></div>
                <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-map-signs"></i></div><div><h3>${s.total_routes ?? 0}</h3><span>Routes</span></div></div>
                <div class="stat-card"><div class="stat-icon green"><i class="fas fa-user-shield"></i></div><div><h3>${s.total_users ?? 0}</h3><span>Total Users</span></div></div>
            </div>`;
            loaded.overview = true;
        } catch (e) {
            el.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Could not load stats</h3><p>' + (e.message || 'API error') + '</p></div>';
        }
    }

    /* ════════════════════════════════════════════════════════════
       DRIVERS SECTION
       ════════════════════════════════════════════════════════════ */
    let allDrivers = [];

    async function loadDrivers() {
        const tbody = document.getElementById('driversTableBody');
        tbody.innerHTML = '<tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>';
        try {
            const res = await api(API + '/drivers?per_page=all');
            allDrivers = res.data || [];
            renderDriversTable(allDrivers);
            loaded.drivers = true;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Failed to load drivers</td></tr>';
        }
    }

    function renderDriversTable(list) {
        const tbody = document.getElementById('driversTableBody');
        if (!list.length) { tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)">No drivers found</td></tr>'; return; }
        tbody.innerHTML = list.map(d => `<tr>
            <td style="font-weight:600">${escHtml(d.name || d.full_name)}</td>
            <td>${escHtml(d.email)}</td>
            <td>${escHtml(d.phone)}</td>
            <td>${escHtml(d.license_number)}</td>
            <td>${d.years_experience ?? '—'}</td>
            <td><span class="badge badge-${d.active ? 'active' : 'inactive'}">${d.active ? 'Active' : 'Inactive'}</span></td>
            <td><span class="badge badge-${d.status || 'pending'}">${d.status || 'pending'}</span></td>
            <td>
                <button class="action-btn view" onclick="viewDriver(${d.id})" title="View"><i class="fas fa-eye"></i></button>
                <button class="action-btn edit" onclick="editDriver(${d.id})" title="Edit"><i class="fas fa-edit"></i></button>
                ${d.status==='pending'?`<button class="action-btn approve" onclick="approveDriver(${d.id})" title="Approve"><i class="fas fa-check"></i></button>`:''}
                <button class="action-btn delete" onclick="deleteDriver(${d.id})" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
    }

    window.filterDrivers = function(q) {
        q = q.toLowerCase();
        renderDriversTable(allDrivers.filter(d => (d.name||d.full_name||'').toLowerCase().includes(q) || (d.email||'').toLowerCase().includes(q) || (d.phone||'').includes(q)));
    };

    window.showAddDriverModal = function() {
        document.getElementById('driverFormTitle').textContent = 'Add New Driver';
        document.getElementById('driverForm').reset();
        document.getElementById('driverFormId').value = '';
        document.getElementById('driverModal').classList.add('active');
    };

    window.editDriver = function(id) {
        const d = allDrivers.find(x => x.id === id);
        if (!d) return;
        document.getElementById('driverFormTitle').textContent = 'Edit Driver';
        document.getElementById('driverFormId').value = id;
        document.getElementById('df_name').value = d.name || d.full_name || '';
        document.getElementById('df_email').value = d.email || '';
        document.getElementById('df_phone').value = d.phone || '';
        document.getElementById('df_license').value = d.license_number || '';
        document.getElementById('df_experience').value = d.years_experience || '';
        document.getElementById('df_car_type').value = d.car_type || '';
        document.getElementById('df_car_model').value = d.car_model || '';
        document.getElementById('df_car_plate').value = d.car_plate || '';
        document.getElementById('df_address').value = d.address || '';
        document.getElementById('driverModal').classList.add('active');
    };

    window.viewDriver = function(id) {
        const d = allDrivers.find(x => x.id === id);
        if (!d) return;
        const c = document.getElementById('driverDetailContent');
        c.innerHTML = `<div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Name</div><div class="detail-value">${escHtml(d.name||d.full_name)}</div></div>
            <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">${escHtml(d.email)}</div></div>
            <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">${escHtml(d.phone)}</div></div>
            <div class="detail-item"><div class="detail-label">License</div><div class="detail-value">${escHtml(d.license_number)}</div></div>
            <div class="detail-item"><div class="detail-label">Experience</div><div class="detail-value">${d.years_experience??'—'} years</div></div>
            <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-${d.status}">${d.status}</span></div></div>
            <div class="detail-item"><div class="detail-label">Car</div><div class="detail-value">${escHtml(d.car_type)} ${escHtml(d.car_model)} — ${escHtml(d.car_plate)}</div></div>
            <div class="detail-item full"><div class="detail-label">Address</div><div class="detail-value">${escHtml(d.address)}</div></div>
            <div class="detail-item full"><div class="detail-label">Password</div><div class="detail-value">${escHtml(d.password_plain)}</div></div>
        </div>`;
        document.getElementById('driverDetailModal').classList.add('active');
    };

    window.submitDriverForm = async function(e) {
        e.preventDefault();
        const id = document.getElementById('driverFormId').value;
        const data = {
            name: document.getElementById('df_name').value,
            email: document.getElementById('df_email').value,
            phone: document.getElementById('df_phone').value,
            license_number: document.getElementById('df_license').value,
            years_experience: parseInt(document.getElementById('df_experience').value) || 0,
            car_type: document.getElementById('df_car_type').value,
            car_model: document.getElementById('df_car_model').value,
            car_plate: document.getElementById('df_car_plate').value,
            address: document.getElementById('df_address').value,
        };
        if (!id) data.password = 'password';
        try {
            await api(API + '/drivers' + (id ? '/' + id : ''), { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
            showToast('success', id ? 'Driver updated!' : 'Driver created!');
            document.getElementById('driverModal').classList.remove('active');
            loaded.drivers = false; loadDrivers();
        } catch (e) {
            const msg = e.errors ? Object.values(e.errors).flat().join(', ') : (e.message || 'Error');
            showToast('error', msg);
        }
    };

    window.approveDriver = async function(id) {
        try { await api(API + '/drivers/' + id + '/approve', { method: 'POST' }); showToast('success', 'Driver approved'); loaded.drivers = false; loadDrivers(); }
        catch { showToast('error', 'Failed to approve'); }
    };

    window.deleteDriver = async function(id) {
        if (!confirm('Delete this driver permanently?')) return;
        try { await api(API + '/drivers/' + id, { method: 'DELETE' }); showToast('success', 'Driver deleted'); loaded.drivers = false; loadDrivers(); }
        catch { showToast('error', 'Failed to delete'); }
    };

    /* ════════════════════════════════════════════════════════════
       PARENTS SECTION
       ════════════════════════════════════════════════════════════ */
    let allParents = [];

    async function loadParents() {
        const tbody = document.getElementById('parentsTableBody');
        tbody.innerHTML = '<tr><td colspan="6" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>';
        try {
            const res = await api(API + '/parents?per_page=all');
            allParents = res.data || [];
            renderParentsTable(allParents);
            loaded.parents = true;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Failed to load parents</td></tr>';
        }
    }

    function renderParentsTable(list) {
        const tbody = document.getElementById('parentsTableBody');
        if (!list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary)">No parents found</td></tr>'; return; }
        tbody.innerHTML = list.map(p => {
            const name = p.user?.name || p.name || '—';
            const email = p.user?.email || p.email || '—';
            return `<tr>
            <td style="font-weight:600">${escHtml(name)}</td>
            <td>${escHtml(email)}</td>
            <td>${escHtml(p.phone)}</td>
            <td>${escHtml(p.address)}</td>
            <td>${p.children_count ?? '—'}</td>
            <td>
                <button class="action-btn edit" onclick="editParent(${p.id})" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="action-btn delete" onclick="deleteParent(${p.id})" title="Delete"><i class="fas fa-trash"></i></button>
            </td></tr>`;
        }).join('');
    }

    window.filterParents = function(q) {
        q = q.toLowerCase();
        renderParentsTable(allParents.filter(p => {
            const n = (p.user?.name||p.name||'').toLowerCase();
            return n.includes(q) || (p.phone||'').includes(q);
        }));
    };

    window.showAddParentModal = function() {
        document.getElementById('parentFormTitle').textContent = 'Add New Parent';
        document.getElementById('parentForm').reset();
        document.getElementById('parentFormId').value = '';
        document.getElementById('parentModal').classList.add('active');
    };

    window.editParent = function(id) {
        const p = allParents.find(x => x.id === id);
        if (!p) return;
        document.getElementById('parentFormTitle').textContent = 'Edit Parent';
        document.getElementById('parentFormId').value = id;
        document.getElementById('pf_name').value = p.user?.name || p.name || '';
        document.getElementById('pf_email').value = p.user?.email || p.email || '';
        document.getElementById('pf_phone').value = p.phone || '';
        document.getElementById('pf_address').value = p.address || '';
        document.getElementById('parentModal').classList.add('active');
    };

    window.submitParentForm = async function(e) {
        e.preventDefault();
        const id = document.getElementById('parentFormId').value;
        const data = {
            name: document.getElementById('pf_name').value,
            email: document.getElementById('pf_email').value,
            phone: document.getElementById('pf_phone').value,
            address: document.getElementById('pf_address').value,
        };
        if (!id) data.password = 'password';
        try {
            await api(API + '/parents' + (id ? '/' + id : ''), { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
            showToast('success', id ? 'Parent updated!' : 'Parent created!');
            document.getElementById('parentModal').classList.remove('active');
            loaded.parents = false; loadParents();
        } catch (e) {
            const msg = e.errors ? Object.values(e.errors).flat().join(', ') : (e.message || 'Error');
            showToast('error', msg);
        }
    };

    window.deleteParent = async function(id) {
        if (!confirm('Delete this parent permanently?')) return;
        try { await api(API + '/parents/' + id, { method: 'DELETE' }); showToast('success', 'Parent deleted'); loaded.parents = false; loadParents(); }
        catch { showToast('error', 'Failed to delete'); }
    };

    /* ════════════════════════════════════════════════════════════
       NOTIFICATIONS SECTION
       ════════════════════════════════════════════════════════════ */
    async function loadNotifications() {
        const tbody = document.getElementById('notificationsTableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>';
        try {
            const res = await api(API + '/notifications');
            const list = res.data || [];
            if (!list.length) { tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-secondary)">No notifications yet</td></tr>'; }
            else {
                tbody.innerHTML = list.map(n => `<tr>
                    <td style="font-weight:600">${escHtml(n.title)}</td>
                    <td>${escHtml(n.message || n.body)}</td>
                    <td>${escHtml(n.target || n.audience || 'all')}</td>
                    <td>${n.created_at ? new Date(n.created_at).toLocaleString() : '—'}</td>
                </tr>`).join('');
            }
            loaded.notifications = true;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-secondary)">Failed to load notifications</td></tr>';
        }
    }

    window.showSendNotificationModal = function() {
        document.getElementById('notifForm').reset();
        document.getElementById('notifModal').classList.add('active');
    };

    window.submitNotification = async function(e) {
        e.preventDefault();
        const target = document.getElementById('nf_target').value;
        const data = {
            title: document.getElementById('nf_title').value,
            body: document.getElementById('nf_message').value,
        };
        if (target !== 'all') data.role = target === 'parents' ? 'parent' : 'driver';
        try {
            await api(API + '/notifications/send-bulk', { method: 'POST', body: JSON.stringify(data) });
            showToast('success', 'Notification sent!');
            document.getElementById('notifModal').classList.remove('active');
            loaded.notifications = false; loadNotifications();
        } catch (e) {
            showToast('error', e.message || 'Failed to send');
        }
    };

    /* ── APPLICATIONS (server-rendered, add client-side helpers) ── */
    window.viewApplication = function(id) {
        const modal = document.getElementById('detailModal');
        const content = document.getElementById('detailContent');
        content.innerHTML = '<p style="text-align:center;color:var(--text-secondary)"><i class="fas fa-spinner fa-spin"></i> Loading…</p>';
        modal.classList.add('active');
        window._currentAppId = id;

        fetch('/admin/applications/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                const d = res.data;
                document.getElementById('statusSelect').value = d.status;
                let metaHtml = '';
                if (d.metadata && Object.keys(d.metadata).length > 0) {
                    metaHtml = '<div class="meta-section"><h4><i class="fas fa-tags"></i> Role-Specific Details</h4><div class="detail-grid">';
                    for (const [key, val] of Object.entries(d.metadata)) {
                        if (val) metaHtml += `<div class="detail-item"><div class="detail-label">${key.replace(/_/g,' ')}</div><div class="detail-value">${escHtml(val)}</div></div>`;
                    }
                    metaHtml += '</div></div>';
                }
                content.innerHTML = `<div class="detail-grid">
                    <div class="detail-item"><div class="detail-label">Full Name</div><div class="detail-value">${escHtml(d.full_name)}</div></div>
                    <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">${escHtml(d.email)}</div></div>
                    <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">${escHtml(d.phone)}</div></div>
                    <div class="detail-item"><div class="detail-label">Role</div><div class="detail-value"><span class="badge badge-${d.role}">${d.role}</span></div></div>
                    <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-${d.status}" id="currentStatusBadge">${d.status}</span></div></div>
                    <div class="detail-item"><div class="detail-label">Submitted</div><div class="detail-value">${escHtml(d.created_at)}</div></div>
                    <div class="detail-item full"><div class="detail-label">Address</div><div class="detail-value">${escHtml(d.address)}</div></div>
                    <div class="detail-item full"><div class="detail-label">Experience</div><div class="detail-value">${escHtml(d.experience)}</div></div>
                    ${d.notes ? `<div class="detail-item full"><div class="detail-label">Notes</div><div class="detail-value">${escHtml(d.notes)}</div></div>` : ''}
                </div>${metaHtml}`;
            })
            .catch(() => { content.innerHTML = '<p style="color:var(--danger)">Failed to load details.</p>'; });
    };

    window.saveStatus = function() {
        if (!window._currentAppId) return;
        const status = document.getElementById('statusSelect').value;
        const btn = document.getElementById('saveStatusBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
        fetch('/admin/applications/' + window._currentAppId + '/status', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ status }),
        }).then(r => r.json()).then(res => {
            showToast('success', res.message || 'Status updated');
            const badge = document.getElementById('currentStatusBadge');
            if (badge) { badge.className = 'badge badge-' + status; badge.textContent = status; }
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save';
            setTimeout(() => location.reload(), 1200);
        }).catch(() => { showToast('error', 'Failed to update status'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save'; });
    };

    window.confirmDelete = function(id, name) {
        window._deleteId = id;
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteModal').classList.add('active');
    };

    window.executeDelete = function() {
        if (!window._deleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting…';
        fetch('/admin/applications/' + window._deleteId, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(r => r.json()).then(res => {
            closeModal('deleteModal'); showToast('success', res.message || 'Deleted');
            setTimeout(() => location.reload(), 800);
        }).catch(() => { showToast('error', 'Failed to delete'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Delete'; });
    };

    /* ── modal helpers ───────────────────────────────────────── */
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    window.closeModal = closeModal;

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('active'); });
    });

    /* ── INIT ────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        // nav click handlers
        document.querySelectorAll('.nav-items a[data-section]').forEach(a => {
            a.addEventListener('click', e => { e.preventDefault(); switchSection(a.dataset.section); });
        });

        // read hash or default to applications
        const hash = window.location.hash.replace('#', '') || 'applications';
        switchSection(hash);

        // debounced search for applications
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let t;
            searchInput.addEventListener('input', () => { clearTimeout(t); t = setTimeout(() => document.getElementById('filterForm')?.submit(), 500); });
        }
    });
})();
