<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Live Tracking - SAFESTEP BUS</title>
      <!-- علشان التابلت والموبيل-->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- مكان كلمات البحث والوصف -->
    <meta content="live tracking, gps, school bus, safestep bus, real-time" name="keywords">
    <meta content="Live GPS tracking for all SafeStep school buses in real-time" name="description">

    <!-- الايقونة الي فوق -->
    <link href="{{ asset('img/icon.jpg') }}" rel="icon">

    <!--تحميل خطوط Inter و Roboto preconnect يسرّع التحميل   -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!--الخطوط والايقونات الصغيرة  -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/website-responsive.css') }}" rel="stylesheet">

    <style>
        /* Stats */
        .tracking-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-box {
            padding: 1.5rem;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-box:hover {
            transform: translateY(-3px);
        }
        .stat-box .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .stat-box .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Live Badge */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: #16a34a;
            font-size: 0.875rem;
        }
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        @keyframes ping {
            75%, 100% { transform: scale(2); opacity: 0; }
        }

        /* Map */
        .map-container {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .map-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .map-view {
            position: relative;
            height: 520px;
            background: linear-gradient(135deg, rgba(254, 93, 20, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        .map-grid {
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(0deg, rgba(0,0,0,0.03) 0px, transparent 1px, transparent 40px, rgba(0,0,0,0.03) 41px),
                              repeating-linear-gradient(90deg, rgba(0,0,0,0.03) 0px, transparent 1px, transparent 40px, rgba(0,0,0,0.03) 41px);
        }

        /* Bus Markers */
        .map-bus-marker {
            position: absolute;
            width: 36px;
            height: 36px;
            background: #FE5D14;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(254, 93, 20, 0.4);
            z-index: 10;
        }
        .map-bus-marker::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: #FE5D14;
            opacity: 0.3;
            animation: ping 2s infinite;
        }
        .map-bus-marker.selected {
            background: #1d4ed8;
            box-shadow: 0 4px 20px rgba(29, 78, 216, 0.6);
            transform: translate(-50%, -50%) scale(1.3);
            z-index: 20;
        }
        .map-bus-marker.idle {
            background: #f59e0b;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }
        .map-bus-marker i {
            font-size: 0.9rem;
            color: white;
            position: relative;
            z-index: 1;
        }

        /* Tooltip */
        .marker-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            color: white;
        }
        .map-bus-marker:hover .marker-tooltip {
            opacity: 1;
        }
        .tooltip-title {
            font-size: 0.75rem;
            font-weight: 600;
        }
        .tooltip-speed {
            font-size: 0.65rem;
            color: #94a3b8;
        }

        /* Bus List */
        .buses-list {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            padding: 1.5rem;
            max-height: 650px;
            overflow-y: auto;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .bus-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .bus-card:hover {
            background: #f1f3f5;
        }
        .bus-card.selected {
            border-color: #1d4ed8;
            background: rgba(29, 78, 216, 0.05);
        }
        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        .bus-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .status-dot.active { background: #22c55e; animation: pulse 2s infinite; }
        .status-dot.idle { background: #f59e0b; }
        .bus-name { font-weight: 600; font-size: 0.9rem; }
        .bus-id { font-size: 0.75rem; color: #6c757d; }
        .bus-eta { font-size: 0.875rem; font-weight: 600; color: #FE5D14; }
        .bus-details {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .detail-item i { font-size: 0.8rem; color: #FE5D14; }
        .bus-driver {
            font-size: 0.8rem;
            color: #6c757d;
            padding-top: 0.75rem;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 1024px) {
            .tracking-grid { grid-template-columns: 1fr !important; }
            .buses-list { max-height: 400px; }
        }
        @media (max-width: 768px) {
            .tracking-stats { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow border-top border-5 border-primary sticky-top p-0">
        <a href="/" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
            <h2 class="mb-2 text-white">SAFESTEP BUS</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="/" class="nav-item nav-link">Home</a>
                <a href="/about" class="nav-item nav-link">About</a>
                <a href="/service" class="nav-item nav-link">Services</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-up m-0">
                        <a href="/price" class="dropdown-item">Pricing Plan</a>
                        <a href="/feature" class="dropdown-item">Features</a>
                        <a href="/quote" class="dropdown-item">Free Quote</a>
                        <a href="/how-it-works" class="dropdown-item">How It Works</a>
                        <a href="/faq" class="dropdown-item">FAQ</a>
                        <a href="/tracking" class="dropdown-item active">Live Tracking</a>
                    </div>
                </div>
                <a href="/contact" class="nav-item nav-link">Contact</a>
            </div>
            <h4 class="m-0 pe-lg-5 d-none d-lg-block"><i class="fa fa-headphones text-primary me-3"></i>+20 3 123 4567</h4>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5" style="margin-bottom: 6rem;">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Live Tracking</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-white" href="/">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Live Tracking</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Tracking Content Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h6 class="text-secondary text-uppercase">Real-Time Monitoring</h6>
                    <h2>Live Bus Tracking Dashboard</h2>
                </div>
                <div class="live-badge">
                    <span class="live-dot"></span>
                    <span>Live</span>
                </div>
            </div>

            <!-- Stats -->
            <div class="tracking-stats">
                <div class="stat-box wow fadeInUp" data-wow-delay="0.1s">
                    <div class="stat-value" style="color: #22c55e;" id="activeBuses">0</div>
                    <div class="stat-label">Active Buses</div>
                </div>
                <div class="stat-box wow fadeInUp" data-wow-delay="0.2s">
                    <div class="stat-value" style="color: #3b82f6;" id="totalStudents">0</div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-box wow fadeInUp" data-wow-delay="0.3s">
                    <div class="stat-value" style="color: #FE5D14;" id="avgSpeed">0</div>
                    <div class="stat-label">Avg Speed</div>
                </div>
                <div class="stat-box wow fadeInUp" data-wow-delay="0.4s">
                    <div class="stat-value" style="color: #eab308;" id="onTime">0</div>
                    <div class="stat-label">On Time</div>
                </div>
            </div>

            <!-- Tracking Grid -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 3rem;" class="tracking-grid">
                <!-- Map View -->
                <div class="map-container wow fadeInUp" data-wow-delay="0.1s">
                    <div class="map-header">
                        <h5 class="mb-0"><i class="fa fa-map me-2 text-primary"></i>Live Map</h5>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-sm btn-outline-primary">Satellite</button>
                            <button class="btn btn-sm btn-outline-primary">Traffic</button>
                        </div>
                    </div>
                    <div class="map-view" id="busMap">
                        <div class="map-grid"></div>
                        <!-- Bus markers will be added by JavaScript -->
                    </div>
                </div>

                <!-- Bus List -->
                <div class="buses-list wow fadeInUp" data-wow-delay="0.3s">
                    <h5 class="mb-3"><i class="fa fa-bus me-2 text-primary"></i>Active Buses</h5>
                    <div id="busList">
                        <!-- Bus cards will be added by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tracking Content End -->
        

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 wow fadeIn" data-wow-delay="0.1s" style="margin-top: 6rem;">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Address</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Alexandria, Egypt</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+20 3 123 4567</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@safestep.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Services</h4>
                    <a class="btn btn-link" href="">Live Bus Tracking</a>
                    <a class="btn btn-link" href="">Trip History</a>
                    <a class="btn btn-link" href="">Route Replay</a>
                    <a class="btn btn-link" href="">Arrival & Departure Alerts</a>
                    <a class="btn btn-link" href="">School Fleet Monitoring</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Newsletter</h4>
                    <p>Stay updated with SafeStep Bus latest news and offers.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">SafeStep Bus</a>, All Right Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    
    @include('website.partials.ai-chat')

<!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- Tracking Logic -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample bus data for SafeStep
        const buses = [
            {
                id: 'BUS001',
                name: 'Bus 1 - Route A',
                route: 'Smouha → Victoria College',
                driver: 'Ahmed Hassan',
                students: 28,
                status: 'active',
                speed: 45,
                position: { lat: 30, lng: 25 },
                eta: '8 min'
            },
            {
                id: 'BUS002',
                name: 'Bus 2 - Route B',
                route: 'Mandara → British School',
                driver: 'Mohamed Ali',
                students: 32,
                status: 'active',
                speed: 38,
                position: { lat: 45, lng: 55 },
                eta: '12 min'
            },
            {
                id: 'BUS003',
                name: 'Bus 3 - Route C',
                route: 'Montaza → German School',
                driver: 'Omar Khaled',
                students: 25,
                status: 'active',
                speed: 42,
                position: { lat: 65, lng: 35 },
                eta: '5 min'
            },
            {
                id: 'BUS004',
                name: 'Bus 4 - Route D',
                route: 'Agami → International Academy',
                driver: 'Sara Mahmoud',
                students: 30,
                status: 'idle',
                speed: 0,
                position: { lat: 20, lng: 75 },
                eta: 'Stopped'
            },
            {
                id: 'BUS005',
                name: 'Bus 5 - Route E',
                route: 'Sidi Bishr → Alexandria School',
                driver: 'Nour Ibrahim',
                students: 22,
                status: 'active',
                speed: 50,
                position: { lat: 80, lng: 60 },
                eta: '15 min'
            }
        ];

        let selectedBusId = null;

        function initTracking() {
            renderBusList();
            renderBusMarkers();
            updateStats();
            setInterval(function() {
                updateBusPositions();
                renderBusMarkers();
                updateStats();
            }, 2000);
        }

        function renderBusList() {
            const busList = document.getElementById('busList');
            if (!busList) return;
            busList.innerHTML = buses.map(bus => `
                <div class="bus-card ${selectedBusId === bus.id ? 'selected' : ''}"
                     onclick="selectBus('${bus.id}')">
                    <div class="bus-header">
                        <div class="bus-info">
                            <span class="status-dot ${bus.status}"></span>
                            <div>
                                <div class="bus-name">${bus.name}</div>
                                <div class="bus-id">${bus.id}</div>
                            </div>
                        </div>
                        <div class="bus-eta">${bus.eta}</div>
                    </div>
                    <div class="bus-details">
                        <div class="detail-item">
                            <i class="fa fa-map-marker-alt"></i>
                            <span>${bus.route}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-users"></i>
                            <span>${bus.students} students</span>
                        </div>
                        ${bus.status === 'active' ? `
                            <div class="detail-item">
                                <i class="fa fa-tachometer-alt"></i>
                                <span>${Math.round(bus.speed)} km/h</span>
                            </div>
                        ` : ''}
                    </div>
                    <div class="bus-driver"><i class="fa fa-user me-1"></i> Driver: ${bus.driver}</div>
                </div>
            `).join('');
        }

        function renderBusMarkers() {
            const mapContainer = document.getElementById('busMap');
            if (!mapContainer) return;
            const existingMarkers = mapContainer.querySelectorAll('.map-bus-marker');
            existingMarkers.forEach(marker => marker.remove());

            buses.forEach(bus => {
                const marker = document.createElement('div');
                marker.className = `map-bus-marker ${bus.status} ${selectedBusId === bus.id ? 'selected' : ''}`;
                marker.style.left = bus.position.lng + '%';
                marker.style.top = bus.position.lat + '%';
                marker.setAttribute('data-bus-id', bus.id);
                marker.onclick = () => selectBus(bus.id);
                marker.innerHTML = `
                    <i class="fa fa-bus"></i>
                    <div class="marker-tooltip">
                        <div class="tooltip-title">${bus.name}</div>
                        <div class="tooltip-speed">${Math.round(bus.speed)} km/h</div>
                    </div>
                `;
                mapContainer.appendChild(marker);
            });
        }

        function updateBusPositions() {
            buses.forEach(bus => {
                if (bus.status === 'active') {
                    bus.position.lat = Math.min(95, Math.max(5, bus.position.lat + (Math.random() - 0.5) * 3));
                    bus.position.lng = Math.min(95, Math.max(5, bus.position.lng + (Math.random() - 0.5) * 3));
                    bus.speed = Math.max(30, Math.min(60, bus.speed + (Math.random() - 0.5) * 10));
                }
            });
        }

        window.selectBus = function(busId) {
            selectedBusId = busId;
            renderBusList();
            renderBusMarkers();
        };

        function updateStats() {
            const activeBuses = buses.filter(b => b.status === 'active').length;
            const totalStudents = buses.reduce((sum, b) => sum + b.students, 0);
            const avgSpeed = Math.round(
                buses.filter(b => b.status === 'active')
                    .reduce((sum, b) => sum + b.speed, 0) / activeBuses
            );
            const statsData = {
                'activeBuses': activeBuses,
                'totalStudents': totalStudents,
                'avgSpeed': avgSpeed + ' km/h',
                'onTime': '98%'
            };
            Object.keys(statsData).forEach(key => {
                const element = document.getElementById(key);
                if (element) element.textContent = statsData[key];
            });
        }

        initTracking();
    });
    </script>

<!-- Language Support -->
<script src='{{ asset('js/language.js') }}'></script>

</body>

</html>
