<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Reports - SafeStep</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <a href="#reports" class="sr-only skip-link">Skip to content</a>

    <aside class="sidebar" role="navigation" aria-label="Main navigation">
        <div class="logo">
            <i class="fas fa-bus"></i>
            <h2>BusTracker</h2>
        </div>
        <nav id="mainNav" class="nav-menu" aria-label="Primary navigation">
            <a href="{{ url('/parent') }}" class="nav-link" role="button" aria-pressed="false">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ url('/parent/report') }}" class="nav-link active" role="button" aria-pressed="true" aria-current="page">
                <i class="fas fa-file-lines"></i>
                <span>Reports</span>
            </a>
            <a href="{{ url('/parent/request') }}" class="nav-link external-link" role="button" aria-pressed="false">
                <i class="fas fa-file-lines"></i>
                <span>Parent Requests</span>
            </a>
            <a href="{{ url('/logout') }}" class="nav-link logout" role="button" aria-pressed="false">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <div class="main-content" role="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav" aria-label="Toggle menu">
                    <i class="fas fa-bars" aria-hidden="true"></i>
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
            </div>
        </div>

        <div class="page active" id="reports">
            <div class="dashboard-grid">
                <div class="card stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-child"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="parentReportChildren">0</h3>
                        <p>Children</p>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="parentReportAttendance">0%</h3>
                        <p>Attendance (This Month)</p>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="parentReportAlerts">0</h3>
                        <p>Alerts</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-lines"></i> Attendance Summary</h3>
                        <div class="card-actions">
                            <button class="btn-secondary btn-compact" type="button" onclick="alert('Export will be connected soon.')">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button class="btn-primary btn-compact" type="button" onclick="alert('Export will be connected soon.')">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table" id="parentReportsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Child</th>
                                    <th>Pickup</th>
                                    <th>Drop-off</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="parentReportsBody">
                                <!-- Populated by backend later -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-message"></i> Report an Issue</h3>
                    </div>
                    <div class="emergency-form">
                        <div class="form-group">
                            <label for="issueType">Issue Type</label>
                            <select id="issueType" class="form-control" required>
                                <option value="">Select type</option>
                                <option value="late">Late pickup/drop</option>
                                <option value="safety">Safety concern</option>
                                <option value="behavior">Behavior</option>
                                <option value="payment">Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="issueNote">Short Note</label>
                            <input id="issueNote" class="form-control" type="text" placeholder="Describe the issue" required>
                        </div>
                        <button class="btn-primary" type="button" onclick="alert('Issue submission will be connected to backend soon.')">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/i18n-parent.js') }}"></script>
</body>
</html>

