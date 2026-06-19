<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Parent Dashboard - School Bus Tracking</title>
    <link rel="icon" href="{{ asset('img/icon.jpg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
        defer
    ></script>
    <style>
        :root{
            --bg:#f0f4ff;--card:#ffffff;--muted:#64748b;--accent:#2563eb;--focus:#1d4ed8
        }
        body{background:var(--bg);font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial}
        .sr-only{position:absolute!important;height:1px;width:1px;overflow:hidden;clip:rect(1px,1px,1px,1px);white-space:nowrap;border:0;padding:0;margin:-1px}
        .card{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 6px 18px rgba(16,24,40,0.06);transition:transform .18s ease,box-shadow .18s ease}
        .card:focus-within,.card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(16,24,40,0.08)}
        a.nav-link:focus { outline: none; }      
        a.nav-link:focus-visible,button:focus-visible{outline:3px solid rgba(37,99,235,0.18);outline-offset:3px;border-radius:6px}
        .sidebar.collapsed{width:72px}
        .site-footer{margin:28px 16px;padding:12px 16px;background:transparent;color:var(--muted);font-size:13px;border-top:1px solid rgba(15,23,42,0.04);display:flex;justify-content:space-between;align-items:center}
        .small-muted{color:var(--muted);font-size:13px}
        /* Map overlay + markers */
        .map-container{position:relative;border-radius:12px;overflow:hidden}
        .map-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #e8eff5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
            pointer-events: none;
            transition: opacity 0.4s ease;
            border-radius: 12px;
            color: var(--muted);
        }
        .map-placeholder i {
            font-size: 54px;
            margin-bottom: 12px;
            color: var(--accent);
            opacity: 0.7;
        }
        .map-placeholder p {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
        }
        .map-overlay{position:absolute;right:18px;top:18px;background:rgba(255,255,255,0.96);padding:10px;border-radius:10px;box-shadow:0 6px 20px rgba(16,24,40,.06);min-width:180px;z-index:600}
        .map-overlay .overlay-row{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:6px}
        .map-overlay button{font-size:13px;padding:6px 10px;border-radius:8px}
        .bus-marker i,.child-marker i{font-size:16px;color:#fff;padding:6px;border-radius:50%;display:inline-block}
        .bus-marker{background:#1d4ed8}
        .child-marker{background:#2563eb}
        .map-overlay .progress{height:8px;background:rgba(15,23,42,0.06);border-radius:8px;overflow:hidden;margin-top:6px}
        .map-overlay .progress > i{display:block;height:100%;background:linear-gradient(90deg,var(--accent),var(--focus));width:0%;transition:width .3s ease}
        .map-overlay .meta{font-size:12px;color:var(--muted);display:block;margin-top:6px}
        .overlay-toggle{display:inline-flex;align-items:center;gap:8px}
        .overlay-toggle input{width:16px;height:16px}

        /* RTL Support */
        html[dir="rtl"] body {
            font-family: 'Inter', 'Noto Sans Arabic', sans-serif;
        }
        html[dir="rtl"] .sidebar {
            left: auto;
            right: 0;
            border-right: none;
            border-left: 1px solid rgba(15, 23, 42, 0.04);
        }
        html[dir="rtl"] .main-content {
            margin-left: 0;
            margin-right: 280px; /* sidebar width */
        }
        @media (max-width: 768px) {
            html[dir="rtl"] .main-content {
                margin-right: 0;
            }
            html[dir="rtl"] .sidebar {
                transform: translateX(100%);
            }
            html[dir="rtl"] .sidebar.active {
                transform: translateX(0);
            }
        }
        html[dir="rtl"] .topbar {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .topbar-left, html[dir="rtl"] .topbar-right {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .stat-card {
            flex-direction: row-reverse;
            text-align: right;
        }
        html[dir="rtl"] .card-header {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .activity-item {
            flex-direction: row-reverse;
            text-align: right;
        }
        html[dir="rtl"] .profile {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .nav-link {
            text-align: right;
        }
    </style>
    {{-- Seed the Sanctum API token into localStorage before any JS loads --}}
    <script>
    (function(){
        var t = '{{ $apiToken ?? '' }}';
        window.__API_TOKEN = t;
        window.__INITIAL_PAGE = 'dashboard';
        window.__ONBOARDING_POLL = {
            appStatus: @json($appStatus),
            endpoint: '/api/parent/profile-status',
            isDashboardUnlocked: @json($isDashboardUnlocked ?? false),
        };
        if(t){ localStorage.setItem('safestep_token', t); localStorage.setItem('token', t); }
    })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/dashboard-lock.css') }}">
    <script src="{{ asset('js/api-service.js') }}"></script>
    <script src="{{ asset('js/spa-navigation.js') }}"></script>
    <script src="{{ asset('js/dashboard-lock.js') }}"></script>
    <script src="{{ asset('js/dashboard-mobile.js') }}"></script>
    <script src="{{ asset('js/onboarding-poll.js') }}"></script>
</head>
<body class="dashboard-body">
    <a href="#dashboard" class="sr-only skip-link">Skip to content</a>
    <!-- Sidebar -->
    <aside class="sidebar" role="navigation" aria-label="Main navigation">
        <div class="logo" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px; padding: 16px; min-height: 90px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                <i class="fas fa-shield-alt" style="font-size: 28px; background: linear-gradient(135deg, var(--primary-light), var(--primary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.3));"></i>
                <h2 style="font-size: 20px; font-weight: 700; letter-spacing: -0.5px; background: linear-gradient(135deg, #FFFFFF, #E2E8F0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0;">SAFESTEP BUS</h2>
            </div>
            <div style="margin-top: 6px; width: 100%;">
                <span class="portal-tag" style="display:block; font-size:11px; font-weight:600; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.06em;">Parent Portal</span>
            </div>
        </div>
        <nav id="mainNav" class="nav-menu" aria-label="Primary navigation">
            <a href="#" class="nav-link active" data-page="dashboard" role="button" aria-pressed="true" aria-current="page">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-link" data-page="tracking" role="button" aria-pressed="false">
                <i class="fas fa-map-marker-alt"></i>
                <span>Live Tracking</span>
            </a>
            <a href="#" class="nav-link" data-page="children" role="button" aria-pressed="false">
                <i class="fas fa-child"></i>
                <span>Children</span>
            </a>
            <a href="#" class="nav-link" data-page="child-qr" role="button" aria-pressed="false">
                <i class="fas fa-qrcode"></i>
                <span>Student QR</span>
            </a>
            <a href="#" class="nav-link" data-page="attendance" role="button" aria-pressed="false">
                <i class="fas fa-clipboard-check"></i>
                <span>Attendance</span>
            </a>
            <a href="#" class="nav-link" data-page="notifications" role="button" aria-pressed="false">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="#" class="nav-link" data-page="payments" role="button" aria-pressed="false">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
            <a href="#" class="nav-link" data-page="support" role="button" aria-pressed="false">
                <i class="fas fa-headset"></i>
                <span>Support</span>
            </a>
            <a href="#" class="nav-link" data-page="trip-history" role="button" aria-pressed="false">
                <i class="fas fa-history"></i>
                <span>Trip History</span>
            </a>
            <a href="#" class="nav-link" data-page="emergency-alerts" role="button" aria-pressed="false">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Emergency Alerts</span>
            </a>
            <a href="#" class="nav-link" data-page="profile-settings" role="button" aria-pressed="false">
                <i class="fas fa-user-cog"></i>
                <span>Profile & Settings</span>
            </a>
            <a href="/parent/request" class="nav-link external-link" data-page="requests">
                <i class="fas fa-file-alt"></i>
                <span>Parent Requests</span>
            </a>
            <a href="/parent/applications" class="nav-link external-link" data-page="my-applications">
                <i class="fas fa-folder-open"></i>
                <span>My Applications</span>
            </a>
            <a href="{{ route('logout') }}" class="nav-link logout"
               onclick="event.preventDefault(); localStorage.removeItem('safestep_token'); localStorage.removeItem('token'); window.location.href='{{ route('logout') }}'">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content" role="main">
        <!-- Top Navbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav" aria-label="Toggle menu">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
                <h1 id="pageTitle">Dashboard</h1>
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
                <button class="notification-icon" type="button" aria-label="Open notifications">
                    <i class="fas fa-bell" aria-hidden="true"></i>
                    <span class="badge">3</span>
                </button>
                <a class="profile" href="#" onclick="event.preventDefault(); openParentProfileModal();" aria-label="Open profile">
                    <img src="{{ asset('img/admin.png') }}" alt="Parent Profile" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName ?? 'Parent') }}&background=0ea5a4&color=fff'">
                    <span>{{ $userName ?? 'Parent' }}</span>
                </a>
            </div>
        </div>

        <!-- Dashboard Page -->
        <div class="page active" id="dashboard">
            <div class="dashboard-grid">
                
                @if($appStatus === 'pending')
                <!-- State A: Pending Approval -->
                <div class="card status-pending-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; margin: 20px 0; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(245, 158, 11, 0.08); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px; animation: pulse 2s infinite;">
                        <i class="fas fa-clock" style="font-size: 44px; color: #f59e0b;"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 14px;">Your application is under review</h2>
                    <p style="font-size: 16px; color: var(--text-light); max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">Welcome to SafeStep. Your account is registered but has not been approved yet. Your account will be activated and you will be notified upon approval.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-light); background: var(--light-bg); padding: 8px 16px; border-radius: 30px; border: 1px solid var(--border-color);">
                        <span>Application Status:</span>
                        <span class="status-badge pending" style="padding: 4px 12px; border-radius: 12px; font-weight: 700;">Pending</span>
                    </div>
                </div>

                @elseif($appStatus === 'rejected')
                <!-- State B: Rejected Application -->
                <div class="card status-rejected-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; margin: 20px 0; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(239, 68, 68, 0.08); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <i class="fas fa-times-circle" style="font-size: 48px; color: #ef4444;"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 14px;">Application Rejected</h2>
                    <p style="font-size: 16px; color: var(--text-light); max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">We apologize, your application to join SafeStep has been rejected by the administration. Please review your details or contact support.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-light); background: var(--light-bg); padding: 8px 16px; border-radius: 30px; border: 1px solid var(--border-color);">
                        <span>Application Status:</span>
                        <span class="status-badge rejected" style="padding: 4px 12px; border-radius: 12px; font-weight: 700; background: rgba(239, 68, 68, 0.08); color: var(--danger-color);">Rejected</span>
                    </div>
                </div>

                @elseif($appStatus === 'pending_details')
                <!-- State C: Onboarding Form (Needs Children Details) -->
                <div class="card" id="childrenOnboardingCard" style="grid-column:1/-1; border-radius: 20px; padding: 28px; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-user-plus" style="font-size: 20px; color: var(--accent);"></i>
                            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--text-dark);">Register Children Details</h3>
                        </div>
                        <span style="font-size:12px;color:var(--text-light);background: var(--light-bg);padding: 4px 12px;border-radius: 12px;font-weight: 500;">Required step to activate account</span>
                    </div>
                    <form id="childrenOnboardingForm" style="display:flex;flex-direction:column;gap:16px;">
                        @csrf
                        <div id="childrenOnboardingMessage" style="display:none;padding:10px 12px;border-radius:8px;font-size:13px;"></div>
                        @for($i = 0; $i < $childFormCount; $i++)
                        <div style="border:1px solid var(--border-color);border-radius:14px;padding:20px;background:var(--light-bg);transition: all 0.3s ease;">
                            <h4 style="margin:0 0 16px;color:var(--text-dark);font-size:15px;font-weight: 700;display: flex;align-items: center;gap:8px;">
                                <i class="fas fa-child" style="color: var(--accent);"></i> Child {{ $i + 1 }}
                            </h4>
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Full Name</label>
                                    <input name="children[{{ $i }}][full_name]" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Age</label>
                                    <input name="children[{{ $i }}][age]" type="number" min="2" max="25" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Grade</label>
                                    <input name="children[{{ $i }}][grade]" type="text" value="{{ $acceptedApplication?->metadata['student_degree'] ?? '' }}" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">School</label>
                                    <input name="children[{{ $i }}][school_name]" type="text" value="{{ $acceptedApplication?->metadata['school_name'] ?? '' }}" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Pickup Location (From)</label>
                                    <input name="children[{{ $i }}][pickup_location]" type="text" value="{{ $acceptedApplication?->address ?? '' }}" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Drop-off Location (To)</label>
                                    <input name="children[{{ $i }}][dropoff_location]" type="text" value="{{ $acceptedApplication?->metadata['school_address'] ?? $acceptedApplication?->metadata['school_name'] ?? '' }}" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Pickup Time</label>
                                    <input name="children[{{ $i }}][pickup_time]" type="time" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Drop-off Time</label>
                                    <input name="children[{{ $i }}][dropoff_time]" type="time" class="form-control" style="font-size: 13px;">
                                </div>
                            </div>
                            <label style="display:inline-flex;align-items:center;gap:8px;margin-top:16px;color:var(--text-dark);font-size:13px;font-weight: 600;cursor: pointer;">
                                <input type="checkbox" name="children[{{ $i }}][has_medical_condition]" value="1" data-medical-toggle="{{ $i }}" style="width: 16px; height: 16px;">
                                Has any medical condition or health issue
                            </label>
                            <div data-medical-fields="{{ $i }}" style="display:none;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:12px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Medical condition details</label>
                                    <textarea name="children[{{ $i }}][medical_condition]" rows="3" class="form-control" placeholder="Write details here..." style="font-size: 13px;"></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Medication / dosage details</label>
                                    <textarea name="children[{{ $i }}][medication]" rows="3" class="form-control" placeholder="Write medication details..." style="font-size: 13px;"></textarea>
                                </div>
                            </div>
                        </div>
                        @endfor
                        <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                            <button class="btn-primary" id="childrenOnboardingSubmit" type="submit" style="padding: 12px 24px; font-weight: 700; border-radius: 10px;">
                                <i class="fas fa-paper-plane"></i> Send to Admin
                            </button>
                        </div>
                    </form>
                </div>

                @elseif($appStatus === 'pending_approval')
                <!-- State D: Children submitted, waiting for Admin approval -->
                <div class="card status-pending-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; margin: 20px 0; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(37,99,235,0.08); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <i class="fas fa-user-clock" style="font-size: 44px; color: var(--accent);"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 14px;">Children Details Submitted</h2>
                    <p style="font-size: 16px; color: var(--text-light); max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">Thank you for registering your children details. The administration is currently reviewing your profile. Once approved, your dashboard will open automatically and all sections will become available.</p>
                    <p style="font-size: 14px; color: var(--text-muted); max-width: 520px; margin: 0 auto 20px; line-height: 1.6;">بعد موافقة الإدارة سيتم فتح الداشبورد تلقائياً وتفعيل جميع الأقسام.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-light); background: var(--light-bg); padding: 8px 16px; border-radius: 30px; border: 1px solid var(--border-color);">
                        <span>Status:</span>
                        <span class="status-badge pending" style="padding: 4px 12px; border-radius: 12px; font-weight: 700;">Pending Approval</span>
                    </div>
                </div>

                @if($children->count())
                <div class="card" style="grid-column: 1/-1; padding: 28px; border: 1px solid var(--border-color); background: var(--card-bg); margin-top: 16px;">
                    <h4 style="margin: 0 0 16px; color: var(--text-dark); font-size: 15px; font-weight: 700;">Submitted Children</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        @foreach($children as $child)
                        <div style="border: 1px solid var(--border-color); border-radius: 14px; padding: 16px; background: var(--light-bg);">
                            <strong style="color: var(--text-dark);">{{ $child->full_name }}</strong>
                            <div style="color: var(--text-light); font-size: 13px; margin-top: 6px;">Grade {{ $child->grade ?? '—' }} &bull; {{ $child->school_name ?? '—' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @elseif($appStatus === 'approved' && $assignedChildrenCount === 0)
                <!-- State E: Approved but waiting for Bus/Driver Assignment -->
                <div class="card" style="grid-column: 1/-1; padding: 40px; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; flex-wrap: wrap;">
                        <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(37,99,235,0.08); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 26px;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 6px; font-size: 20px; font-weight: 800; color: var(--text-dark);">Route and Bus assignment pending</h3>
                            <span style="font-size: 14px; color: var(--text-light); line-height: 1.5;">Children details submitted successfully. The school admin is assigning the proper bus and driver, we will activate live tracking as soon as it's completed.</span>
                        </div>
                    </div>
                    
                    <h4 style="margin: 0 0 16px; color: var(--text-dark); font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-children" style="color: var(--text-light);"></i> Registered Children in the System:
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
                        @foreach($children as $child)
                        <div style="border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; background: var(--light-bg); display: flex; flex-direction: column; gap: 12px; transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0ea5a4, #2563eb); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px;">{{ substr($child->full_name, 0, 2) }}</div>
                                <div>
                                    <strong style="color: var(--text-dark); font-size: 15px; font-weight: 700;">{{ $child->full_name }}</strong>
                                    <div style="color: var(--text-light); font-size: 12px; font-weight: 500;">Age: {{ $child->age ?? '—' }} years &bull; Grade: {{ $child->grade ?? '—' }}</div>
                                </div>
                            </div>
                            <div style="font-size: 13px; color: var(--text-light); display: flex; flex-direction: column; gap: 6px; border-top: 1px dashed var(--border-color); padding-top: 12px;">
                                <div><i class="fas fa-school" style="width: 20px; color: var(--text-muted); text-align: center;"></i> <strong>School:</strong> {{ $child->school_name ?? '—' }}</div>
                                <div><i class="fas fa-map-marker-alt" style="width: 20px; color: var(--text-muted); text-align: center;"></i> <strong>From:</strong> {{ $child->pickup_location ?? '—' }}</div>
                                <div><i class="fas fa-map-marker-alt" style="width: 20px; color: var(--text-muted); text-align: center;"></i> <strong>To:</strong> {{ $child->dropoff_location ?? '—' }}</div>
                                <div><i class="fas fa-clock" style="width: 20px; color: var(--text-muted); text-align: center;"></i> <strong>Suggested Time:</strong> {{ $child->pickup_time ? $child->pickup_time : '—' }} / {{ $child->dropoff_time ? $child->dropoff_time : '—' }}</div>
                                @if($child->has_medical_condition)
                                <div style="color: #b91c1c; background: rgba(239, 68, 68, 0.05); padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.1); margin-top: 6px;">
                                    <i class="fas fa-prescription-bottle-medical" style="margin-right: 4px;"></i> <strong>Special Health Condition:</strong> {{ $child->medical_condition }} <br>
                                    <strong>Medication:</strong> {{ $child->medication ?? 'None' }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @else
                <!-- State E: Active Dashboard (Children Assigned to Bus/Driver) -->
                <!-- Bus Status Card -->
                <div class="card bus-status-card">
                    <div class="card-header">
                        <h3>Bus Status</h3>
                        <i class="fas fa-bus-alt"></i>
                    </div>
                    <div class="status-content">
                        <div class="status-badge on-the-way">
                            <i class="fas fa-circle"></i>
                            <span>On the Way</span>
                        </div>
                        <div class="eta">
                            <h2 id="etaTime">8 mins</h2>
                            <p>Estimated Arrival Time</p>
                        </div>
                        <div class="distance">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="distanceText">2.3 km away</span>
                        </div>
                    </div>
                </div>

                <!-- Driver Info Card -->
                <div class="card driver-info-card">
                    <div class="card-header">
                        <h3>Driver Information</h3>
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="driver-content">
                        <div class="driver-avatar">
                            <img src="https://source.unsplash.com/200x200/?egyptian,driver,portrait&sig=12" alt="Driver" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($latestTrip?->driver?->user?->name ?? $assignedDriver?->user?->name ?? $assignedDriver?->full_name ?? 'Driver') }}&background=764ba2&color=fff'">
                        </div>
                        <div class="driver-details">
                            <h4>{{ $latestTrip?->driver?->user?->name ?? $assignedDriver?->user?->name ?? $assignedDriver?->full_name ?? 'No Driver Assigned' }}</h4>
                            <p><i class="fas fa-phone"></i> {{ $latestTrip?->driver?->phone ?? $assignedDriver?->phone ?? '—' }}</p>
                            <p><i class="fas fa-bus"></i> {{ ($latestTrip?->bus?->bus_number ?? $assignedBus?->bus_number) ? 'Bus #'.($latestTrip?->bus?->bus_number ?? $assignedBus?->bus_number) : '—' }}</p>
                            <p><i class="fas fa-route"></i> {{ $latestTrip?->route?->name ?? $assignedRoute?->name ?? '—' }}</p>
                        </div>
                        @php
                            $driverPhone = $latestTrip?->driver?->phone ?? $assignedDriver?->phone;
                        @endphp
                        @if($driverPhone)
                        <button class="btn-call" type="button" onclick="window.location.href='tel:{{ $driverPhone }}'">
                            <i class="fas fa-phone-alt"></i> Call Driver
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="stats-container">
                    <div class="card stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-children"></i>
                        </div>
                        <div class="stat-details">
                            <h3>{{ $stats['children_count'] ?? 0 }}</h3>
                            <p>Total Children</p>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-details">
                            <h3>{{ ($stats['children_count'] ?? 0) > 0 ? round((($stats['attendance_present'] ?? 0) / (($stats['attendance_present'] ?? 0) + ($stats['attendance_absent'] ?? 0) + 0.001)) * 100) : 0 }}%</h3>
                            <p>Today Attendance</p>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="stat-details">
                            <h3 id="busSpeed">{{ $latestTrip?->bus?->current_speed ?? $assignedBus?->current_speed ?? 0 }}</h3>
                            <p>Bus Speed (km/h)</p>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-route"></i>
                        </div>
                        <div class="stat-details">
                            <h3>{{ $latestTrip?->route?->distance_km ?? $assignedRoute?->distance_km ?? '0' }} km</h3>
                            <p>Total Distance</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card activity-card">
                    <div class="card-header">
                        <h3>Recent Activity</h3>
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="activity-list">
                        @forelse($attendanceRecords as $record)
                        <div class="activity-item">
                            @if($record->status === 'present' || $record->status === 'picked_up' || $record->status === 'dropped_off')
                            <div class="activity-icon green">
                                <i class="fas fa-check"></i>
                            </div>
                            @elseif($record->status === 'absent')
                            <div class="activity-icon orange">
                                <i class="fas fa-user-times"></i>
                            </div>
                            @else
                            <div class="activity-icon blue">
                                <i class="fas fa-info"></i>
                            </div>
                            @endif
                            <div class="activity-content">
                                <p>
                                    <strong>{{ $record->student?->full_name }}</strong> 
                                    @if($record->status === 'picked_up')
                                        picked up
                                    @elseif($record->status === 'dropped_off')
                                        dropped off
                                    @elseif($record->status === 'present')
                                        marked present
                                    @elseif($record->status === 'absent')
                                        marked absent
                                    @else
                                        status: {{ $record->status }}
                                    @endif
                                </p>
                                <span>
                                    @if($record->picked_up_at && ($record->status === 'picked_up' || $record->status === 'present'))
                                        {{ $record->picked_up_at->format('Y-m-d h:i A') }}
                                    @elseif($record->dropped_off_at && $record->status === 'dropped_off')
                                        {{ $record->dropped_off_at->format('Y-m-d h:i A') }}
                                    @else
                                        {{ $record->created_at->format('Y-m-d h:i A') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @empty
                        <div style="padding: 16px; text-align: center; color: var(--text-light);">
                            No recent activity found.
                        </div>
                        @endforelse
                    </div>
                </div>
                @endif

            </div>
        </div>
        

        <!-- Tracking Page -->
        <div class="page" id="tracking">
            <div class="tracking-container">
                <div class="card tracking-card">
                    <div class="tracking-header">
                        <div class="tracking-status">
                            <h3>Live Tracking</h3>
                            <div class="status-badge on-the-way">
                                <i class="fas fa-circle pulse"></i>
                                <span>Bus is on the way</span>
                            </div>
                        </div>
                        <div class="tracking-info">
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>ETA: <strong id="trackingEta">8 mins</strong></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Speed: <strong id="trackingSpeed">45 km/h</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="map-container" id="map">
                        <div class="map-placeholder">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>Map Loading...</p>
                        </div>
                        <div id="gpsMap" style="width: 100%; height: 100%; border-radius: 12px; overflow: hidden;"></div>
                        <canvas id="mapCanvas" style="position:absolute; inset:0; pointer-events:none;"></canvas>
                        <div class="map-overlay" id="mapOverlay" aria-hidden="false" role="region" aria-label="Tracking controls">
                            <div class="overlay-row">
                                <div>
                                    <strong id="childNameOverlay">Farida Mohamed</strong>
                                    <div id="childLastSeen" class="small-muted">Last seen: --</div>
                                </div>
                                <div class="overlay-toggle" title="Auto-follow bus">
                                    <label for="autoFollow">Follow</label>
                                    <input id="autoFollow" type="checkbox" checked aria-label="Toggle auto-follow bus">
                                </div>
                            </div>
                            <div class="overlay-row">
                                <button id="locateChildBtn" class="btn-primary" type="button">Locate Child</button>
                                <button id="followBusBtn" class="btn-secondary" type="button">Center Bus</button>
                            </div>
                            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="arrivalProgress"><i></i></div>
                            <div class="meta"><span id="etaShort">ETA: --</span> &bull; <button id="shareChildBtn" class="btn-secondary" type="button">Share</button></div>
                        </div>
                    </div>
                    <div class="tracking-footer">
                        <button class="btn-secondary" type="button">
                            <i class="fas fa-phone" aria-hidden="true"></i> Contact Driver
                        </button>
                        <button class="btn-primary" type="button">
                            <i class="fas fa-share-alt" aria-hidden="true"></i> Share Location
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Children Page -->
        <div class="page" id="children">
            <div class="children-container">
                <div id="childrenSectionsContainer">
                    <!-- Generated by JS -->
                </div>
            </div>
        </div>

        <!-- Student QR Page (linked to admin Activity Logs QR generation) -->
        <div class="page" id="child-qr">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-qrcode"></i> Student QR Codes</h3>
                    <button type="button" class="btn-secondary" onclick="loadParentQrCodes()">
                        <i class="fas fa-rotate"></i> Refresh
                    </button>
                </div>
                <div style="padding: 16px 20px 0; color: var(--muted); font-size: 14px;">
                    QR codes appear here automatically when the admin generates them from Activity Logs.
                </div>
                <div id="parentQrGrid" style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
                    <div class="small-muted">Loading QR codes...</div>
                </div>
            </div>
        </div>

        <!-- Notifications Page -->
        <div class="page" id="notifications">
            <div class="notifications-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Notifications</h3>
                        <button class="btn-primary" id="addNotificationBtn">
                            <i class="fas fa-plus"></i> Test Notification
                        </button>
                    </div>
                    <div class="notifications-list" id="notificationsList">
                        <!-- Notifications will be added here by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Page -->
        <div class="page" id="attendance">
            <div class="card">
                <div class="card-header">
                    <h3>Attendance Records</h3>
                </div>
                <div class="attendance-records">
                    <div class="attendance-summary-cards">
                        <div class="card stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ ($stats['children_count'] ?? 0) > 0 ? round((($stats['attendance_present'] ?? 0) / (($stats['attendance_present'] ?? 0) + ($stats['attendance_absent'] ?? 0) + 0.001)) * 100) : 0 }}%</h3>
                                <p>Overall Attendance</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $stats['attendance_present'] ?? 0 }}</h3>
                                <p>Days Present</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $stats['attendance_absent'] ?? 0 }}</h3>
                                <p>Days Absent</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div class="stat-icon purple">
                                <i class="fas fa-person-circle-xmark"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="missedPickupCount">{{ $stats['attendance_absent'] ?? 0 }}</h3>
                                <p>Missed Pickups</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-person-walking-arrow-right"></i>
                            </div>
                            <div class="stat-info">
                                <h3 id="missedDropoffCount">0</h3>
                                <p>Missed Drop-offs</p>
                            </div>
                        </div>
                    </div>
                    <div class="attendance-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Child</th>
                                    <th>Status</th>
                                    <th>Pickup Time</th>
                                    <th>Drop Time</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Generated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Page -->
        <div class="page" id="payments">
            <div class="card">
                <div class="card-header">
                    <h3>Payment History</h3>
                    <button class="btn-primary" id="makePaymentBtn">
                        <i class="fas fa-plus"></i> Make Payment
                    </button>
                </div>
                <div class="payment-summary">
                    <div class="card stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-info">
                            <h3>EGP 1,200</h3>
                            <p>Total Paid</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>EGP 300</h3>
                            <p>Pending Payment</p>
                        </div>
                    </div>
                </div>
                <div class="card discount-panel" id="paymentDiscountsPanel">
                    <div class="card-header">
                        <h3><i class="fas fa-percent"></i> Usage Discounts</h3>
                    </div>
                    <div class="discount-list" id="paymentDiscountsList">
                        <!-- Generated by JS -->
                    </div>
                </div>
                <div class="card packages-panel" id="paymentPackagesPanel">
                    <div class="card-header">
                        <h3><i class="fas fa-layer-group"></i> Packages & Features</h3>
                    </div>
                    <div id="packagesFeaturesContainer">
                        <!-- Generated by JS from price.html -->
                    </div>
                </div>
                <div class="card family-offers-panel" id="familyOffersPanel">
                    <div class="card-header">
                        <h3><i class="fas fa-gift"></i> Family Offers</h3>
                    </div>
                    <ul class="family-offers-list" id="familyOffersList">
                        <!-- Generated by JS -->
                    </ul>
                </div>
                <div class="card family-calc-panel" id="familyCalcPanel">
                    <div class="card-header">
                        <h3><i class="fas fa-calculator"></i> Family Savings Calculator</h3>
                    </div>
                    <div class="family-calc-grid">
                        <div class="family-calc-result">
                            <h4>Estimated Monthly Savings</h4>
                            <p id="familySavingValue">0 EGP</p>
                            <div class="family-calc-lines">
                                <div><span>Current Monthly Cost</span><strong id="familyCurrentTotal">0 EGP</strong></div>
                                <div><span>School Monthly Cost</span><strong id="familySchoolTotal">0 EGP</strong></div>
                            </div>
                        </div>
                        <div class="family-calc-controls">
                            <div class="form-group">
                                <label for="calcChildrenCount">Number of Children</label>
                                <input id="calcChildrenCount" class="form-control" type="number" min="1" max="6" value="1">
                            </div>
                            <div class="form-group">
                                <label for="calcCurrentCost">Current Cost per Child (Monthly)</label>
                                <input id="calcCurrentCost" class="form-control" type="number" min="0" value="900">
                            </div>
                            <div class="form-group">
                                <label for="calcPlanPrice">Plan Price per Child</label>
                                <select id="calcPlanPrice" class="form-control">
                                    <option value="450">Smart Plan - 450 EGP</option>
                                    <option value="650">Premium Plan - 650 EGP</option>
                                    <option value="950">Private VIP - 950 EGP</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="calcPaymentMode">Payment Mode</label>
                                <select id="calcPaymentMode" class="form-control">
                                    <option value="monthly">Monthly (0%)</option>
                                    <option value="quarterly">Quarterly (10%)</option>
                                    <option value="yearly">Yearly (25%)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsTableBody">
                            <!-- Generated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Support Page -->
        <div class="page" id="support">
            <div class="card">
                <div class="card-header">
                    <h3>Support Center</h3>
                </div>
                <div class="support-options">
                    <div class="card support-card">
                        <i class="fas fa-headset"></i>
                        <h4>Contact Support</h4>
                        <p>Get help from our support team</p>
                        <button class="btn-primary">
                            <i class="fas fa-phone"></i> Call Now
                        </button>
                    </div>
                    <div class="card support-card">
                        <i class="fas fa-comments"></i>
                        <h4>Live Chat</h4>
                        <p>Chat with our support team</p>
                        <button class="btn-primary">
                            <i class="fas fa-comment-dots"></i> Start Chat
                        </button>
                    </div>
                    <div class="card support-card">
                        <i class="fas fa-envelope"></i>
                        <h4>Email Support</h4>
                        <p>Send us an email</p>
                        <button class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Email
                        </button>
                    </div>
                </div>
                <div class="faq-section">
                    <h3>Frequently Asked Questions</h3>
                    <div class="faq-list">
                        <!-- FAQ items -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Trip History Page -->
        <div class="page" id="trip-history">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Trip History</h3>
                    <div class="card-actions">
                        <button class="btn-secondary btn-compact" onclick="exportTableToCsv('parentTripHistoryTable', 'trip_history')">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn-primary btn-compact" onclick="exportTableToPdf('parentTripHistoryTable', 'trip_history')">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="filters" style="margin-bottom: 20px;">
                    <div class="filter-item">
                        <label for="tripHistoryDateFilter" class="form-label">Date</label>
                        <input type="date" id="tripHistoryDateFilter" class="form-control">
                    </div>
                    <div class="filter-item">
                        <label for="tripHistoryRouteFilter" class="form-label">Route</label>
                        <select id="tripHistoryRouteFilter" class="form-control">
                            <option value="all">All Routes</option>
                        </select>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="parentTripHistoryTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bus</th>
                                <th>Driver</th>
                                <th>Pickup Time</th>
                                <th>Drop-off Time</th>
                                <th>Route</th>
                            </tr>
                        </thead>
                        <tbody id="tripHistoryBody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Emergency Alerts Page -->
        <div class="page" id="emergency-alerts">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-triangle-exclamation"></i> Emergency Alerts</h3>
                    <span class="status-badge emergency" id="emergencyAlertsBadge">0 Active</span>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="emergencyAlertsTable">
                        <thead>
                            <tr>
                                <th>Bus</th>
                                <th>Driver</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Time</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="emergencyAlertsBody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Profile & Settings Page -->
        <div class="page" id="profile-settings">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-cog"></i> Profile & Settings</h3>
                </div>
                <div class="profile-settings-grid">
                    <div class="card">
                        <h4>Parent Information</h4>
                        <div class="form-group">
                            <label for="parentName">Full Name</label>
                            <input id="parentName" class="form-control" type="text" value="{{ auth()->user()?->name ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label for="parentPhone">Phone</label>
                            <input id="parentPhone" class="form-control" type="tel" value="{{ auth()->user()?->parentProfile?->phone ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label for="parentEmail">Email</label>
                            <input id="parentEmail" class="form-control" type="email" value="{{ auth()->user()?->email ?? '' }}" required>
                        </div>
                    </div>
                    <div class="card">
                        <h4>Account Settings</h4>
                        <div class="form-group">
                            <label for="parentPassword">Change Password</label>
                            <input id="parentPassword" class="form-control" type="password" placeholder="New password">
                        </div>
                        <div class="form-group">
                            <label class="overlay-toggle">
                                <input type="checkbox" id="emailAlerts" checked>
                                Email Notifications
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="overlay-toggle">
                                <input type="checkbox" id="smsAlerts" checked>
                                SMS Notifications
                            </label>
                        </div>
                        <button class="btn-primary" type="button" onclick="saveParentSettings()">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <footer class="site-footer" role="contentinfo">
        <div class="small-muted">BusTracker &bull; v1.0</div>
        <div class="small-muted">Last updated: <span id="lastUpdated">-</span></div>
    </footer>

    <script>
    (function(){
        // restore child selection
        const sel = document.getElementById('childSelector');
        try{
            if(sel){
                const saved = localStorage.getItem('selectedChild');
                if(saved) sel.value = saved;
                sel.addEventListener('change', ()=> localStorage.setItem('selectedChild', sel.value));
            }
        }catch(e){console.warn('restore child failed',e)}

        // menu toggle
        const menuBtn = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');
        
        // Create sidebar overlay for mobile if it doesn't exist
        let sidebarOverlay = document.querySelector('.sidebar-overlay');
        if (!sidebarOverlay) {
            sidebarOverlay = document.createElement('div');
            sidebarOverlay.className = 'sidebar-overlay';
            document.body.appendChild(sidebarOverlay);
        }

        function setSidebarState(isOpen) {
            if (!sidebar) return;
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.toggle('active', isOpen);
                sidebarOverlay.classList.toggle('active', isOpen);
                document.body.classList.toggle('sidebar-open', isOpen);
                if (menuBtn) {
                    menuBtn.setAttribute('aria-expanded', String(isOpen));
                    const icon = menuBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-bars', !isOpen);
                        icon.classList.toggle('fa-times', isOpen);
                    }
                }
            } else {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
                if (menuBtn) {
                    const expanded = sidebar.classList.contains('collapsed');
                    menuBtn.setAttribute('aria-expanded', String(!expanded));
                    const icon = menuBtn.querySelector('i');
                    if (icon) {
                        icon.classList.add('fa-bars');
                        icon.classList.remove('fa-times');
                    }
                }
            }
        }

        if (menuBtn && sidebar) {
            menuBtn.addEventListener('click', () => {
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    const isOpen = sidebar.classList.contains('active');
                    setSidebarState(!isOpen);
                } else {
                    const collapsed = sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
                    menuBtn.setAttribute('aria-expanded', String(!collapsed));
                }
            });

            // Restore collapsed state on desktop
            try {
                if (window.innerWidth > 768) {
                    const savedCollapsed = localStorage.getItem('sidebar_collapsed') === '1';
                    if (savedCollapsed) {
                        sidebar.classList.add('collapsed');
                        menuBtn.setAttribute('aria-expanded', 'false');
                    }
                }
            } catch(e) {}
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => setSidebarState(false));
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                setSidebarState(false);
            }
        });

        // map + tracking helpers
        const mapEl = document.getElementById('gpsMap');
        let mapInited = false, mapInstance = null, busMarker = null, childMarker = null, routeLine = null;

        function haversine(lat1,lon1,lat2,lon2){
            const toRad = v => v * Math.PI/180;
            const R=6371; const dLat = toRad(lat2-lat1); const dLon = toRad(lon2-lon1);
            const a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(toRad(lat1))*Math.cos(toRad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
            const c = 2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); return R*c;
        }

                function initMap(){
            if(mapInited || typeof L === 'undefined') return; mapInited = true;
            try{
                mapInstance = L.map('gpsMap',{attributionControl:false}).setView([30.0444,31.2357],13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(mapInstance);

                // sample positions (replace with real data when available)
                const busPos = [30.0460,31.2350];
                const childPos = [30.0449,31.2390];
                const initialDist = haversine(busPos[0],busPos[1], childPos[0],childPos[1]);

                busMarker = L.marker(busPos, {icon: L.divIcon({className:'bus-marker', html:'<i class="fas fa-bus"></i>'})}).addTo(mapInstance).bindPopup('Bus #42');
                childMarker = L.marker(childPos, {icon: L.divIcon({className:'child-marker', html:'<i class="fas fa-child"></i>'})}).addTo(mapInstance).bindPopup(document.getElementById('childNameOverlay')?.textContent || 'Child');

                routeLine = L.polyline([busPos, childPos], {color:'#0ea5a4', weight:4, opacity:0.9, dashArray:'6 6'}).addTo(mapInstance);
                updateTrackingInfo(busPos, childPos, initialDist);

                // hide placeholder
                const placeholder = document.querySelector('.map-placeholder');
                if(placeholder){
                    placeholder.style.opacity = '0';
                    setTimeout(() => placeholder.style.display = 'none', 400);
                }

                // simulate bus movement towards child (gentle)
                let t=0; const steps=120; const simulate = ()=>{
                    t++; const lat = busPos[0] + (childPos[0]-busPos[0])*(t/steps);
                    const lon = busPos[1] + (childPos[1]-busPos[1])*(t/steps);
                    if(busMarker){ busMarker.setLatLng([lat,lon]); }
                    if(routeLine){ routeLine.setLatLngs([ [lat,lon], childPos ]); }
                    updateTrackingInfo([lat,lon], childPos, initialDist);
                    if(autoFollow) { try{ mapInstance.setView([lat,lon],14,{animate:true}); }catch(e){} }
                    if(t<steps) requestAnimationFrame(simulate);
                };
                requestAnimationFrame(simulate);
            }catch(e){console.warn('Leaflet init failed',e)}
        }

        let autoFollow = true;
        function updateTrackingInfo(busPos, childPos, initialDist){
            try{
                const dist = haversine(busPos[0],busPos[1], childPos[0], childPos[1]);
                const speedKmh = 30; // assumed avg
                const etaMin = Math.max(1, Math.round((dist / speedKmh) * 60));
                const distanceEl = document.getElementById('distanceText');
                const etaEl = document.getElementById('etaTime');
                const trackingEta = document.getElementById('trackingEta');
                const trackingSpeed = document.getElementById('trackingSpeed');
                if(distanceEl) distanceEl.textContent = dist.toFixed(1) + ' km away';
                if(etaEl) etaEl.textContent = (dist<0.05 ? 'Arrived' : etaMin + ' mins');
                if(trackingEta) trackingEta.textContent = etaEl.textContent;
                if(trackingSpeed) trackingSpeed.textContent = speedKmh + ' km/h';
                const lastSeen = new Date().toLocaleTimeString();
                const lastSeenEl = document.getElementById('childLastSeen');
                if(lastSeenEl) lastSeenEl.textContent = 'Last seen: ' + lastSeen;
                // update short ETA and progress
                const etaShort = document.getElementById('etaShort');
                if(etaShort) etaShort.textContent = (dist<0.05 ? 'Arrived' : 'ETA: ' + etaMin + 'm');
                const progressEl = document.querySelector('#arrivalProgress > i');
                if(progressEl){
                    const pct = initialDist && initialDist>0 ? Math.min(100, Math.round((1 - (dist/initialDist))*100)) : 0;
                    progressEl.style.width = pct + '%';
                    const progWrap = document.getElementById('arrivalProgress'); if(progWrap) progWrap.setAttribute('aria-valuenow', String(pct));
                }
            }catch(e){console.warn('updateTrackingInfo',e)}
        }

        function showChildLocation(lat,lon){
            if(!mapInstance) initMap();
            const loc = (lat && lon) ? [lat,lon] : (childMarker ? childMarker.getLatLng() : [30.0449,31.2390]);
            if(childMarker) childMarker.setLatLng(loc).openPopup();
            if(mapInstance) mapInstance.setView(loc,16,{animate:true});
            // small pulse
            try{ const ring = L.circle(loc, {radius:40, color:'#0ea5a4', weight:2, fill:false}).addTo(mapInstance); setTimeout(()=> mapInstance.removeLayer(ring),1600);}catch(e){}
        }

        // Handle SPA page transitions for tracking page
        document.addEventListener('spa:pageChanged', function(e) {
            // Close mobile sidebar on page navigation to reset state
            if (typeof setSidebarState === 'function') {
                setSidebarState(false);
            }

            if (e.detail.pageId === 'tracking') {
                setTimeout(function() {
                    if (typeof L !== 'undefined') {
                        if (!mapInited) {
                            initMap();
                        } else if (mapInstance) {
                            try {
                                mapInstance.invalidateSize();
                            } catch(err) {
                                console.warn('invalidateSize failed', err);
                            }
                        }
                    }
                }, 150);
            }
        });

        // Check if page is already active on load
        if (mapEl && document.getElementById('tracking')?.classList.contains('active')) {
            setTimeout(function() {
                if (typeof L !== 'undefined' && !mapInited) initMap();
            }, 150);
        }

        // wire overlay buttons
        const locateBtn = document.getElementById('locateChildBtn');
        if(locateBtn){ locateBtn.addEventListener('click', ()=>{ // try real geolocation first
            if(navigator.geolocation){ navigator.geolocation.getCurrentPosition(pos=>{ showChildLocation(pos.coords.latitude,pos.coords.longitude); }, ()=>{ showChildLocation(); }); } else { showChildLocation(); }
        }); }
        const followBus = document.getElementById('followBusBtn');
        if(followBus){ followBus.addEventListener('click', ()=>{ if(busMarker && mapInstance){ mapInstance.setView(busMarker.getLatLng(),14,{animate:true}); busMarker.openPopup(); autoFollow = true; const auto = document.getElementById('autoFollow'); if(auto) auto.checked = true; } }); }
        const autoFollowChk = document.getElementById('autoFollow'); if(autoFollowChk){ autoFollowChk.addEventListener('change', ()=>{ autoFollow = !!autoFollowChk.checked; }); }
        const shareBtn = document.getElementById('shareChildBtn'); if(shareBtn){ shareBtn.addEventListener('click', ()=>{
            const coords = childMarker ? childMarker.getLatLng() : null;
            const href = coords ? `${location.origin}${location.pathname}#child=${coords.lat.toFixed(6)},${coords.lng.toFixed(6)}` : window.location.href;
            navigator.clipboard?.writeText(href).then(()=>{ shareBtn.textContent = 'Copied'; setTimeout(()=> shareBtn.textContent = 'Share',1200); }).catch(()=>{ alert('Copy failed, URL: '+href); });
        }); }

        // footer timestamp
        const lastUpdated = document.getElementById('lastUpdated');
        if(lastUpdated) lastUpdated.textContent = new Date().toLocaleString();
    })();
    </script>

    <script>
    window.__PARENT_DATA = {
        children: @json($children),
        attendance: @json($attendanceRecords),
        applications: @json($applications),
        childFormCount: @json($childFormCount ?? 1),
        stats: @json($stats),
        assignedChildrenCount: @json($assignedChildrenCount ?? 0),
        isApproved: @json($isApproved),
        isDashboardUnlocked: @json($isDashboardUnlocked ?? false),
        appStatus: @json($appStatus),
        profileApprovedAt: @json($profileApprovedAt ?? null)
    };

    function isParentDashboardReady() {
        const data = window.__PARENT_DATA || {};
        return data.isDashboardUnlocked === true
            || data.appStatus === 'approved'
            || !!data.profileApprovedAt;
    }

    function getParentLockMessage() {
        const data = window.__PARENT_DATA || {};
        const isAr = (localStorage.getItem('lang_parent') || 'en') === 'ar';
        if (data.appStatus === 'pending_details') {
            return {
                title: isAr ? 'أكمل استمارة الأطفال' : 'Complete Children Form',
                body: isAr
                    ? 'يرجى ملء استمارة بيانات الأطفال من لوحة التحكم أولاً. لا يمكن فتح بقية الأقسام قبل إرسال البيانات.'
                    : 'Please fill out the children details form on the dashboard first. Other sections stay locked until you submit.',
                sub: isAr ? 'بعد الإرسال، ستنتظر موافقة الإدارة.' : 'After submitting, admin approval is required.',
                showDashboardBtn: true,
                dashboardBtn: isAr ? 'الذهاب للوحة التحكم' : 'Go to Dashboard',
            };
        }
        if (data.appStatus === 'pending_approval') {
            return {
                title: isAr ? 'بانتظار موافقة الإدارة' : 'Awaiting Admin Approval',
                body: isAr
                    ? 'تم إرسال بيانات الأطفال. لا يمكن فتح أي قسم آخر حتى توافق الإدارة على حسابك.'
                    : 'Your children details were submitted. No other section can open until admin approves your account.',
                sub: isAr ? 'سيتم فتح الداشبورد تلقائياً بعد الموافقة.' : 'The dashboard will unlock automatically after approval.',
                showDashboardBtn: false,
            };
        }
        if (data.appStatus === 'pending') {
            return {
                title: isAr ? 'الطلب قيد المراجعة' : 'Application Under Review',
                body: isAr
                    ? 'حسابك قيد المراجعة حالياً. سيتم تفعيل الأقسام بمجرد قبول طلب التقديم.'
                    : 'Your account is under review. Sections unlock once your application is accepted.',
                showDashboardBtn: true,
                dashboardBtn: isAr ? 'الذهاب للوحة التحكم' : 'Go to Dashboard',
            };
        }
        if (data.appStatus === 'rejected') {
            return {
                title: isAr ? 'تم رفض الحساب' : 'Account Rejected',
                body: isAr
                    ? 'تم رفض طلبك. تواصل مع الدعم أو أعد إرسال البيانات بعد التعديل.'
                    : 'Your request was rejected. Contact support or resubmit your details.',
                showDashboardBtn: true,
                dashboardBtn: isAr ? 'الذهاب للوحة التحكم' : 'Go to Dashboard',
            };
        }
        return {
            title: isAr ? 'القسم غير متاح' : 'Section Unavailable',
            body: isAr ? 'هذا القسم غير متاح حالياً.' : 'This section is not available yet.',
            showDashboardBtn: true,
            dashboardBtn: isAr ? 'الذهاب للوحة التحكم' : 'Go to Dashboard',
        };
    }

    function getParentAssignmentLockMessage() {
        const isAr = (localStorage.getItem('lang_parent') || 'en') === 'ar';
        return {
            title: isAr ? 'في انتظار تعيين الحافلة' : 'Bus Assignment Pending',
            body: isAr
                ? 'هذا القسم سيصبح نشطاً بمجرد قيام الإدارة بتعيين حافلة وسائق لأطفالك.'
                : 'This section activates once administration assigns a bus and driver to your children.',
            showDashboardBtn: true,
            dashboardBtn: isAr ? 'الذهاب للوحة التحكم' : 'Go to Dashboard',
        };
    }

    window.__DASHBOARD_LOCK = {
        isReady: isParentDashboardReady,
        shouldLockPage: function(pageId) {
            if (!isParentDashboardReady()) return true;
            if (window.__PARENT_DATA.assignedChildrenCount === 0) {
                return ['tracking', 'attendance', 'trip-history', 'emergency-alerts'].includes(pageId);
            }
            return false;
        },
        getMessage: function(pageId) {
            if (isParentDashboardReady() && window.__PARENT_DATA.assignedChildrenCount === 0) {
                if (['tracking', 'attendance', 'trip-history', 'emergency-alerts'].includes(pageId)) {
                    return getParentAssignmentLockMessage();
                }
            }
            return getParentLockMessage();
        },
    };

    if (window.DashboardLock) {
        window.DashboardLock.refresh();
    }

    (function(){
        const container = document.getElementById('childrenSectionsContainer');
        if(container && window.__PARENT_DATA.children.length){
            container.innerHTML = window.__PARENT_DATA.children.map(child => `
                <div class="child-card" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#0ea5a4,#2563eb);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">${child.full_name.charAt(0)}</div>
                        <div>
                            <strong style="color:#1e293b;font-size:15px;">${child.full_name}</strong>
                            <div style="color:#64748b;font-size:13px;">Grade ${child.grade || '—'} &bull; ${child.school_name || '—'}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else if(container) {
            container.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>No children registered.</div>';
        }

        const tbody = document.getElementById('attendanceTableBody');
        if(tbody && window.__PARENT_DATA.attendance.length){
            tbody.innerHTML = window.__PARENT_DATA.attendance.map(a => {
                const date = a.trip?.trip_date ? new Date(a.trip.trip_date).toLocaleDateString() : '—';
                const pickup = a.picked_up_at ? new Date(a.picked_up_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) : '—';
                const dropoff = a.dropped_off_at ? new Date(a.dropped_off_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) : '—';
                const statusBg = a.status==='present'?'rgba(34,197,94,.15)':a.status==='absent'?'rgba(239,68,68,.15)':'rgba(99,102,241,.15)';
                const statusColor = a.status==='present'?'#4ade80':a.status==='absent'?'#f87171':'#a5b4fc';
                return `<tr>
                    <td>${date}</td>
                    <td>${a.student?.full_name || '—'}</td>
                    <td><span style="background:${statusBg};color:${statusColor};padding:2px 8px;border-radius:4px;font-size:12px;text-transform:capitalize;">${a.status}</span></td>
                    <td>${pickup}</td>
                    <td>${dropoff}</td>
                </tr>`;
            }).join('');
        } else if(tbody) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">No attendance records found.</td></tr>';
        }

        document.querySelectorAll('[data-medical-toggle]').forEach(toggle => {
            toggle.addEventListener('change', () => {
                const fields = document.querySelector(`[data-medical-fields="${toggle.dataset.medicalToggle}"]`);
                if (fields) fields.style.display = toggle.checked ? 'grid' : 'none';
            });
        });

        const onboardingForm = document.getElementById('childrenOnboardingForm');
        if (onboardingForm) {
            onboardingForm.addEventListener('submit', async event => {
                event.preventDefault();

                const message = document.getElementById('childrenOnboardingMessage');
                const submit = document.getElementById('childrenOnboardingSubmit');
                const formData = new FormData(onboardingForm);
                const count = Number(window.__PARENT_DATA.childFormCount || 1);
                const children = [];

                for (let i = 0; i < count; i += 1) {
                    children.push({
                        full_name: formData.get(`children[${i}][full_name]`) || '',
                        age: formData.get(`children[${i}][age]`) || null,
                        grade: formData.get(`children[${i}][grade]`) || '',
                        school_name: formData.get(`children[${i}][school_name]`) || '',
                        pickup_location: formData.get(`children[${i}][pickup_location]`) || '',
                        dropoff_location: formData.get(`children[${i}][dropoff_location]`) || '',
                        pickup_time: formData.get(`children[${i}][pickup_time]`) || null,
                        dropoff_time: formData.get(`children[${i}][dropoff_time]`) || null,
                        has_medical_condition: formData.has(`children[${i}][has_medical_condition]`),
                        medical_condition: formData.get(`children[${i}][medical_condition]`) || '',
                        medication: formData.get(`children[${i}][medication]`) || ''
                    });
                }

                message.style.display = 'none';
                submit.disabled = true;
                submit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                try {
                    const res = await fetch('/api/parent/children/submit', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{ $apiToken ?? '' }}',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ children })
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'Unable to submit children details.');
                    message.textContent = data.message || 'Children details submitted.';
                    message.style.background = 'rgba(34,197,94,.12)';
                    message.style.color = '#15803d';
                    message.style.display = 'block';
                    setTimeout(() => window.location.reload(), 900);
                } catch (error) {
                    message.textContent = error.message || 'Submission failed.';
                    message.style.background = 'rgba(239,68,68,.12)';
                    message.style.color = '#b91c1c';
                    message.style.display = 'block';
                    submit.disabled = false;
                    submit.innerHTML = '<i class="fas fa-paper-plane"></i> Send to Admin';
                }
            });
        }
    })();
    </script>

    <!-- Parent Profile Modal -->
    <div id="parentProfileModal" class="profile-modal">
        <div class="profile-modal-overlay" onclick="closeParentProfileModal()"></div>
        <div class="profile-modal-content">
            <button class="profile-modal-close" onclick="closeParentProfileModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="profile-modal-header">
                <div class="profile-modal-avatar">
                    <img src="../../IMAGE/ADMIN.png" alt="Parent Profile">
                    <div class="profile-status-badge active">
                        <i class="fas fa-circle"></i>
                        <span>Active</span>
                    </div>
                </div>
                <div class="profile-modal-info">
                    <h2>{{ $userName ?? 'Parent' }}</h2>
                    <p class="profile-role">Parent Account</p>
                    <div class="profile-children-count">
                        <i class="fas fa-child"></i>
                        <span>{{ $stats['children_count'] ?? 0 }} Children</span>
                    </div>
                </div>
            </div>
            <div class="profile-modal-body">
                <div class="profile-section">
                    <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                    <div class="profile-details-grid">
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
                            <span class="detail-value">{{ auth()->user()?->parentProfile?->phone ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="detail-value">{{ auth()->user()?->email ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                            <span class="detail-value">{{ auth()->user()?->parentProfile?->address ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-alt"></i> Member Since</span>
                            <span class="detail-value">{{ auth()->user()?->created_at?->format('F Y') ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="profile-section">
                    <h3><i class="fas fa-child"></i> Children Information</h3>
                    <div class="children-list">
                        @forelse($children as $child)
                        <div class="child-profile-card">
                            <div class="child-avatar">
                                <span>{{ substr($child->full_name, 0, 2) }}</span>
                            </div>
                            <div class="child-info">
                                <h4>{{ $child->full_name }}</h4>
                                <p><i class="fas fa-graduation-cap"></i> Grade {{ $child->grade ?? '—' }}</p>
                                <p><i class="fas fa-school"></i> {{ $child->school_name ?? '—' }}</p>
                            </div>
                            <div class="child-status">
                                <span class="status-badge {{ $child->active ? 'active' : 'inactive' }}">{{ $child->active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                        @empty
                        <p style="color:#94a3b8;padding:12px;">No children registered.</p>
                        @endforelse
                    </div>
                </div>
                <div class="profile-section">
                    <h3><i class="fas fa-chart-line"></i> Account Statistics</h3>
                    <div class="profile-stats-grid">
                        <div class="profile-stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ ($stats['children_count'] ?? 0) > 0 ? round((($stats['attendance_present'] ?? 0) / (($stats['attendance_present'] ?? 0) + ($stats['attendance_absent'] ?? 0) + 0.001)) * 100) : 0 }}%</h4>
                                <p>Attendance Rate</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $applications->count() ?? 0 }}</h4>
                                <p>Requests Sent</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $stats['applications_pending'] ?? 0 }}</h4>
                                <p>Pending Requests</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon purple">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $stats['children_count'] ?? 0 }}</h4>
                                <p>Children</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile-modal-footer">
                <button class="btn-profile-primary" onclick="alert('Edit feature coming soon!')">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button class="btn-profile-secondary" onclick="window.location.href='{{ url('/parent/request') }}'">
                    <i class="fas fa-file-lines"></i> Parent Requests
                </button>
                <button class="btn-profile-secondary" onclick="window.location.href='{{ url('/parent/applications') }}'">
                    <i class="fas fa-folder-open"></i> My Applications
                </button>
                <button class="btn-profile-secondary" onclick="closeParentProfileModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <style>
        .profile-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }

        .profile-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        .profile-modal-content {
            position: relative;
            background: white;
            border-radius: 24px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 10001;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-50px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .profile-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.1);
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .profile-modal-close:hover {
            background: #ef4444;
            color: white;
            transform: rotate(90deg) scale(1.1);
        }

        .profile-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-modal-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .profile-modal-avatar {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-modal-avatar img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            object-fit: cover;
            animation: avatarPulse 2s ease-in-out infinite;
        }

        @keyframes avatarPulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
            50% { box-shadow: 0 10px 40px rgba(102, 126, 234, 0.6); }
        }

        .profile-status-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: white;
            color: #10b981;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .profile-status-badge i {
            font-size: 8px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .profile-modal-info h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .profile-role {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .profile-children-count {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            z-index: 1;
            font-size: 16px;
            font-weight: 600;
        }

        .profile-children-count i {
            font-size: 20px;
        }

        .profile-modal-body {
            padding: 32px;
        }

        .profile-section {
            margin-bottom: 32px;
        }

        .profile-section h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .profile-section h3 i {
            color: #667eea;
        }

        .profile-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .profile-detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .profile-detail-item:hover {
            background: #f1f5f9;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-label i {
            color: #667eea;
        }

        .detail-value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .children-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .child-profile-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .child-profile-card:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .child-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .child-info {
            flex: 1;
        }

        .child-info h4 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .child-info p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .child-info i {
            color: #667eea;
            width: 16px;
        }

        .child-status {
            flex-shrink: 0;
        }

        .profile-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
        }

        .profile-stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .profile-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        .stat-content h4 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .stat-content p {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .profile-modal-footer {
            padding: 24px 32px;
            border-top: 2px solid #e2e8f0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-profile-primary,
        .btn-profile-secondary {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-profile-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-profile-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-profile-secondary {
            background: #f1f5f9;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-profile-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .profile-modal-content {
                width: 95%;
                max-height: 95vh;
            }

            .profile-details-grid,
            .profile-stats-grid {
                grid-template-columns: 1fr;
            }

            .profile-modal-footer {
                flex-direction: column;
            }

            .btn-profile-primary,
            .btn-profile-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        function openParentProfileModal() {
            const modal = document.getElementById('parentProfileModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeParentProfileModal() {
            const modal = document.getElementById('parentProfileModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeParentProfileModal();
            }
        });
    </script>

    <script>
    let parentQrPollTimer = null;

    async function loadParentQrCodes() {
        const grid = document.getElementById('parentQrGrid');
        if (!grid) return;

        try {
            const response = await safestepApi('/api/parent/qr-codes');
            const items = response.data || [];

            if (!items.length) {
                grid.innerHTML = '<div class="small-muted" style="padding:12px;">No QR codes yet. They will appear here when admin generates them from Activity Logs.</div>';
                return;
            }

            grid.innerHTML = items.map(item => `
                <div class="card" style="text-align:center;">
                    <h4 style="margin-bottom:6px;">${item.full_name || 'Student'}</h4>
                    <p class="small-muted">${item.grade || ''}${item.school_name ? ' · ' + item.school_name : ''}</p>
                    ${item.image_url ? `<img src="${item.image_url}" alt="QR for ${item.full_name}" width="200" height="200" style="margin:12px auto;border-radius:8px;">` : ''}
                    <p class="small-muted">${item.qr_code || ''}</p>
                    <p class="small-muted">${item.generated_at ? new Date(item.generated_at).toLocaleString() : ''}</p>
                    ${item.image_url ? `<a href="${item.image_url}" download class="btn-primary" style="display:inline-block;margin-top:8px;padding:8px 14px;text-decoration:none;">Download QR</a>` : ''}
                </div>
            `).join('');
        } catch (error) {
            grid.innerHTML = '<div class="small-muted" style="padding:12px;">Unable to load QR codes right now.</div>';
        }
    }

    document.addEventListener('spa:pageChanged', function(e) {
        if (e.detail.pageId === 'child-qr') {
            loadParentQrCodes();
            if (parentQrPollTimer) clearInterval(parentQrPollTimer);
            parentQrPollTimer = setInterval(loadParentQrCodes, 10000);
        } else if (parentQrPollTimer) {
            clearInterval(parentQrPollTimer);
            parentQrPollTimer = null;
        }
    });
    </script>

    <script src="{{ asset('js/dashboard-theme.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/parent-api.js') }}"></script>
    <script src="{{ asset('js/i18n-parent.js') }}"></script>
    <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>

