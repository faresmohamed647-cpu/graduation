<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انضم إلى SafeStep — SafeStep Bus</title>
    <link rel="icon" href="{{ asset('img/icon.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f172a;
            --surface:   #1e293b;
            --card:      #1e293b;
            --border:    #334155;
            --accent:    #6366f1;
            --accent-h:  #818cf8;
            --success:   #22c55e;
            --text:      #f1f5f9;
            --muted:     #94a3b8;
            --parent:    #3b82f6;
            --driver:    #10b981;
            --school:    #8b5cf6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 40px;
            background: rgba(15, 23, 42, .92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-links { display: flex; gap: 24px; list-style: none; align-items: center; }
        .navbar-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color .2s;
        }
        .navbar-links a:hover { color: var(--text); }
        .btn-login {
            padding: 8px 18px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            color: #fff !important;
            font-weight: 600;
        }

        .hero {
            text-align: center;
            padding: 56px 24px 24px;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(99,102,241,.14) 0%, transparent 70%);
            pointer-events: none;
        }
        .step-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .04em;
            margin-bottom: 20px;
            background: rgba(99,102,241,.12);
            color: var(--accent-h);
            border: 1px solid rgba(99,102,241,.3);
        }
        .hero h1 {
            font-size: clamp(28px, 5vw, 40px);
            font-weight: 800;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        .hero p {
            font-size: 16px;
            color: var(--muted);
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.7;
        }
        .hero .en-sub {
            display: block;
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
        }

        .role-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1040px;
            margin: 40px auto 48px;
            padding: 0 24px;
            width: 100%;
        }

        .role-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 36px 28px 32px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            overflow: hidden;
        }
        .role-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .25s ease;
            pointer-events: none;
        }
        .role-card.parent::before  { background: linear-gradient(135deg, rgba(59,130,246,.08), transparent 60%); }
        .role-card.driver::before  { background: linear-gradient(135deg, rgba(16,185,129,.08), transparent 60%); }
        .role-card.school::before  { background: linear-gradient(135deg, rgba(139,92,246,.08), transparent 60%); }
        .role-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
        }
        .role-card:hover::before { opacity: 1; }
        .role-card.parent:hover  { border-color: rgba(59,130,246,.45); }
        .role-card.driver:hover  { border-color: rgba(16,185,129,.45); }
        .role-card.school:hover  { border-color: rgba(139,92,246,.45); }

        .role-card .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            font-size: 32px;
            position: relative;
            z-index: 1;
        }
        .role-card.parent .icon  { background: rgba(59,130,246,.15); color: var(--parent); }
        .role-card.driver .icon  { background: rgba(16,185,129,.15); color: var(--driver); }
        .role-card.school .icon  { background: rgba(139,92,246,.15); color: var(--school); }

        .role-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }
        .role-card .role-en {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }
        .role-card p {
            font-size: .9rem;
            color: var(--muted);
            line-height: 1.65;
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }
        .role-card .cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            position: relative;
            z-index: 1;
            transition: gap .2s ease;
        }
        .role-card:hover .cta { gap: 12px; }
        .role-card.parent .cta  { background: rgba(59,130,246,.15); color: #60a5fa; }
        .role-card.driver .cta  { background: rgba(16,185,129,.15); color: #34d399; }
        .role-card.school .cta  { background: rgba(139,92,246,.15); color: #a78bfa; }

        .features-strip {
            max-width: 1040px;
            margin: 0 auto 64px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: rgba(30,41,59,.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 13px;
            color: var(--muted);
        }
        .feature-item i { color: var(--accent-h); font-size: 18px; flex-shrink: 0; }

        .footer-note {
            text-align: center;
            padding: 0 24px 48px;
            color: var(--muted);
            font-size: 14px;
        }
        .footer-note a { color: var(--accent-h); text-decoration: none; font-weight: 600; }
        .footer-note a:hover { text-decoration: underline; }

        @media (max-width: 900px) {
            .role-cards { grid-template-columns: 1fr; max-width: 420px; }
            .features-strip { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .navbar { padding: 14px 20px; }
            .navbar-links li:not(:last-child) { display: none; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ url('/') }}" class="navbar-brand">
        <i class="fas fa-shield-alt"></i> SafeStep Bus
    </a>
    <ul class="navbar-links">
        <li><a href="{{ url('/') }}">الرئيسية</a></li>
        <li><a href="{{ url('/login') }}" class="btn-login"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a></li>
    </ul>
</nav>

<section class="hero">
    <div class="step-pill"><i class="fas fa-hand-pointer"></i> الخطوة 1 — اختر دورك</div>
    <h1>انضم إلى SafeStep</h1>
    <p>
        اختر كيف تريد الانضمام إلى شبكة النقل المدرسي الذكية.
        <span class="en-sub">Choose your role to continue with registration.</span>
    </p>
</section>

<div class="role-cards">
    <a href="{{ url('/apply/parent') }}" class="role-card parent">
        <div class="icon"><i class="fas fa-users"></i></div>
        <h3>ولي أمر</h3>
        <div class="role-en">Parent</div>
        <p>تابع أبناءك، استلم التنبيهات، واطمئن على رحلاتهم اليومية بأمان.</p>
        <span class="cta">ابدأ التسجيل <i class="fas fa-arrow-left"></i></span>
    </a>
    <a href="{{ url('/apply/driver') }}" class="role-card driver">
        <div class="icon"><i class="fas fa-id-card"></i></div>
        <h3>سائق</h3>
        <div class="role-en">Driver</div>
        <p>انضم لأسطولنا، أدر الرحلات، وضمن سلامة الطلاب على الطريق.</p>
        <span class="cta">ابدأ التسجيل <i class="fas fa-arrow-left"></i></span>
    </a>
    <a href="{{ url('/apply/school') }}" class="role-card school">
        <div class="icon"><i class="fas fa-school"></i></div>
        <h3>مدرسة</h3>
        <div class="role-en">School</div>
        <p>سجّل مدرستك لإدارة الطلاب والحافلات والمسارات والعمليات.</p>
        <span class="cta">ابدأ التسجيل <i class="fas fa-arrow-left"></i></span>
    </a>
</div>

<div class="features-strip">
    <div class="feature-item"><i class="fas fa-map-marker-alt"></i><span>تتبع مباشر للحافلات والطلاب</span></div>
    <div class="feature-item"><i class="fas fa-bell"></i><span>تنبيهات فورية لأولياء الأمور</span></div>
    <div class="feature-item"><i class="fas fa-shield-alt"></i><span>نظام موحّد للإدارة والمدارس والسائقين</span></div>
</div>

<p class="footer-note">
    لديك حساب بالفعل؟ <a href="{{ url('/login') }}">سجّل الدخول من هنا</a>
</p>

</body>
</html>
