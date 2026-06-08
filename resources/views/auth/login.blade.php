<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SafeStep Bus</title>
    <meta name="description" content="Sign in to your SafeStep Bus dashboard. Manage school bus tracking, student safety, and transportation operations.">
    <link rel="icon" href="{{ asset('img/icon.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ═══════════════════════════════════════════
           CSS CUSTOM PROPERTIES — DESIGN TOKENS
           ═══════════════════════════════════════════ */
        :root {
            --bg-primary: #070b1a;
            --bg-secondary: #0d1529;
            --glass-bg: rgba(255,255,255,0.04);
            --glass-border: rgba(255,255,255,0.08);
            --glass-card: rgba(13,21,41,0.85);

            /* Role accent colors */
            --admin-primary: #f43f5e;
            --admin-secondary: #e11d48;
            --admin-glow: rgba(244,63,94,0.25);
            --admin-gradient: linear-gradient(135deg, #f43f5e, #be123c);

            --parent-primary: #3b82f6;
            --parent-secondary: #2563eb;
            --parent-glow: rgba(59,130,246,0.25);
            --parent-gradient: linear-gradient(135deg, #3b82f6, #1d4ed8);

            --driver-primary: #10b981;
            --driver-secondary: #059669;
            --driver-glow: rgba(16,185,129,0.25);
            --driver-gradient: linear-gradient(135deg, #10b981, #047857);

            --school-primary: #8b5cf6;
            --school-secondary: #7c3aed;
            --school-glow: rgba(139,92,246,0.25);
            --school-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);

            /* Active role (default = parent) */
            --active-primary: var(--parent-primary);
            --active-secondary: var(--parent-secondary);
            --active-glow: var(--parent-glow);
            --active-gradient: var(--parent-gradient);

            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --input-bg: rgba(255,255,255,0.05);
            --input-border: rgba(255,255,255,0.1);
            --input-focus-border: var(--active-primary);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
            --transition: all 0.35s cubic-bezier(.4,0,.2,1);
        }

        /* ═══════════════════════════════════════════
           RESET & BASE
           ═══════════════════════════════════════════ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Outfit', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ═══════════════════════════════════════════
           ANIMATED BACKGROUND
           ═══════════════════════════════════════════ */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-scene::before {
            content: '';
            position: absolute;
            width: 150vw; height: 150vh;
            top: -25vh; left: -25vw;
            background:
                radial-gradient(ellipse 600px 600px at 20% 30%, rgba(59,130,246,0.12) 0%, transparent 70%),
                radial-gradient(ellipse 500px 500px at 80% 70%, rgba(16,185,129,0.08) 0%, transparent 70%),
                radial-gradient(ellipse 400px 400px at 50% 50%, rgba(244,63,94,0.06) 0%, transparent 70%);
            animation: bgDrift 20s ease-in-out infinite alternate;
        }

        @keyframes bgDrift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-30px, 20px) scale(1.05); }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat linear infinite;
        }

        @keyframes particleFloat {
            0%   { opacity: 0; transform: translateY(100vh) scale(0); }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        /* Grid overlay */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        }

        /* ═══════════════════════════════════════════
           MAIN LAYOUT
           ═══════════════════════════════════════════ */
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
            margin: 24px;
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow:
                0 0 0 1px var(--glass-border),
                0 25px 80px rgba(0,0,0,0.5),
                0 0 120px var(--active-glow);
            animation: cardReveal 0.8s cubic-bezier(.16,1,.3,1);
            transition: box-shadow 0.5s ease;
        }

        @keyframes cardReveal {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ═══════════════════════════════════════════
           LEFT PANEL — BRANDING
           ═══════════════════════════════════════════ */
        .brand-panel {
            position: relative;
            background: var(--active-gradient);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 36px;
            overflow: hidden;
            transition: background 0.6s ease;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 30% 20%, rgba(255,255,255,0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(0,0,0,0.1) 0%, transparent 50%);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .brand-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            transition: var(--transition);
        }

        .brand-content h1 {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .brand-content p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            line-height: 1.7;
            max-width: 260px;
            margin: 0 auto;
        }

        .brand-features {
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            font-weight: 500;
        }

        .brand-feature .feat-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ═══════════════════════════════════════════
           RIGHT PANEL — FORM
           ═══════════════════════════════════════════ */
        .form-panel {
            background: var(--glass-card);
            backdrop-filter: blur(40px);
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-header h2 {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* ═══════════════════════════════════════════
           ROLE TABS
           ═══════════════════════════════════════════ */
        .role-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 28px;
            background: rgba(255,255,255,0.03);
            border-radius: var(--radius-md);
            padding: 4px;
            border: 1px solid var(--glass-border);
        }

        .role-tab {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 12px 8px;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Inter', sans-serif;
        }

        .role-tab i {
            font-size: 14px;
            transition: var(--transition);
        }

        .role-tab:hover {
            color: var(--text-secondary);
            background: rgba(255,255,255,0.04);
        }

        .role-tab.active {
            background: var(--active-gradient);
            color: white;
            box-shadow: 0 4px 16px var(--active-glow);
        }

        /* ═══════════════════════════════════════════
           FORM FIELDS
           ═══════════════════════════════════════════ */
        .form-field {
            margin-bottom: 18px;
        }

        .form-field label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .form-field label i {
            font-size: 10px;
            opacity: 0.6;
        }

        .input-container {
            position: relative;
        }

        .input-container .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .input-container input {
            width: 100%;
            padding: 15px 48px 15px 48px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: var(--transition);
        }

        .input-container input::placeholder {
            color: var(--text-muted);
        }

        .input-container input:focus {
            border-color: var(--active-primary);
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 0 4px var(--active-glow);
        }

        .input-container input:focus ~ .input-icon {
            color: var(--active-primary);
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 14px;
        }

        .toggle-pass:hover {
            color: var(--text-secondary);
            background: rgba(255,255,255,0.05);
        }

        /* Remember & Options Row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 24px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-wrap input[type="checkbox"] {
            appearance: none;
            width: 18px; height: 18px;
            border: 1.5px solid var(--input-border);
            border-radius: 5px;
            background: var(--input-bg);
            cursor: pointer;
            position: relative;
            transition: var(--transition);
        }

        .remember-wrap input[type="checkbox"]:checked {
            background: var(--active-primary);
            border-color: var(--active-primary);
        }

        .remember-wrap input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 11px;
            font-weight: 700;
        }

        .remember-wrap span {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ═══════════════════════════════════════════
           SUBMIT BUTTON
           ═══════════════════════════════════════════ */
        .submit-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: var(--radius-md);
            background: var(--active-gradient);
            color: white;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 8px 24px var(--active-glow);
            letter-spacing: 0.3px;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px var(--active-glow);
        }

        .submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        /* ═══════════════════════════════════════════
           FEEDBACK MESSAGES
           ═══════════════════════════════════════════ */
        .msg-box {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
            animation: msgPop 0.4s cubic-bezier(.16,1,.3,1);
        }

        .msg-box.error {
            background: rgba(244,63,94,0.1);
            color: #fda4af;
            border: 1px solid rgba(244,63,94,0.2);
        }

        .msg-box.success {
            background: rgba(16,185,129,0.1);
            color: #6ee7b7;
            border: 1px solid rgba(16,185,129,0.2);
        }

        .msg-box i { font-size: 16px; flex-shrink: 0; }

        @keyframes msgPop {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            15%, 45%, 75% { transform: translateX(-4px); }
            30%, 60% { transform: translateX(4px); }
        }

        /* ═══════════════════════════════════════════
           DIVIDER & LINKS
           ═══════════════════════════════════════════ */
        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 24px 0;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--glass-border);
        }

        .bottom-links {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .bottom-links a {
            color: var(--active-primary);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .bottom-links a:hover {
            text-decoration: underline;
            filter: brightness(1.2);
        }

        /* ═══════════════════════════════════════════
           LANGUAGE TOGGLE
           ═══════════════════════════════════════════ */
        .lang-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-secondary);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .lang-toggle:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        /* Back to home */
        .home-link {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-secondary);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .home-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        /* ═══════════════════════════════════════════
           SPINNER
           ═══════════════════════════════════════════ */
        .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ═══════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 440px;
                margin: 16px;
            }

            .brand-panel {
                padding: 32px 24px;
            }

            .brand-features { display: none; }

            .brand-content h1 { font-size: 22px; }
            .brand-content p { font-size: 13px; }

            .brand-icon {
                width: 60px; height: 60px;
                font-size: 28px;
                margin-bottom: 16px;
            }

            .form-panel {
                padding: 32px 24px;
            }

            .role-tab { font-size: 11px; padding: 10px 6px; }
        }

        @media (max-width: 400px) {
            .login-container { margin: 8px; }
            .form-panel { padding: 24px 18px; }
            .role-tab span { display: none; }
            .role-tab i { font-size: 16px; }
        }

        /* RTL */
        html[dir="rtl"] .input-container .input-icon {
            left: auto;
            right: 16px;
        }

        html[dir="rtl"] .input-container input {
            padding: 15px 48px 15px 48px;
        }

        html[dir="rtl"] .toggle-pass {
            right: auto;
            left: 12px;
        }

        html[dir="rtl"] .lang-toggle {
            right: auto;
            left: 20px;
        }

        html[dir="rtl"] .home-link {
            left: auto;
            right: 20px;
        }
    </style>
</head>
<body>

    <!-- ═══════ ANIMATED BACKGROUND ═══════ -->
    <div class="bg-scene" id="bgScene">
        <div class="bg-grid"></div>
    </div>

    <!-- ═══════ LANGUAGE TOGGLE ═══════ -->
    <button class="lang-toggle" id="langToggle" onclick="toggleLang()">
        <i class="fas fa-globe"></i>
        <span id="langLabel">عربي</span>
    </button>

    <!-- ═══════ BACK TO HOME ═══════ -->
    <a href="{{ url('/') }}" class="home-link" id="homeLink">
        <i class="fas fa-arrow-left"></i>
        <span data-i18n="home">Home</span>
    </a>

    <!-- ═══════ MAIN LOGIN CARD ═══════ -->
    <div class="login-container" id="loginContainer">

        <!-- LEFT: BRANDING -->
        <div class="brand-panel" id="brandPanel">
            <div class="brand-content">
                <div class="brand-icon" id="brandIcon">
                    <i class="fas fa-bus"></i>
                </div>
                <h1>SafeStep Bus</h1>
                <p id="brandDesc" data-i18n="brand_desc">Smart school bus tracking for a safer journey. Real-time GPS, instant alerts, and full peace of mind.</p>

                <div class="brand-features" id="brandFeatures">
                    <div class="brand-feature">
                        <div class="feat-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <span data-i18n="feat_gps">Live GPS tracking every second</span>
                    </div>
                    <div class="brand-feature">
                        <div class="feat-icon"><i class="fas fa-bell"></i></div>
                        <span data-i18n="feat_alerts">Instant pickup & drop-off alerts</span>
                    </div>
                    <div class="brand-feature">
                        <div class="feat-icon"><i class="fas fa-shield-halved"></i></div>
                        <span data-i18n="feat_safety">Military-grade data encryption</span>
                    </div>
                    <div class="brand-feature">
                        <div class="feat-icon"><i class="fas fa-headset"></i></div>
                        <span data-i18n="feat_support">24/7 customer support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: FORM -->
        <div class="form-panel">
            <div class="form-header">
                <h2 id="formTitle" data-i18n="form_title">Welcome Back</h2>
                <p id="formSubtitle" data-i18n="form_subtitle">Sign in to access your dashboard</p>
            </div>

            <!-- ROLE TABS -->
            <div class="role-tabs" id="roleTabs">
                <button type="button" class="role-tab" data-role="admin" id="tabAdmin" onclick="selectRole('admin')">
                    <i class="fas fa-shield-halved"></i>
                    <span data-i18n="role_admin">Admin</span>
                </button>
                <button type="button" class="role-tab active" data-role="parent" id="tabParent" onclick="selectRole('parent')">
                    <i class="fas fa-users"></i>
                    <span data-i18n="role_parent">Parent</span>
                </button>
                <button type="button" class="role-tab" data-role="driver" id="tabDriver" onclick="selectRole('driver')">
                    <i class="fas fa-id-card"></i>
                    <span data-i18n="role_driver">Driver</span>
                </button>
                <button type="button" class="role-tab" data-role="school_admin" id="tabSchoolAdmin" onclick="selectRole('school_admin')">
                    <i class="fas fa-school"></i>
                    <span data-i18n="role_school">School</span>
                </button>
            </div>

            <!-- ERROR / SUCCESS MESSAGE -->
            <div class="msg-box error" id="errorMsg">
                <i class="fas fa-exclamation-circle"></i>
                <span class="msg-text">{{ $errors->first('email') }}</span>
            </div>

            <div class="msg-box success" id="successMsg">
                <i class="fas fa-check-circle"></i>
                <span class="msg-text"></span>
            </div>

            <!-- FORM -->
            <form id="loginForm" autocomplete="on">
                @csrf

                <div class="form-field">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        <span data-i18n="label_email">Email Address</span>
                    </label>
                    <div class="input-container">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                            data-i18n-placeholder="ph_email"
                        >
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-field">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        <span data-i18n="label_password">Password</span>
                    </label>
                    <div class="input-container">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-wrap">
                        <input type="checkbox" name="remember" value="1">
                        <span data-i18n="remember">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    <span id="submitText" data-i18n="btn_login">Sign In</span>
                </button>
            </form>

            <div class="divider" data-i18n="or_divider">or</div>

            <div class="bottom-links">
                <span data-i18n="no_account">New here?</span>
                <a href="{{ url('/apply') }}" data-i18n="apply_link">Apply to Join SafeStep</a>
            </div>
        </div>
    </div>

    <script>
    (function() {
        'use strict';

        // ════════════ STATE ════════════
        let currentRole = 'parent';
        let currentLang = localStorage.getItem('safestep_login_lang') || 'en';
        const initialServerError = @json($errors->first('email'));

        // ════════════ ROLE CONFIG ════════════
        const roleConfig = {
            admin: {
                gradient: 'linear-gradient(135deg, #f43f5e, #be123c)',
                primary: '#f43f5e',
                glow: 'rgba(244,63,94,0.25)',
                icon: 'fas fa-shield-halved',
                dashboardPath: '/admin'
            },
            parent: {
                gradient: 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                primary: '#3b82f6',
                glow: 'rgba(59,130,246,0.25)',
                icon: 'fas fa-users',
                dashboardPath: '/parent'
            },
            driver: {
                gradient: 'linear-gradient(135deg, #10b981, #047857)',
                primary: '#10b981',
                glow: 'rgba(16,185,129,0.25)',
                icon: 'fas fa-id-card',
                dashboardPath: '/driver'
            },
            school_admin: {
                gradient: 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                primary: '#8b5cf6',
                glow: 'rgba(139,92,246,0.25)',
                icon: 'fas fa-school',
                dashboardPath: '/school-admin'
            }
        };

        // ════════════ i18n ════════════
        const i18n = {
            en: {
                brand_desc: 'Smart school bus tracking for a safer journey. Real-time GPS, instant alerts, and full peace of mind.',
                feat_gps: 'Live GPS tracking every second',
                feat_alerts: 'Instant pickup & drop-off alerts',
                feat_safety: 'Military-grade data encryption',
                feat_support: '24/7 customer support',
                form_title: 'Welcome Back',
                form_subtitle: 'Sign in to access your dashboard',
                role_admin: 'Admin',
                role_parent: 'Parent',
                role_driver: 'Driver',
                role_school: 'School',
                label_email: 'Email Address',
                label_password: 'Password',
                ph_email: 'you@example.com',
                remember: 'Remember me',
                btn_login: 'Sign In',
                btn_loading: 'Authenticating…',
                or_divider: 'or',
                no_account: 'New here?',
                apply_link: 'Apply to Join SafeStep',
                err_invalid: 'Invalid email or password',
                err_network: 'Network error. Check your connection.',
                err_unexpected: 'Unexpected error. Please try again.',
                home: 'Home',
                lang_label: 'عربي'
            },
            ar: {
                brand_desc: 'تتبع ذكي لباصات المدارس لرحلة أكثر أماناً. تتبع GPS مباشر، تنبيهات فورية، وراحة بال كاملة.',
                feat_gps: 'تتبع GPS مباشر كل ثانية',
                feat_alerts: 'تنبيهات فورية للتوصيل والاستلام',
                feat_safety: 'تشفير بيانات بدرجة عسكرية',
                feat_support: 'دعم فني على مدار الساعة',
                form_title: 'أهلاً بعودتك',
                form_subtitle: 'سجّل دخولك للوصول للوحة التحكم',
                role_admin: 'أدمن',
                role_parent: 'ولي أمر',
                role_driver: 'سائق',
                role_school: 'مدرسة',
                label_email: 'البريد الإلكتروني',
                label_password: 'كلمة المرور',
                ph_email: 'you@example.com',
                remember: 'تذكرني',
                btn_login: 'تسجيل الدخول',
                btn_loading: 'جاري التحقق...',
                or_divider: 'أو',
                no_account: 'جديد هنا؟',
                apply_link: 'قدّم طلب انضمام لـ SafeStep',
                err_invalid: 'بيانات الدخول غير صحيحة',
                err_network: 'خطأ في الشبكة. تأكد من اتصالك.',
                err_unexpected: 'حدث خطأ غير متوقع. حاول مرة أخرى.',
                home: 'الرئيسية',
                lang_label: 'English'
            }
        };

        // ════════════ PARTICLES ════════════
        function createParticles() {
            const scene = document.getElementById('bgScene');
            const colors = ['rgba(59,130,246,0.4)', 'rgba(16,185,129,0.3)', 'rgba(244,63,94,0.3)', 'rgba(139,92,246,0.3)'];
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const size = Math.random() * 4 + 2;
                p.style.cssText = `
                    width: ${size}px; height: ${size}px;
                    left: ${Math.random() * 100}%;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    animation-duration: ${Math.random() * 15 + 10}s;
                    animation-delay: ${Math.random() * 10}s;
                `;
                scene.appendChild(p);
            }
        }
        createParticles();

        // ════════════ ROLE SELECTION ════════════
        window.selectRole = function(role) {
            currentRole = role;
            const cfg = roleConfig[role];

            // Update tabs
            document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`[data-role="${role}"]`).classList.add('active');

            // Update CSS variables
            const root = document.documentElement;
            root.style.setProperty('--active-primary', cfg.primary);
            root.style.setProperty('--active-glow', cfg.glow);
            root.style.setProperty('--active-gradient', cfg.gradient);

            // Update brand panel
            document.getElementById('brandPanel').style.background = cfg.gradient;
            document.getElementById('brandIcon').innerHTML = `<i class="${cfg.icon}"></i>`;

            // Update container glow
            document.getElementById('loginContainer').style.boxShadow =
                `0 0 0 1px var(--glass-border), 0 25px 80px rgba(0,0,0,0.5), 0 0 120px ${cfg.glow}`;
        };

        // ════════════ LANGUAGE ════════════
        window.toggleLang = function() {
            currentLang = currentLang === 'en' ? 'ar' : 'en';
            localStorage.setItem('safestep_login_lang', currentLang);
            applyLang();
        };

        function applyLang() {
            const t = i18n[currentLang];
            const isAr = currentLang === 'ar';

            document.documentElement.dir = isAr ? 'rtl' : 'ltr';
            document.documentElement.lang = isAr ? 'ar' : 'en';

            // Update all i18n elements
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (t[key]) el.textContent = t[key];
            });

            // Update placeholders
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (t[key]) el.placeholder = t[key];
            });

            // Lang toggle label
            document.getElementById('langLabel').textContent = t.lang_label;
        }

        // ════════════ PASSWORD TOGGLE ════════════
        document.getElementById('togglePass').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            icon.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
        });

        // ════════════ AUTH STATE CLEANUP ════════════
        function clearAuthState() {
            localStorage.removeItem('token');
            localStorage.removeItem('safestep_token');
            localStorage.removeItem('safestep_user');
            localStorage.removeItem('safestep_role');
        }
        clearAuthState();

        // ════════════ ERROR/SUCCESS DISPLAY ════════════
        function showError(msg) {
            const box = document.getElementById('errorMsg');
            box.querySelector('.msg-text').textContent = msg;
            box.style.display = 'flex';
            box.style.animation = 'none';
            void box.offsetWidth;
            box.style.animation = 'shake 0.4s ease, msgPop 0.4s cubic-bezier(.16,1,.3,1)';
            document.getElementById('successMsg').style.display = 'none';
        }

        function hideMessages() {
            document.getElementById('errorMsg').style.display = 'none';
            document.getElementById('successMsg').style.display = 'none';
        }

        function resetButton() {
            const btn = document.getElementById('submitBtn');
            const t = i18n[currentLang];
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-arrow-right-to-bracket"></i> <span>${t.btn_login}</span>`;
        }

        function setLoading() {
            const btn = document.getElementById('submitBtn');
            const t = i18n[currentLang];
            btn.disabled = true;
            btn.innerHTML = `<div class="spinner"></div> <span>${t.btn_loading}</span>`;
        }

        // ════════════ LOGIN FLOW ════════════
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            hideMessages();

            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('password').value;
            const t = i18n[currentLang];

            if (!email || !pass) {
                showError(t.err_invalid);
                return;
            }

            setLoading();

            // Step 1: Get API token via Sanctum
            fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email, password: pass })
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function(result) {
                if (!result.ok || !result.data.success) {
                    let msg = t.err_invalid;
                    if (result.data.message) msg = result.data.message;
                    showError(msg);
                    resetButton();
                    clearAuthState();
                    return;
                }

                const token = result.data.data.token;
                const user = result.data.data.user;
                const role = user.roles[0];
                const selectedRole = currentRole;

                if (role !== selectedRole) {
                    const labels = {
                        admin: currentLang === 'ar' ? 'الأدمن' : 'Admin',
                        parent: currentLang === 'ar' ? 'ولي الأمر' : 'Parent',
                        driver: currentLang === 'ar' ? 'السائق' : 'Driver'
                    };
                    const article = selectedRole === 'admin' ? 'an' : 'a';
                    showError(
                        currentLang === 'ar'
                            ? `هذا الحساب مسجل كـ ${labels[role] || role}. اختر تبويب ${labels[role] || role} أو استخدم حساب ${labels[selectedRole] || selectedRole}.`
                            : `This account is registered as ${labels[role] || role}. Select the ${labels[role] || role} tab or use ${article} ${labels[selectedRole] || selectedRole} account.`
                    );
                    resetButton();
                    clearAuthState();
                    return;
                }

                // Store API token
                localStorage.setItem('token', token);
                localStorage.setItem('safestep_token', token);
                localStorage.setItem('safestep_user', JSON.stringify(user));
                localStorage.setItem('safestep_role', role);

                // Step 2: Create web session
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', pass);
                formData.append('role', role);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('/login', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(sessionRes) {
                    return sessionRes.json().catch(function() { return {}; });
                })
                .then(function(sessionData) {
                    // Use server redirect or role-based path
                    const dest = (sessionData && sessionData.redirect)
                        ? sessionData.redirect
                        : (roleConfig[role] ? roleConfig[role].dashboardPath : '/');
                    window.location.href = dest;
                })
                .catch(function() {
                    // Session creation failed — use token-based redirect
                    const dest = roleConfig[role] ? roleConfig[role].dashboardPath : '/';
                    window.location.href = dest;
                });
            })
            .catch(function(err) {
                console.error('[login] Network error:', err);
                showError(t.err_network);
                resetButton();
            });
        });

        // ════════════ INIT ════════════
        // Check URL for role hint
        const urlParams = new URLSearchParams(window.location.search);
        const roleHint = urlParams.get('role');
        if (roleHint && roleConfig[roleHint]) {
            selectRole(roleHint);
        }

        if (initialServerError) {
            showError(initialServerError);
        }

        // Apply saved language
        applyLang();

    })();
    </script>
</body>
</html>
