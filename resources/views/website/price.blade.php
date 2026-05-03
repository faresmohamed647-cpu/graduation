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
    <link href="{{ asset('css/price.css') }}" rel="stylesheet">
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
                        <a href="team.html" class="dropdown-item">Our Team</a>
                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="404.html" class="dropdown-item">404 Page</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
            </div>
            <h4 class="m-0 pe-lg-5 d-none d-lg-block"><i class="fa fa-headphones text-primary me-3"></i>+012 345 6789</h4>
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
        <!-- Offers -->
    <div class="offers">
        <h3>Family Offers</h3>
        <ul>
            <li>7-day free trial for every family</li>
            <li>20% discount for 3 children or more</li>
            <li>25% discount on yearly payment</li>
        </ul>
    </div>

    <!-- Calculator Card -->
    <div class="calculator-card">

        <h1>Family Savings Calculator</h1>
        <p class="desc">
            See how much you can save monthly by switching to our school plans.
        </p>

        <div class="calculator">

            <!-- Result -->
            <div class="result-box">
                <h2>Estimated Monthly Savings</h2>

                <div class="big-number" id="saving">0 EGP</div>

                <div class="lines">
                    <div>
                        <span>Current Monthly Cost</span>
                        <strong id="currentTotal">0</strong> EGP
                    </div>
                    <div>
                        <span>School Monthly Cost</span>
                        <strong id="schoolTotal">0</strong> EGP
                    </div>
                </div>

                <ul class="features">
                    <li>Dedicated family support</li>
                    <li>Real-time tracking</li>
                    <li>Cancel anytime within 48 hours</li>
                </ul>
            </div>

            <!-- Controls -->
            <div class="controls">

                <div class="field">
                    <label>Number of Children</label>
                    <input type="number" id="children" min="1" max="6" value="1">
                </div>

                <div class="field">
                    <label>Current Cost per Child (Monthly)</label>
                    <input type="number" id="currentCost" value="900">
                </div>

                <div class="field">
                    <label>Select Plan</label>

                    <label class="option">
                        <input type="radio" name="plan" value="450" checked>
                        <span>Basic Plan</span>
                        <strong>450 EGP</strong>
                    </label>

                    <label class="option">
                        <input type="radio" name="plan" value="650">
                        <span>Premium Plan</span>
                        <strong>650 EGP</strong>
                    </label>

                    <label class="option">
                        <input type="radio" name="plan" value="950">
                        <span>VIP Plan</span>
                        <strong>950 EGP</strong>
                    </label>
                </div>

                <div class="field">
                    <label>Payment Method</label>

                    <div class="payment">
                        <label>
                            <input type="radio" name="payment" value="monthly" checked>
                            Monthly
                        </label>
                        <label>
                            <input type="radio" name="payment" value="quarterly">
                            Quarterly
                        </label>
                        <label>
                            <input type="radio" name="payment" value="yearly">
                            Yearly
                            
                        </label>
                        <a href="pay.html"class="btn-link order-btn"data-aos="fade-up"data-aos-duration="1200"data-aos-delay="600">Order now</a>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>


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
                            <h3 class="text-primary m-0">+012 345 6789</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="bg-light text-center p-5 wow fadeIn" data-wow-delay="0.5s">
                        <form>
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
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@example.com</p>
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
                        &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right Reserved.
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

