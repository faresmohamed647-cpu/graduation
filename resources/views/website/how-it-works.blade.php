<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>How It Works - SAFESTEP BUS</title>
      <!-- علشان التابلت والموبيل-->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- مكان كلمات البحث والوصف -->
    <meta content="how it works, safestep bus, school bus tracking, gps setup" name="keywords">
    <meta content="Learn how SafeStep Bus works - from setup to live tracking in 4 simple steps" name="description">

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

    <style>
        /* Step Cards */
        .step-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        .step-number {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FE5D14, #ff8c42);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .step-number span {
            font-size: 2rem;
            font-weight: 800;
            color: white;
        }
        .step-icon {
            width: 56px;
            height: 56px;
            background: rgba(254, 93, 20, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .step-icon i {
            font-size: 1.5rem;
            color: #FE5D14;
        }
        .step-details-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0 0;
        }
        .step-details-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .step-details-list li i {
            color: #28a745;
            font-size: 0.85rem;
        }

        /* Timeline */
        .timeline-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .timeline-item {
            text-align: center;
            padding: 1rem;
        }
        .timeline-num {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(254, 93, 20, 0.15), rgba(255, 140, 66, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .timeline-num span {
            font-size: 1.75rem;
            font-weight: 800;
            color: #FE5D14;
        }
        .timeline-day {
            color: #FE5D14;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .timeline-task {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* System Flow */
        .flow-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: transform 0.3s;
            height: 100%;
        }
        .flow-card:hover {
            transform: translateY(-5px);
        }
        .flow-num {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(254, 93, 20, 0.15), rgba(255, 140, 66, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .flow-num span {
            font-size: 1.75rem;
            font-weight: 800;
            color: #FE5D14;
        }

        /* CTA */
        .hiw-cta {
            background: linear-gradient(135deg, #FE5D14, #ff8c42);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            color: white;
        }
        .hiw-cta h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .hiw-cta p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .hiw-cta .btn-white {
            background: white;
            color: #FE5D14;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s;
            margin: 0 0.5rem;
        }
        .hiw-cta .btn-white:hover {
            transform: translateY(-2px);
        }

        /* Arrow connector */
        .step-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #FE5D14;
        }
        @media (max-width: 768px) {
            .step-arrow { transform: rotate(90deg); margin: 1rem auto; }
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
                        <a href="/how-it-works" class="dropdown-item active">How It Works</a>
                        <a href="/faq" class="dropdown-item">FAQ</a>
                        <a href="/tracking" class="dropdown-item">Live Tracking</a>
                    </div>
                </div>
                <a href="/contact" class="nav-item nav-link">Contact</a>
            </div>
            <h4 class="m-0 pe-lg-5 d-none d-lg-block"><i class="fa fa-headphones text-primary me-3"></i>+20 3 123 4567</h4>
            <button type="button" class="dark-mode-toggle me-4" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5" style="margin-bottom: 6rem;">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">How It Works</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-white" href="/">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">How It Works</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Steps Section Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">Getting Started</h6>
                <h1 class="mb-5">4 Simple Steps to Safe School Transport</h1>
            </div>

            <div class="row g-4">
                <!-- Step 1 -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="step-card">
                        <div class="step-number"><span>01</span></div>
                        <div class="step-icon"><i class="fa fa-user-plus"></i></div>
                        <h4>Sign Up & Setup</h4>
                        <p class="text-muted">Create your account and add your school information, buses, routes, and drivers in minutes.</p>
                        <ul class="step-details-list">
                            <li><i class="fa fa-check-circle"></i> Quick registration process</li>
                            <li><i class="fa fa-check-circle"></i> Import existing data via CSV</li>
                            <li><i class="fa fa-check-circle"></i> Intuitive setup wizard</li>
                            <li><i class="fa fa-check-circle"></i> Guided onboarding</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="step-card">
                        <div class="step-number"><span>02</span></div>
                        <div class="step-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <h4>Install GPS Devices</h4>
                        <p class="text-muted">Install our GPS tracking devices in your buses. We provide full installation support.</p>
                        <ul class="step-details-list">
                            <li><i class="fa fa-check-circle"></i> Plug-and-play GPS devices</li>
                            <li><i class="fa fa-check-circle"></i> Professional installation available</li>
                            <li><i class="fa fa-check-circle"></i> Real-time connectivity test</li>
                            <li><i class="fa fa-check-circle"></i> Backup power included</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="step-card">
                        <div class="step-number"><span>03</span></div>
                        <div class="step-icon"><i class="fa fa-cogs"></i></div>
                        <h4>Configure & Customize</h4>
                        <p class="text-muted">Set up routes, schedules, geofences, and notification preferences for your specific needs.</p>
                        <ul class="step-details-list">
                            <li><i class="fa fa-check-circle"></i> Drag-and-drop route builder</li>
                            <li><i class="fa fa-check-circle"></i> Custom notification rules</li>
                            <li><i class="fa fa-check-circle"></i> Geofence setup</li>
                            <li><i class="fa fa-check-circle"></i> Schedule automation</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="step-card">
                        <div class="step-number"><span>04</span></div>
                        <div class="step-icon"><i class="fa fa-satellite-dish"></i></div>
                        <h4>Go Live & Monitor</h4>
                        <p class="text-muted">Start tracking your buses in real-time. Monitor all operations from your dashboard.</p>
                        <ul class="step-details-list">
                            <li><i class="fa fa-check-circle"></i> Real-time bus tracking</li>
                            <li><i class="fa fa-check-circle"></i> Live notifications</li>
                            <li><i class="fa fa-check-circle"></i> Parent app access</li>
                            <li><i class="fa fa-check-circle"></i> 24/7 monitoring</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Steps Section End -->


    <!-- Implementation Timeline Start -->
    <div class="container-xxl py-5 hiw-timeline-section" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">Timeline</h6>
                <h1 class="mb-5">Implementation Timeline</h1>
            </div>
            <div class="timeline-card wow fadeInUp" data-wow-delay="0.3s">
                <div class="row g-4">
                    <div class="col-md-3 col-6">
                        <div class="timeline-item">
                            <div class="timeline-num"><span>1</span></div>
                            <div class="timeline-day">Day 1</div>
                            <div class="timeline-task">Account Setup & Training</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="timeline-item">
                            <div class="timeline-num"><span>2</span></div>
                            <div class="timeline-day">Day 2-3</div>
                            <div class="timeline-task">GPS Installation</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="timeline-item">
                            <div class="timeline-num"><span>3</span></div>
                            <div class="timeline-day">Day 4-5</div>
                            <div class="timeline-task">Route Configuration</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="timeline-item">
                            <div class="timeline-num"><span>4</span></div>
                            <div class="timeline-day">Day 6+</div>
                            <div class="timeline-task">Live Operation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Implementation Timeline End -->


    <!-- System Flow Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">System Flow</h6>
                <h1 class="mb-5">How Data Flows Through SafeStep</h1>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="flow-card">
                        <div class="flow-num"><span>1</span></div>
                        <h4>GPS Device</h4>
                        <p class="text-muted">Installed in bus, sends real-time location data every 10 seconds via cellular network</p>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="flow-card">
                        <div class="flow-num"><span>2</span></div>
                        <h4>Cloud Platform</h4>
                        <p class="text-muted">Processes data, applies rules, sends notifications, stores history securely</p>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="flow-card">
                        <div class="flow-num"><span>3</span></div>
                        <h4>User Devices</h4>
                        <p class="text-muted">Parents, drivers, and admins access real-time data via web and mobile apps</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- System Flow End -->


    <!-- CTA Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="hiw-cta wow fadeInUp" data-wow-delay="0.1s">
                <h3>Ready to Get Started?</h3>
                <p>Join hundreds of schools already using SafeStep Bus for safe school transportation</p>
                <a href="/apply/parent" class="btn-white">Start Free Trial</a>
                <a href="/faq" class="btn-white">View FAQ</a>
            </div>
        </div>
    </div>
    <!-- CTA End -->
        

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

<!-- Language Support -->
<script src='{{ asset('js/language.js') }}'></script>

</body>

</html>
