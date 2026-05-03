<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Reports - SafeStep</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-bus"></i>
            <h2>Driver Portal</h2>
        </div>
        <nav class="nav-menu">
            <a href="{{ url('/driver') }}" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ url('/driver/report') }}" class="nav-link active">
                <i class="fas fa-file-lines"></i>
                <span>Reports</span>
            </a>
            <a href="{{ url('/driver/request') }}" class="nav-link external-link">
                <i class="fas fa-file-circle-plus"></i>
                <span>Requests</span>
            </a>
            <a href="{{ url('/logout') }}" class="nav-link logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 id="pageTitle">Reports</h1>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                    <span>Dark</span>
                </button>
                <button class="theme-toggle lang-toggle" id="langToggle" type="button" aria-label="Switch language to Arabic">
                    <i class="fas fa-language"></i>
                    <span>AR</span>
                </button>
                <div class="time-display">
                    <i class="fas fa-clock"></i>
                    <span id="currentTime"></span>
                </div>
            </div>
        </div>

        <div class="page active" id="reports">
            <div class="dashboard-grid">
                <div class="card stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="reportTripsCount">0</h2>
                        <p>Trips (This Week)</p>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="reportAttendanceRate">0%</h2>
                        <p>Attendance Rate</p>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="reportIncidentsCount">0</h2>
                        <p>Incidents</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-lines"></i> Trip Reports</h3>
                        <select class="form-control period-selector" id="driverReportFilter" style="max-width: 220px;">
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                            <option value="quarter">Last 90 Days</option>
                        </select>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table" id="driverReportsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Route</th>
                                    <th>Trip</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody id="driverReportsBody">
                                <!-- Populated by backend later -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card notes-card">
                    <h3><i class="fas fa-pen"></i> Create Quick Report</h3>
                    <textarea id="driverQuickReport" placeholder="Write a short report (incident, delay, maintenance note)..." style="min-height: 140px;"></textarea>
                    <button class="btn-primary" type="button" onclick="alert('Report submission will be connected to backend soon.')">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/driver.js') }}"></script>
    <script src="{{ asset('js/i18n-driver.js') }}"></script>
</body>
</html>

