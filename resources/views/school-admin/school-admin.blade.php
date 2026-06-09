<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>School Dashboard - SafeStep Bus</title>
    <link rel="icon" href="{{ asset('img/icon.jpg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>
    <script>
    (function(){
        var t = '{{ session('api_token', '') }}';
        window.__API_TOKEN = t;
        window.__INITIAL_PAGE = '{{ $initialPage ?? 'dashboard' }}';
        window.__SCHOOL_ADMIN_DATA = {
            school: @json($school ?? null),
            user: @json(['name' => $user->name ?? 'School Admin', 'email' => $user->email ?? '']),
            stats: @json($stats ?? [])
        };
        if(t){ localStorage.setItem('safestep_token', t); localStorage.setItem('token', t); }
    })();
    </script>
    <!-- Prevent dark-mode flash before body exists -->
    <script>
    (function(){
        var t = localStorage.getItem('safestep-theme');
        if(t === 'dark') document.documentElement.classList.add('dark-mode');
    })();
    </script>
    <script src="{{ asset('js/api-service.js') }}"></script>
    <script src="{{ asset('js/spa-navigation.js') }}"></script>
    <style>
        .topbar-school-meta { display: flex; flex-direction: column; gap: 2px; }
        .topbar-school-meta .school-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            letter-spacing: .02em;
        }
        body.dark-mode .topbar-school-meta .school-label { color: var(--text-muted); }
        .topbar-school-meta #pageTitle { margin: 0; }
        .sidebar .portal-tag {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,.55);
            margin-top: 2px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px; padding: 16px; min-height: 90px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                <i class="fas fa-shield-alt" style="font-size: 28px; background: linear-gradient(135deg, var(--primary-light), var(--primary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.3));"></i>
                <h2 style="font-size: 20px; font-weight: 700; letter-spacing: -0.5px; background: linear-gradient(135deg, #FFFFFF, #E2E8F0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0;">SAFESTEP BUS</h2>
            </div>
            <div style="margin-top: 6px; width: 100%;">
                <span class="portal-tag" style="display:block; font-size:11px; font-weight:600; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.06em;">School Portal</span>
                <span id="schoolNameBar" style="display:block; font-size:12px; font-weight:700; color:var(--success-color); margin-top:2px; text-transform:uppercase; letter-spacing:.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;" title="{{ $school->name ?? 'School' }}">{{ $school->name ?? 'School' }}</span>
            </div>
        </div>
        <nav class="nav-menu">
            <a href="#" class="nav-link active" data-page="dashboard"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <a href="#" class="nav-link" data-page="parents"><i class="fas fa-users"></i><span>Parents</span></a>
            <a href="#" class="nav-link" data-page="students"><i class="fas fa-graduation-cap"></i><span>Students</span></a>
            <a href="#" class="nav-link" data-page="buses"><i class="fas fa-bus"></i><span>Buses</span></a>
            <a href="#" class="nav-link" data-page="drivers"><i class="fas fa-id-card"></i><span>Drivers</span></a>
            <a href="#" class="nav-link" data-page="routes"><i class="fas fa-map-signs"></i><span>Routes</span></a>
            <a href="#" class="nav-link" data-page="trips"><i class="fas fa-route"></i><span>Trip Monitoring</span></a>
            <a href="#" class="nav-link" data-page="tracking"><i class="fas fa-map-marker-alt"></i><span>Live Tracking</span></a>
            <a href="#" class="nav-link" data-page="attendance"><i class="fas fa-clipboard-check"></i><span>Attendance</span></a>
            <a href="#" class="nav-link" data-page="notifications"><i class="fas fa-bell"></i><span>Communication</span></a>
            <a href="#" class="nav-link" data-page="emergency"><i class="fas fa-triangle-exclamation"></i><span>Emergency Center</span></a>
            <a href="#" class="nav-link" data-page="reports"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="#" class="nav-link" data-page="settings"><i class="fas fa-cog"></i><span>Settings</span></a>
            <a href="#" class="nav-link" data-page="activity-logs"><i class="fas fa-history"></i><span>Activity Logs</span></a>
            <a href="{{ route('logout') }}" class="nav-link logout"
               onclick="event.preventDefault(); localStorage.removeItem('safestep_token'); localStorage.removeItem('token'); window.location.href='{{ route('logout') }}'">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 id="pageTitle">School Dashboard</h1>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i><span>Dark</span>
                </button>
                <button class="theme-toggle lang-toggle" id="langToggle" type="button" aria-label="Switch language to Arabic">
                    <i class="fas fa-language"></i><span>AR</span>
                </button>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="schoolSearch" placeholder="Search students, parents, buses...">
                </div>
                <div class="notification-icon" onclick="SchoolAdmin.showNotifications(true)">
                    <i class="fas fa-bell"></i><span class="badge" id="notifBadge">0</span>
                </div>
                <div class="profile" style="cursor:pointer;" onclick="SchoolAdmin.navigate('settings')">
                    <img src="{{ asset('img/admin.png') }}" alt="School Admin">
                    <span id="adminName">{{ $user->name ?? 'School Admin' }}</span>
                </div>
            </div>
        </div>

        <div class="pages-container">
            <!-- Dashboard -->
            <div class="page active" id="dashboard">
                <div class="dashboard-grid">
                    <div class="card stat-card">
                        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-info">
                            <h3 id="statStudents">{{ $stats['students_count'] ?? 0 }}</h3>
                            <p>Total Students</p>
                            <span class="stat-trend up"><i class="fas fa-link"></i> Linked to parents</span>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                        <div class="stat-info">
                            <h3 id="statActiveStudents">{{ $stats['active_students'] ?? 0 }}</h3>
                            <p>Active Students</p>
                            <span class="stat-trend up"><i class="fas fa-check"></i> On fleet routes</span>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon orange"><i class="fas fa-user-tie"></i></div>
                        <div class="stat-info">
                            <h3 id="statDrivers">{{ $stats['drivers_count'] ?? 0 }}</h3>
                            <p>Total Drivers</p>
                            <span class="stat-trend up"><i class="fas fa-bus"></i> Assigned to buses</span>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon purple"><i class="fas fa-bus"></i></div>
                        <div class="stat-info">
                            <h3 id="statBuses">{{ $stats['buses_count'] ?? 0 }}</h3>
                            <p>Total Buses</p>
                            <span class="stat-trend up"><i class="fas fa-route"></i> Active routes</span>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3 id="statActiveTrips">{{ $stats['trips_active'] ?? 0 }}</h3>
                            <p>Active Trips</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon green"><i class="fas fa-clipboard-check"></i></div>
                        <div class="stat-info">
                            <h3 id="statTodayAttendance">0</h3>
                            <p>Today's Attendance</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon orange"><i class="fas fa-user-times"></i></div>
                        <div class="stat-info">
                            <h3 id="statTodayAbsence">0</h3>
                            <p>Today's Absence</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon purple"><i class="fas fa-triangle-exclamation"></i></div>
                        <div class="stat-info">
                            <h3 id="statEmergencies">{{ $stats['emergency_alerts'] ?? 0 }}</h3>
                            <p>Emergency Alerts</p>
                        </div>
                    </div>
                    <div class="card chart-card">
                        <div class="card-header"><h3>Student Attendance Trends</h3></div>
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                    <div class="card chart-card">
                        <div class="card-header"><h3>Weekly Trips Analysis</h3></div>
                        <canvas id="tripsChart"></canvas>
                    </div>
                    <div class="card chart-card">
                        <div class="card-header"><h3>Bus Usage Statistics</h3></div>
                        <canvas id="busUsageChart"></canvas>
                    </div>
                    <div class="card chart-card">
                        <div class="card-header"><h3>Monthly Safety Reports</h3></div>
                        <canvas id="safetyChart"></canvas>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3>School Performance KPIs</h3></div>
                        <div class="card-content" id="kpiPanel" style="padding:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;"></div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3>At-Risk Students</h3></div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead><tr><th>Student</th><th>Grade</th><th>Absent Rate</th><th>Risk</th></tr></thead>
                                <tbody id="riskStudentsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parents -->
            <div class="page" id="parents">
                <div class="card">
                    <div class="card-header"><h3>Parent Management</h3></div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Children</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody id="parentsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div class="page" id="students">
                <div class="card">
                    <div class="card-header">
                        <h3>Student Management</h3>
                        <div class="card-actions">
                            <input type="text" id="studentSearch" class="form-control" placeholder="Search students..." style="max-width:220px;">
                            <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.openStudentModal()"><i class="fas fa-plus"></i> Add Student</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Name</th><th>Grade</th><th>Parent</th><th>Bus</th><th>Route</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="studentsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Buses -->
            <div class="page" id="buses">
                <div class="card">
                    <div class="card-header">
                        <h3>Bus Management</h3>
                        <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.openBusModal()"><i class="fas fa-plus"></i> Add Bus</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Bus #</th><th>Plate</th><th>Capacity</th><th>Driver</th><th>Route</th><th>Insurance</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="busesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Drivers -->
            <div class="page" id="drivers">
                <div class="card">
                    <div class="card-header"><h3>Driver Management</h3></div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Name</th><th>License</th><th>Phone</th><th>Experience</th><th>Bus</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="driversTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Routes -->
            <div class="page" id="routes">
                <div class="card">
                    <div class="card-header">
                        <h3>Route Management</h3>
                        <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.openRouteModal()"><i class="fas fa-plus"></i> Create Route</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Name</th><th>Type</th><th>Stops</th><th>Duration</th><th>Distance</th><th>Bus</th><th>Driver</th><th>Students</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="routesTableBody"></tbody>
                        </table>
                    </div>
                    <div id="routeMap" style="height:360px;margin:20px;border-radius:12px;"></div>
                </div>
            </div>

            <!-- Trips -->
            <div class="page" id="trips">
                <div class="card">
                    <div class="card-header">
                        <h3>Trip Monitoring</h3>
                        <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.openTripModal()"><i class="fas fa-plus"></i> Schedule Trip</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Date</th><th>Shift</th><th>Route</th><th>Bus</th><th>Driver</th><th>Students</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="tripsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Live Tracking -->
            <div class="page" id="tracking">
                <div class="card">
                    <div class="card-header"><h3>Live Bus Tracking</h3></div>
                    <div id="liveTrackingMap" style="height:480px;border-radius:12px;"></div>
                    <div class="table-responsive" style="margin-top:16px;">
                        <table class="data-table">
                            <thead><tr><th>Bus</th><th>Driver</th><th>Speed</th><th>Status</th><th>Last Update</th></tr></thead>
                            <tbody id="trackingTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attendance -->
            <div class="page" id="attendance">
                <div class="card">
                    <div class="card-header">
                        <h3>Daily Attendance</h3>
                        <input type="date" id="attendanceDate" class="form-control" style="max-width:180px;">
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>Student</th><th>Bus</th><th>Route</th><th>Pickup</th><th>Drop-off</th><th>Status</th></tr></thead>
                            <tbody id="attendanceTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="page" id="notifications">
                <div class="dashboard-grid" style="margin-bottom:20px;">
                    <div class="card stat-card"><div class="stat-info"><h3 id="notifSent">0</h3><p>Sent</p></div></div>
                    <div class="card stat-card"><div class="stat-info"><h3 id="notifRead">0</h3><p>Read</p></div></div>
                    <div class="card stat-card"><div class="stat-info"><h3 id="notifUnread">0</h3><p>Unread</p></div></div>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Parent Communication</h3></div>
                    <div class="card-content" style="padding:20px;">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" id="notifTitle" class="form-control" placeholder="Announcement title">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea id="notifBody" class="form-control" rows="4" placeholder="Write your message..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select id="notifType" class="form-control">
                                <option value="announcement">School Announcement</option>
                                <option value="general">General</option>
                                <option value="delay">Delay Alert</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                        <button class="btn-primary" type="button" onclick="SchoolAdmin.sendBroadcast()"><i class="fas fa-paper-plane"></i> Broadcast to Parents</button>
                    </div>
                </div>
            </div>

            <!-- Emergency -->
            <div class="page" id="emergency">
                <div class="card">
                    <div class="card-header">
                        <h3>Emergency Center</h3>
                        <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.openEmergencyModal()"><i class="fas fa-plus"></i> Report Emergency</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>Type</th><th>Severity</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                            <tbody id="emergencyTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reports -->
            <div class="page" id="reports">
                <div class="card">
                    <div class="card-header">
                        <h3>Reports & Analytics</h3>
                        <div class="card-actions">
                            <select id="reportType" class="form-control" style="max-width:180px;">
                                <option value="summary">Summary</option>
                                <option value="students">Students</option>
                                <option value="drivers">Drivers</option>
                                <option value="buses">Buses</option>
                                <option value="attendance">Attendance</option>
                                <option value="safety">Safety</option>
                            </select>
                            <button class="btn-secondary btn-compact" type="button" onclick="SchoolAdmin.loadReport()"><i class="fas fa-sync-alt"></i> Load</button>
                            <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.exportReport('csv')"><i class="fas fa-file-csv"></i> Export CSV</button>
                            <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.exportReport('xlsx')"><i class="fas fa-file-excel"></i> Export Excel</button>
                            <button class="btn-primary btn-compact" type="button" onclick="SchoolAdmin.exportReport('pdf')"><i class="fas fa-file-pdf"></i> Export PDF</button>
                        </div>
                    </div>
                    <pre id="reportOutput" style="padding:20px;white-space:pre-wrap;font-size:13px;color:#94a3b8;"></pre>
                </div>
            </div>

            <!-- Settings -->
            <div class="page" id="settings">
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header"><h3>School Profile</h3></div>
                        <div class="card-content" style="padding:20px;">
                            <form id="schoolSettingsForm" onsubmit="SchoolAdmin.saveSchoolSettings(event)">
                                <div class="form-group"><label class="form-label">School Name</label><input type="text" id="schoolName" class="form-control"></div>
                                <div class="form-group"><label class="form-label">Principal</label><input type="text" id="schoolPrincipal" class="form-control"></div>
                                <div class="form-group"><label class="form-label">Email</label><input type="email" id="schoolEmail" class="form-control"></div>
                                <div class="form-group"><label class="form-label">Phone</label><input type="text" id="schoolPhone" class="form-control"></div>
                                <div class="form-group"><label class="form-label">Address</label><textarea id="schoolAddress" class="form-control" rows="2"></textarea></div>
                                <button class="btn-primary" type="submit"><i class="fas fa-save"></i> Save School Profile</button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3>Admin Profile</h3></div>
                        <div class="card-content" style="padding:20px;">
                            <form id="profileSettingsForm" onsubmit="SchoolAdmin.saveProfileSettings(event)">
                                <div class="form-group"><label class="form-label">Name</label><input type="text" id="profileName" class="form-control"></div>
                                <div class="form-group"><label class="form-label">Email</label><input type="email" id="profileEmail" class="form-control"></div>
                                <button class="btn-primary" type="submit"><i class="fas fa-save"></i> Save Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="page" id="activity-logs">
                <div class="card">
                    <div class="card-header"><h3>Activity Logs</h3></div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>Action</th><th>User</th><th>Entity</th><th>Date</th></tr></thead>
                            <tbody id="activityLogsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Modal -->
    <div class="modal" id="schoolModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="schoolModalTitle">Modal</h3>
                <button type="button" class="modal-close" onclick="SchoolAdmin.closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="schoolModalBody"></div>
            <div class="modal-footer" id="schoolModalFooter"></div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard-theme.js') }}"></script>
    <script src="{{ asset('js/i18n-school-admin.js') }}"></script>
    <script src="{{ asset('js/school-admin.js') }}"></script>
</body>
</html>
