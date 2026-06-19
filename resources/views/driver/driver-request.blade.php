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
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap');

    :root {
      --font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
      --font-title: 'Outfit', sans-serif;
      
      /* Color Palette */
      --bg-primary: #f8fafc;
      --bg-mesh: radial-gradient(circle at 0% 0%, rgba(243, 244, 246, 0.8) 0%, rgba(229, 231, 235, 0.4) 100%);
      --card-bg: rgba(255, 255, 255, 0.65);
      --card-border: rgba(255, 255, 255, 0.45);
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      
      --primary: #6366f1;
      --primary-glow: rgba(99, 102, 241, 0.15);
      --secondary: #0ea5e9;
      --secondary-glow: rgba(14, 165, 233, 0.15);
      --accent: #8b5cf6;
      
      --success: #10b981;
      --success-bg: rgba(16, 185, 129, 0.1);
      --success-border: rgba(16, 185, 129, 0.2);
      --success-text: #065f46;
      
      --warning: #f59e0b;
      --warning-bg: rgba(245, 158, 11, 0.1);
      --warning-border: rgba(245, 158, 11, 0.2);
      --warning-text: #92400e;
      
      --danger: #ef4444;
      --danger-bg: rgba(239, 68, 68, 0.1);
      --danger-border: rgba(239, 68, 68, 0.2);
      --danger-text: #991b1b;
      
      --input-bg: rgba(255, 255, 255, 0.8);
      --input-border: rgba(226, 232, 240, 0.8);
      
      /* Animations & Transitions */
      --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      --card-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.05), 0 0 0 1px rgba(15, 23, 42, 0.05);
    }

    /* Dark mode variables override */
    body.dark-mode {
      --bg-primary: #090d16;
      --bg-mesh: radial-gradient(circle at 0% 0%, rgba(17, 24, 39, 0.8) 0%, rgba(9, 13, 22, 0.95) 100%);
      --card-bg: rgba(17, 24, 39, 0.6);
      --card-border: rgba(255, 255, 255, 0.06);
      --text-primary: #f8fafc;
      --text-secondary: #cbd5e1;
      --text-muted: #64748b;
      
      --primary-glow: rgba(99, 102, 241, 0.3);
      --secondary-glow: rgba(14, 165, 233, 0.3);
      
      --success-bg: rgba(16, 185, 129, 0.15);
      --success-border: rgba(16, 185, 129, 0.3);
      --success-text: #a7f3d0;
      
      --warning-bg: rgba(245, 158, 11, 0.15);
      --warning-border: rgba(245, 158, 11, 0.3);
      --warning-text: #fde68a;
      
      --danger-bg: rgba(239, 68, 68, 0.15);
      --danger-border: rgba(239, 68, 68, 0.3);
      --danger-text: #fecaca;
      
      --input-bg: rgba(15, 23, 42, 0.6);
      --input-border: rgba(255, 255, 255, 0.08);
      --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      background: var(--bg-primary);
      font-family: var(--font-family);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      position: relative;
      overflow-x: hidden;
      color: var(--text-primary);
      transition: background-color var(--transition-slow), color var(--transition-normal);
    }
    
    /* Animated Floating Background Orbs */
    body::before, body::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      filter: blur(140px);
      pointer-events: none;
      z-index: 0;
      opacity: 0.35;
      animation: floatOrb 20s infinite alternate ease-in-out;
    }
    
    body::before {
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, var(--primary) 0%, rgba(99, 102, 241, 0) 70%);
      top: -10%;
      left: -10%;
    }
    
    body::after {
      width: 550px;
      height: 550px;
      background: radial-gradient(circle, var(--secondary) 0%, rgba(14, 165, 233, 0) 70%);
      bottom: -15%;
      right: -10%;
      animation-delay: -7s;
    }
    
    @keyframes floatOrb {
      0% { transform: translate(0, 0) scale(1) rotate(0deg); }
      50% { transform: translate(60px, -40px) scale(1.1) rotate(180deg); }
      100% { transform: translate(-40px, 60px) scale(0.95) rotate(360deg); }
    }

    .container {
      width: 100%;
      max-width: 900px;
      position: relative;
      z-index: 2;
      animation: containerAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes containerAppear {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.98);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Header Redesign */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      gap: 16px;
      animation: slideInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .header-main {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .header .back-link {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      color: var(--text-secondary);
      padding: 10px 18px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      transition: all var(--transition-normal);
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      backdrop-filter: blur(12px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .header .back-link i {
      transition: transform var(--transition-fast);
    }

    .header .back-link:hover {
      background: var(--primary);
      border-color: var(--primary);
      color: #ffffff;
      transform: translateX(-4px);
      box-shadow: 0 10px 20px -8px var(--primary-glow);
    }

    .header .back-link:hover i {
      transform: translateX(-3px);
    }

    .header h1 {
      color: var(--text-primary);
      font-family: var(--font-title);
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .header-tools {
      display: flex;
      align-items: center;
    }

    .theme-toggle {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      color: var(--text-secondary);
      border-radius: 14px;
      padding: 10px 18px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      backdrop-filter: blur(12px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      transition: all var(--transition-normal);
    }

    .theme-toggle:hover {
      background: var(--text-primary);
      color: var(--bg-primary);
      transform: translateY(-2px);
      box-shadow: 0 12px 20px -8px rgba(0,0,0,0.15);
    }

    /* Card Styling: Glassmorphism */
    .card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 24px;
      box-shadow: var(--card-shadow);
      overflow: hidden;
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      position: relative;
      transition: transform var(--transition-slow), box-shadow var(--transition-normal), border-color var(--transition-normal);
      margin-bottom: 24px;
    }

    .card:hover {
      box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.12), 0 0 0 1px var(--card-border);
      border-color: rgba(255, 255, 255, 0.15);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
      z-index: 1;
    }

    .card-header {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(14, 165, 233, 0.08) 100%);
      border-bottom: 1px solid var(--card-border);
      padding: 28px 32px;
      position: relative;
      overflow: hidden;
    }

    .card-header h3 {
      font-family: var(--font-title);
      font-size: 22px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 12px;
      color: var(--text-primary);
    }

    .card-header h3 i {
      color: var(--primary);
      font-size: 24px;
    }

    .card-header p {
      margin-top: 6px;
      color: var(--text-secondary);
      font-size: 14px;
      font-weight: 500;
    }

    .form-section {
      padding: 32px;
    }

    .form-section-title {
      font-family: var(--font-title);
      font-size: 15px;
      font-weight: 700;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 24px;
      margin-top: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--card-border);
    }

    .form-section-title i {
      font-size: 16px;
    }

    .form-group {
      margin-bottom: 24px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text-secondary);
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: color var(--transition-fast);
    }

    .form-group label .icon {
      color: var(--text-muted);
      font-size: 14px;
      transition: transform var(--transition-normal), color var(--transition-fast);
    }

    .form-group:focus-within label {
      color: var(--primary);
    }

    .form-group:focus-within label .icon {
      color: var(--primary);
      transform: scale(1.15) rotate(5deg);
    }

    .form-control {
      width: 100%;
      padding: 14px 18px;
      border: 1.5px solid var(--input-border);
      border-radius: 14px;
      font-size: 14px;
      font-family: inherit;
      color: var(--text-primary);
      background: var(--input-bg);
      transition: all var(--transition-normal);
      outline: none;
    }

    .form-control::placeholder {
      color: var(--text-muted);
      opacity: 0.7;
    }

    .form-control:hover {
      border-color: rgba(99, 102, 241, 0.3);
      box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.02);
    }

    .form-control:focus {
      border-color: var(--primary);
      background: var(--card-bg);
      box-shadow: 0 0 0 4px var(--primary-glow);
      transform: translateY(-1px);
    }

    textarea.form-control {
      resize: vertical;
      min-height: 120px;
      line-height: 1.6;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
        gap: 0;
      }
      
      .form-section {
        padding: 20px;
      }
      
      .card-header {
        padding: 20px 24px;
      }
      
      .button-group {
        flex-direction: column;
      }
    }

    /* Custom Styled Priority Radio Toggles */
    .priority-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
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
      padding: 14px;
      border: 1.5px solid var(--input-border);
      border-radius: 14px;
      cursor: pointer;
      transition: all var(--transition-normal);
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 0;
      background: var(--input-bg);
      color: var(--text-secondary);
      gap: 6px;
    }

    .priority-option label:hover {
      transform: translateY(-2px);
      border-color: rgba(99, 102, 241, 0.3);
      box-shadow: 0 6px 15px -4px rgba(0,0,0,0.06);
    }

    .priority-option input[type="radio"]:checked + label {
      transform: translateY(-2px) scale(1.02);
      font-weight: 800;
    }

    .priority-option.low input[type="radio"]:checked + label {
      border-color: var(--success);
      background: var(--success-bg);
      color: var(--success-text);
      box-shadow: 0 10px 20px -8px rgba(16, 185, 129, 0.3);
    }

    .priority-option.medium input[type="radio"]:checked + label {
      border-color: var(--warning);
      background: var(--warning-bg);
      color: var(--warning-text);
      box-shadow: 0 10px 20px -8px rgba(245, 158, 11, 0.3);
    }

    .priority-option.high input[type="radio"]:checked + label {
      border-color: var(--danger);
      background: var(--danger-bg);
      color: var(--danger-text);
      box-shadow: 0 10px 20px -8px rgba(239, 68, 68, 0.3);
    }

    /* File input styling */
    input[type="file"]::file-selector-button {
      background: var(--primary);
      border: none;
      color: white;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 12px;
      cursor: pointer;
      transition: background var(--transition-fast);
      margin-right: 12px;
    }

    input[type="file"]::file-selector-button:hover {
      background: var(--accent);
    }

    /* Checkbox alignment & style */
    input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
      cursor: pointer;
    }

    /* Custom styled Submit / Reset buttons */
    .button-group {
      display: flex;
      gap: 16px;
      margin-top: 32px;
    }

    .btn {
      flex: 1;
      padding: 16px 24px;
      border: none;
      border-radius: 14px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all var(--transition-normal);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .btn-submit {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
    }

    .btn-submit::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
      transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-submit:hover:not(:disabled)::after {
      left: 100%;
    }

    .btn-submit:hover:not(:disabled) {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px -8px var(--primary-glow);
    }

    .btn-submit:active:not(:disabled) {
      transform: translateY(-1px);
    }

    .btn-submit:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .btn-reset {
      background: var(--input-bg);
      border: 1.5px solid var(--input-border);
      color: var(--text-secondary);
    }

    .btn-reset:hover {
      background: var(--danger-bg);
      border-color: var(--danger);
      color: var(--danger-text);
      transform: translateY(-3px);
      box-shadow: 0 12px 24px -8px rgba(239, 68, 68, 0.2);
    }

    .btn-reset:active {
      transform: translateY(-1px);
    }

    /* My Requests List Items Redesign */
    .request-item {
      background: var(--input-bg) !important;
      border: 1px solid var(--input-border) !important;
      border-radius: 16px !important;
      padding: 18px !important;
      margin-bottom: 12px !important;
      transition: all var(--transition-normal) !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02) !important;
    }

    .request-item:hover {
      transform: translateY(-2px) scale(1.01);
      box-shadow: 0 12px 20px -8px rgba(15, 23, 42, 0.08) !important;
      border-color: rgba(99, 102, 241, 0.3) !important;
    }

    .request-item strong {
      font-family: var(--font-title);
      font-size: 15px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .request-item span[style*="background"] {
      border-radius: 10px !important;
      padding: 6px 14px !important;
      font-size: 11px !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px !important;
      box-shadow: 0 4px 8px -2px rgba(0, 0, 0, 0.05) !important;
    }

    .request-item .fa-calendar, .request-item .fa-envelope {
      color: var(--primary);
      margin-right: 4px;
    }

    /* Alerts and feedback styling */
    .alert {
      padding: 16px 20px;
      border-radius: 14px;
      margin-bottom: 24px;
      display: none;
      font-size: 13px;
      font-weight: 600;
      align-items: center;
      gap: 12px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
      animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .alert.show {
      display: flex;
    }

    .alert-success {
      background: var(--success-bg);
      color: var(--success-text);
      border: 1.5px solid var(--success-border);
    }

    .alert-error {
      background: var(--danger-bg);
      color: var(--danger-text);
      border: 1.5px solid var(--danger-border);
    }

    .alert-info {
      background: var(--warning-bg);
      color: var(--warning-text);
      border: 1.5px solid var(--warning-border);
    }

    .helper-text {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 6px;
    }

    .spinner {
      display: inline-block;
      width: 18px;
      height: 18px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .required {
      color: var(--danger);
      font-weight: 700;
      margin-left: 2px;
    }

    /* Custom premium scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(99, 102, 241, 0.2);
      border-radius: 99px;
      border: 2px solid transparent;
      background-clip: padding-box;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(99, 102, 241, 0.4);
      border-radius: 99px;
      border: 2px solid transparent;
      background-clip: padding-box;
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

    <!-- MY REQUESTS -->
    <div class="card" style="margin-bottom: 20px;" id="myRequestsCard">
      <div class="card-header" style="background: linear-gradient(135deg, #0ea5a4, #2563eb);">
        <h3><i class="fas fa-list-check"></i> My Requests (<span id="myRequestsCount">{{ $serviceRequests->count() }}</span>)</h3>
        <p>Your previously submitted requests</p>
      </div>
      <div class="form-section" id="myRequestsList">
        @forelse($serviceRequests as $req)
        <div class="request-item" data-id="{{ $req->id }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <strong style="color: #1e293b;">{{ $req->subject }}</strong>
            @php
              $statusColors = [
                'pending' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                'in-progress' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                'resolved' => ['bg' => '#f0fdf4', 'color' => '#16a34a'],
                'rejected' => ['bg' => '#fee2e2', 'color' => '#dc2626'],
              ];
              $style = $statusColors[$req->status] ?? ['bg' => '#f0f9ff', 'color' => '#1e40af'];
            @endphp
            <span style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase;">{{ $req->status }}</span>
          </div>
          <div style="color: #64748b; font-size: 13px; margin-top: 6px;">
            <i class="fas fa-calendar"></i> {{ $req->created_at->format('M d, Y') }}
            <span style="margin: 0 8px;">|</span>
            <i class="fas fa-tag"></i> {{ str_replace('_', ' ', $req->request_type) }}
            <span style="margin: 0 8px;">|</span>
            <i class="fas fa-flag"></i> {{ ucfirst($req->priority) }}
          </div>
          @if($req->description)
          <div style="color: #475569; font-size: 13px; margin-top: 6px; border-top: 1px dashed #e2e8f0; padding-top: 6px;">{{ Str::limit($req->description, 120) }}</div>
          @endif
        </div>
        @empty
        <div id="noRequestsMsg" style="text-align:center;padding:24px;color:#94a3b8;">
          <i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>
          No requests yet. Submit your first request below.
        </div>
        @endforelse
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

        <form id="driverRequestForm" class="ajax-form" action="#" method="POST" data-keep-values>
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
              value="{{ $driverProfile->full_name ?? $user->name ?? '' }}"
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
                value="{{ $user->driverProfile->phone ?? '' }}"
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
                value="{{ $user->email ?? '' }}"
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
                value="{{ $driverProfile->license_number ?? '' }}"
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
                placeholder="DRV-001" 
                value="{{ $driverProfile ? 'DRV-' . str_pad((string) $driverProfile->id, 3, '0', STR_PAD_LEFT) : '' }}"
                readonly
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
                placeholder="{{ $assignedBus ? '' : 'No bus assigned yet — enter manually if known' }}" 
                value="{{ $assignedBus?->bus_number ? 'Bus #' . $assignedBus->bus_number : '' }}"
                @if($assignedBus) readonly @endif
                {{ $assignedBus ? 'required' : '' }}
              >
              @unless($assignedBus)
              <div class="helper-text">No bus is assigned to you yet. You can still submit the request.</div>
              @endunless
            </div>

            <div class="form-group">
              <label for="route">
                <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                Route <span class="required">*</span>
              </label>
              <select id="route" class="form-control" {{ ($routes->isNotEmpty() || $assignedRoute) ? 'required' : '' }}>
                <option value="">Select Route</option>
                @forelse($routes as $routeOption)
                <option value="{{ $routeOption->name }}" @selected($assignedRoute && $assignedRoute->id === $routeOption->id)>{{ $routeOption->name }}</option>
                @empty
                @if($assignedRoute)
                <option value="{{ $assignedRoute->name }}" selected>{{ $assignedRoute->name }}</option>
                @endif
                @endforelse
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
                value="{{ $driverProfile->years_experience ?? '' }}"
                required
              >
            </div>

            <div class="form-group">
              <label for="employmentStatus">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                Employment Status <span class="required">*</span>
              </label>
              <select id="employmentStatus" class="form-control" required>
                @php
                  $employment = $employmentStatus ?? 'pending';
                @endphp
                <option value="active" @selected($employment === 'active')>Active</option>
                <option value="pending" @selected($employment === 'pending')>Pending Approval</option>
                <option value="probation" @selected($employment === 'probation')>Probation</option>
                <option value="contract" @selected($employment === 'contract')>Contract</option>
                <option value="inactive" @selected($employment === 'inactive')>Inactive</option>
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
    window.__REQUEST_CONTEXT = @json($requestContext);
    window.__API_TOKEN = '{{ $apiToken ?? '' }}';
    if (window.__API_TOKEN) {
      localStorage.setItem('safestep_token', window.__API_TOKEN);
      localStorage.setItem('token', window.__API_TOKEN);
    }

    function requestStatusStyle(status) {
      if (status === 'resolved') return { bg: '#f0fdf4', color: '#16a34a' };
      if (status === 'rejected') return { bg: '#fee2e2', color: '#dc2626' };
      if (status === 'in-progress') return { bg: '#fffbeb', color: '#d97706' };
      if (status === 'pending') return { bg: '#fef2f2', color: '#dc2626' };
      return { bg: '#f0f9ff', color: '#1e40af' };
    }

    function escapeHtml(value) {
      return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
      }[char]));
    }

    function renderMyRequests(requests) {
      const list = document.getElementById('myRequestsList');
      const count = document.getElementById('myRequestsCount');
      if (!list) return;
      if (count) count.textContent = requests.length;
      if (!requests.length) {
        list.innerHTML = '<div id="noRequestsMsg" style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>No requests yet</div>';
        return;
      }
      list.innerHTML = requests.map(req => {
        const style = requestStatusStyle(req.status);
        const created = req.created_at ? String(req.created_at).slice(0, 10) : '';
        return `<div class="request-item" data-id="${req.id}" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:10px;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <strong style="color:#1e293b;">${escapeHtml(req.subject || 'Request')}</strong>
            <span style="background:${style.bg};color:${style.color};padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;text-transform:uppercase;">${escapeHtml(req.status)}</span>
          </div>
          <div style="color:#64748b;font-size:13px;margin-top:6px;">
            <i class="fas fa-calendar"></i> ${escapeHtml(created)}
            <span style="margin:0 8px;">|</span>
            <i class="fas fa-tag"></i> ${escapeHtml((req.request_type || '').replace(/_/g, ' '))}
            <span style="margin:0 8px;">|</span>
            <i class="fas fa-flag"></i> ${escapeHtml(req.priority || 'medium')}
          </div>
          ${req.description ? `<div style="color:#475569;font-size:13px;margin-top:6px;border-top:1px dashed #e2e8f0;padding-top:6px;">${escapeHtml(req.description).slice(0, 120)}</div>` : ''}
        </div>`;
      }).join('');
    }

    async function loadMyRequests() {
      if (!window.__API_TOKEN) return;
      try {
        const res = await fetch('/api/service-requests/my', {
          headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + window.__API_TOKEN }
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.status === 'success') renderMyRequests(data.data || []);
      } catch (error) {
        console.warn('Failed to refresh driver requests', error);
      }
    }

    setInterval(loadMyRequests, 10000);

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

    function resetDriverFormDefaults() {
      const ctx = window.__REQUEST_CONTEXT || {};
      document.getElementById('driverName').value = ctx.driverName || '';
      document.getElementById('driverEmail').value = ctx.driverEmail || '';
      document.getElementById('driverPhone').value = ctx.driverPhone || '';
      document.getElementById('driverLicense').value = ctx.driverLicense || '';
      document.getElementById('employeeId').value = ctx.employeeId || '';
      document.getElementById('busNumber').value = ctx.busNumber || '';
      document.getElementById('route').value = ctx.route || '';
      document.getElementById('yearsExperience').value = ctx.yearsExperience ?? '';
      document.getElementById('employmentStatus').value = ctx.employmentStatus || 'pending';
      document.getElementById('priorityMedium').checked = true;
    }

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

      if (!document.getElementById('acknowledgment').checked) {
        showAlert('Please confirm that the information is accurate', 'error');
        return;
      }

      const submitBtn = driverForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<div class="spinner"></div><span>Submitting...</span>';

      const requestType = document.getElementById('requestType').value;
      const subject = document.getElementById('subject').value.trim();
      const description = document.getElementById('description').value.trim();
      const priority = document.querySelector('input[name="priority"]:checked').value;

      // Build metadata with full request context
      const metadata = {
        driverName: document.getElementById('driverName').value.trim(),
        driverEmail: document.getElementById('driverEmail').value.trim(),
        driverPhone: document.getElementById('driverPhone').value.trim(),
        driverLicense: document.getElementById('driverLicense').value.trim(),
        employeeId: document.getElementById('employeeId').value.trim(),
        busNumber: document.getElementById('busNumber').value,
        route: document.getElementById('route').value,
        yearsExperience: document.getElementById('yearsExperience').value,
        employmentStatus: document.getElementById('employmentStatus').value,
        leaveStartDate: document.getElementById('leaveStartDate')?.value || null,
        leaveEndDate: document.getElementById('leaveEndDate')?.value || null,
        leaveType: document.getElementById('leaveType')?.value || null,
        maintenanceType: document.getElementById('maintenanceType')?.value || null,
        maintenanceUrgency: document.getElementById('maintenanceUrgency')?.value || null,
      };

      const token = window.__API_TOKEN || localStorage.getItem('safestep_token') || localStorage.getItem('token') || '';

      try {
        const res = await fetch('/api/service-requests', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(token ? { 'Authorization': 'Bearer ' + token } : {})
          },
          body: JSON.stringify({
            request_type: requestType,
            subject: subject,
            description: description,
            priority: priority,
            notes: `Subject: ${subject}\nPriority: ${priority}\nBus: ${metadata.busNumber}\nRoute: ${metadata.route}`,
            metadata: metadata
          })
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.status === 'error') {
          throw new Error(data.message || 'Failed to submit request');
        }

        showAlert('✓ Request saved successfully!', 'success');
        driverForm.reset();
        resetDriverFormDefaults();
        loadMyRequests();

        // Dynamically prepend new request without reload
        const req = data.data;
        if (req) {
          const list = document.getElementById('myRequestsList');
          const noMsg = document.getElementById('noRequestsMsg');
          if (noMsg) noMsg.remove();

          const countSpan = document.getElementById('myRequestsCount');
          if (countSpan) countSpan.textContent = (parseInt(countSpan.textContent) || 0) + 1;

          const card = document.createElement('div');
          card.className = 'request-item';
          card.style.cssText = 'background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:10px;border-left:4px solid #6366f1;';
          const statusBg = req.status === 'pending' ? '#fef2f2' : (req.status === 'resolved' ? '#f0fdf4' : '#f0f9ff');
          const statusColor = req.status === 'pending' ? '#dc2626' : (req.status === 'resolved' ? '#16a34a' : '#1e40af');
          card.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <strong style="color:#1e293b;">${req.request_type || 'Request'}</strong>
              <span style="background:${statusBg};color:${statusColor};padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;text-transform:uppercase;">${req.status}</span>
            </div>
            <div style="color:#64748b;font-size:13px;margin-top:6px;">
              <i class="fas fa-calendar"></i> Just now
              <span style="margin:0 8px;">|</span>
              <i class="fas fa-envelope"></i> ${req.subject || ''}
            </div>
          `;
          list.prepend(card);
        }

      } catch (err) {
        console.warn('Error sending driver request', err);
        showAlert('⚠ ' + err.message, 'error');
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
    loadMyRequests();
  </script>
  <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
