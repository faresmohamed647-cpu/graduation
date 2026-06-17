<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>FAQ - SAFESTEP BUS</title>
      <!-- علشان التابلت والموبيل-->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- مكان كلمات البحث والوصف -->
    <meta content="faq, questions, safestep bus, school bus tracking" name="keywords">
    <meta content="Frequently asked questions about SafeStep Bus school bus tracking system" name="description">

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
        /* FAQ Search */
        .faq-search-wrap {
            position: relative;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .faq-search-wrap i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .faq-search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .faq-search-input:focus {
            border-color: #FE5D14;
        }

        /* Category Filters */
        .cat-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .cat-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
            background: #fff;
            color: #6c757d;
        }
        .cat-btn.active {
            background: #FE5D14;
            color: white;
            border-color: #FE5D14;
        }
        .cat-btn:not(.active):hover {
            border-color: #FE5D14;
            color: #FE5D14;
        }

        /* FAQ Items */
        .faq-list {
            max-width: 800px;
            margin: 0 auto;
        }
        .faq-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: box-shadow 0.3s;
        }
        .faq-item:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .faq-question {
            width: 100%;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
            gap: 1rem;
        }
        .faq-question:hover {
            background: #f8f9fa;
        }
        .faq-question-content {
            flex: 1;
        }
        .faq-category-badge {
            display: inline-block;
            padding: 0.2rem 0.75rem;
            background: rgba(254, 93, 20, 0.1);
            color: #FE5D14;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 50px;
            margin-bottom: 0.375rem;
        }
        .faq-question h5 {
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
            color: #212529;
        }
        .faq-chevron {
            font-size: 1.25rem;
            color: #6c757d;
            transition: transform 0.3s ease;
        }
        .faq-item.open .faq-chevron {
            transform: rotate(180deg);
            color: #FE5D14;
        }
        .faq-answer {
            display: none;
            padding: 0 1.5rem 1.25rem;
        }
        .faq-answer p {
            color: #6c757d;
            line-height: 1.8;
            margin: 0;
        }
        .faq-item.open .faq-answer {
            display: block;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 3rem 0;
            color: #6c757d;
        }

        /* CTA Card */
        .faq-cta {
            background: linear-gradient(135deg, #FE5D14, #ff8c42);
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            color: white;
            max-width: 800px;
            margin: 3rem auto 0;
        }
        .faq-cta h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .faq-cta p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .faq-cta .btn-white {
            background: white;
            color: #FE5D14;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s;
        }
        .faq-cta .btn-white:hover {
            transform: translateY(-2px);
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
                        <a href="/faq" class="dropdown-item active">FAQ</a>
                        <a href="/tracking" class="dropdown-item">Live Tracking</a>
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">Frequently Asked Questions</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-white" href="/">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">FAQ</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- FAQ Content Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="text-center">
                <h6 class="text-secondary text-uppercase">FAQ</h6>
                <h1 class="mb-5">Find Answers to Common Questions</h1>
            </div>

            <!-- Search -->
            <div class="faq-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="faq-search-input" id="faqSearch" placeholder="Search for answers..." oninput="filterFAQ()">
            </div>

            <!-- Category Filters -->
            <div class="cat-filters" id="catFilters">
                <button class="cat-btn active" onclick="setCategory('all', this)">All</button>
                <button class="cat-btn" onclick="setCategory('General', this)">General</button>
                <button class="cat-btn" onclick="setCategory('Pricing', this)">Pricing</button>
                <button class="cat-btn" onclick="setCategory('Technical', this)">Technical</button>
                <button class="cat-btn" onclick="setCategory('Security', this)">Security</button>
                <button class="cat-btn" onclick="setCategory('Support', this)">Support</button>
            </div>

            <!-- FAQ List -->
            <div class="faq-list" id="faqList"></div>
            <div class="no-results" id="noResults" style="display:none;">
                <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
                <p class="h5">No results found</p>
                <p class="text-muted">Try adjusting your search or filters</p>
            </div>

            <!-- CTA -->
            <div class="faq-cta wow fadeInUp" data-wow-delay="0.3s">
                <h3>Still Have Questions?</h3>
                <p>Our support team is here to help you get the answers you need</p>
                <a href="/contact" class="btn-white">Contact Support</a>
            </div>
        </div>
    </div>
    <!-- FAQ Content End -->
        

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

    <!-- FAQ Logic -->
    <script>
        const faqs = [
            { category: 'General', question: 'What is SafeStep Bus?', answer: 'SafeStep Bus is a comprehensive school bus tracking and management system that uses GPS technology to monitor buses in real-time, send notifications to parents, and help schools optimize their transportation operations.' },
            { category: 'General', question: 'How does the GPS tracking work?', answer: 'We install a GPS device in each bus that sends location data to our cloud platform every 10 seconds. This data is then displayed on our web dashboard and mobile apps, allowing real-time tracking of all buses.' },
            { category: 'General', question: 'Who can use SafeStep Bus?', answer: 'SafeStep Bus is designed for schools, educational institutions, and school districts of all sizes. We serve K-12 schools, private schools, charter schools, and university campuses across Egypt and the region.' },
            { category: 'General', question: 'How do parents get started?', answer: 'Parents can register through our website by clicking "Parent" in the navigation or visiting the Parent Portal. After filling out the application form, an admin will review and approve the account.' },
            { category: 'Pricing', question: 'How much does it cost?', answer: 'We offer three pricing tiers: Smart Plan (450 EGP/month per child), Premium Plan (650 EGP/month per child), and Private VIP (950 EGP/month per child). Each plan includes different features and levels of support.' },
            { category: 'Pricing', question: 'Is there a free trial?', answer: 'Yes! Our Premium Plan includes a 7-day free trial so you can experience all features before committing. No credit card required during the trial period.' },
            { category: 'Pricing', question: 'Can I cancel anytime?', answer: 'Absolutely. There are no long-term contracts. You can cancel your subscription at any time with no penalties or cancellation fees.' },
            { category: 'Pricing', question: 'Do you offer discounts for annual billing?', answer: 'Yes, we offer a 25% discount for annual billing and a 10% discount for quarterly billing instead of monthly billing.' },
            { category: 'Technical', question: 'What devices are compatible with SafeStep Bus?', answer: 'Our system works on all modern web browsers (Chrome, Firefox, Safari, Edge) and we have features optimized for both desktop and mobile devices.' },
            { category: 'Technical', question: 'How is the GPS device installed?', answer: 'Installation is simple and takes about 15 minutes per bus. The device plugs into the OBD-II port and requires no wiring. We also offer professional installation services.' },
            { category: 'Technical', question: 'What happens if there\'s no cellular coverage?', answer: 'The GPS device stores location data locally when there\'s no signal and automatically uploads it once connection is restored. This ensures no data is lost during the trip.' },
            { category: 'Technical', question: 'How accurate is the GPS tracking?', answer: 'Our GPS devices provide accuracy within 3-5 meters under normal conditions. Location updates are sent every 10 seconds while the bus is moving.' },
            { category: 'Security', question: 'Is my data secure?', answer: 'Yes, we take security very seriously. All data is encrypted in transit using SSL/TLS and at rest using AES-256 encryption. We conduct regular security audits to ensure your data is always protected.' },
            { category: 'Security', question: 'Who can access the tracking information?', answer: 'Access is role-based. School administrators have full access, drivers see only their assigned bus, and parents only see information for their own children. All access is password-protected with Laravel Sanctum authentication.' },
            { category: 'Security', question: 'Do you have surveillance cameras?', answer: 'Yes, we support 24/7 surveillance cameras inside buses with high-quality recording, secure storage, and review capability for maximum safety and accountability.' },
            { category: 'Support', question: 'What kind of support do you offer?', answer: 'We offer multiple support channels: in-app AI chat assistant, email support, phone support, and WhatsApp support for VIP plan customers. Our support team is available during school hours.' },
            { category: 'Support', question: 'Do you provide training?', answer: 'Yes, all new customers receive onboarding guidance. Our AI chat assistant is available 24/7 to answer questions about the system in both English and Arabic.' },
            { category: 'Support', question: 'How do I report an emergency?', answer: 'Our app includes an SOS button with direct connection to our support team and relevant authorities. Parents and drivers can trigger emergency alerts instantly from the app.' },
        ];

        let currentCategory = 'all';

        function renderFAQ() {
            const search = document.getElementById('faqSearch').value.toLowerCase();
            const list = document.getElementById('faqList');
            const noResults = document.getElementById('noResults');
            const filtered = faqs.filter(f => {
                const matchCat = currentCategory === 'all' || f.category === currentCategory;
                const matchSearch = f.question.toLowerCase().includes(search) || f.answer.toLowerCase().includes(search);
                return matchCat && matchSearch;
            });
            if (filtered.length === 0) {
                list.innerHTML = '';
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
                list.innerHTML = filtered.map((f, i) => `
                    <div class="faq-item wow fadeInUp" data-wow-delay="${Math.min(i * 0.05, 0.5)}s">
                        <button class="faq-question" onclick="toggleFAQ(this)">
                            <div class="faq-question-content">
                                <span class="faq-category-badge">${f.category}</span>
                                <h5>${f.question}</h5>
                            </div>
                            <i class="bi bi-chevron-down faq-chevron"></i>
                        </button>
                        <div class="faq-answer"><p>${f.answer}</p></div>
                    </div>
                `).join('');
                // Open first item
                const first = list.querySelector('.faq-item');
                if (first) first.classList.add('open');
            }
        }

        function toggleFAQ(btn) {
            const item = btn.closest('.faq-item');
            item.classList.toggle('open');
        }

        function setCategory(cat, btn) {
            currentCategory = cat;
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderFAQ();
        }

        function filterFAQ() { renderFAQ(); }

        renderFAQ();
    </script>

<!-- Language Support -->
<script src='{{ asset('js/language.js') }}'></script>

</body>

</html>
