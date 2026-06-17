<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Dashboard - School Bus Tracking</title>
    <link rel="icon" href="{{ asset('img/icon.jpg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
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
        /* RTL Support */
        html[dir="rtl"] body {
            font-family: 'Inter', 'Noto Sans Arabic', sans-serif;
        }
        html[dir="rtl"] .sidebar {
            left: auto;
            right: 0;
            border-right: none;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
        }
        html[dir="rtl"] .main-content {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }
        html[dir="rtl"] .nav-link::before {
            left: auto;
            right: 0;
        }
        html[dir="rtl"] .nav-link:hover {
            transform: translateX(-4px);
        }
        html[dir="rtl"] .view-all:hover {
            transform: translateX(-4px);
        }
        html[dir="rtl"] .view-all::after {
            content: '←';
            display: inline-block;
            transform: scaleX(-1);
        }
        html[dir="rtl"] .stat-card {
            flex-direction: row-reverse;
            text-align: right;
        }
        html[dir="rtl"] .activity-item {
            flex-direction: row-reverse;
            text-align: right;
        }
        html[dir="rtl"] .activity-item:hover {
            transform: translateX(-8px);
        }
        html[dir="rtl"] .card-header {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .topbar {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .topbar-left, html[dir="rtl"] .topbar-right {
            flex-direction: row-reverse;
        }
        html[dir="rtl"] .profile {
            flex-direction: row-reverse;
        }

        /* Responsive RTL */
        @media (max-width: 992px) {
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
    </style>
    {{-- Seed the Sanctum API token into localStorage before any JS loads --}}
    <script>
    (function(){
        var t = '{{ $apiToken ?? '' }}';
        window.__API_TOKEN = t;
        window.__INITIAL_PAGE = 'dashboard';
        window.__DRIVER_DATA = {
            isApproved: @json($isApproved),
            appStatus: @json($appStatus),
            hasBus: @json($assignedBus ? true : false)
        };
        if(t){ localStorage.setItem('safestep_token', t); localStorage.setItem('token', t); }

        // SPA Page Navigation Guard
        document.addEventListener('spa:pageChanged', function(e) {
            const pageId = e.detail.pageId;
            if (pageId === 'dashboard' || pageId === 'notifications' || pageId === 'requests' || pageId === 'my-applications') {
                return;
            }
            if (!window.__DRIVER_DATA.isApproved) {
                alert('حسابك قيد المراجعة حالياً. سيتم تفعيل الأقسام بمجرد تفعيل الحساب.');
                window.navigateTo('dashboard');
                return;
            }
        });
    })();
    </script>
    <script src="{{ asset('js/api-service.js') }}"></script>
    <script src="{{ asset('js/spa-navigation.js') }}"></script>
    <script src="{{ asset('js/dashboard-mobile.js') }}"></script>
</head>
<body class="dashboard-body">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px; padding: 16px; min-height: 90px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 12px; width: 100%;">
                <i class="fas fa-shield-alt" style="font-size: 28px; background: linear-gradient(135deg, var(--primary-light), var(--primary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.3));"></i>
                <h2 style="font-size: 20px; font-weight: 700; letter-spacing: -0.5px; background: linear-gradient(135deg, #FFFFFF, #E2E8F0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0;">SAFESTEP BUS</h2>
            </div>
            <div style="margin-top: 6px; width: 100%;">
                <span class="portal-tag" style="display:block; font-size:11px; font-weight:600; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.06em;">Driver Portal</span>
            </div>
        </div>
        <nav class="nav-menu">
            <a href="#" class="nav-link active" data-page="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-link" data-page="today-trip">
                <i class="fas fa-calendar-day"></i>
                <span>Today Trip</span>
            </a>
            <a href="#" class="nav-link" data-page="students">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>
            <a href="#" class="nav-link" data-page="route">
                <i class="fas fa-route"></i>
                <span>Route</span>
            </a>
            <a href="#" class="nav-link" data-page="trip">
                <i class="fas fa-play-circle"></i>
                <span>Start / End Trip</span>
            </a>
            <a href="#" class="nav-link" data-page="notifications">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="#" class="nav-link" data-page="trip-history">
                <i class="fas fa-history"></i>
                <span>Trip History</span>
            </a>
            <a href="/driver/request" class="nav-link external-link" data-page="requests">
                <i class="fas fa-file-alt"></i>
                <span>Driver Requests</span>
            </a>
            <a href="/driver/applications" class="nav-link external-link" data-page="my-applications">
                <i class="fas fa-folder-open"></i>
                <span>My Applications</span>
            </a>
            <a href="{{ route('logout') }}" class="nav-link logout"
               onclick="event.preventDefault(); localStorage.removeItem('safestep_token'); localStorage.removeItem('token'); window.location.href='{{ route('logout') }}'">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
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
                <button class="notification-icon" type="button" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </button>
                <div class="time-display">
                    <i class="fas fa-clock"></i>
                    <span id="currentTime"></span>
                </div>
                <a class="profile" href="#" onclick="event.preventDefault(); openDriverProfileModal();" aria-label="Open driver profile">
                    <img src="{{ asset('img/admin.png') }}" alt="Driver" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName ?? 'Driver') }}&background=764ba2&color=fff'">
                    <span>{{ $userName ?? 'Driver' }}</span>
                </a>
            </div>
        </div>

        <!-- Dashboard Page -->
        <div class="page active" id="dashboard">
            <div class="dashboard-grid">
                
                @if($appStatus === 'pending')
                <!-- State A: Pending Approval -->
                <div class="card status-pending-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02); margin: 20px 0;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px; animation: pulse 2s infinite;">
                        <i class="fas fa-clock" style="font-size: 44px; color: #f59e0b;"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 14px;">طلب التقديم قيد المراجعة</h2>
                    <p style="font-size: 16px; color: #64748b; max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">مرحباً بك كقائد حافلة في SafeStep. حسابك مسجل ولكن لم يتم اعتماده وتعيين ميعاد المقابلة بالكامل بعد من قبل الإدارة. سيتم تفعيل حسابك بمجرد الموافقة وتعديل الحالة.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 30px; border: 1px solid #e2e8f0;">
                        <span>حالة الحساب:</span>
                        <span class="status-badge pending" style="padding: 4px 12px; border-radius: 12px; font-weight: 700;">تحت المراجعة / المقابلة الشخصية</span>
                    </div>
                </div>

                @elseif($appStatus === 'rejected')
                <!-- State B: Rejected -->
                <div class="card status-rejected-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02); margin: 20px 0;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <i class="fas fa-times-circle" style="font-size: 48px; color: #ef4444;"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: #1e293b; margin-bottom: 14px;">تم رفض طلب السائق</h2>
                    <p style="font-size: 16px; color: #64748b; max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">نعتذر منك، لقد تم رفض طلب تعيينك كقائد حافلة في SafeStep من قبل الإدارة. يرجى مراجعة البيانات أو التواصل مع الإدارة الفنية.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; background: #f8fafc; padding: 8px 16px; border-radius: 30px; border: 1px solid #e2e8f0;">
                        <span>حالة الطلب:</span>
                        <span class="status-badge rejected" style="padding: 4px 12px; border-radius: 12px; font-weight: 700;">مرفوض</span>
                    </div>
                </div>

                @elseif($appStatus === 'pending_details')
                <!-- State C: Register Driver Details Form -->
                <div class="card" id="driverOnboardingCard" style="grid-column:1/-1; border-radius: 20px; padding: 28px; border: 1px solid var(--border-color); background: var(--card-bg);">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-id-card" style="font-size: 20px; color: var(--accent);"></i>
                            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--text-dark);">Register Driver Profile Details</h3>
                        </div>
                        <span style="font-size:12px;color:var(--text-light);background: var(--light-bg);padding: 4px 12px;border-radius: 12px;font-weight: 500;">Required step to activate driver account</span>
                    </div>
                    <form id="driverOnboardingForm" style="display:flex;flex-direction:column;gap:16px;">
                        @csrf
                        <div id="driverOnboardingMessage" style="display:none;padding:10px 12px;border-radius:8px;font-size:13px;"></div>
                        <div style="border:1px solid var(--border-color);border-radius:14px;padding:20px;background:var(--light-bg);">
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Full Name</label>
                                    <input name="full_name" required type="text" class="form-control" value="{{ auth()->user()->name }}" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Phone Number</label>
                                    <input name="phone" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Age</label>
                                    <input name="age" required type="number" min="20" max="70" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Gender</label>
                                    <select name="gender" required class="form-control" style="font-size: 13px;">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">License Number</label>
                                    <input name="license_number" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Years of Experience</label>
                                    <input name="years_experience" required type="number" min="0" max="40" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Bus/Car Type</label>
                                    <input name="car_type" placeholder="e.g., Mini Bus, Coaster, Sedan" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Bus/Car Model</label>
                                    <input name="car_model" placeholder="e.g., Toyota Coaster 2022" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Plate Number</label>
                                    <input name="car_plate" placeholder="e.g., س ج ط 5432" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="grid-column: 1/-1; margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Home Address</label>
                                    <input name="address" required type="text" class="form-control" style="font-size: 13px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">National ID Document</label>
                                    <input name="national_id" required type="file" accept=".pdf,image/*" class="form-control" style="font-size: 13px; padding: 6px;">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-weight:600;">Criminal Record Document (Fish)</label>
                                    <input name="criminal_record" required type="file" accept=".pdf,image/*" class="form-control" style="font-size: 13px; padding: 6px;">
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;">
                            <button class="btn-primary" id="driverOnboardingSubmit" type="submit" style="padding: 12px 24px; font-weight: 700; border-radius: 10px;">
                                <i class="fas fa-paper-plane"></i> Submit Details
                            </button>
                        </div>
                    </form>
                </div>

                @elseif($appStatus === 'pending_approval')
                <!-- State D: Details submitted, waiting for Admin approval -->
                <div class="card status-pending-card" style="grid-column: 1/-1; padding: 60px 40px; text-align: center; background: var(--card-bg); border-radius: 20px; border: 1px solid var(--border-color); margin: 20px 0;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(37,99,235,0.08); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                        <i class="fas fa-user-clock" style="font-size: 44px; color: var(--accent);"></i>
                    </div>
                    <h2 style="font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 14px;">Profile Details Submitted</h2>
                    <p style="font-size: 16px; color: var(--text-light); max-width: 580px; margin: 0 auto 28px; line-height: 1.6;">Thank you for registering your profile details. The administration is currently reviewing your application, license information, and vehicle details. You will receive access to your driver dashboard once approved.</p>
                    <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-light); background: var(--light-bg); padding: 8px 16px; border-radius: 30px; border: 1px solid var(--border-color);">
                        <span>Status:</span>
                        <span class="status-badge pending" style="padding: 4px 12px; border-radius: 12px; font-weight: 700;">Pending Approval</span>
                    </div>
                </div>

                @elseif(! $assignedBus)
                <!-- State C: Approved but No Bus/Route Assigned -->
                <div class="card" style="grid-column: 1/-1; padding: 40px; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.01);">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 28px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; flex-wrap: wrap;">
                        <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(37,99,235,0.08); display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 26px;">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0 0 6px; font-size: 20px; font-weight: 800; color: #1e293b;">بانتظار تعيين حافلة وخط سير</h3>
                            <span style="font-size: 14px; color: #64748b; line-height: 1.5;">تهانينا! تم قبول طلبك كقائد حافلة معتمد في SafeStep. تقوم الإدارة حاليًا بتخصيص خط السير والحافلة المناسبة لك. ستظهر رحلتك اليومية هنا بمجرد التعيين.</span>
                        </div>
                    </div>
                    
                    <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-id-card" style="color: #64748b;"></i> بيانات السائق المعتمدة:
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc;">
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">الاسم الكامل</span>
                            <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ auth()->user()->name }}</div>
                        </div>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc;">
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">رقم الهاتف</span>
                            <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ auth()->user()->driverProfile?->phone ?? '—' }}</div>
                        </div>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc;">
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">رخصة القيادة</span>
                            <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ auth()->user()->driverProfile?->license_number ?? '—' }}</div>
                        </div>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc;">
                            <span style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">سنوات الخبرة</span>
                            <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ auth()->user()->driverProfile?->years_experience ?? '—' }} سنوات خبرة</div>
                        </div>
                    </div>
                </div>

                @else
                <!-- State D: Active Dashboard -->
                <!-- Bus Status Card -->
                <div class="card status-card ready">
                    <div class="status-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <div class="status-info">
                        <h3>Bus Status</h3>
                        <div class="status-badge" id="busStatusBadge">Ready to Start</div>
                    </div>
                </div>

                <!-- Students Count -->
                <div class="card stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="totalStudents">{{ $todayTrip?->attendance?->count() ?? 0 }}</h2>
                        <p>Students Today</p>
                    </div>
                </div>

                <!-- Current Speed -->
                <div class="card stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="currentSpeed">0</h2>
                        <p>Current Speed (km/h)</p>
                    </div>
                </div>

                <!-- Present Students -->
                <div class="card stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h2 id="presentCount">{{ $todayTrip?->attendance?->where('status', 'present')->count() ?? 0 }}</h2>
                        <p>Present Students</p>
                    </div>
                </div>

                <!-- Route Progress -->
                <div class="card progress-card">
                    <h3><i class="fas fa-route"></i> Route Progress</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-info">
                        <span id="progressText">0% Complete</span>
                        <span id="stopsText">0/8 Stops</span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card actions-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="action-buttons">
                        <button class="action-btn" onclick="startTrip()">
                            <i class="fas fa-play"></i>
                            Start Trip
                        </button>
                        <button class="action-btn" onclick="pauseTrip()">
                            <i class="fas fa-pause"></i>
                            Pause Trip
                        </button>
                        <button class="action-btn" onclick="endTrip()">
                            <i class="fas fa-stop"></i>
                            End Trip
                        </button>
                        <button class="action-btn" onclick="sendAlert()">
                            <i class="fas fa-exclamation-triangle"></i>
                            Send Alert
                        </button>
                    </div>
                </div>

                <!-- Emergency -->
                <div class="card emergency-card">
                    <div class="card-header">
                        <h3><i class="fas fa-triangle-exclamation"></i> Emergency</h3>
                        <span class="status-badge emergency">Immediate</span>
                    </div>
                    <div class="emergency-form">
                        <div class="form-group">
                            <label for="emergencyType">Alert Type</label>
                            <select id="emergencyType" class="form-control" required>
                                <option value="">Select type</option>
                                <option value="general">General</option>
                                <option value="medical">Medical</option>
                                <option value="breakdown">Breakdown</option>
                                <option value="accident">Accident</option>
                                <option value="delay">Delay</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="emergencyNote">Short Note</label>
                            <input id="emergencyNote" class="form-control" type="text" placeholder="Describe the issue" required>
                        </div>
                        <button class="emergency-btn" type="button" onclick="triggerEmergency()">
                            <i class="fas fa-bell"></i> Send Emergency Alert
                        </button>
                    </div>
                    <div class="emergency-log" id="emergencyLog">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="card schedule-card">
                    <h3><i class="fas fa-calendar-day"></i> Today's Schedule</h3>
                    <div class="schedule-list">
                        <div class="schedule-item completed">
                            <div class="schedule-time">7:00 AM</div>
                            <div class="schedule-details">
                                <h4>Pre-Trip Inspection</h4>
                                <p>Vehicle safety check completed</p>
                            </div>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="schedule-item active">
                            <div class="schedule-time">7:30 AM</div>
                            <div class="schedule-details">
                                <h4>Morning Route</h4>
                                <p>Pick up students - 8 stops</p>
                            </div>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="schedule-item">
                            <div class="schedule-time">2:30 PM</div>
                            <div class="schedule-details">
                                <h4>Afternoon Route</h4>
                                <p>Drop off students - 8 stops</p>
                            </div>
                            <i class="fas fa-circle"></i>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <!-- Today Trip Page -->
        <div class="page" id="today-trip">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-day"></i> Today's Trip Schedule</h3>
                    <span class="status-badge active">Active</span>
                </div>
                <div class="today-trip-grid">
                    <div class="card stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $stats['trips_today'] ?? 0 }}</h3>
                            <p>Trips Today</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $stats['trips_completed'] ?? 0 }}</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ max(0, ($stats['trips_today'] ?? 0) - ($stats['trips_completed'] ?? 0)) }}</h3>
                            <p>Upcoming</p>
                        </div>
                    </div>
                </div>
                <div class="schedule-list">
                    <div class="schedule-item completed">
                        <div class="schedule-time">7:00 AM</div>
                        <div class="schedule-details">
                            <h4>Morning Route</h4>
                            <p>Pick up students - 8 stops • Completed at 8:15 AM</p>
                        </div>
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="schedule-item active">
                        <div class="schedule-time">2:30 PM</div>
                        <div class="schedule-details">
                            <h4>Afternoon Route</h4>
                            <p>Drop off students - 8 stops • Starts in 4h 15m</p>
                        </div>
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="trip-extra-grid">
                <div class="card bus-info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bus-alt"></i> Bus Info</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Bus Number</span>
                            <span class="info-value" id="busNumberInfo">Bus #42</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Capacity</span>
                            <span class="info-value" id="busCapacityInfo">45 Seats</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Assigned Route</span>
                            <span class="info-value" id="busRouteInfo">Route A - Morning</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-clipboard-check"></i> Pickup / Drop-off</h3>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Pickup</th>
                                    <th>Drop-off</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Page -->
        <div class="page" id="students">
            <div class="card students-card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Students List</h3>
                    <div class="attendance-summary">
                        <span class="summary-item present">
                            <i class="fas fa-check"></i>
                            Present: <strong id="summaryPresent">0</strong>
                        </span>
                        <span class="summary-item absent">
                            <i class="fas fa-times"></i>
                            Absent: <strong id="summaryAbsent">{{ $todayTrip?->attendance?->where('status', 'absent')->count() ?? 0 }}</strong>
                        </span>
                    </div>
                </div>
                <div class="students-grid" id="studentsGrid">
                    <!-- Students will be populated by JS -->
                </div>
            </div>
        </div>

        <!-- Route Page -->
        <div class="page" id="route">
            <div class="route-container">
                <div class="card route-card">
                    <div class="route-header">
                        <h3><i class="fas fa-route"></i> Route Map</h3>
                        <div class="route-stats">
                            <span><i class="fas fa-map-marker-alt"></i> 8 Stops</span>
                            <span><i class="fas fa-road"></i> 12.5 km</span>
                            <span><i class="fas fa-clock"></i> 45 mins</span>
                        </div>
                    </div>
                    <div class="map-container">
                        <div id="driverGpsMap" style="width: 100%; height: 100%; border-radius: 12px; overflow: hidden;"></div>
                        <canvas id="routeCanvas" style="position:absolute; inset:0; pointer-events:none;"></canvas>
                    </div>
                    <div class="route-hint">
                        Suggested route optimization: save ~5 minutes by prioritizing Stop 3 before Stop 4.
                    </div>
                </div>

                <div class="card stops-card">
                    <h3><i class="fas fa-list"></i> Stop List</h3>
                    <div class="stops-list" id="stopsList">
                        <!-- Stops will be populated by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Trip Page -->
        <div class="page" id="trip">
            <div class="trip-container">
                <div class="card trip-card">
                    <div class="trip-header">
                        <h2>Trip Management</h2>
                        <div class="trip-status" id="tripStatus">
                            <i class="fas fa-circle"></i>
                            <span>Not Started</span>
                        </div>
                    </div>

                    <div class="trip-controls">
                        <button class="trip-btn start" id="startTripBtn">
                            <i class="fas fa-play-circle"></i>
                            <span>Start Trip</span>
                        </button>
                        <button class="trip-btn pause" id="pauseTripBtn" disabled>
                            <i class="fas fa-pause-circle"></i>
                            <span>Pause Trip</span>
                        </button>
                        <button class="trip-btn end" id="endTripBtn" disabled>
                            <i class="fas fa-stop-circle"></i>
                            <span>End Trip</span>
                        </button>
                    </div>

                    <div class="trip-info">
                        <div class="trip-stat">
                            <h4>Duration</h4>
                            <p id="tripDuration">00:00:00</p>
                        </div>
                        <div class="trip-stat">
                            <h4>Distance</h4>
                            <p id="tripDistance">0.0 km</p>
                        </div>
                        <div class="trip-stat">
                            <h4>Students Picked</h4>
                            <p id="tripStudents">0/24</p>
                        </div>
                        <div class="trip-stat">
                            <h4>Stops Completed</h4>
                            <p id="tripStops">0/8</p>
                        </div>
                    </div>
                </div>

                <div class="card notes-card">
                    <h3><i class="fas fa-sticky-note"></i> Trip Notes</h3>
                    <textarea id="tripNotes" placeholder="Add any notes about today's trip..."></textarea>
                    <button class="btn-primary" onclick="saveTripNotes()">
                        <i class="fas fa-save"></i> Save Notes
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications Page -->
        <div class="page" id="notifications">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> Notifications</h3>
                </div>
                <div class="notifications-list" id="driverNotificationsList">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>

        <!-- Trip History Page -->
        <div class="page" id="trip-history">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Trip History</h3>
                    <select class="form-control period-selector" id="tripHistoryFilter" style="max-width: 200px;">
                        <option value="week">Last 7 Days</option>
                        <option value="month">Last 30 Days</option>
                        <option value="quarter">Last 90 Days</option>
                    </select>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Students</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tripHistoryBody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Driver Profile Modal -->
    <div id="driverProfileModal" class="profile-modal">
        <div class="profile-modal-overlay" onclick="closeDriverProfileModal()"></div>
        <div class="profile-modal-content">
            <button class="profile-modal-close" onclick="closeDriverProfileModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="profile-modal-header">
                <div class="profile-modal-avatar">
                    <img src="../../IMAGE/ADMIN.png" alt="Driver Profile">
                    <div class="profile-status-badge active">
                        <i class="fas fa-circle"></i>
                        <span>Active</span>
                    </div>
                </div>
                <div class="profile-modal-info">
                    <h2>{{ $userName ?? 'Driver' }}</h2>
                    <p class="profile-role">Bus Driver</p>
                    <div class="profile-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span>4.8/5.0</span>
                    </div>
                </div>
            </div>
            <div class="profile-modal-body">
                <div class="profile-section">
                    <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                    <div class="profile-details-grid">
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
                            <span class="detail-value">{{ $todayTrip?->driver?->phone ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="detail-value">{{ auth()->user()?->email ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-bus"></i> Bus Number</span>
                            <span class="detail-value">{{ $todayTrip?->bus?->bus_number ? 'Bus #'.$todayTrip->bus->bus_number : '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-route"></i> Route</span>
                            <span class="detail-value">{{ $todayTrip?->route?->name ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-id-badge"></i> License</span>
                            <span class="detail-value">{{ $todayTrip?->driver?->license_number ?? '—' }}</span>
                        </div>
                        <div class="profile-detail-item">
                            <span class="detail-label"><i class="fas fa-briefcase"></i> Experience</span>
                            <span class="detail-value">{{ $todayTrip?->driver?->experience ? $todayTrip->driver->experience.' Years' : '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="profile-section">
                    <h3><i class="fas fa-chart-line"></i> Performance Stats</h3>
                    <div class="profile-stats-grid">
                        <div class="profile-stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h4>98%</h4>
                                <p>On-Time Rate</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $todayTrip?->attendance?->count() ?? 0 }}</h4>
                                <p>Students Served</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h4>{{ $stats['trips_completed'] ?? 0 }}</h4>
                                <p>Trips Completed</p>
                            </div>
                        </div>
                        <div class="profile-stat-card">
                            <div class="stat-icon purple">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h4>100%</h4>
                                <p>Safety Record</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile-modal-footer">
                <button class="btn-profile-primary" onclick="alert('Contact feature coming soon!')">
                    <i class="fas fa-phone"></i> Contact Driver
                </button>
                <button class="btn-profile-secondary" onclick="window.location.href='{{ url('/driver/request') }}'">
                    <i class="fas fa-file-lines"></i> Driver Requests
                </button>
                <button class="btn-profile-secondary" onclick="window.location.href='{{ url('/driver/applications') }}'">
                    <i class="fas fa-folder-open"></i> My Applications
                </button>
                <button class="btn-profile-secondary" onclick="closeDriverProfileModal()">
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

        .profile-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .profile-rating i {
            color: #fbbf24;
            font-size: 18px;
        }

        .profile-rating span {
            font-weight: 600;
            margin-left: 4px;
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

    <script src="{{ asset('js/dashboard-theme.js') }}"></script>
    <script src="{{ asset('js/driver.js') }}"></script>
    <script src="{{ asset('js/driver-api.js') }}"></script>
    <script src="{{ asset('js/i18n-driver.js') }}"></script>
    <script>
        function openDriverProfileModal() {
            const modal = document.getElementById('driverProfileModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDriverProfileModal() {
            const modal = document.getElementById('driverProfileModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDriverProfileModal();
            }
        });
    </script>

    <script>
    window.__DRIVER_DATA = {
        todayTrip: @json($todayTrip),
        trips: @json($trips),
        applications: @json($applications),
        stats: @json($stats)
    };
    (function(){
        // Populate students grid
        const studentsGrid = document.getElementById('studentsGrid');
        const attendanceList = window.__DRIVER_DATA.todayTrip?.attendance || [];
        if(studentsGrid && attendanceList.length){
            studentsGrid.innerHTML = attendanceList.map(a => {
                const student = a.student || {};
                const statusClass = a.status === 'present' ? 'present' : (a.status === 'absent' ? 'absent' : 'pending');
                return `
                <div class="student-card ${statusClass}" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px;display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#0ea5a4,#2563eb);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">${(student.full_name || 'S').charAt(0)}</div>
                    <div style="flex:1;">
                        <strong style="color:#1e293b;font-size:14px;">${student.full_name || 'Student'}</strong>
                        <div style="color:#64748b;font-size:12px;">Status: <span style="text-transform:capitalize;">${a.status}</span></div>
                    </div>
                    <span style="background:${a.status==='present'?'rgba(34,197,94,.15)':a.status==='absent'?'rgba(239,68,68,.15)':'rgba(99,102,241,.15)'};color:${a.status==='present'?'#4ade80':a.status==='absent'?'#f87171':'#a5b4fc'};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;">${a.status}</span>
                </div>`;
            }).join('');
            // Update summary counts
            const presentCount = attendanceList.filter(a=>a.status==='present').length;
            const absentCount = attendanceList.filter(a=>a.status==='absent').length;
            const presentEl = document.getElementById('summaryPresent');
            const absentEl = document.getElementById('summaryAbsent');
            if(presentEl) presentEl.textContent = presentCount;
            if(absentEl) absentEl.textContent = absentCount;
        } else if(studentsGrid){
            studentsGrid.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>No students assigned for today.</div>';
        }

        // Populate attendance table (today trip page)
        const attendanceBody = document.getElementById('attendanceTableBody');
        if(attendanceBody && attendanceList.length){
            attendanceBody.innerHTML = attendanceList.map(a => {
                const student = a.student || {};
                const pickup = a.picked_up_at ? new Date(a.picked_up_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) : '—';
                const dropoff = a.dropped_off_at ? new Date(a.dropped_off_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) : '—';
                return `<tr>
                    <td>${student.full_name || '—'}</td>
                    <td>${pickup}</td>
                    <td>${dropoff}</td>
                    <td><span style="background:${a.status==='present'?'rgba(34,197,94,.15)':a.status==='absent'?'rgba(239,68,68,.15)':'rgba(99,102,241,.15)'};color:${a.status==='present'?'#4ade80':a.status==='absent'?'#f87171':'#a5b4fc'};padding:2px 8px;border-radius:4px;font-size:12px;text-transform:uppercase;">${a.status}</span></td>
                </tr>`;
            }).join('');
        } else if(attendanceBody){
            attendanceBody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:24px;color:#94a3b8;">No attendance records for today.</td></tr>';
        }

        // Populate trip history
        const tripHistoryBody = document.getElementById('tripHistoryBody');
        const trips = window.__DRIVER_DATA.trips || [];
        if(tripHistoryBody && trips.length){
            tripHistoryBody.innerHTML = trips.map(t => {
                const date = t.trip_date ? new Date(t.trip_date).toLocaleDateString() : '—';
                const routeName = t.route?.name || '—';
                const type = t.shift || 'morning';
                const duration = '—';
                const students = t.attendance_count || 0;
                const statusBadge = t.status === 'completed' ? '<span style="background:rgba(34,197,94,.15);color:#4ade80;padding:2px 8px;border-radius:4px;font-size:12px;">Completed</span>' :
                    t.status === 'active' ? '<span style="background:rgba(59,130,246,.15);color:#60a5fa;padding:2px 8px;border-radius:4px;font-size:12px;">Active</span>' :
                    '<span style="background:rgba(99,102,241,.15);color:#a5b4fc;padding:2px 8px;border-radius:4px;font-size:12px;">' + (t.status || 'Pending') + '</span>';
                return `<tr>
                    <td>${date}</td>
                    <td>${routeName}</td>
                    <td>${type}</td>
                    <td>${duration}</td>
                    <td>${students}</td>
                    <td>${statusBadge}</td>
                </tr>`;
            }).join('');
        } else if(tripHistoryBody){
            tripHistoryBody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">No trip history found.</td></tr>';
        }
    })();
    </script>
    <script src="{{ asset('js/ajax-forms.js') }}"></script>
    <script>
    (function(){
        const onboardingForm = document.getElementById('driverOnboardingForm');
        if (onboardingForm) {
            onboardingForm.addEventListener('submit', async event => {
                event.preventDefault();

                const message = document.getElementById('driverOnboardingMessage');
                const submit = document.getElementById('driverOnboardingSubmit');
                const formData = new FormData(onboardingForm);

                message.style.display = 'none';
                submit.disabled = true;
                submit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                try {
                    const res = await fetch('/api/driver/details/submit', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer {{ $apiToken ?? '' }}',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: formData
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'Unable to submit details.');
                    message.textContent = data.message || 'Driver profile details submitted successfully.';
                    message.style.background = 'rgba(34,197,94,.12)';
                    message.style.color = '#15803d';
                    message.style.display = 'block';

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    message.textContent = error.message;
                    message.style.background = 'rgba(239,68,68,.12)';
                    message.style.color = '#b91c1c';
                    message.style.display = 'block';
                    submit.disabled = false;
                    submit.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Details';
                }
            });
        }
    })();

    document.addEventListener('spa:pageChanged', (e) => {
        const pageId = e.detail.pageId;
        console.log('[Driver SPA] Page changed to:', pageId);
        
        // Handle any driver-specific page init here
        if (pageId === 'trip') {
            // init trip logic
        } else if (pageId === 'route') {
            if (typeof initDriverGpsMap === 'function') initDriverGpsMap();
        }
    });
    </script>
</body>
</html>
