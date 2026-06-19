/**
 * SafeStep School Administrator Dashboard
 */
(function () {
    'use strict';

    const API = '/school-admin';
    const ALEX_CENTER = [31.2001, 29.9187];
    let charts = {};
    let trackingMap = null;
    let routeMap = null;
    let trackingMarkers = [];
    let trackingPollTimer = null;
    const cache = { students: [], parents: [], buses: [], drivers: [], routes: [], trips: [], emergencies: [] };

    function asArray(data) {
        const list = data?.data ?? data;
        return Array.isArray(list) ? list : [];
    }
    function asObject(data) {
        const obj = data?.data ?? data;
        return obj && typeof obj === 'object' && !Array.isArray(obj) ? obj : {};
    }

    const pageTitles = {
        dashboard: 'School Dashboard',
        parents: 'Parent Management',
        students: 'Student Management',
        buses: 'Bus Management',
        drivers: 'Driver Management',
        routes: 'Route Management',
        trips: 'Trip Monitoring',
        tracking: 'Live Bus Tracking',
        attendance: 'Attendance Management',
        notifications: 'Parent Communication',
        emergency: 'Emergency Center',
        reports: 'Reports & Analytics',
        settings: 'School Settings',
        'activity-logs': 'Activity Logs',
    };

    function api(endpoint, options = {}) {
        return safestepApi(`${API}${endpoint}`, options);
    }

    function el(id) {
        return document.getElementById(id);
    }

    function toast(msg, type = 'info') {
        let container = document.getElementById('saToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'saToastContainer';
            container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
            document.body.appendChild(container);
        }
        const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
        const icons  = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-circle', info: 'fa-info-circle' };
        const t = document.createElement('div');
        t.style.cssText = `display:flex;align-items:center;gap:10px;background:var(--card-bg,#1e2130);border:1px solid ${colors[type]||colors.info}30;color:var(--text-dark,#f1f5f9);padding:12px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.35);min-width:260px;max-width:360px;font-size:14px;pointer-events:all;opacity:0;transform:translateX(30px);transition:all .3s ease;`;
        t.innerHTML = `<i class="fas ${icons[type]||icons.info}" style="color:${colors[type]||colors.info};font-size:18px;"></i><span style="flex:1;">${msg}</span>`;
        container.appendChild(t);
        requestAnimationFrame(() => { t.style.opacity = '1'; t.style.transform = 'translateX(0)'; });
        setTimeout(() => {
            t.style.opacity = '0'; t.style.transform = 'translateX(30px)';
            setTimeout(() => t.remove(), 300);
        }, 3500);
    }

    function badge(status) {
        let cls = 'badge-warning';
        if (status === 'active' || status === 'approved' || status === 'completed' || status === 'present' || status === 'on_route' || status === 'resolved') cls = 'badge-success';
        else if (status === 'absent' || status === 'high' || status === 'open') cls = 'badge-danger';
        else if (status === 'late' || status === 'medium' || status === 'maintenance' || status === 'idle' || status === 'inactive') cls = 'badge-warning';
        else if (status === 'low') cls = 'badge-info';
        return `<span class="badge ${cls}">${status || 'unknown'}</span>`;
    }

    function navigate(page) {
        if (typeof window.navigateTo === 'function') {
            window.navigateTo(page);
        } else {
            document.querySelectorAll('.nav-link[data-page]').forEach(link => {
                link.classList.toggle('active', link.dataset.page === page);
            });
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            const target = el(page);
            if (target) target.classList.add('active');
            if (el('pageTitle')) el('pageTitle').textContent = pageTitles[page] || 'School Dashboard';
            loadPage(page);
        }
    }

    function isSchoolDashboardReady() {
        const data = window.__SCHOOL_ADMIN_DATA || {};
        return data.isDashboardUnlocked === true || data.appStatus === 'active';
    }

    function getPageLockMessage() {
        const data = window.__SCHOOL_ADMIN_DATA || {};
        if (data.appStatus === 'pending_details' || data.needsOnboarding) {
            return {
                title: 'أكمل ملف المدرسة',
                body: 'يرجى إكمال نموذج تفاصيل المدرسة من لوحة التحكم لتفعيل هذا القسم.',
                showDashboardBtn: true,
            };
        }
        if (data.appStatus === 'pending_approval' || data.awaitingProfileApproval) {
            return {
                title: 'الملف قيد المراجعة',
                body: 'تم إرسال ملف المدرسة. سيتم تفعيل جميع الأقسام بعد موافقة إدارة SafeStep.',
                showDashboardBtn: false,
            };
        }
        if (data.appStatus === 'rejected') {
            return {
                title: 'تم رفض الملف',
                body: 'تم رفض ملف المدرسة. راجع البيانات أو تواصل مع الدعم.',
                showDashboardBtn: true,
            };
        }
        return null;
    }

    function clearPageLock(pageEl) {
        if (!pageEl) return;
        const lock = pageEl.querySelector('.school-page-lock');
        if (lock) lock.remove();
    }

    function showPageLock(pageEl) {
        if (!pageEl) return;
        const msg = getPageLockMessage();
        if (!msg) {
            clearPageLock(pageEl);
            return;
        }

        let lock = pageEl.querySelector('.school-page-lock');
        if (!lock) {
            lock = document.createElement('div');
            lock.className = 'school-page-lock';
            pageEl.appendChild(lock);
        }

        const dashboardBtn = msg.showDashboardBtn
            ? '<button type="button" class="btn-primary" onclick="SchoolAdmin.navigate(\'dashboard\')"><i class="fas fa-chart-line"></i> الذهاب للوحة التحكم</button>'
            : '';

        lock.innerHTML = `
            <div class="school-page-lock-card">
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(37,99,235,.1);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="fas fa-lock" style="font-size:28px;color:var(--accent);"></i>
                </div>
                <h3>${msg.title}</h3>
                <p>${msg.body}</p>
                ${dashboardBtn}
            </div>`;
    }

    async function loadPage(page) {
        const target = el(page);
        if (!isSchoolDashboardReady() && page !== 'dashboard') {
            if (window.DashboardLock) {
                window.DashboardLock.showPageLock(target);
            }
            return;
        }

        if (window.DashboardLock) {
            window.DashboardLock.clearPageLock(target);
        }
        const loaders = {
            dashboard: loadDashboard,
            parents: loadParents,
            students: loadStudents,
            buses: loadBuses,
            drivers: loadDrivers,
            routes: loadRoutes,
            trips: loadTrips,
            tracking: loadTracking,
            attendance: loadAttendance,
            emergency: loadEmergencies,
            notifications: loadNotificationCenter,
            settings: loadSettings,
            'activity-logs': loadActivityLogs,
        };
        if (loaders[page]) await loaders[page]();
    }

    async function loadDashboard() {
        if (!isSchoolDashboardReady()) {
            return;
        }

        try {
            let s, tripsData, trendsData, fleetData, safetyData;
            try {
                const [stats, attendance, trips, fleet, trends, safety] = await Promise.all([
                    api('/dashboard/stats'),
                    api('/dashboard/attendance-summary'),
                    api('/dashboard/trips-overview?days=7'),
                    api('/dashboard/fleet-status'),
                    api('/dashboard/attendance-trends?days=30'),
                    api('/dashboard/safety-reports?months=6'),
                ]);
                s = asObject(stats.data);
                tripsData = asArray(trips.data);
                trendsData = asArray(trends.data);
                fleetData = asArray(fleet.data);
                safetyData = asArray(safety.data);
            } catch (_) {
                s = {};
                tripsData = [];
                trendsData = [];
                fleetData = [];
                safetyData = [];
            }

            setText('statStudents', s.total_students);
            setText('statActiveStudents', s.active_students);
            setText('statDrivers', s.total_drivers);
            setText('statBuses', s.total_buses);
            setText('statActiveTrips', s.active_trips);
            setText('statTodayAttendance', s.today_attendance);
            setText('statTodayAbsence', s.today_absence);
            setText('statEmergencies', s.emergency_alerts);

            renderTripsChart(tripsData);
            renderAttendanceTrendChart(trendsData);
            renderBusUsageChart(fleetData);
            renderSafetyChart(safetyData);
            loadKpis();
            loadRiskStudents();
        } catch (e) {
            console.warn('[SchoolAdmin] dashboard load:', e);
        }
    }

    async function loadKpis() {
        let k;
        try { const res = await api('/dashboard/kpis'); k = asObject(res.data); } catch (_) { k = {}; }
        const panel = el('kpiPanel');
        if (!panel) return;
        panel.innerHTML = `
            <div class="kpi-item">
                <div class="kpi-icon blue"><i class="fas fa-clipboard-user"></i></div>
                <div class="kpi-data">
                    <strong>${k.attendance_rate ?? 0}%</strong>
                    <span>Attendance Rate</span>
                </div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon green"><i class="fas fa-truck-monster"></i></div>
                <div class="kpi-data">
                    <strong>${k.fleet_utilization ?? 0}%</strong>
                    <span>Fleet Utilization</span>
                </div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon orange"><i class="fas fa-user-clock"></i></div>
                <div class="kpi-data">
                    <strong>${k.on_time_trips ?? 0}%</strong>
                    <span>On-Time Trips</span>
                </div>
            </div>
            <div class="kpi-item">
                <div class="kpi-icon purple"><i class="fas fa-shield-halved"></i></div>
                <div class="kpi-data">
                    <strong>${k.safety_score ?? 0}</strong>
                    <span>Safety Score</span>
                </div>
            </div>`;
    }

    async function loadRiskStudents() {
        let data;
        try { const res = await api('/dashboard/student-risk'); data = asArray(res.data); } catch (_) { data = []; }
        const tbody = el('riskStudentsBody');
        if (!tbody) return;
        tbody.innerHTML = data.map(r => `
            <tr><td>${r.name}</td><td>${r.grade || '-'}</td><td>${r.absent_rate}%</td><td>${badge(r.risk_level)}</td></tr>`
        ).join('') || '<tr><td colspan="4">No at-risk students detected.</td></tr>';
    }

    async function loadParents() {
        let data;
        try { const res = await api('/parents'); data = asArray(res.data); } catch (_) { data = []; }
        cache.parents = data;
        const tbody = el('parentsTableBody');
        if (!tbody) return;
        tbody.innerHTML = data.map(p => `
            <tr>
                <td>${p.name || '-'}</td><td>${p.email || '-'}</td><td>${p.phone || '-'}</td>
                <td>${(p.children || []).map(c => c.name || c.full_name || '').join(', ') || '-'}</td>
                <td>${badge(p.active ? 'active' : 'inactive')}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.viewParent(${p.id})" title="Profile"><i class="fas fa-user"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="6">No parents found.</td></tr>';
    }

    async function viewParent(id) {
        let p = cache.parents.find(x => x.id === id) || {};
        try { const res = await api(`/parents/${id}`); p = { ...p, ...(res.data || {}) }; } catch (_) {}
        const children = (p.students || p.children || []).map(s => s.full_name || s.name || '').filter(Boolean).join(', ') || '-';
        openModal('Parent Profile', `
            <div style="display:grid;gap:12px;">
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--light-bg);border-radius:10px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#10b981,#34d399);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div><h3 style="margin:0;">${p.name || '-'}</h3><span style="color:var(--text-light);">${badge(p.active ? 'active' : 'inactive')}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Email</div><div style="font-weight:600;margin-top:4px;word-break:break-all;">${p.email || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Phone</div><div style="font-weight:600;margin-top:4px;">${p.phone || '-'}</div></div>
                    <div style="grid-column:1/-1;padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Address</div><div style="font-weight:600;margin-top:4px;">${p.address || '-'}</div></div>
                    <div style="grid-column:1/-1;padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Children</div><div style="font-weight:600;margin-top:4px;">${children}</div></div>
                </div>
            </div>
        `, `<button class="btn-secondary" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    function setText(id, val) {
        const node = el(id);
        if (node) node.textContent = val ?? 0;
    }

    function renderTripsChart(data) {
        const ctx = el('tripsChart');
        if (!ctx) return;
        const labels = [...new Set(data.map(r => r.trip_date))];
        const statuses = [...new Set(data.map(r => r.status))];
        const datasets = statuses.map((status, i) => ({
            label: status,
            data: labels.map(date => {
                const row = data.find(r => r.trip_date === date && r.status === status);
                return row ? row.count : 0;
            }),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'][i % 4],
        }));
        destroyChart('trips');
        charts.trips = new Chart(ctx, { type: 'bar', data: { labels, datasets }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
    }

    function renderAttendanceTrendChart(data) {
        const ctx = el('attendanceTrendChart');
        if (!ctx) return;
        const labels = [...new Set(data.map(r => r.date))];
        const statuses = [...new Set(data.map(r => r.status))];
        const datasets = statuses.map((status, i) => ({
            label: status,
            data: labels.map(date => {
                const row = data.find(r => r.date === date && r.status === status);
                return row ? row.count : 0;
            }),
            borderColor: ['#3b82f6', '#10b981', '#f59e0b'][i % 3],
            tension: 0.3,
        }));
        destroyChart('attendance');
        charts.attendance = new Chart(ctx, { type: 'line', data: { labels, datasets }, options: { responsive: true } });
    }

    function renderBusUsageChart(data) {
        const ctx = el('busUsageChart');
        if (!ctx) return;
        destroyChart('busUsage');
        charts.busUsage = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(b => b.bus_number),
                datasets: [{ data: data.map(b => b.total_trips || 0), backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'] }],
            },
            options: { responsive: true },
        });
    }

    function renderSafetyChart(data) {
        const ctx = el('safetyChart');
        if (!ctx) return;
        const labels = [...new Set(data.map(r => r.month))];
        destroyChart('safety');
        charts.safety = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Alerts', data: labels.map(m => data.filter(r => r.month === m).reduce((s, r) => s + Number(r.count), 0)), backgroundColor: '#ef4444' }],
            },
            options: { responsive: true },
        });
    }

    function destroyChart(key) {
        if (charts[key]) { charts[key].destroy(); delete charts[key]; }
    }

    async function loadStudents() {
        let data;
        try { const res = await api('/students?per_page=all'); data = asArray(res.data); } catch (_) { data = []; }
        cache.students = data;
        const tbody = el('studentsTableBody');
        if (!tbody) return;
        tbody.innerHTML = cache.students.map(s => `
            <tr>
                <td>${s.name}</td><td>${s.grade || '-'}</td>
                <td>${s.parent?.user?.name || s.parent?.name || '-'}</td>
                <td>${s.bus?.bus_number || '-'}</td>
                <td>${s.route?.name || '-'}</td>
                <td>${badge(s.status)}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.viewStudent(${s.id})" title="View"><i class="fas fa-eye"></i></button>
                        <button class="action-icon edit" onclick="SchoolAdmin.editStudent(${s.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="action-icon" style="background:rgba(168,85,247,.15);color:#a855f7;" onclick="SchoolAdmin.getQr(${s.id})" title="QR Code"><i class="fas fa-qrcode"></i></button>
                        <button class="action-icon delete" onclick="SchoolAdmin.deleteStudent(${s.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="7">No students found.</td></tr>';
    }

    async function loadBuses() {
        let data;
        try { const res = await api('/buses'); data = asArray(res.data); } catch (_) { data = []; }
        cache.buses = data;
        const tbody = el('busesTableBody');
        if (!tbody) return;
        tbody.innerHTML = cache.buses.map(b => `
            <tr>
                <td>${b.bus_number}</td><td>${b.plate_number}</td><td>${b.capacity}</td>
                <td>${b.driver || '-'}</td><td>${b.route || '-'}</td>
                <td>${b.insurance_expiry || '-'} ${b.insurance_alert ? '<span style="color:#f59e0b">⚠️</span>' : ''}</td>
                <td>${badge(b.status)}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.viewBus(${b.id})" title="View"><i class="fas fa-eye"></i></button>
                        <button class="action-icon edit" onclick="SchoolAdmin.editBus(${b.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="action-icon delete" onclick="SchoolAdmin.deleteBus(${b.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="8">No buses found.</td></tr>';
    }

    async function loadDrivers() {
        let data;
        try { const res = await api('/drivers'); data = asArray(res.data); } catch (_) { data = []; }
        cache.drivers = data;
        const tbody = el('driversTableBody');
        if (!tbody) return;
        tbody.innerHTML = cache.drivers.map(d => `
            <tr>
                <td>${d.name}</td><td>${d.license || '-'}</td><td>${d.phone || '-'}</td>
                <td>${d.experience || '-'}</td><td>${d.bus || '-'}</td>
                <td>${badge(d.status)}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.viewDriver(${d.id})" title="Profile"><i class="fas fa-id-card"></i></button>
                        <button class="action-icon edit" onclick="SchoolAdmin.editDriver(${d.id})" title="Edit Status"><i class="fas fa-edit"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="7">No drivers found.</td></tr>';
    }

    async function loadRoutes() {
        let data;
        try { const res = await api('/routes'); data = asArray(res.data); } catch (_) { data = []; }
        cache.routes = data;
        const tbody = el('routesTableBody');
        if (!tbody) return;
        tbody.innerHTML = cache.routes.map(r => `
            <tr>
                <td>${r.name}</td><td>${r.type}</td><td>${(r.stops || []).length}</td>
                <td>${r.estimated_minutes || '-'} min</td><td>${r.distance_km || '-'} km</td>
                <td>${r.bus || '-'}</td><td>${r.driver || '-'}</td><td>${r.students_count || 0}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.showRouteMap(${r.id})" title="Map"><i class="fas fa-map-marked-alt"></i></button>
                        <button class="action-icon delete" onclick="SchoolAdmin.deleteRoute(${r.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="9">No routes found.</td></tr>';
    }

    async function loadTrips() {
        let data;
        try { const res = await api('/trips'); data = asArray(res.data); } catch (_) { data = []; }
        cache.trips = data;
        const tbody = el('tripsTableBody');
        if (!tbody) return;
        tbody.innerHTML = cache.trips.map(t => `
            <tr>
                <td>${t.trip_date}</td><td>${t.shift}</td><td>${t.route || '-'}</td>
                <td>${t.bus || '-'}</td><td>${t.driver || '-'}</td><td>${t.students_count || 0}</td>
                <td>${badge(t.status)}</td>
                <td>
                    <div class="table-actions">
                        <button class="action-icon view" onclick="SchoolAdmin.viewTrip(${t.id})" title="View"><i class="fas fa-info-circle"></i></button>
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="8">No trips found.</td></tr>';
    }

    async function loadTracking() {
        let fleet;
        try { const res = await api('/tracking/live'); fleet = asArray(res.data); } catch (_) { fleet = []; }
        const tbody = el('trackingTableBody');
        if (tbody) {
            tbody.innerHTML = fleet.map(b => `
                <tr>
                    <td>${b.bus_number || b.bus_id}</td>
                    <td>${b.driver_name || '-'}</td>
                    <td>${b.speed ?? 0} km/h</td>
                    <td>${badge(b.status)}</td>
                    <td>${b.last_update || '-'}</td>
                </tr>`).join('') || '<tr><td colspan="5">No live buses.</td></tr>';
        }
        initTrackingMap(fleet);
        if (trackingPollTimer) clearInterval(trackingPollTimer);
        trackingPollTimer = setInterval(loadTracking, 15000);
    }

    function initTrackingMap(fleet) {
        if (typeof L === 'undefined') return;
        const mapEl = el('liveTrackingMap');
        if (!mapEl) return;
        if (!trackingMap) {
            trackingMap = L.map('liveTrackingMap').setView(ALEX_CENTER, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(trackingMap);
        }
        trackingMarkers.forEach(m => trackingMap.removeLayer(m));
        trackingMarkers = [];
        fleet.forEach(bus => {
            if (bus.latitude && bus.longitude) {
                const marker = L.marker([bus.latitude, bus.longitude]).addTo(trackingMap)
                    .bindPopup(`<b>${bus.bus_number}</b><br>${bus.status || ''}`);
                trackingMarkers.push(marker);
            }
        });
    }

    async function loadAttendance() {
        const date = el('attendanceDate')?.value || new Date().toISOString().slice(0, 10);
        let data;
        try { const res = await api(`/attendance?date=${date}`); data = asArray(res.data); } catch (_) { data = []; }
        const tbody = el('attendanceTableBody');
        if (!tbody) return;
        tbody.innerHTML = data.map(a => `
            <tr>
                <td>${a.student_name}</td><td>${a.bus_number || '-'}</td><td>${a.route_name || '-'}</td>
                <td>${a.picked_up_at || '-'}</td><td>${a.dropped_off_at || '-'}</td>
                <td>${badge(a.status)}</td>
            </tr>`).join('') || '<tr><td colspan="6">No attendance records.</td></tr>';
    }

    async function loadEmergencies() {
        let data;
        try { const res = await api('/emergency-alerts'); data = asArray(res.data); } catch (_) { data = []; }
        cache.emergencies = data;
        const tbody = el('emergencyTableBody');
        if (!tbody) return;
        tbody.innerHTML = data.map(a => `
            <tr>
                <td>${a.type}</td><td>${a.severity}</td><td>${a.message || '-'}</td>
                <td>${badge(a.status)}</td><td>${a.created_at || '-'}</td>
                <td>
                    <div class="table-actions">
                        ${a.status === 'open' ? `<button class="btn-primary btn-compact" onclick="SchoolAdmin.resolveEmergency(${a.id})"><i class="fas fa-check-circle"></i> Resolve</button>` : ''}
                    </div>
                </td>
            </tr>`).join('') || '<tr><td colspan="6">No emergency alerts.</td></tr>';
    }

    async function loadSettings() {
        try {
            const res = await api('/settings');
            const data = res.data || {};
            if (data.school) {
                setVal('schoolName', data.school.name);
                setVal('schoolPrincipal', data.school.principal_name);
                setVal('schoolEmail', data.school.email);
                setVal('schoolPhone', data.school.phone);
                setVal('schoolAddress', data.school.address);
            }
            if (data.admin) {
                setVal('profileName', data.admin.name);
                setVal('profileEmail', data.admin.email);
            }
        } catch (_) {
            // Silently fall back to pre-filled server data
        }
    }

    function setVal(id, val) {
        const node = el(id);
        if (node) node.value = val || '';
    }

    async function loadActivityLogs() {
        let data;
        try { const res = await api('/activity-logs'); data = asArray(res.data); } catch (_) { data = []; }
        const tbody = el('activityLogsBody');
        if (!tbody) return;
        tbody.innerHTML = data.map(l => `
            <tr>
                <td>${l.action}</td>
                <td>${l.user?.name || '-'}</td>
                <td>${l.entity_type || '-'} #${l.entity_id || ''}</td>
                <td>${l.created_at || '-'}</td>
            </tr>`).join('') || '<tr><td colspan="4">No activity logs.</td></tr>';
    }

    function openModal(title, bodyHtml, footerHtml) {
        el('schoolModalTitle').textContent = title;
        el('schoolModalBody').innerHTML = bodyHtml;
        el('schoolModalFooter').innerHTML = footerHtml || '';
        el('schoolModal').style.display = 'flex';
    }

    function closeModal() {
        el('schoolModal').style.display = 'none';
    }

    function openStudentModal() {
        const parentsOpt = (cache.parents || []).map(p => `<option value="${p.id}">${p.name} (${p.phone})</option>`).join('') || '<option value="">No parents loaded</option>';
        const busesOpt = '<option value="">Select Bus (Optional)</option>' + (cache.buses || []).map(b => `<option value="${b.id}">${b.bus_number}</option>`).join('');
        const routesOpt = '<option value="">Select Route (Optional)</option>' + (cache.routes || []).map(r => `<option value="${r.id}">${r.name}</option>`).join('');

        openModal('Add Student', `
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <div style="position:relative;">
                    <input id="mStudentName" class="form-control" placeholder="Student's full name" style="padding-left: 35px;">
                    <i class="fas fa-user" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Grade</label>
                <div style="position:relative;">
                    <input id="mStudentGrade" class="form-control" placeholder="e.g. Grade 4" style="padding-left: 35px;">
                    <i class="fas fa-graduation-cap" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Parent</label>
                <div style="position:relative;">
                    <select id="mStudentParent" class="form-control" style="padding-left: 35px;">
                        ${parentsOpt}
                    </select>
                    <i class="fas fa-users" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Assigned Bus</label>
                <div style="position:relative;">
                    <select id="mStudentBus" class="form-control" style="padding-left: 35px;">
                        ${busesOpt}
                    </select>
                    <i class="fas fa-bus" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Assigned Route</label>
                <div style="position:relative;">
                    <select id="mStudentRoute" class="form-control" style="padding-left: 35px;">
                        ${routesOpt}
                    </select>
                    <i class="fas fa-map-signs" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.saveStudent()"><i class="fas fa-save"></i> Save</button>`);
    }

    async function saveStudent() {
        const pId = parseInt(el('mStudentParent').value, 10);
        const pObj = cache.parents.find(p => p.id === pId) || { name: 'Parent' };
        
        const busId = parseInt(el('mStudentBus').value, 10);
        const busObj = cache.buses.find(b => b.id === busId) || {};
        
        const routeId = parseInt(el('mStudentRoute').value, 10);
        const routeObj = cache.routes.find(r => r.id === routeId) || {};

        const payload = {
            id: Date.now(),
            name: el('mStudentName').value,
            grade: el('mStudentGrade').value,
            parent: { user: { name: pObj.name } },
            bus: { bus_number: busObj.bus_number || '-' },
            route: { name: routeObj.name || '-' },
            status: 'active',
            qr_code: `QR-${Math.floor(100 + Math.random() * 900)}`,
            rfid_tag: `RFID-${Math.floor(100 + Math.random() * 900)}`
        };

        const postBody = {
            full_name: payload.name,
            grade: payload.grade,
            parent_id: pId,
        };
        if (busId) postBody.bus_id = busId;
        if (routeId) postBody.bus_route_id = routeId;

        const requestBody = {
            request_type: 'add_student',
            subject: `Add Student: ${payload.name}`,
            description: `Request to add student: ${payload.name}, Grade: ${payload.grade}`,
            priority: 'medium',
            metadata: postBody
        };

        try {
            await safestepApi('/api/service-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestBody)
            });
            toast('Addition request sent to Admin for approval', 'success');
        } catch (_) {
            cache.students.push(payload);
            toast('Student added (demo mode)', 'info');
        }
        closeModal();
        loadStudents();
    }

    async function viewStudent(id) {
        let s = cache.students.find(x => x.id === id) || {};
        try { const res = await api(`/students/${id}`); s = res.data || s; } catch (_) {}
        openModal('Student Profile', `
            <div style="display:grid;gap:12px;">
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--light-bg);border-radius:10px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--primary-color),var(--primary-light));display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div><h3 style="margin:0;">${s.name || '-'}</h3><span style="color:var(--text-light);">${s.grade || '-'}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Parent</div><div style="font-weight:600;margin-top:4px;">${s.parent?.user?.name || s.parent?.name || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Status</div><div style="margin-top:4px;">${badge(s.status)}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Bus</div><div style="font-weight:600;margin-top:4px;">${s.bus?.bus_number || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Route</div><div style="font-weight:600;margin-top:4px;">${s.route?.name || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">QR Code</div><div style="font-weight:600;margin-top:4px;font-family:monospace;">${s.qr_code || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">RFID Tag</div><div style="font-weight:600;margin-top:4px;font-family:monospace;">${s.rfid_tag || '-'}</div></div>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.editStudent(${id})"><i class="fas fa-edit"></i> Edit</button><button class="btn-secondary" style="margin-left:8px;" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    function editStudent(id) {
        const s = cache.students.find(x => x.id === id) || {};
        const parentsOpt = (cache.parents || []).map(p => `<option value="${p.id}" ${(s.parent?.id === p.id || s.parent_id === p.id) ? 'selected' : ''}>${p.name} (${p.phone})</option>`).join('') || '<option value="">No parents loaded</option>';
        const busesOpt = '<option value="">-- No Change --</option>' + (cache.buses || []).map(b => `<option value="${b.id}" ${(s.bus?.id === b.id || s.bus_id === b.id) ? 'selected' : ''}>${b.bus_number}</option>`).join('');
        const routesOpt = '<option value="">-- No Change --</option>' + (cache.routes || []).map(r => `<option value="${r.id}" ${(s.route?.id === r.id || s.bus_route_id === r.id) ? 'selected' : ''}>${r.name}</option>`).join('');
        openModal('Edit Student', `
            <div class="form-group"><label class="form-label">Full Name</label><div style="position:relative;"><input id="mEditStudentName" class="form-control" value="${s.name || ''}" style="padding-left:35px;"><i class="fas fa-user" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i></div></div>
            <div class="form-group"><label class="form-label">Grade</label><div style="position:relative;"><input id="mEditStudentGrade" class="form-control" value="${s.grade || ''}" style="padding-left:35px;"><i class="fas fa-graduation-cap" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i></div></div>
            <div class="form-group"><label class="form-label">Parent</label><div style="position:relative;"><select id="mEditStudentParent" class="form-control" style="padding-left:35px;">${parentsOpt}</select><i class="fas fa-users" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);z-index:5;"></i></div></div>
            <div class="form-group"><label class="form-label">Assigned Bus</label><div style="position:relative;"><select id="mEditStudentBus" class="form-control" style="padding-left:35px;">${busesOpt}</select><i class="fas fa-bus" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);z-index:5;"></i></div></div>
            <div class="form-group"><label class="form-label">Assigned Route</label><div style="position:relative;"><select id="mEditStudentRoute" class="form-control" style="padding-left:35px;">${routesOpt}</select><i class="fas fa-map-signs" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);z-index:5;"></i></div></div>
            <div class="form-group"><label class="form-label">Status</label><select id="mEditStudentStatus" class="form-control"><option value="active" ${s.status==='active'?'selected':''}>Active</option><option value="inactive" ${s.status==='inactive'?'selected':''}>Inactive</option></select></div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.updateStudent(${id})"><i class="fas fa-save"></i> Update</button>`);
    }

    async function updateStudent(id) {
        const payload = {
            full_name: el('mEditStudentName').value,
            grade: el('mEditStudentGrade').value,
            status: el('mEditStudentStatus').value,
            parent_id: parseInt(el('mEditStudentParent').value, 10),
        };
        const busVal = el('mEditStudentBus').value;
        if (busVal) payload.bus_id = parseInt(busVal, 10);
        const routeVal = el('mEditStudentRoute').value;
        if (routeVal) payload.bus_route_id = parseInt(routeVal, 10);
        try {
            await api(`/students/${id}`, { method: 'PUT', body: payload });
            toast('Student updated successfully', 'success');
        } catch (_) {
            // Update demo cache locally if API not available
            const idx = cache.students.findIndex(s => s.id === id);
            if (idx >= 0) { cache.students[idx] = { ...cache.students[idx], ...payload }; }
            toast('Student updated (demo mode)', 'info');
        }
        closeModal();
        loadStudents();
    }

    async function getQr(id) {
        const cached = cache.students.find(s => s.id === id);
        let qr = cached?.qr_code || 'N/A';
        let name = cached?.name || 'Student';
        try { const res = await api(`/students/${id}/qr`); qr = res.data?.qr_code || qr; name = res.data?.name || name; } catch (_) {}
        const qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qr)}`;
        openModal('Student QR Code', `
            <div style="text-align:center; padding: 20px;">
                <div style="margin-bottom:16px;">
                    <img src="${qrImgUrl}" alt="QR Code" style="width:150px;height:150px;border: 6px solid #fff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); background:#fff; padding: 4px;">
                </div>
                <h3 style="margin-bottom:4px;color:var(--text-dark);">${name}</h3>
                <span style="color:var(--text-muted);font-size:13px;">${cached?.grade || ''}</span>
                <div style="margin-top:16px;padding:14px;background:var(--light-bg);border-radius:10px;">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;margin-bottom:6px;">QR Data Payload</div>
                    <div style="font-family:monospace;font-size:15px;font-weight:700;letter-spacing:1px;color:var(--text-dark);">${qr}</div>
                </div>
                <div style="margin-top:10px;padding:10px 14px;background:var(--light-bg);border-radius:10px;">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">RFID Tag</div>
                    <div style="font-family:monospace;font-size:14px;color:var(--text-dark);">${cached?.rfid_tag || '-'}</div>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    async function deleteStudent(id) {
        const s = cache.students.find(x => x.id === id);
        if (!confirm(`Delete student "${s?.name || id}"? This cannot be undone.`)) return;
        try {
            await api(`/students/${id}`, { method: 'DELETE' });
            toast('Student deleted', 'success');
        } catch (_) {
            cache.students = cache.students.filter(x => x.id !== id);
            toast('Student removed (demo mode)', 'info');
        }
        loadStudents();
    }

    function openBusModal() {
        openModal('Add Bus', `
            <div class="form-group">
                <label class="form-label">Bus Number</label>
                <div style="position:relative;">
                    <input id="mBusNumber" class="form-control" placeholder="e.g. BUS-07" style="padding-left: 35px;">
                    <i class="fas fa-bus" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Plate Number</label>
                <div style="position:relative;">
                    <input id="mBusPlate" class="form-control" placeholder="e.g. أ ب ج ١٢٣٤" style="padding-left: 35px;">
                    <i class="fas fa-id-card" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Capacity</label>
                <div style="position:relative;">
                    <input id="mBusCapacity" type="number" class="form-control" value="40" style="padding-left: 35px;">
                    <i class="fas fa-users" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.saveBus()"><i class="fas fa-save"></i> Save</button>`);
    }

    async function saveBus() {
        const payload = {
            id: Date.now(),
            bus_number: el('mBusNumber').value,
            plate_number: el('mBusPlate').value,
            capacity: parseInt(el('mBusCapacity').value, 10),
            driver: '-',
            route: '-',
            insurance_expiry: new Date(Date.now() + 365*24*60*60*1000).toISOString().slice(0, 10),
            insurance_alert: false,
            status: 'active'
        };

        const postBody = {
            bus_number: payload.bus_number,
            plate_number: payload.plate_number,
            capacity: payload.capacity,
        };

        const requestBody = {
            request_type: 'add_bus',
            subject: `Add Bus: ${payload.bus_number}`,
            description: `Request to add bus: ${payload.bus_number}, Plate: ${payload.plate_number}, Capacity: ${payload.capacity}`,
            priority: 'medium',
            metadata: postBody
        };

        try {
            await safestepApi('/api/service-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestBody)
            });
            toast('Addition request sent to Admin for approval', 'success');
        } catch (_) {
            cache.buses.push(payload);
            toast('Bus added (demo mode)', 'info');
        }
        closeModal();
        loadBuses();
    }

    async function editBus(id) {
        const bus = cache.buses.find(b => b.id === id);
        if (!bus) return;
        openModal('Edit Bus', `
            <div class="form-group">
                <label class="form-label">Status</label>
                <div style="position:relative;">
                    <select id="mBusStatus" class="form-control" style="padding-left: 35px;">
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <i class="fas fa-info-circle" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.updateBus(${id})"><i class="fas fa-save"></i> Update</button>`);
        el('mBusStatus').value = bus.status || 'active';
    }

    async function updateBus(id) {
        try {
            await api(`/buses/${id}`, { method: 'PUT', body: { status: el('mBusStatus').value } });
            toast('Bus updated successfully', 'success');
        } catch (_) {
            const idx = cache.buses.findIndex(b => b.id === id);
            if (idx >= 0) cache.buses[idx].status = el('mBusStatus').value;
            toast('Bus updated (demo mode)', 'info');
        }
        closeModal();
        loadBuses();
    }

    async function viewBus(id) {
        const b = cache.buses.find(x => x.id === id) || {};
        openModal('Bus Details', `
            <div style="display:grid;gap:12px;">
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--light-bg);border-radius:10px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#60a5fa);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="fas fa-bus"></i>
                    </div>
                    <div><h3 style="margin:0;">${b.bus_number || '-'}</h3><span style="color:var(--text-light);">Plate: ${b.plate_number || '-'}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Capacity</div><div style="font-weight:600;margin-top:4px;">${b.capacity || '-'} seats</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Status</div><div style="margin-top:4px;">${badge(b.status)}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Driver</div><div style="font-weight:600;margin-top:4px;">${b.driver || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Route</div><div style="font-weight:600;margin-top:4px;">${b.route || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;${b.insurance_alert ? 'border:1px solid #f59e0b;' : ''}"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Insurance Expiry</div><div style="font-weight:600;margin-top:4px;color:${b.insurance_alert ? '#f59e0b' : 'inherit'};">${b.insurance_expiry || '-'} ${b.insurance_alert ? '⚠️' : ''}</div></div>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.editBus(${id})"><i class="fas fa-edit"></i> Edit</button><button class="btn-secondary" style="margin-left:8px;" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    async function deleteBus(id) {
        const b = cache.buses.find(x => x.id === id);
        if (!confirm(`Delete bus "${b?.bus_number || id}"? This cannot be undone.`)) return;
        try {
            await api(`/buses/${id}`, { method: 'DELETE' });
            toast('Bus deleted', 'success');
        } catch (_) {
            cache.buses = cache.buses.filter(x => x.id !== id);
            toast('Bus removed (demo mode)', 'info');
        }
        loadBuses();
    }

    async function viewDriver(id) {
        let d = cache.drivers.find(x => x.id === id) || {};
        try { const res = await api(`/drivers/${id}`); d = { ...d, ...(res.data || {}) }; } catch (_) {}
        openModal('Driver Profile', `
            <div style="display:grid;gap:12px;">
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--light-bg);border-radius:10px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#fbbf24);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div><h3 style="margin:0;">${d.name || '-'}</h3><span style="color:var(--text-light);">License: ${d.license || '-'}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Phone</div><div style="font-weight:600;margin-top:4px;">${d.phone || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Experience</div><div style="font-weight:600;margin-top:4px;">${d.experience || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Bus Assigned</div><div style="font-weight:600;margin-top:4px;">${d.bus || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Status</div><div style="margin-top:4px;">${badge(d.status)}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Total Trips</div><div style="font-weight:600;margin-top:4px;">${d.metrics?.total_trips || d.total_trips || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">On-time Rate</div><div style="font-weight:600;margin-top:4px;">${d.metrics?.on_time_rate || d.on_time_rate || '-'}%</div></div>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.editDriver(${id})"><i class="fas fa-edit"></i> Edit</button><button class="btn-secondary" style="margin-left:8px;" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    function editDriver(id) {
        const d = cache.drivers.find(x => x.id === id) || {};
        const busesOpt = '<option value="">-- Not Assigned --</option>' + (cache.buses || []).map(b => `<option value="${b.id}">${b.bus_number}</option>`).join('');
        openModal('Edit Driver', `
            <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--light-bg);border-radius:10px;margin-bottom:16px;">
                <i class="fas fa-user-tie" style="font-size:24px;color:var(--primary-color);"></i>
                <div><strong>${d.name || 'Driver'}</strong><br><span style="font-size:13px;color:var(--text-muted);">License: ${d.license || '-'}</span></div>
            </div>
            <div class="form-group"><label class="form-label">Status</label><select id="mEditDriverStatus" class="form-control"><option value="active" ${d.status==='active'?'selected':''}>Active</option><option value="inactive" ${d.status==='inactive'?'selected':''}>Inactive</option><option value="suspended" ${d.status==='suspended'?'selected':''}>Suspended</option></select></div>
            <div class="form-group"><label class="form-label">Assign Bus</label><div style="position:relative;"><select id="mEditDriverBus" class="form-control" style="padding-left:35px;">${busesOpt}</select><i class="fas fa-bus" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);z-index:5;"></i></div></div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.updateDriver(${id})"><i class="fas fa-save"></i> Update</button>`);
    }

    async function updateDriver(id) {
        const payload = { status: el('mEditDriverStatus').value };
        const busVal = el('mEditDriverBus').value;
        if (busVal) payload.bus_id = parseInt(busVal, 10);
        try {
            await api(`/drivers/${id}`, { method: 'PUT', body: payload });
            toast('Driver updated successfully', 'success');
        } catch (_) {
            const idx = cache.drivers.findIndex(d => d.id === id);
            if (idx >= 0) cache.drivers[idx] = { ...cache.drivers[idx], ...payload };
            toast('Driver updated (demo mode)', 'info');
        }
        closeModal();
        loadDrivers();
    }

    function openRouteModal() {
        openModal('Create Route', `
            <div class="form-group">
                <label class="form-label">Name</label>
                <div style="position:relative;">
                    <input id="mRouteName" class="form-control" placeholder="e.g. Route A" style="padding-left: 35px;">
                    <i class="fas fa-map-signs" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <div style="position:relative;">
                    <select id="mRouteType" class="form-control" style="padding-left: 35px;">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                    </select>
                    <i class="fas fa-clock" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Estimated Minutes</label>
                <div style="position:relative;">
                    <input id="mRouteMinutes" type="number" class="form-control" value="30" style="padding-left: 35px;">
                    <i class="fas fa-hourglass-half" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.saveRoute()"><i class="fas fa-save"></i> Save</button>`);
    }

    async function saveRoute() {
        const payload = {
            id: Date.now(),
            name: el('mRouteName').value,
            type: el('mRouteType').value,
            estimated_minutes: parseInt(el('mRouteMinutes').value, 10),
            distance_km: '5.0',
            bus: '-',
            driver: '-',
            students_count: 0,
            stops: [{ name: 'School Gate', lat: 31.2001, lng: 29.9187, order: 1 }],
        };

        const postBody = {
            name: payload.name,
            type: payload.type,
            estimated_minutes: payload.estimated_minutes,
            stops: payload.stops,
        };

        const requestBody = {
            request_type: 'add_route',
            subject: `Add Route: ${payload.name}`,
            description: `Request to add route: ${payload.name} (${payload.type}), Estimated time: ${payload.estimated_minutes} min`,
            priority: 'medium',
            metadata: postBody
        };

        try {
            await safestepApi('/api/service-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestBody)
            });
            toast('Addition request sent to Admin for approval', 'success');
        } catch (_) {
            cache.routes.push(payload);
            toast('Route added (demo mode)', 'info');
        }
        closeModal();
        loadRoutes();
    }

    function showRouteMap(id) {
        const route = cache.routes.find(r => r.id === id);
        if (!route) return;
        openModal(`Route Map — ${route.name}`, `
            <div style="margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;">
                ${(route.stops||[]).map((s,i)=>`<span style="background:var(--light-bg);padding:4px 10px;border-radius:20px;font-size:13px;"><b>${i+1}.</b> ${s.name}</span>`).join('<i class="fas fa-arrow-right" style="color:var(--text-muted);align-self:center;font-size:12px;"></i>')}
            </div>
            <div id="routeMapModal" style="height:380px;border-radius:10px;overflow:hidden;"></div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px;">
                <div style="padding:10px;background:var(--light-bg);border-radius:8px;text-align:center;"><div style="font-size:11px;color:var(--text-muted);">Duration</div><div style="font-weight:700;">${route.estimated_minutes||'-'} min</div></div>
                <div style="padding:10px;background:var(--light-bg);border-radius:8px;text-align:center;"><div style="font-size:11px;color:var(--text-muted);">Distance</div><div style="font-weight:700;">${route.distance_km||'-'} km</div></div>
                <div style="padding:10px;background:var(--light-bg);border-radius:8px;text-align:center;"><div style="font-size:11px;color:var(--text-muted);">Students</div><div style="font-weight:700;">${route.students_count||0}</div></div>
            </div>
        `, `<button class="btn-secondary" onclick="SchoolAdmin.closeModal()">Close</button>`);
        // Initialize map inside modal after render
        setTimeout(() => {
            if (typeof L === 'undefined') return;
            const mapEl = document.getElementById('routeMapModal');
            if (!mapEl) return;
            if (routeMap) { routeMap.remove(); routeMap = null; }
            routeMap = L.map('routeMapModal').setView(ALEX_CENTER, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(routeMap);
            const stops = route.stops || [];
            const latlngs = [];
            stops.forEach((stop, i) => {
                if (stop.lat && stop.lng) {
                    const icon = L.divIcon({ html: `<div style="background:var(--primary-color,#3b82f6);color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);">${i+1}</div>`, className: '' });
                    L.marker([stop.lat, stop.lng], { icon }).addTo(routeMap).bindPopup(`<b>${stop.name}</b>`);
                    latlngs.push([stop.lat, stop.lng]);
                }
            });
            if (latlngs.length > 1) L.polyline(latlngs, { color: '#3b82f6', weight: 3, dashArray: '6 4' }).addTo(routeMap);
            if (latlngs.length) routeMap.fitBounds(latlngs, { padding: [30, 30] });
        }, 200);
    }

    async function deleteRoute(id) {
        const r = cache.routes.find(x => x.id === id);
        if (!confirm(`Delete route "${r?.name || id}"? This cannot be undone.`)) return;
        try {
            await api(`/routes/${id}`, { method: 'DELETE' });
            toast('Route deleted', 'success');
        } catch (_) {
            cache.routes = cache.routes.filter(x => x.id !== id);
            toast('Route removed (demo mode)', 'info');
        }
        loadRoutes();
    }

    function openTripModal() {
        const driversOpt = (cache.drivers || []).map(d => `<option value="${d.id}">${d.name}</option>`).join('') || '<option value="">No drivers loaded</option>';
        const busesOpt = (cache.buses || []).map(b => `<option value="${b.id}">${b.bus_number}</option>`).join('') || '<option value="">No buses loaded</option>';
        const routesOpt = (cache.routes || []).map(r => `<option value="${r.id}">${r.name}</option>`).join('') || '<option value="">No routes loaded</option>';

        openModal('Schedule Trip', `
            <div class="form-group">
                <label class="form-label">Date</label>
                <div style="position:relative;">
                    <input id="mTripDate" type="date" class="form-control" style="padding-left: 35px;">
                    <i class="fas fa-calendar-alt" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Shift</label>
                <div style="position:relative;">
                    <select id="mTripShift" class="form-control" style="padding-left: 35px;">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                    </select>
                    <i class="fas fa-clock" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Driver</label>
                <div style="position:relative;">
                    <select id="mTripDriver" class="form-control" style="padding-left: 35px;">
                        ${driversOpt}
                    </select>
                    <i class="fas fa-user-tie" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bus</label>
                <div style="position:relative;">
                    <select id="mTripBus" class="form-control" style="padding-left: 35px;">
                        ${busesOpt}
                    </select>
                    <i class="fas fa-bus" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Route</label>
                <div style="position:relative;">
                    <select id="mTripRoute" class="form-control" style="padding-left: 35px;">
                        ${routesOpt}
                    </select>
                    <i class="fas fa-map-signs" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.saveTrip()"><i class="fas fa-save"></i> Save</button>`);
        el('mTripDate').value = new Date().toISOString().slice(0, 10);
    }

    async function saveTrip() {
        const postBody = {
            trip_date: el('mTripDate').value,
            shift: el('mTripShift').value,
            driver_id: parseInt(el('mTripDriver').value, 10),
            bus_id: parseInt(el('mTripBus').value, 10),
            bus_route_id: parseInt(el('mTripRoute').value, 10),
        };

        const routeObj = cache.routes.find(r => r.id === postBody.bus_route_id) || { name: 'Route' };

        const requestBody = {
            request_type: 'add_trip',
            subject: `Schedule Trip: ${routeObj.name}`,
            description: `Request to schedule trip on ${postBody.trip_date} (${postBody.shift})`,
            priority: 'medium',
            metadata: postBody
        };

        try {
            await safestepApi('/api/service-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestBody)
            });
            toast('Schedule trip request sent to Admin for approval', 'success');
        } catch (_) {
            toast('Trip scheduled (demo mode)', 'info');
        }
        closeModal();
        loadTrips();
    }

    async function viewTrip(id) {
        let t = cache.trips.find(x => x.id === id) || {};
        try { const res = await api(`/trips/${id}`); t = { ...t, ...(res.data || {}) }; } catch (_) {}
        openModal('Trip Details', `
            <div style="display:grid;gap:12px;">
                <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--light-bg);border-radius:10px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="fas fa-route"></i>
                    </div>
                    <div><h3 style="margin:0;">${t.route || 'Trip'}</h3><span style="color:var(--text-light);">${t.trip_date || '-'} &bull; ${t.shift || '-'}</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Bus</div><div style="font-weight:600;margin-top:4px;">${t.bus || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Driver</div><div style="font-weight:600;margin-top:4px;">${t.driver || '-'}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Students</div><div style="font-size:22px;font-weight:700;margin-top:4px;">${t.students_count || 0}</div></div>
                    <div style="padding:12px;background:var(--light-bg);border-radius:8px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">Status</div><div style="margin-top:4px;">${badge(t.status)}</div></div>
                </div>
            </div>
        `, `<button class="btn-secondary" onclick="SchoolAdmin.closeModal()">Close</button>`);
    }

    async function loadNotificationCenter() {
        let d;
        try { const res = await api('/notifications/center'); d = asObject(res.data); } catch (_) { d = {}; }
        setText('notifSent', d.sent_total);
        setText('notifRead', d.read_total);
        setText('notifUnread', d.unread_total);
    }

    async function sendBroadcast() {
        const title = el('notifTitle').value.trim();
        const body = el('notifBody').value.trim();
        if (!title || !body) { toast('Please fill in title and message', 'warning'); return; }
        try {
            await api('/notifications/send-bulk', {
                method: 'POST',
                body: { title, body, type: el('notifType').value },
            });
            toast('Broadcast sent to all parents!', 'success');
            el('notifTitle').value = '';
            el('notifBody').value = '';
        } catch (_) {
            toast('Broadcast sent (demo mode)', 'info');
        }
        loadNotificationCenter();
    }

    function openEmergencyModal() {
        const busesOpt = '<option value="">Select Bus (Optional)</option>' + (cache.buses || []).map(b => `<option value="${b.id}">${b.bus_number}</option>`).join('');
        openModal('Report Emergency', `
            <div class="form-group">
                <label class="form-label">Type</label>
                <div style="position:relative;">
                    <select id="mEmergType" class="form-control" style="padding-left: 35px;">
                        <option value="sos">SOS</option>
                        <option value="breakdown">Bus Breakdown</option>
                        <option value="student_emergency">Student Emergency</option>
                        <option value="accident">Accident</option>
                        <option value="delay">Delay</option>
                        <option value="medical">Medical</option>
                        <option value="other">Other</option>
                    </select>
                    <i class="fas fa-triangle-exclamation" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Severity</label>
                <div style="position:relative;">
                    <select id="mEmergSeverity" class="form-control" style="padding-left: 35px;">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high" selected>High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <i class="fas fa-shield-alt" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bus Impacted</label>
                <div style="position:relative;">
                    <select id="mEmergBus" class="form-control" style="padding-left: 35px;">
                        ${busesOpt}
                    </select>
                    <i class="fas fa-bus" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); z-index: 5;"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea id="mEmergMsg" class="form-control" rows="3" placeholder="Describe the emergency details..."></textarea>
            </div>
        `, `<button class="btn-primary" onclick="SchoolAdmin.saveEmergency()"><i class="fas fa-paper-plane"></i> Report</button>`);
    }

    async function saveEmergency() {
        const payload = {
            type: el('mEmergType').value,
            message: el('mEmergMsg').value,
            severity: el('mEmergSeverity').value,
        };
        const busVal = el('mEmergBus').value;
        if (busVal) payload.bus_id = parseInt(busVal, 10);
        try {
            await api('/emergency-alerts', { method: 'POST', body: payload });
            toast('Emergency reported', 'warning');
        } catch (_) {
            toast('Emergency reported (demo mode)', 'warning');
        }
        closeModal();
        loadEmergencies();
    }

    async function resolveEmergency(id) {
        try {
            await api(`/emergency-alerts/${id}/resolve`, { method: 'POST' });
            toast('Emergency resolved', 'success');
        } catch (_) {
            const idx = cache.emergencies ? cache.emergencies.findIndex(x => x.id === id) : -1;
            if (idx >= 0) cache.emergencies[idx].status = 'resolved';
            toast('Emergency resolved (demo mode)', 'success');
        }
        loadEmergencies();
    }

    async function loadReport() {
        const type = el('reportType')?.value || 'summary';
        const out = el('reportOutput');
        if (!out) return;
        out.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);"><i class="fas fa-spinner fa-spin" style="font-size:28px;"></i><br>Loading report...</div>';
        let reportData;
        try {
            const res = await api(`/reports?type=${type}`);
            reportData = res.data;
        } catch (_) {
            // Compile demo data based on type
            const typeMap = { students: cache.students, buses: cache.buses, drivers: cache.drivers, routes: cache.routes, attendance: [], safety: [], summary: {} };
            reportData = typeMap[type] || cache.students;
        }
        // Render as formatted report cards
        if (Array.isArray(reportData)) {
            const keys = reportData.length ? Object.keys(reportData[0]).slice(0, 6) : [];
            out.innerHTML = `
                <div style="padding:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <strong style="font-size:15px;">Report: ${type.charAt(0).toUpperCase()+type.slice(1)} (${reportData.length} records)</strong>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead><tr>${keys.map(k=>`<th>${k.replace(/_/g,' ')}</th>`).join('')}</tr></thead>
                            <tbody>${reportData.slice(0,20).map(row=>`<tr>${keys.map(k=>`<td>${row[k]??'-'}</td>`).join('')}</tr>`).join('')}</tbody>
                        </table>
                        ${reportData.length > 20 ? `<p style="text-align:center;color:var(--text-muted);padding:10px;">Showing 20 of ${reportData.length} records. Export to see all.</p>` : ''}
                    </div>
                </div>`;
        } else {
            const entries = Object.entries(reportData || {});
            out.innerHTML = `<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;padding:16px;">${
                entries.map(([k,v])=>`<div style="padding:14px;background:var(--light-bg);border-radius:10px;"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;">${k.replace(/_/g,' ')}</div><div style="font-size:22px;font-weight:700;margin-top:6px;">${v??'-'}</div></div>`).join('')
            }</div>`;
        }
    }

    async function exportReport(format) {
        const type = el('reportType')?.value || 'students';
        toast(`Preparing ${type} report as ${format.toUpperCase()}...`, 'info');
        try {
            const token = localStorage.getItem('safestep_token') || localStorage.getItem('token') || window.__API_TOKEN || '';
            const res = await fetch(`${API}/reports/export?type=${type}&format=${format}`, {
                headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `school-report-${type}.${format}`;
            a.click();
            URL.revokeObjectURL(url);
            toast(`${type} report exported as ${format.toUpperCase()}`, 'success');
        } catch (_) {
            toast('Export unavailable in demo mode. Connect backend to enable.', 'warning');
        }
    }

    async function saveSchoolSettings(e) {
        e.preventDefault();
        try {
            await api('/settings/school', {
                method: 'PUT',
                body: {
                    name: el('schoolName').value,
                    principal_name: el('schoolPrincipal').value,
                    email: el('schoolEmail').value,
                    phone: el('schoolPhone').value,
                    address: el('schoolAddress').value,
                },
            });
            toast('School profile saved', 'success');
        } catch (_) { toast('School profile saved (demo mode)', 'info'); }
    }

    async function saveProfileSettings(e) {
        e.preventDefault();
        try {
            await api('/settings/profile', {
                method: 'PUT',
                body: { name: el('profileName').value, email: el('profileEmail').value },
            });
            toast('Profile saved', 'success');
        } catch (_) { toast('Profile saved (demo mode)', 'info'); }
    }

    async function showNotifications(showModalFlag = false) {
        let list = [];
        try {
            const res = await api('/notifications');
            list = res.data || [];
        } catch (_) {
            if (showModalFlag) {
                toast('تعذر تحميل الإشعارات حالياً.', 'warning');
            }
            return;
        }
        const badge = el('notifBadge');
        if (badge) badge.textContent = list.length;

        if (showModalFlag) {
            const listHtml = list.map(n => {
                const title = n.data?.title || 'Announcement';
                const body = n.data?.body || n.data?.message || '-';
                const isEmergency = n.data?.type === 'emergency';
                return `
                    <div style="padding:12px; border-bottom:1px solid var(--border-light); display:flex; gap:12px; align-items:flex-start; text-align:left;">
                        <div style="background:var(--light-bg); border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:${isEmergency ? 'var(--danger-color)' : 'var(--primary-color)'}; flex-shrink:0;">
                            <i class="${isEmergency ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle'}"></i>
                        </div>
                        <div style="flex:1;">
                            <h4 style="margin:0; font-size:14px; color:var(--text-dark);">${title}</h4>
                            <p style="margin:4px 0 0; font-size:13px; color:var(--text-light);">${body}</p>
                            <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:2px;">${n.created_at ? new Date(n.created_at).toLocaleString() : ''}</span>
                        </div>
                    </div>
                `;
            }).join('') || '<p style="text-align:center; color:var(--text-muted); padding:20px;">No notifications yet.</p>';

            openModal('Recent Notifications', `
                <div style="max-height: 350px; overflow-y: auto;">
                    ${listHtml}
                </div>
            `, `<button class="btn-primary" onclick="SchoolAdmin.closeModal()">Close</button>`);
        }
    }

    const MOBILE_BREAKPOINT = 768;
    let sidebarOverlay = null;

    function isMobileView() {
        return window.innerWidth <= MOBILE_BREAKPOINT;
    }

    function setMenuToggleIcon(isOpen) {
        const menuToggle = el('menuToggle');
        if (!menuToggle) return;
        const icon = menuToggle.querySelector('i');
        if (!icon) return;
        icon.classList.toggle('fa-bars', !isOpen);
        icon.classList.toggle('fa-times', isOpen);
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function setSidebarOpen(isOpen) {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        sidebar.classList.toggle('active', isOpen);
        sidebarOverlay?.classList.toggle('active', isOpen);
        document.body.classList.toggle('sidebar-open', isOpen);
        setMenuToggleIcon(isOpen);
    }

    function initSidebarOverlay() {
        if (sidebarOverlay) return;
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        sidebarOverlay.addEventListener('click', () => setSidebarOpen(false));
        document.body.appendChild(sidebarOverlay);
    }

    function initGlobalSearch() {
        const input = el('schoolSearch');
        if (!input || input.dataset.bound === '1') return;
        input.dataset.bound = '1';
        input.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') return;
            const q = e.target.value.trim().toLowerCase();
            if (!q) return;
            if (q.includes('parent') || q.includes('ولي')) {
                navigate('parents');
            } else if (q.includes('driver') || q.includes('سائق')) {
                navigate('drivers');
            } else if (q.includes('bus') || q.includes('حاف')) {
                navigate('buses');
            } else {
                navigate('students');
                const studentSearch = el('studentSearch');
                if (studentSearch) {
                    studentSearch.value = e.target.value;
                    studentSearch.dispatchEvent(new Event('input'));
                }
            }
        });
    }

    function prefillFromServerData() {
        const data = window.__SCHOOL_ADMIN_DATA || {};
        if (data.user?.name && el('adminName')) el('adminName').textContent = data.user.name;
        if (data.school?.name && el('schoolNameBar')) el('schoolNameBar').textContent = data.school.name;
        if (data.school) {
            setVal('schoolName', data.school.name);
            setVal('schoolPrincipal', data.school.principal_name);
            setVal('schoolEmail', data.school.email);
            setVal('schoolPhone', data.school.phone);
            setVal('schoolAddress', data.school.address);
        }
        if (data.user) {
            setVal('profileName', data.user.name);
            setVal('profileEmail', data.user.email);
        }
    }

    function initNavigation() {
        initSidebarOverlay();
        if (el('attendanceDate')) {
            el('attendanceDate').value = new Date().toISOString().slice(0, 10);
            el('attendanceDate').addEventListener('change', loadAttendance);
        }
        if (el('studentSearch')) {
            el('studentSearch').addEventListener('input', e => {
                const q = e.target.value.toLowerCase();
                document.querySelectorAll('#studentsTableBody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }
        const menuToggle = el('menuToggle');
        if (menuToggle && menuToggle.dataset.bound !== '1') {
            menuToggle.dataset.bound = '1';
            menuToggle.addEventListener('click', () => {
                const sidebar = document.querySelector('.sidebar');
                setSidebarOpen(!sidebar?.classList.contains('active'));
            });
        }
        window.addEventListener('resize', () => {
            if (!isMobileView()) setSidebarOpen(false);
        });
        initGlobalSearch();
    }

    function init() {
        initNavigation();
        prefillFromServerData();
        const initial = window.__INITIAL_PAGE || 'dashboard';
        navigate(initial);
        if (isSchoolDashboardReady()) {
            showNotifications();
        }
    }

    // Connect to global SPA Navigation event
    document.addEventListener('spa:pageChanged', (e) => {
        const page = e.detail.pageId;
        if (el('pageTitle')) el('pageTitle').textContent = pageTitles[page] || 'School Dashboard';
        loadPage(page);
    });

    window.SchoolAdmin = {
        navigate, closeModal,
        openStudentModal, saveStudent, viewStudent, editStudent, updateStudent, getQr, deleteStudent,
        openBusModal, saveBus, viewBus, editBus, updateBus, deleteBus,
        viewDriver, editDriver, updateDriver,
        openRouteModal, saveRoute, showRouteMap, deleteRoute,
        openTripModal, saveTrip, viewTrip,
        sendBroadcast,
        openEmergencyModal, saveEmergency, resolveEmergency,
        loadReport, exportReport,
        viewParent,
        saveSchoolSettings, saveProfileSettings, showNotifications,
    };

    document.addEventListener('DOMContentLoaded', init);
})();
