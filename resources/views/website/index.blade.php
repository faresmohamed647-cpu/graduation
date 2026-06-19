<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SCHOOL BUS TRACKING</title>
      <!-- Ø¹Ù„Ø´Ø§Ù† Ø§Ù„ØªØ§Ø¨Ù„Øª ÙˆØ§Ù„Ù…ÙˆØ¨ÙŠÙ„-->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Ù…ÙƒØ§Ù† ÙƒÙ„Ù…Ø§Øª Ø§Ù„Ø¨Ø­Ø« ÙˆØ§Ù„ÙˆØµÙ (Ù„Ø³Ù‡ ÙØ§Ø¶ÙŠÙŠÙ† Ù…Ø´ Ù…Ø´ØºÙ„Ù‡Ù… )-->
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Ø§Ù„Ø§ÙŠÙ‚ÙˆÙ†Ø© Ø§Ù„ÙŠ ÙÙˆÙ‚ -->
    <link href="{{ asset('img/icon.jpg') }}" rel="icon">

    <!--ØªØ­Ù…ÙŠÙ„ Ø®Ø·ÙˆØ· Inter Ùˆ Roboto preconnect ÙŠØ³Ø±Ù‘Ø¹ Ø§Ù„ØªØ­Ù…ÙŠÙ„   -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!--Ø§Ù„Ø®Ø·ÙˆØ· ÙˆØ§Ù„Ø§ÙŠÙ‚ÙˆÙ†Ø§Øª Ø§Ù„ØµØºÙŠØ±Ø©  -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/public-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/website-responsive.css') }}" rel="stylesheet">

    <!-- RTL Support Stylesheet -->
    <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
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
                <a href="/" class="nav-item nav-link active">Home</a>
                <a href="/about" class="nav-item nav-link">About</a>
                <a href="/service" class="nav-item nav-link">Services</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-up m-0">
                        <a href="/price" class="dropdown-item">Pricing Plan</a>
                        <a href="/feature" class="dropdown-item">Features</a>
                        <a href="/quote" class="dropdown-item">Free Quote</a>
                        <a href="/how-it-works" class="dropdown-item">How It Works</a>
                        <a href="/faq" class="dropdown-item">FAQ</a>
                        <a href="/tracking" class="dropdown-item">Live Tracking</a>
                    </div>
                </div>
                <a href="/contact" class="nav-item nav-link">Contact</a>
            </div>
            <div class="buttons">
                <a href="{{ url('/join') }}" class="btn-link join-btn" data-aos="fade-up" data-aos-duration="1200" data-aos-delay="500">Join SafeStep</a>
            </div>
            <div class="lang-wrapper">
    <a href="javascript:void(0)" class="order-btn lang-toggle">
        Language
    </a>

    <div class="lang-menu">
        <button onclick="setLanguage('en')">English</button>
        <button onclick="setLanguage('ar')">العربية</button>
    </div>
</div>
<button type="button" class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
    <i class="fas fa-moon"></i>
</button>
</div>


        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0 pb-5">
        <div class="owl-carousel header-carousel position-relative mb-5">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="{{ asset('img/pack1.jpg') }}" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(6, 3, 21, .5);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-10 col-lg-8">
                                <h5 class="text-white text-uppercase mb-3 animated slideInDown">Your ticket to peace of mind!</h5>
                                <h1 class="display-3 text-white animated slideInDown mb-4">#1 Place For Your <span class="text-primary">SAFTY</span> of your children</h1>
                                <p class="fs-5 fw-medium text-white mb-4 pb-2">Trusted by leading schools across multiple regions</p>
                                <a href="{{ url('/join') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Join SafeStep</a>
                                <a href="{{ url('/how-it-works') }}" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">How It Works</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="{{ asset('img/pack2.jpg') }}" alt="">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(6, 3, 21, .5);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-10 col-lg-8">
                                <h5 class="text-white text-uppercase mb-3 animated slideInDown">Your ticket to peace of mind!</h5>
                                <h1 class="display-3 text-white animated slideInDown mb-4">#1 Place For Your <span class="text-primary">Transport</span> Solution</h1>
                                <p class="fs-5 fw-medium text-white mb-4 pb-2">Parents can follow the school bus live, receive alerts, and feel confident that their children arrive safely and on time.</p>
                                <a href="{{ url('/about') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Read More</a>
                                <a href="{{ url('/quote') }}" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">Free Quote</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    <div class="container-fluid overflow-hidden py-5 px-lg-0">
        <div class="container about py-5 px-lg-0">
            <div class="row g-5 mx-lg-0">
                <div class="col-lg-6 ps-lg-0 wow fadeInLeft" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute img-fluid w-100 h-100" src="{{ asset('img/aboutnew.jpg') }}" style="object-fit: cover;" alt="">
                    </div>
                </div>
                <div class="col-lg-6 about-text wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="text-secondary text-uppercase mb-3">About Us</h6>
                    <h1 class="mb-5">Built for schools and parents. Designed for safety. Powered by simplicity.</h1>
                    <p class="mb-5">SAFESTEP BUS is a very useful mobile app to track the current whereabouts of the bus and get real-time updates. The app uses vehicle tracking system to create a real time tracking system so that parents and school authorities may locate and receive instant updates and notifications regarding the school bus trips. Friendly, easy to use and enormously helpful, it is a companion in your pocket.
Learn More</p>
                    <div class="row g-4 mb-5">
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.5s">
                            <i class="fa fa-user fa-3x text-primary mb-3"></i>
                            <h5>Parents</h5>
                            <p class="m-0">This is the app that suits the parent's use. SMS notifications or Online App notifications are sent instantly to parents.</p>
                        </div>
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.7s">
                            <i class="fa fa-shipping-fast fa-3x text-primary mb-3"></i>
                            <h5>Driver</h5>
                            <p class="m-0">This is the app created for the use of drivers. It is very easy to use and lets the driver instantly update the whereabouts.</p>
                        </div>
                    </div>
                    <a href="" class="btn btn-primary py-3 px-5">Explore More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Fact Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="text-secondary text-uppercase mb-3">Some Facts</h6>
                    <h1 class="mb-5">#1 Safe and Smart School Transportation - For Families & Schools</h1>
                    <p class="mb-5">Track your child's bus in real-time, receive arrival and departure notifications, and rest assured that every trip is under control.</p>
                    <div class="d-flex align-items-center">
                        <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary p-3 text-white"></i>
                        <div class="ps-4">
                            <h6>Call for any query!</h6>
                            <h3 class="text-primary m-0">+20 3 123 4567</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-6">
                            <div class="bg-primary p-4 mb-4 wow fadeIn" data-wow-delay="0.3s">
                                <i class="fa fa-users fa-2x text-white mb-3"></i>
                                <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                                <p class="text-white mb-0">Happy parents</p>
                            </div>
                            <div class="bg-secondary p-4 wow fadeIn" data-wow-delay="0.5s">
                                <i class="fa fa-bus fa-2x text-white mb-3"></i>
                                <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                                <p class="text-white mb-0">Happy driver</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-success p-4 wow fadeIn" data-wow-delay="0.7s">
                                <i class="fa fa-school fa-2x text-white mb-3"></i>
                                <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                                <p class="text-white mb-0">CReliability for schools</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">Our Services</h6>
                <h1 class="mb-5">Explore Our Services</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service1.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">Advanced Real-time Tracking</h4>
                        <p>Pricies real-time location with updates every second on an interactive map</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service2.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">Smart Notifications</h4>
                        <p>Instant alerts via SMS,email,and,app,for drivers and parents.</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service3.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">24/7 Surveillance Cameras</h4>
                        <p>High-quality recording inside the bus with secure storage and review capability.</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service4.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">Driver Background check</h4>
                        <p>All driver undergo comprehensive background checks and mandatory safety training.</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service5.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">Emergency Alert Sestem</h4>
                        <p>SOS button in the app with direct connection to support team and authorities.</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item p-4">
                        <div class="overflow-hidden mb-4">
                            <img class="img-fluid" src="{{ asset('img/service6.jpg') }}" alt="">
                        </div>
                        <h4 class="mb-3">SSL/TLS Encryption</h4>
                        <p>All data protected with military-gradeencryption for maximum security.</p>
                        <a class="btn-slide mt-2" href=""><i class="fa fa-arrow-right"></i><span>Read More</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- Feature Start -->
    <div class="container-fluid overflow-hidden py-5 px-lg-0">
        <div class="container feature py-5 px-lg-0">
            <div class="row g-5 mx-lg-0">
                <div class="col-lg-6 feature-text wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="text-secondary text-uppercase mb-3">Our Features</h6>
                    <h1 class="mb-5">Advanced School Bus Tracking for Parents & Schools</h1>
                    <div class="d-flex mb-5 wow fadeInUp" data-wow-delay="0.3s">
                        <i class="fa fa-globe text-primary fa-3x flex-shrink-0"></i>
                        <div class="ms-4">
                            <h5>Worldwide Service</h5>
                            <p class="mb-0">Multi-language Support - Interface available in multiple languages; more added regularly.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-5 wow fadeIn" data-wow-delay="0.5s">
                        <i class="fa fa-bell text-primary fa-3x flex-shrink-0"></i>
                        <div class="ms-4">
                            <h5>On Time Delivery</h5>
                            <p class="mb-0">In-App Chat & Notifications - Secure, real-time messaging and customizable alerts.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-0 wow fadeInUp" data-wow-delay="0.7s">
                        <i class="fa fa-history text-primary fa-3x flex-shrink-0"></i>
                        <div class="ms-4">
                            <h5>Trip History & Playback</h5>
                            <p class="mb-0">Review any completed trip exactly as it happened.<br>
                                Includes speed data, waiting durations at each stop, stop order, and delays.<br>
                                Crucial for incident resolution and operational analysis.

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 pe-lg-0 wow fadeInRight" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute img-fluid w-100 h-100" src="{{ asset('img/feature1.jpg') }}" style="object-fit: cover;" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature End -->


    <!-- Pricing Start -->
    <div class="container-xxl py-5">
        <div class="container py-5">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase">NEW · Direct for Families</h6>
                <h1 class="mb-5">Simple Pricing for Families</h1>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="price-item">
                        <div class="border-bottom p-4 mb-4">
                            <h5 class="text-primary mb-1">Smart Plan</h5>
                            <h1 class="display-5 mb-0">
                                <small class="align-top" style="font-size: 22px; line-height: 45px;"></small>450<small
                                    class="align-bottom" style="font-size: 16px; line-height: 40px;">EGP / month / per child</small>
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
                        <form id="homeQuoteForm" class="ajax-form" action="/api/public/quote" method="POST">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <input type="text" name="subject" class="form-control border-0" placeholder="Your Name" style="height: 55px;" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" name="email" class="form-control border-0" placeholder="Your Email" style="height: 55px;">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" name="phone" class="form-control border-0" placeholder="Your Mobile" style="height: 55px;" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <select name="request_type" class="form-select border-0" style="height: 55px;" required>
                                        <option value="">Select Service Type</option>
                                        <option value="Live Bus Tracking">Live Bus Tracking</option>
                                        <option value="Trip History">Trip History</option>
                                        <option value="Trip Replay">Trip Replay</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea name="description" class="form-control border-0" placeholder="Your message" required></textarea>
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


    @include('website.partials.team-section')


    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="text-center">
                <h6 class="text-secondary text-uppercase">Testimonial</h6>
                <h1 class="mb-0">Our Clients Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item p-4 my-5">
                    <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                    <div class="d-flex align-items-end mb-4">
                        <img class="img-fluid flex-shrink-0" src="{{ asset('img/testimonial-1.jpg') }}" style="width: 80px; height: 80px;">
                        <div class="ms-4">
                            <h5 class="mb-1">Ahmed Hassan</h5>
                            <p class="m-0">Parent of Grade 4 Student</p>
                        </div>
                    </div>
                    <p class="mb-0">The live tracking and instant alerts made our morning routine much easier and safer.</p>
                </div>
                <div class="testimonial-item p-4 my-5">
                    <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                    <div class="d-flex align-items-end mb-4">
                        <img class="img-fluid flex-shrink-0" src="{{ asset('img/testimonial-2.jpg') }}" style="width: 80px; height: 80px;">
                        <div class="ms-4">
                            <h5 class="mb-1">Mona Ali</h5>
                            <p class="m-0">School Principal</p>
                        </div>
                    </div>
                    <p class="mb-0">Since using SAFESTEP BUS, pickup delays dropped and parent trust increased significantly.</p>
                </div>
                <div class="testimonial-item p-4 my-5">
                    <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                    <div class="d-flex align-items-end mb-4">
                        <img class="img-fluid flex-shrink-0" src="{{ asset('img/testimonial-3.jpg') }}" style="width: 80px; height: 80px;">
                        <div class="ms-4">
                            <h5 class="mb-1">Omar Khaled</h5>
                            <p class="m-0">Bus Supervisor</p>
                        </div>
                    </div>
                    <p class="mb-0">Driver check-ins and route playback help us solve issues quickly and keep trips on schedule.</p>
                </div>
                <div class="testimonial-item p-4 my-5">
                    <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                    <div class="d-flex align-items-end mb-4">
                        <img class="img-fluid flex-shrink-0" src="{{ asset('img/testimonial-4.jpg') }}" style="width: 80px; height: 80px;">
                        <div class="ms-4">
                            <h5 class="mb-1">Nour Ibrahim</h5>
                            <p class="m-0">Operations Manager</p>
                        </div>
                    </div>
                    <p class="mb-0">The platform is simple for families and gives our school full visibility over daily transport.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


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


    @include('website.partials.ai-chat')

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
<script src="{{ asset('js/language.js') }}"></script>
<script src="{{ asset('js/ajax-forms.js') }}"></script>



</body>

</html>
