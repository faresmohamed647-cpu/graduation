<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SCHOOL BUS TRACKING</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('img/icon.jpg') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
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
    <link href="{{ asset('css/price.css') }}" rel="stylesheet">
    <style>
        /* ===== BASE STYLES — Pricing Calculator ===== */
        .pricing-offers-card, .pricing-calc-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            height: 100%;
            transition: all 0.3s ease;
        }
        .pricing-icon-box {
            width: 56px; height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
        }
        .pricing-field-input {
            border-radius: 12px; padding: 0.75rem 1rem; border: 2px solid #e9ecef;
        }
        .pricing-plan-option {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 12px;
            cursor: pointer; transition: all 0.3s;
        }
        .pricing-plan-option.active {
            border-color: #1d4ed8; background: rgba(29,78,216,0.05);
        }
        .pricing-payment-btn {
            flex: 1; text-align: center; padding: 0.6rem;
            border: 2px solid #e9ecef; border-radius: 12px;
            cursor: pointer; font-size: 0.85rem; font-weight: 600;
            color: #6c757d; transition: all 0.3s;
        }
        .pricing-payment-btn.active {
            border-color: #1d4ed8; background: #1d4ed8; color: #fff;
        }

        /* ===== DARK MODE — Pricing Calculator ===== */
        [data-theme="dark"] .pricing-offers-card,
        [data-theme="dark"] .pricing-calc-card {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2) !important;
            color: #e2e8f0 !important;
        }
        [data-theme="dark"] .pricing-offers-card h4,
        [data-theme="dark"] .pricing-calc-card h4 {
            color: #f1f5f9 !important;
        }
        [data-theme="dark"] .pricing-calc-card p {
            color: #94a3b8 !important;
        }
        [data-theme="dark"] .pricing-offers-list li {
            border-bottom-color: #334155 !important;
        }
        [data-theme="dark"] .pricing-field-input {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        [data-theme="dark"] .pricing-plan-option {
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        [data-theme="dark"] .pricing-plan-option.active {
            background: rgba(59, 130, 246, 0.1) !important;
            border-color: #3b82f6 !important;
        }
        [data-theme="dark"] .pricing-payment-btn {
            background: transparent !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        [data-theme="dark"] .pricing-payment-btn.active {
            background: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #fff !important;
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
        <a href="index.html" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
            <h2 class="mb-2 text-white">SAFESTEP BUS</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html" class="nav-item nav-link">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="service.html" class="nav-item nav-link">Services</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-up m-0">
                        <a href="price.html" class="dropdown-item active">Pricing Plan</a>
                        <a href="feature.html" class="dropdown-item">Features</a>
                        <a href="quote.html" class="dropdown-item">Free Quote</a>
                        <a href="/how-it-works" class="dropdown-item">How It Works</a>
                        <a href="/faq" class="dropdown-item">FAQ</a>
                        <a href="/tracking" class="dropdown-item">Live Tracking</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
            </div>
            <h4 class="m-0 pe-lg-5 d-none d-lg-block"><i class="fa fa-headphones text-primary me-3"></i>+20 3 123 4567</h4>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5" style="margin-bottom: 6rem;">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Pricing</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Pricing</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Pricing Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">NEW � Direct for Families</h6>
                <h1 class="mb-5">Simple Pricing for Families</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="price-item">
                        <div class="border-bottom p-4 mb-4">
                            <h5 class="text-primary mb-1">Smart Plan</h5>
                            <h1 class="display-5 mb-0">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;"></small>450<small
                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">EGP / month / per childh</small>
                            </h1>
                        </div>
                        <div class="p-4 pt-0">
                            <p><i class="fa fa-check text-success me-3"></i>Live GPS tracking on map</p>
                            <p><i class="fa fa-check text-success me-3"></i>Pickup & drop-off notifications</p>
                            <p><i class="fa fa-check text-success me-3"></i>Direct chat with driver</p>
                            <p><i class="fa fa-check text-success me-3"></i>Monthly trip reports</p>
                            <p><i class="fa fa-check text-success me-3"></i>Daily school-time support</p>
                            <a class="btn-slide mt-2" href="pay.html"><i class="fa fa-arrow-right"></i><span >Order Now</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="price-item">
                        <div class="border-bottom p-4 mb-4">
                            <h5 class="text-primary mb-1">Premium Plan</h5>
                            <h1 class="display-5 mb-0">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;"></small>650<small
                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">EGP / month / per childh</small>
                            </h1>
                        </div>
                        <div class="p-4 pt-0">
                            <p><i class="fa fa-check text-success me-3"></i>All Smart Plan features</p>
                            <p><i class="fa fa-check text-success me-3"></i>Priority customer support</p>
                            <p><i class="fa fa-check text-success me-3"></i>Arrival time sharing with family</p>
                            <p><i class="fa fa-check text-success me-3"></i>Unlimited parent accounts</p>
                            <p><i class="fa fa-check text-success me-3"></i>Driver rating & feedback</p>
                            <p><i class="fa fa-check text-success me-3"></i>Smart route optimization</p>
                            <a class="btn-slide mt-2" href="pay.html"><i class="fa fa-arrow-right"></i><span >7 Days Free Trial</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="price-item">
                        <div class="border-bottom p-4 mb-4">
                            <h5 class="text-primary mb-1">Private VIP</h5>
                            <h1 class="display-5 mb-0">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;"></small>950<small
                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">EGP / month / per childh</small>
                            </h1>
                        </div>
                        <div class="p-4 pt-0">
                            <p><i class="fa fa-check text-success me-3"></i>Private vehicle (6-8 children)</p>
                            <p><i class="fa fa-check text-success me-3"></i>Dedicated driver</p>
                            <p><i class="fa fa-check text-success me-3"></i>Door-to-door escort service</p>
                            <p><i class="fa fa-check text-success me-3"></i>Home to classroom supervision</p>
                            <p><i class="fa fa-check text-success me-3"></i>Live monitoring all hours</p>
                            <p><i class="fa fa-check text-success me-3"></i>Phone & WhatsApp support</p>
                            <a class="btn-slide mt-2" href="pay.html"><i class="fa fa-arrow-right"></i><span >Order Now</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pricing End -->
        <!-- Family Offers & Calculator Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Family Offers Card -->
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="pricing-offers-card">
                        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                            <div class="pricing-icon-box">
                                <i class="fa fa-gift" style="font-size:1.5rem;color:#fff;"></i>
                            </div>
                            <h4 style="margin:0;">Family Offers</h4>
                        </div>
                        <ul class="pricing-offers-list" style="list-style:none;padding:0;margin:0;">
                            <li style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #f1f3f5;"><i class="fa fa-check-circle" style="color:#059669;"></i> 7-day free trial for every family</li>
                            <li style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #f1f3f5;"><i class="fa fa-check-circle" style="color:#059669;"></i> 20% discount for 3 children or more</li>
                            <li style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #f1f3f5;"><i class="fa fa-check-circle" style="color:#059669;"></i> 25% discount on yearly payment</li>
                            <li style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #f1f3f5;"><i class="fa fa-check-circle" style="color:#059669;"></i> 10% discount on quarterly payment</li>
                            <li style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 0;"><i class="fa fa-check-circle" style="color:#059669;"></i> Priority support for VIP families</li>
                        </ul>
                    </div>
                </div>
                <!-- Savings Calculator -->
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="pricing-calc-card">
                        <div class="pricing-calc-header" style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                            <div class="pricing-icon-box">
                                <i class="fa fa-calculator" style="font-size:1.5rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h4 style="margin:0 0 0.25rem;">Family Savings Calculator</h4>
                                <p style="margin:0;color:#6c757d;font-size:0.9rem;">See how much you can save monthly by switching to our school plans.</p>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-5">
                                <div id="calcResultBox" style="background:linear-gradient(135deg,#0f172a,#1e3a8a,#1d4ed8);border-radius:16px;padding:2rem;color:#fff;text-align:center;height:100%;">
                                    <div style="font-size:0.85rem;opacity:0.8;margin-bottom:0.5rem;">Estimated Monthly Savings</div>
                                    <div id="saving" style="font-size:2.5rem;font-weight:800;margin-bottom:1rem;">0 EGP</div>
                                    <div style="background:rgba(255,255,255,0.1);border-radius:12px;padding:1rem;margin-bottom:1rem;">
                                        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;font-size:0.85rem;"><span>Current Cost</span><strong id="currentTotal">0 EGP</strong></div>
                                        <div style="display:flex;justify-content:space-between;font-size:0.85rem;"><span>SafeStep Cost</span><strong id="schoolTotal">0 EGP</strong></div>
                                    </div>
                                    <ul style="list-style:none;padding:0;margin:0;text-align:left;font-size:0.8rem;">
                                        <li style="margin-bottom:0.4rem;"><i class="fa fa-check" style="color:#4ade80;margin-right:0.5rem;"></i>Dedicated family support</li>
                                        <li style="margin-bottom:0.4rem;"><i class="fa fa-check" style="color:#4ade80;margin-right:0.5rem;"></i>Real-time tracking</li>
                                        <li><i class="fa fa-check" style="color:#4ade80;margin-right:0.5rem;"></i>Cancel anytime</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div style="display:flex;flex-direction:column;gap:1rem;">
                                    <div>
                                        <label style="font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;display:block;">Number of Children</label>
                                        <input type="number" class="form-control pricing-field-input" id="children" min="1" max="6" value="1">
                                    </div>
                                    <div>
                                        <label style="font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;display:block;">Current Cost per Child (Monthly)</label>
                                        <input type="number" class="form-control pricing-field-input" id="currentCost" value="900">
                                    </div>
                                    <div>
                                        <label style="font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;display:block;">Select Plan</label>
                                        <div style="display:flex;flex-direction:column;gap:0.5rem;" id="planOptions">
                                            <label class="pricing-plan-option active">
                                                <div style="display:flex;align-items:center;gap:0.5rem;"><input type="radio" name="plan" value="450" checked style="accent-color:#1d4ed8;"> <span>Basic Plan</span></div>
                                                <strong style="color:#1d4ed8;">450 EGP</strong>
                                            </label>
                                            <label class="pricing-plan-option">
                                                <div style="display:flex;align-items:center;gap:0.5rem;"><input type="radio" name="plan" value="650" style="accent-color:#1d4ed8;"> <span>Premium Plan</span></div>
                                                <strong style="color:#1d4ed8;">650 EGP</strong>
                                            </label>
                                            <label class="pricing-plan-option">
                                                <div style="display:flex;align-items:center;gap:0.5rem;"><input type="radio" name="plan" value="950" style="accent-color:#1d4ed8;"> <span>VIP Plan</span></div>
                                                <strong style="color:#1d4ed8;">950 EGP</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label style="font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;display:block;">Payment Frequency</label>
                                        <div style="display:flex;gap:0.5rem;" id="paymentToggle">
                                            <label class="pricing-payment-btn active">
                                                <input type="radio" name="payment" value="monthly" checked style="display:none;"> Monthly
                                            </label>
                                            <label class="pricing-payment-btn">
                                                <input type="radio" name="payment" value="quarterly" style="display:none;"> Quarterly
                                            </label>
                                            <label class="pricing-payment-btn">
                                                <input type="radio" name="payment" value="yearly" style="display:none;"> Yearly
                                            </label>
                                        </div>
                                    </div>
                                    <a href="/pay" class="btn btn-primary w-100 py-3" style="border-radius:12px;font-size:1.1rem;"><i class="fa fa-shopping-cart me-2"></i>Order Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Family Offers & Calculator End -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function calcSavings() {
            var children = parseInt(document.getElementById('children').value) || 1;
            var currentCost = parseInt(document.getElementById('currentCost').value) || 0;
            var plan = parseInt(document.querySelector('input[name="plan"]:checked').value);
            var payment = document.querySelector('input[name="payment"]:checked').value;
            var discount = payment === 'yearly' ? 0.25 : (payment === 'quarterly' ? 0.10 : 0);
            if (children >= 3) discount += 0.20;
            var currentTotal = children * currentCost;
            var schoolCost = Math.round(children * plan * (1 - discount));
            var savings = Math.max(0, currentTotal - schoolCost);
            document.getElementById('saving').textContent = savings + ' EGP';
            document.getElementById('currentTotal').textContent = currentTotal + ' EGP';
            document.getElementById('schoolTotal').textContent = schoolCost + ' EGP';
        }
        document.querySelectorAll('#children, #currentCost').forEach(function(el) { el.addEventListener('input', calcSavings); });
        
        document.querySelectorAll('input[name="plan"]').forEach(function(el) {
            el.addEventListener('change', function() {
                document.querySelectorAll('#planOptions label').forEach(function(l) { l.classList.remove('active'); });
                this.closest('label').classList.add('active');
                calcSavings();
            });
        });
        
        document.querySelectorAll('input[name="payment"]').forEach(function(el) {
            el.addEventListener('change', function() {
                document.querySelectorAll('#paymentToggle label').forEach(function(l) { l.classList.remove('active'); });
                this.closest('label').classList.add('active');
                calcSavings();
            });
        });
        
        calcSavings();
    });
    </script>


    <!-- Quote Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="text-secondary text-uppercase mb-3">Track Your School Bus</h6>
                    <h1 class="mb-5">Live Location & Trip Replay!</h1>
                    <p class="mb-5">Follow the school bus in real time, review past trips, and ensure your child's safety with smart tracking technology</p>
                    <div class="d-flex align-items-center">
                        <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary p-3 text-white"></i>
                        <div class="ps-4">
                            <h6>Call for any query!</h6>
                            <h3 class="text-primary m-0">+20 3 123 4567</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="bg-light text-center p-5 wow fadeIn" data-wow-delay="0.5s">
                        <form class="ajax-form">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" placeholder="Your Name" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="form-control border-0" placeholder="Your Email" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" placeholder="Your Mobile" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <select class="form-select border-0" style="height: 55px;">
                                        <option selected>Select Service Type</option>
                                        <option value="1">Live Bus Tracking</option>
                                        <option value="2">Trip History</option>
                                        <option value="3">Trip Replay</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control border-0" placeholder="Special Note"></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quote End -->
        

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
                    <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
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
<script src="{{ asset('js/ajax-forms.js') }}"></script>

</body>

</html>

