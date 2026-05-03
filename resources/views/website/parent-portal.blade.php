<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SafeStep Parent Portal - Premium Access</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="{{ asset('img/icon.jpg') }}" rel="icon">

    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons & Animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-glow: rgba(37, 99, 235, 0.4);
            --secondary: #0f172a;
            --accent: #38bdf8;
            --glass: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.3);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --success: #10b981;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Image */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/parent_portal_background_1777722216595.png') center/cover no-repeat;
            opacity: 0.4;
            filter: blur(5px);
            z-index: -1;
            animation: bgZoom 20s infinite alternate;
        }

        @keyframes bgZoom {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }

        .portal-container {
            width: 1200px;
            max-width: 100%;
            background: var(--glass);
            backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            box-shadow: 0 50px 120px rgba(0, 0, 0, 0.4);
            display: flex;
            overflow: hidden;
            min-height: 750px;
            position: relative;
            z-index: 10;
        }

        /* Left Branding Section */
        .portal-brand {
            flex: 1;
            padding: 80px 60px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.9) 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .brand-logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 60px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo i {
            color: var(--accent);
            filter: drop-shadow(0 0 10px var(--accent));
        }

        .portal-brand h1 {
            font-size: 52px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            background: linear-gradient(to right, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .portal-brand p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .stat-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }

        .stat-card h3 { font-size: 24px; font-weight: 800; margin: 0; color: var(--accent); }
        .stat-card p { font-size: 13px; margin: 0; color: rgba(255, 255, 255, 0.5); }

        /* Right Form Section */
        .portal-content {
            width: 55%;
            padding: 60px;
            background: #fff;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Form Stepper */
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #f1f5f9;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            background: #fff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: var(--text-muted);
            transition: all 0.4s ease;
        }

        .step.active {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .step.completed {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* Tabs and Forms */
        .nav-tabs-premium {
            display: flex;
            gap: 8px;
            background: #f8fafc;
            padding: 8px;
            border-radius: 20px;
            margin-bottom: 40px;
        }

        .nav-tab-btn {
            flex: 1;
            padding: 14px;
            border: none;
            background: transparent;
            border-radius: 16px;
            font-weight: 700;
            color: var(--text-muted);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-tab-btn.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        .form-pane {
            display: none;
            animation: fadeInBlur 0.6s ease;
        }

        .form-pane.active { display: block; }

        @keyframes fadeInBlur {
            from { opacity: 0; filter: blur(10px); transform: translateY(20px); }
            to { opacity: 1; filter: blur(0); transform: translateY(0); }
        }

        /* Inputs */
        .premium-group { margin-bottom: 24px; }
        .premium-label { font-weight: 700; font-size: 14px; margin-bottom: 10px; display: block; color: var(--text-main); }
        
        .premium-input-container { position: relative; }
        .premium-input-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .premium-control {
            width: 100%;
            height: 56px;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            padding: 0 20px 0 54px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .premium-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 5px var(--primary-glow);
        }

        .premium-control:focus + i { color: var(--primary); }

        .btn-premium {
            width: 100%;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #fff;
            border: none;
            border-radius: 18px;
            font-weight: 800;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.25);
            transition: all 0.4s ease;
            margin-top: 10px;
        }

        .btn-premium:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 25px 50px rgba(37, 99, 235, 0.4);
        }

        .btn-outline-premium {
            background: transparent;
            border: 2px solid #f1f5f9;
            color: var(--text-main);
            height: 60px;
            border-radius: 18px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-outline-premium:hover {
            background: #f8fafc;
            border-color: var(--primary);
        }

        /* Success/Error Alerts */
        .premium-alert {
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
        }

        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #10b981; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #ef4444; }

        @media (max-width: 992px) {
            .portal-container { flex-direction: column; width: 100%; border-radius: 30px; height: auto; }
            .portal-brand { padding: 40px 30px; text-align: center; }
            .brand-logo { justify-content: center; }
            .portal-content { width: 100%; padding: 40px 20px; max-height: none; }
        }
    </style>
</head>
<body>

    <div class="bg-overlay"></div>

    <div class="portal-container" data-aos="zoom-in" data-aos-duration="1000">
        
        <!-- Left Section -->
        <div class="portal-brand">
            <div class="brand-logo">
                <i class="fa fa-shield-heart"></i>
                SAFESTEP
            </div>

            <h1>The Smartest Way To Protect Your Kids.</h1>
            <p>Every trip, every mile, tracked with precision and care. Join the network that prioritizes student safety above all else.</p>

            <div class="stat-group">
                <div class="stat-card">
                    <h3>2.4k+</h3>
                    <p>Active Parents</p>
                </div>
                <div class="stat-card">
                    <h3>99.9%</h3>
                    <p>Uptime Security</p>
                </div>
                <div class="stat-card">
                    <h3>Instant</h3>
                    <p>Alert Response</p>
                </div>
                <div class="stat-card">
                    <h3>4.9/5</h3>
                    <p>Parent Rating</p>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top border-secondary">
                <a href="{{ url('/') }}" class="text-white text-decoration-none small fw-bold">
                    <i class="fa fa-arrow-left me-2"></i> Return to Homepage
                </a>
            </div>
        </div>

        <!-- Right Content Section -->
        <div class="portal-content">
            
            @if(session('success'))
                <div class="premium-alert alert-success">
                    <i class="fa fa-check-circle fs-4"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="premium-alert alert-danger">
                    <i class="fa fa-exclamation-triangle fs-4"></i>
                    <div>
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="nav-tabs-premium">
                <button class="nav-tab-btn active" id="tab-login" onclick="switchMainTab('login')">Dashboard Access</button>
                <button class="nav-tab-btn" id="tab-apply" onclick="switchMainTab('apply')">New Application</button>
            </div>

            <!-- Login Form -->
            <div id="pane-login" class="form-pane active">
                <div class="mb-4">
                    <h2 class="fw-bold h3">Welcome Back</h2>
                    <p class="text-muted small">Sign in to track your child's bus in real-time.</p>
                </div>

                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="parent">
                    
                    <div class="premium-group">
                        <label class="premium-label">Authorized Email</label>
                        <div class="premium-input-container">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" class="premium-control" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="premium-group">
                        <label class="premium-label">Secure Password</label>
                        <div class="premium-input-container">
                            <i class="fa-solid fa-lock-open"></i>
                            <input type="password" name="password" class="premium-control" placeholder="Your password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4 small">
                        <label class="d-flex align-items-center gap-2 cursor-pointer">
                            <input type="checkbox"> <span class="text-muted">Keep me signed in</span>
                        </label>
                        <a href="#" class="text-primary fw-bold text-decoration-none">Reset Password?</a>
                    </div>

                    <button type="submit" class="btn-premium">Sign In To Portal</button>
                </form>

                <div class="text-center mt-5">
                    <p class="text-muted small">New to SafeStep? <a href="javascript:void(0)" onclick="switchMainTab('apply')" class="text-primary fw-bold text-decoration-none">Apply for transport tracking</a></p>
                </div>
            </div>

            <!-- Application Multi-Step Form -->
            <div id="pane-apply" class="form-pane">
                <div class="mb-4">
                    <h2 class="fw-bold h3">Parent Application</h2>
                    <p class="text-muted small">Complete the 3-step form to register your children.</p>
                </div>

                <div class="stepper">
                    <div class="step active" id="step-1">1</div>
                    <div class="step" id="step-2">2</div>
                    <div class="step" id="step-3">3</div>
                </div>

                <form action="{{ url('/register/parent') }}" method="POST" id="multiStepForm">
                    @csrf
                    
                    <!-- Step 1: Basic Info -->
                    <div class="step-content active" id="step-1-content">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Mobile Phone</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-phone"></i>
                                        <input type="tel" name="student_phone" class="premium-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Relationship</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-user"></i>
                                        <select name="student_relationship" class="premium-control form-select" style="padding-left: 54px;" required>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="premium-group">
                                    <label class="premium-label">Residence Address</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-house-chimney"></i>
                                        <input type="text" name="student_address" class="premium-control" placeholder="Street, Building, Area" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-premium mt-3" onclick="nextStep(2)">Continue to Details <i class="fa fa-arrow-right ms-2"></i></button>
                    </div>

                    <!-- Step 2: Student & School Details -->
                    <div class="step-content d-none" id="step-2-content">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">School Name</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-school"></i>
                                        <input type="text" name="school_name" class="premium-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Education System</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-book"></i>
                                        <input type="text" name="student_education_system" class="premium-control" placeholder="e.g. IG / National" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Number of Children</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-children"></i>
                                        <input type="number" name="student_count" class="premium-control" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Current Grade/Level</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-graduation-cap"></i>
                                        <input type="text" name="student_degree" class="premium-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-3">
                            <button type="button" class="btn-outline-premium w-50" onclick="nextStep(1)">Back</button>
                            <button type="button" class="btn-premium w-50" onclick="nextStep(3)">Last Step <i class="fa fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: Account Creation -->
                    <div class="step-content d-none" id="step-3-content">
                        <div class="premium-group">
                            <label class="premium-label">Choose Your Portal Email</label>
                            <div class="premium-input-container">
                                <i class="fa fa-at"></i>
                                <input type="email" name="student_email" class="premium-control" placeholder="This will be your login" required>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Create Password</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-key"></i>
                                        <input type="password" name="student_password" class="premium-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-group">
                                    <label class="premium-label">Confirm Password</label>
                                    <div class="premium-input-container">
                                        <i class="fa fa-check-double"></i>
                                        <input type="password" name="student_password_confirm" class="premium-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-3">
                            <button type="button" class="btn-outline-premium w-50" onclick="nextStep(2)">Back</button>
                            <button type="submit" class="btn-premium w-50">Complete Application</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        function switchMainTab(tab) {
            const loginPane = document.getElementById('pane-login');
            const applyPane = document.getElementById('pane-apply');
            const loginTab = document.getElementById('tab-login');
            const applyTab = document.getElementById('tab-apply');

            if(tab === 'login') {
                loginPane.classList.add('active');
                applyPane.classList.remove('active');
                loginTab.classList.add('active');
                applyTab.classList.remove('active');
            } else {
                loginPane.classList.remove('active');
                applyPane.classList.add('active');
                loginTab.classList.remove('active');
                applyTab.classList.add('active');
            }
        }

        function nextStep(step) {
            document.querySelectorAll('.step-content').forEach(c => c.classList.add('d-none'));
            document.getElementById(`step-${step}-content`).classList.remove('d-none');
            
            document.querySelectorAll('.step').forEach((s, idx) => {
                if(idx + 1 < step) { s.classList.add('completed'); s.classList.remove('active'); }
                else if(idx + 1 === step) { s.classList.add('active'); s.classList.remove('completed'); }
                else { s.classList.remove('active', 'completed'); }
            });
        }

        window.onload = () => {
            if(window.location.hash === '#apply' || {{ $errors->any() ? 'true' : 'false' }}) {
                switchMainTab('apply');
            }
        }
    </script>
</body>
</html>
