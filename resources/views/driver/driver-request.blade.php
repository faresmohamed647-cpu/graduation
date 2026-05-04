<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Driver Request - School Bus Tracking</title>
  <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
      animation: backgroundMove 20s ease-in-out infinite;
    }

    @keyframes backgroundMove {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(-20px, -20px) scale(1.1); }
    }

    .container {
      width: 100%;
      max-width: 700px;
      animation: containerFadeIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      z-index: 1;
    }

    @keyframes containerFadeIn {
      from {
        opacity: 0;
        transform: scale(0.97);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .header {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
      gap: 15px;
      animation: slideInLeft 0.5s ease-out;
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .header a {
      background: #ffffff;
      border: 2px solid #e1e8f0;
      color: #475569;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      font-size: 18px;
    }

    .header a:hover {
      background: #f8fafc;
      border-color: #64748b;
      transform: translateX(-3px) scale(1.05);
    }

    .header a:active {
      transform: translateX(-3px) scale(0.95);
    }

    .header h1 {
      color: #1e293b;
      font-size: 26px;
      margin: 0;
      font-weight: 600;
    }

    .card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
      overflow: hidden;
      animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
      backdrop-filter: blur(10px);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
      animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(25px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      background-size: 200% 200%;
      color: white;
      padding: 32px;
      animation: slideInTop 0.8s ease-out, gradientAnimation 8s ease infinite;
      position: relative;
      overflow: hidden;
    }

    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .card-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
      animation: rotate 15s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes slideInTop {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-header h3 {
      font-size: 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      animation: fadeInUp 0.8s ease-out 0.2s both;
      position: relative;
      z-index: 1;
    }

    .card-header h3 i {
      font-size: 28px;
      animation: iconBounce 2s ease-in-out infinite;
    }

    @keyframes iconBounce {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-5px) rotate(5deg); }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-header p {
      margin-top: 12px;
      opacity: 0.95;
      font-size: 14px;
      font-weight: 500;
      animation: fadeInUp 0.8s ease-out 0.3s both;
      position: relative;
      z-index: 1;
    }

    .form-section {
      padding: 28px;
      animation: fadeIn 0.6s ease-out 0.2s both;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .form-group {
      margin-bottom: 20px;
      animation: slideInLeft 0.5s ease-out backwards;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.15s; }
    .form-group:nth-child(3) { animation-delay: 0.2s; }
    .form-group:nth-child(4) { animation-delay: 0.25s; }
    .form-group:nth-child(5) { animation-delay: 0.3s; }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #1e293b;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .form-group label .icon {
      color: #6366f1;
      font-size: 15px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-group:hover label .icon {
      transform: scale(1.2) rotate(5deg);
    }

    .form-control {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #e2e8f0;
      border-radius: 6px;
      font-size: 14px;
      font-family: inherit;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background: #ffffff;
    }

    .form-control:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 0 12px rgba(102, 126, 234, 0.3);
      transform: translateY(-2px);
      background: #fafbff;
    }

    .form-control:hover {
      border-color: #cbd5e1;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .form-control:invalid {
      border-color: #ef4444;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 110px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    @media (max-width: 600px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    .priority-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }

    .priority-option {
      position: relative;
    }

    .priority-option input[type="radio"] {
      display: none;
    }

    .priority-option label {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 11px;
      border: 2px solid #e2e8f0;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-weight: 500;
      font-size: 13px;
      margin-bottom: 0;
      background: #f8fafc;
    }

    .priority-option label:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .priority-option input[type="radio"]:checked + label {
      border-color: #6366f1;
      background: #eef2ff;
      color: #4f46e5;
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .priority-option.low input[type="radio"]:checked + label {
      border-color: #22c55e;
      background: #f0fdf4;
      color: #16a34a;
      box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
    }

    .priority-option.medium input[type="radio"]:checked + label {
      border-color: #f59e0b;
      background: #fffbeb;
      color: #d97706;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .priority-option.high input[type="radio"]:checked + label {
      border-color: #ef4444;
      background: #fef2f2;
      color: #dc2626;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }

    .button-group {
      display: flex;
      gap: 12px;
      margin-top: 30px;
    }

    .btn {
      flex: 1;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .btn:active::before {
      width: 300px;
      height: 300px;
    }

    .btn-submit {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      background-size: 200% 200%;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .btn-submit::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s;
    }

    .btn-submit:hover:not(:disabled)::after {
      left: 100%;
    }

    .btn-submit:hover:not(:disabled) {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 24px rgba(102, 126, 234, 0.5);
      background-position: right center;
    }

    .btn-submit:active:not(:disabled) {
      transform: translateY(-1px);
    }

    .btn-submit:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-reset {
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #e2e8f0;
    }

    .btn-reset:hover {
      background: #e2e8f0;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .btn-reset:active {
      transform: translateY(0px);
    }

    .alert {
      padding: 14px;
      border-radius: 6px;
      margin-bottom: 20px;
      display: none;
      animation: slideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      font-size: 13px;
      font-weight: 500;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .alert.show {
      display: block;
    }

    .alert-success {
      background: #f0fdf4;
      color: #166534;
      border-left: 4px solid #22c55e;
    }

    .alert-error {
      background: #fef2f2;
      color: #7f1d1d;
      border-left: 4px solid #ef4444;
    }

    .alert-info {
      background: #f0f9ff;
      color: #1e40af;
      border-left: 4px solid #3b82f6;
    }

    .spinner {
      display: inline-block;
      width: 14px;
      height: 14px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .required {
      color: #ef4444;
      font-weight: 700;
    }

    .helper-text {
      font-size: 12px;
      color: #64748b;
      margin-top: 4px;
      animation: fadeIn 0.3s ease-out 0.2s backwards;
    }

    .form-section-title {
      font-size: 13px;
      font-weight: 700;
      color: #4f46e5;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin-bottom: 18px;
      padding-bottom: 10px;
      border-bottom: 2px solid #e2e8f0;
      animation: slideInLeft 0.5s ease-out;
      transition: all 0.3s ease;
    }

    .form-section-title:hover {
      transform: translateX(3px);
      color: #6366f1;
    }
  </style>
  <style>
    :root {
      --req-bg: #f6f7fb;
      --req-card: #ffffff;
      --req-text: #0f172a;
      --req-muted: #64748b;
      --req-border: #dbe3ee;
      --req-accent: #0ea5a4;
      --req-accent-2: #2563eb;
      --req-success: #16a34a;
      --req-warning: #d97706;
      --req-danger: #dc2626;
    }

    body {
      background: var(--req-bg);
      min-height: 100vh;
      align-items: flex-start;
      padding: 28px 16px;
      color: var(--req-text);
    }

    body::before {
      background:
        radial-gradient(circle at 12% 10%, rgba(14, 165, 164, 0.10) 0%, transparent 42%),
        radial-gradient(circle at 90% 90%, rgba(37, 99, 235, 0.10) 0%, transparent 35%);
      animation: none;
    }

    .container {
      max-width: 980px;
    }

    .header {
      margin-bottom: 18px;
      justify-content: space-between;
    }

    .header-main {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .header a {
      border-radius: 12px;
      width: 42px;
      height: 42px;
      border: 1px solid var(--req-border);
      color: #334155;
      box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    }

    .header a:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      transform: translateX(-2px);
    }

    .header h1 {
      color: var(--req-text);
      font-weight: 700;
      font-size: 28px;
    }

    .header-tools {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .theme-toggle {
      border: 1px solid var(--req-border);
      background: #fff;
      color: #334155;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
      transition: all 0.2s ease;
    }

    .theme-toggle:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      transform: translateY(-1px);
    }

    .card {
      border-radius: 16px;
      box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
      backdrop-filter: none;
    }

    .card::before {
      height: 4px;
      background: linear-gradient(90deg, var(--req-accent), var(--req-accent-2));
      animation: none;
    }

    .card-header {
      background: linear-gradient(135deg, var(--req-accent), var(--req-accent-2));
      padding: 26px 28px;
      animation: none;
    }

    .card-header::before,
    .card-header h3 i {
      animation: none;
    }

    .card-header h3 {
      font-size: 22px;
    }

    .form-section {
      padding: 24px 28px 28px;
    }

    .form-row {
      gap: 16px;
    }

    .form-section-title {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-left: 4px solid var(--req-accent-2);
      color: #1e293b;
      border-radius: 10px;
      margin-bottom: 14px;
    }

    .form-control {
      border: 1px solid var(--req-border);
      border-radius: 10px;
      background: #fff;
      box-shadow: none;
    }

    .form-control:hover {
      border-color: #94a3b8;
    }

    .form-control:focus {
      border-color: var(--req-accent-2);
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .helper-text {
      color: var(--req-muted);
    }

    .priority-group {
      gap: 10px;
    }

    .priority-option label {
      border: 1px solid var(--req-border);
      background: #f8fafc;
      border-radius: 10px;
      color: #334155;
      font-weight: 600;
    }

    .priority-option label:hover {
      transform: none;
      border-color: #94a3b8;
    }

    .priority-option.low input[type="radio"]:checked + label {
      border-color: var(--req-success);
    }

    .priority-option.medium input[type="radio"]:checked + label {
      border-color: var(--req-warning);
    }

    .priority-option.high input[type="radio"]:checked + label {
      border-color: var(--req-danger);
    }

    .button-group {
      justify-content: flex-end;
      gap: 12px;
    }

    .btn {
      border-radius: 10px;
      font-weight: 700;
      transition: all 0.2s ease;
    }

    .btn-submit {
      background: linear-gradient(135deg, var(--req-accent), var(--req-accent-2));
      box-shadow: 0 8px 20px rgba(14, 165, 164, 0.22);
    }

    .btn-submit::after {
      display: none;
    }

    .btn-submit:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(37, 99, 235, 0.22);
    }

    .btn-reset {
      border: 1px solid #cbd5e1;
      background: #fff;
      color: #334155;
    }

    .btn-reset:hover {
      background: #f8fafc;
      transform: translateY(-1px);
    }

    .alert {
      border-radius: 10px;
      border-width: 1px;
      box-shadow: none;
    }

    input[type="checkbox"] {
      accent-color: var(--req-accent-2);
    }

    body.dark-mode {
      background: #0f172a;
      color: #e2e8f0;
    }

    body.dark-mode::before {
      background:
        radial-gradient(circle at 10% 10%, rgba(14, 165, 164, 0.22) 0%, transparent 38%),
        radial-gradient(circle at 85% 85%, rgba(37, 99, 235, 0.22) 0%, transparent 34%);
    }

    body.dark-mode .header h1 {
      color: #e5e7eb;
    }

    body.dark-mode .header a,
    body.dark-mode .theme-toggle {
      background: #111827;
      color: #e2e8f0;
      border-color: #334155;
      box-shadow: none;
    }

    body.dark-mode .header a:hover,
    body.dark-mode .theme-toggle:hover {
      background: #1f2937;
      border-color: #475569;
    }

    body.dark-mode .card {
      background: #111827;
      border-color: #1f2937;
      box-shadow: 0 10px 30px rgba(2, 6, 23, 0.45);
    }

    body.dark-mode .card-header {
      background: linear-gradient(135deg, #0f766e, #1d4ed8);
    }

    body.dark-mode .form-section-title {
      background: #0f172a;
      border-color: #1e293b;
      color: #e2e8f0;
    }

    body.dark-mode label,
    body.dark-mode .helper-text {
      color: #94a3b8;
    }

    body.dark-mode .form-control {
      background: #0b1220;
      border-color: #334155;
      color: #e2e8f0;
    }

    body.dark-mode .form-control::placeholder {
      color: #64748b;
    }

    body.dark-mode .form-control:hover {
      border-color: #475569;
    }

    body.dark-mode .form-control:focus {
      border-color: #38bdf8;
      box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2);
    }

    body.dark-mode .priority-option label,
    body.dark-mode .btn-reset {
      background: #0f172a;
      border-color: #334155;
      color: #e2e8f0;
    }

    body.dark-mode .btn-reset:hover,
    body.dark-mode .priority-option label:hover {
      background: #1e293b;
      border-color: #475569;
    }

    body.dark-mode .alert-success {
      background: rgba(22, 163, 74, 0.18);
      border-color: rgba(22, 163, 74, 0.55);
      color: #bbf7d0;
    }

    body.dark-mode .alert-error {
      background: rgba(220, 38, 38, 0.18);
      border-color: rgba(220, 38, 38, 0.55);
      color: #fecaca;
    }

    body.dark-mode .alert-info {
      background: rgba(37, 99, 235, 0.18);
      border-color: rgba(37, 99, 235, 0.55);
      color: #bfdbfe;
    }

    @media (max-width: 768px) {
      body {
        padding: 16px 12px;
      }

      .header {
        gap: 10px;
        align-items: flex-start;
        flex-direction: column;
      }

      .header-main,
      .header-tools {
        width: 100%;
      }

      .theme-toggle {
        width: 100%;
        justify-content: center;
      }

      .form-section {
        padding: 18px 16px 22px;
      }

      .card-header {
        padding: 20px 16px;
      }

      .button-group .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-main">
        <a href="/driver" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <h1>Driver Request</h1>
      </div>
      <div class="header-tools">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
          <i class="fas fa-moon"></i>
          <span>Dark</span>
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>
          <i class="fas fa-envelope"></i> Submit a Request
        </h3>
        <p>Fill out the form below to submit your request to the admin</p>
      </div>

      <div class="form-section">
        <div id="alertContainer"></div>

        <form id="driverRequestForm">
          <!-- Personal Information Section -->
          <div class="form-section-title">
            <i class="fas fa-user"></i> Personal Information
          </div>

          <div class="form-group">
            <label for="driverName">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
              Full Name <span class="required">*</span>
            </label>
            <input 
              type="text" 
              id="driverName" 
              class="form-control" 
              placeholder="e.g., Ahmed Mohamed Hassan" 
              required
            >
            <div class="helper-text">Your name as it appears in official records</div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="driverPhone">
                <span class="icon"><i class="fas fa-phone"></i></span>
                Phone <span class="required">*</span>
              </label>
              <input 
                type="tel" 
                id="driverPhone" 
                class="form-control" 
                placeholder="+20 100 123 4567" 
                pattern="[0-9+\s\-\(\)]+"
                required
              >
            </div>

            <div class="form-group">
              <label for="driverEmail">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                Email <span class="required">*</span>
              </label>
              <input 
                type="email" 
                id="driverEmail" 
                class="form-control" 
                placeholder="your.email@example.com" 
                required
              >
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="driverLicense">
                <span class="icon"><i class="fas fa-id-card"></i></span>
                License Number <span class="required">*</span>
              </label>
              <input 
                type="text" 
                id="driverLicense" 
                class="form-control" 
                placeholder="DL-123456" 
                required
              >
            </div>

            <div class="form-group">
              <label for="employeeId">
                <span class="icon"><i class="fas fa-barcode"></i></span>
                Employee ID <span class="required">*</span>
              </label>
              <input 
                type="text" 
                id="employeeId" 
                class="form-control" 
                placeholder="EMP-0001" 
                required
              >
            </div>
          </div>

          <!-- Bus & Route Section -->
          <div class="form-section-title">
            <i class="fas fa-bus"></i> Current Assignment
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="busNumber">
                <span class="icon"><i class="fas fa-bus"></i></span>
                Bus Number <span class="required">*</span>
              </label>
              <input 
                type="text" 
                id="busNumber" 
                class="form-control" 
                placeholder="e.g., Bus #42" 
                required
              >
            </div>

            <div class="form-group">
              <label for="route">
                <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                Route <span class="required">*</span>
              </label>
              <select id="route" class="form-control" required>
                <option value="">Select Route</option>
                <option value="Route A">Route A</option>
                <option value="Route B">Route B</option>
                <option value="Route C">Route C</option>
                <option value="Route D">Route D</option>
                <option value="Route E">Route E</option>
                <option value="Route F">Route F</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="yearsExperience">
                <span class="icon"><i class="fas fa-briefcase"></i></span>
                Years of Experience <span class="required">*</span>
              </label>
              <input 
                type="number" 
                id="yearsExperience" 
                class="form-control" 
                placeholder="e.g., 5" 
                min="0"
                max="50"
                required
              >
            </div>

            <div class="form-group">
              <label for="employmentStatus">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                Employment Status <span class="required">*</span>
              </label>
              <select id="employmentStatus" class="form-control" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="probation">Probation</option>
                <option value="contract">Contract</option>
              </select>
            </div>
          </div>

          <!-- Request Type Section -->
          <div class="form-section-title">
            <i class="fas fa-tasks"></i> Request Type
          </div>

          <div class="form-group">
            <label>
              <span class="icon"><i class="fas fa-list-ul"></i></span>
              Request Category <span class="required">*</span>
            </label>
            <select id="requestType" class="form-control" required onchange="updateRequestFields()">
              <option value="">Select Request Type</option>
              <option value="maintenance">🔧 Vehicle Maintenance</option>
              <option value="leave">📅 Leave Request</option>
              <option value="salary">💰 Salary/Payment Issue</option>
              <option value="schedule">🕒 Schedule Change</option>
              <option value="safety">⚠️ Safety Concern</option>
              <option value="training">📚 Training Request</option>
              <option value="complaint">😞 Complaint/Grievance</option>
              <option value="other">📋 Other</option>
            </select>
          </div>

          <!-- Conditional Fields -->
          <div id="leaveFields" style="display: none;">
            <div class="form-row">
              <div class="form-group">
                <label for="leaveStartDate">
                  <span class="icon"><i class="fas fa-calendar-start"></i></span>
                  Start Date
                </label>
                <input type="date" id="leaveStartDate" class="form-control">
              </div>
              <div class="form-group">
                <label for="leaveEndDate">
                  <span class="icon"><i class="fas fa-calendar-end"></i></span>
                  End Date
                </label>
                <input type="date" id="leaveEndDate" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label for="leaveType">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                Leave Type
              </label>
              <select id="leaveType" class="form-control">
                <option value="">Select Type</option>
                <option value="annual">Annual Leave</option>
                <option value="sick">Sick Leave</option>
                <option value="emergency">Emergency Leave</option>
                <option value="unpaid">Unpaid Leave</option>
              </select>
            </div>
          </div>

          <div id="maintenanceFields" style="display: none;">
            <div class="form-group">
              <label for="maintenanceType">
                <span class="icon"><i class="fas fa-wrench"></i></span>
                Maintenance Type
              </label>
              <select id="maintenanceType" class="form-control">
                <option value="">Select Type</option>
                <option value="engine">Engine Problem</option>
                <option value="brakes">Brakes Issue</option>
                <option value="tires">Tires Problem</option>
                <option value="ac">AC/Heating</option>
                <option value="lights">Lights</option>
                <option value="other_maint">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="maintenanceUrgency">
                <span class="icon"><i class="fas fa-exclamation"></i></span>
                Urgency Level
              </label>
              <select id="maintenanceUrgency" class="form-control">
                <option value="low">Low - Can wait</option>
                <option value="medium" selected>Medium - Soon</option>
                <option value="urgent">Urgent - Today</option>
                <option value="emergency">Emergency - Not Safe</option>
              </select>
            </div>
          </div>

          <!-- Request Details Section -->
          <div class="form-section-title">
            <i class="fas fa-list"></i> Request Details
          </div>

          <div class="form-group">
            <label for="subject">
              <span class="icon"><i class="fas fa-heading"></i></span>
              Subject/Title <span class="required">*</span>
            </label>
            <input 
              type="text" 
              id="subject" 
              class="form-control" 
              placeholder="Brief summary of your request..." 
              required
            >
          </div>

          <div class="form-group">
            <label>
              <span class="icon"><i class="fas fa-exclamation-circle"></i></span>
              Priority Level <span class="required">*</span>
            </label>
            <div class="priority-group">
              <div class="priority-option low">
                <input type="radio" id="priorityLow" name="priority" value="low">
                <label for="priorityLow">🟢 Low</label>
              </div>
              <div class="priority-option medium">
                <input type="radio" id="priorityMedium" name="priority" value="medium" checked>
                <label for="priorityMedium">🟡 Medium</label>
              </div>
              <div class="priority-option high">
                <input type="radio" id="priorityHigh" name="priority" value="high">
                <label for="priorityHigh">🔴 High</label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="description">
              <span class="icon"><i class="fas fa-align-left"></i></span>
              Detailed Description <span class="required">*</span>
            </label>
            <textarea 
              id="description" 
              class="form-control" 
              placeholder="Please provide detailed information about your request..." 
              required
            ></textarea>
            <div class="helper-text"><span id="charCount">0</span>/1000 characters</div>
          </div>

          <!-- Supporting Documents -->
          <div class="form-group">
            <label for="attachment">
              <span class="icon"><i class="fas fa-paperclip"></i></span>
              Attach Document (Optional)
            </label>
            <input 
              type="file" 
              id="attachment" 
              class="form-control" 
              accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
            >
            <div class="helper-text">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</div>
          </div>

          <!-- Acknowledgment -->
          <div class="form-group">
            <div style="display: flex; align-items: center; gap: 10px;">
              <input type="checkbox" id="acknowledgment" required>
              <label for="acknowledgment" style="margin: 0; font-size: 13px;">
                I confirm that the information provided is accurate and complete
              </label>
            </div>
          </div>

          <div class="button-group">
            <button type="submit" class="btn btn-submit">
              <i class="fas fa-paper-plane"></i>
              <span>Submit Request</span>
            </button>
            <button type="reset" class="btn btn-reset">
              <i class="fas fa-redo"></i>
              <span>Clear Form</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Server-injected API token for authenticated requests
    window.__API_TOKEN = '{{ $apiToken ?? '' }}';

    const driverForm = document.getElementById('driverRequestForm');
    const themeToggleBtn = document.getElementById('themeToggle');
    const THEME_STORAGE_KEY = 'safestep-theme';
    const alertContainer = document.getElementById('alertContainer');
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const requestTypeSelect = document.getElementById('requestType');
    const leaveFields = document.getElementById('leaveFields');
    const maintenanceFields = document.getElementById('maintenanceFields');

    function applyTheme(theme) {
      const isDark = theme === 'dark';
      document.body.classList.toggle('dark-mode', isDark);
      if (!themeToggleBtn) return;
      themeToggleBtn.innerHTML = isDark
        ? '<i class="fas fa-sun"></i><span>Light</span>'
        : '<i class="fas fa-moon"></i><span>Dark</span>';
    }

    function initThemeToggle() {
      const savedTheme = localStorage.getItem(THEME_STORAGE_KEY) || 'light';
      applyTheme(savedTheme);
      if (!themeToggleBtn) return;

      themeToggleBtn.addEventListener('click', () => {
        const nextTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        applyTheme(nextTheme);
      });
    }

    initThemeToggle();
    window.addEventListener('storage', (e) => {
      if (e.key === THEME_STORAGE_KEY) {
        applyTheme(e.newValue || 'light');
      }
    });

    // Character counter
    descriptionTextarea.addEventListener('input', (e) => {
      charCount.textContent = e.target.value.length;
      if (e.target.value.length > 1000) {
        e.target.value = e.target.value.substring(0, 1000);
        charCount.textContent = '1000';
      }
    });

    // Show/Hide conditional fields based on request type
    function updateRequestFields() {
      const requestType = requestTypeSelect.value;
      leaveFields.style.display = requestType === 'leave' ? 'block' : 'none';
      maintenanceFields.style.display = requestType === 'maintenance' ? 'block' : 'none';
    }

    requestTypeSelect.addEventListener('change', updateRequestFields);

    function showAlert(message, type = 'info') {
      const alert = document.createElement('div');
      alert.className = `alert alert-${type} show`;
      alert.innerHTML = `
        <strong>${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</strong>
        ${message}
      `;
      alertContainer.innerHTML = '';
      alertContainer.appendChild(alert);

      if (type === 'success') {
        setTimeout(() => {
          alert.classList.remove('show');
        }, 4000);
      }
    }

    driverForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Validation
      if (!document.getElementById('acknowledgment').checked) {
        showAlert('Please confirm that the information is accurate', 'error');
        return;
      }

      const submitBtn = driverForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<div class="spinner"></div><span>Submitting...</span>';

      // Build payload with all fields
      const payload = {
        user_role: 'driver',
        full_name: document.getElementById('driverName').value.trim(),
        contact_phone: document.getElementById('driverPhone').value.trim(),
        contact_email: document.getElementById('driverEmail').value.trim(),
        employee_id: document.getElementById('employeeId').value.trim(),
        license_number: document.getElementById('driverLicense').value.trim(),
        bus_number: document.getElementById('busNumber').value.trim(),
        route: document.getElementById('route').value,
        years_experience: document.getElementById('yearsExperience').value,
        employment_status: document.getElementById('employmentStatus').value,
        request_type: document.getElementById('requestType').value,
        subject: document.getElementById('subject').value.trim(),
        priority: document.querySelector('input[name="priority"]:checked').value,
        description: document.getElementById('description').value.trim(),
        
        // Additional fields for specific request types
        leave_start_date: document.getElementById('leaveStartDate')?.value || null,
        leave_end_date: document.getElementById('leaveEndDate')?.value || null,
        leave_type: document.getElementById('leaveType')?.value || null,
        maintenance_type: document.getElementById('maintenanceType')?.value || null,
        maintenance_urgency: document.getElementById('maintenanceUrgency')?.value || null,
        created_at: new Date().toISOString()
      };

      try {
        const token = window.__API_TOKEN || localStorage.getItem('token') || localStorage.getItem('safestep_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch('/api/requests', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token ? `Bearer ${token}` : ''
          },
          body: JSON.stringify({
            request_type: payload.request_type || 'driver-request',
            subject: payload.subject || 'Driver request',
            description: payload.description || '',
            context: payload
          })
        });

        if (res.status === 401) {
          localStorage.removeItem('token');
          localStorage.removeItem('safestep_token');
          window.location.href = '/login';
          return;
        }

        if (res.status === 403) {
          showAlert('Access Denied', 'error');
          return;
        }

        if (!res.ok) {
          throw new Error('Failed to send request');
        }

        showAlert('✓ Your request has been submitted successfully! Reference ID: ' + Math.random().toString(36).substr(2, 9).toUpperCase(), 'success');
        driverForm.reset();
        charCount.textContent = '0';
        document.getElementById('priorityMedium').checked = true;
        leaveFields.style.display = 'none';
        maintenanceFields.style.display = 'none';
        
      } catch (err) {
        console.warn('Error sending driver request', err);
        showAlert('⚠ Error: Unable to submit your request right now.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });

    // Form validation feedback on blur
    driverForm.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('blur', () => {
        if (input.hasAttribute('required') && !input.value.trim()) {
          input.style.borderColor = '#ef4444';
        } else {
          input.style.borderColor = '#e5e7eb';
        }
      });
    });

    // Set minimum date to today for leave start date
    document.getElementById('leaveStartDate')?.addEventListener('click', (e) => {
      const today = new Date().toISOString().split('T')[0];
      e.target.min = today;
    });

    console.log('🚗 Driver Request Form initialized');
  </script>
</body>
</html>
