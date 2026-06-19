<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Parent Request - School Bus Tracking</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap');

    /* Brand Variables */
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
      
      --primary: #0ea5a4;
      --primary-glow: rgba(14, 165, 164, 0.15);
      --secondary: #2563eb;
      --secondary-glow: rgba(37, 99, 235, 0.15);
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

    /* Dark Mode variables override */
    body.dark-mode {
      --bg-primary: #090d16;
      --bg-mesh: radial-gradient(circle at 0% 0%, rgba(17, 24, 39, 0.8) 0%, rgba(9, 13, 22, 0.95) 100%);
      --card-bg: rgba(17, 24, 39, 0.6);
      --card-border: rgba(255, 255, 255, 0.06);
      --text-primary: #f8fafc;
      --text-secondary: #cbd5e1;
      --text-muted: #64748b;
      
      --primary-glow: rgba(14, 165, 164, 0.3);
      --secondary-glow: rgba(37, 99, 235, 0.3);
      
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
      background: radial-gradient(circle, var(--primary) 0%, rgba(14, 165, 164, 0) 70%);
      top: -10%;
      left: -10%;
    }
    
    body::after {
      width: 550px;
      height: 550px;
      background: radial-gradient(circle, var(--secondary) 0%, rgba(37, 99, 235, 0) 70%);
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

    /* Header Styling */
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
      background: var(--secondary);
      border-color: var(--secondary);
      color: #ffffff;
      transform: translateX(-4px);
      box-shadow: 0 10px 20px -8px var(--secondary-glow);
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
      background: linear-gradient(135deg, rgba(14, 165, 164, 0.08) 0%, rgba(37, 99, 235, 0.08) 100%);
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
      border-color: rgba(14, 165, 164, 0.3);
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
      border-color: rgba(14, 165, 164, 0.3);
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

    /* Children container & items styles */
    #childrenContainer {
      border: 1.5px solid var(--input-border) !important;
      border-radius: 14px !important;
      padding: 16px !important;
      background: var(--input-bg);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .child-item {
      background: var(--card-bg) !important;
      border: 1px solid var(--card-border) !important;
      border-radius: 10px !important;
      padding: 12px 16px !important;
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      transition: all var(--transition-normal);
      margin-bottom: 0 !important;
    }

    .child-item:hover {
      background: rgba(255, 255, 255, 0.9) !important;
      border-color: rgba(14, 165, 164, 0.3) !important;
      transform: translateX(4px) scale(1.01);
      box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.05);
    }

    body.dark-mode .child-item:hover {
      background: rgba(31, 41, 55, 0.8) !important;
      border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .child-item input[type="checkbox"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: var(--primary);
      transition: transform var(--transition-fast);
    }

    .child-item input[type="checkbox"]:checked {
      transform: scale(1.1);
    }

    .child-item label {
      flex: 1;
      margin: 0 !important;
      font-size: 14px;
      font-weight: 600 !important;
      cursor: pointer;
      color: var(--text-secondary) !important;
    }

    .child-item:hover label {
      color: var(--text-primary) !important;
    }

    /* Location styles */
    .location-input-group {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .location-btn {
      padding: 14px 20px;
      background: var(--input-bg);
      border: 1.5px solid var(--input-border);
      border-radius: 14px;
      color: var(--text-secondary);
      cursor: pointer;
      font-weight: 700;
      font-size: 13px;
      transition: all var(--transition-normal);
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
      height: 48px;
    }

    .location-btn:hover:not(:disabled) {
      background: var(--primary-glow);
      border-color: var(--primary);
      color: var(--primary);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px -4px var(--primary-glow);
    }

    .location-btn:active {
      transform: translateY(-1px);
    }

    .location-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    body.dark-mode .location-btn {
      background: var(--input-bg);
      border-color: var(--input-border);
      color: var(--text-secondary);
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
      border: 1.5px solid var(--input-border) !important;
      border-radius: 16px !important;
      padding: 18px !important;
      margin-bottom: 12px !important;
      transition: all var(--transition-normal) !important;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02) !important;
      display: block !important;
    }

    .request-item:hover {
      transform: translateY(-2px) scale(1.01) !important;
      box-shadow: 0 12px 20px -8px rgba(15, 23, 42, 0.08) !important;
      border-color: rgba(14, 165, 164, 0.3) !important;
    }

    .request-item strong {
      font-family: var(--font-title);
      font-size: 15px;
      font-weight: 700;
      color: var(--text-primary) !important;
    }

    .request-item span[style*="background"] {
      border-radius: 10px !important;
      padding: 6px 14px !important;
      font-size: 11px !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px !important;
      box-shadow: 0 4px 8px -2px rgba(0, 0, 0, 0.05) !important;
      text-transform: uppercase !important;
    }

    .request-item div[style*="color: #64748b"] {
      color: var(--text-muted) !important;
      font-size: 13px !important;
      margin-top: 8px !important;
    }

    .request-item div[style*="color: #475569"] {
      color: var(--text-secondary) !important;
      font-size: 13px !important;
      margin-top: 8px !important;
      border-top: 1px dashed var(--input-border) !important;
      padding-top: 8px !important;
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
      animation: alertSlideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }

    @keyframes alertSlideDown {
      from {
        opacity: 0;
        transform: translateY(-16px) scale(0.98);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .alert.show {
      display: flex;
    }

    .alert-success {
      background: var(--success-bg);
      border: 1px solid var(--success-border);
      color: var(--success-text);
    }

    .alert-error {
      background: var(--danger-bg);
      border: 1px solid var(--danger-border);
      color: var(--danger-text);
    }

    .alert-info {
      background: rgba(37, 99, 235, 0.08);
      border: 1px solid rgba(37, 99, 235, 0.15);
      color: var(--secondary);
    }

    .spinner {
      width: 18px;
      height: 18px;
      border: 2.5px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      display: inline-block;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .required {
      color: var(--danger);
      font-weight: 700;
      margin-left: 2px;
    }

    .helper-text {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 6px;
      display: block;
    }

    .theme-toggle i {
      font-size: 14px;
      transition: transform var(--transition-normal);
    }
    
    .theme-toggle:hover i {
      transform: rotate(15deg) scale(1.1);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-main">
            <a href="/parent" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1>Parent Request</h1>
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
          <i class="fas fa-envelope-open-text"></i> Submit a Request
        </h3>
        <p>Submit your request regarding your child's school bus services</p>
      </div>

      <div class="form-section">
        <div id="alertContainer"></div>

        <form id="parentRequestForm" class="ajax-form" action="#" method="POST" data-keep-values>
          <!-- Parent Information Section -->
          <div class="form-section-title">
            <i class="fas fa-user"></i> Parent Information
          </div>

          <div class="form-group">
            <label for="parentName">
              <span class="icon"><i class="fas fa-user-circle"></i></span>
              Full Name <span class="required">*</span>
            </label>
            <input 
              type="text" 
              id="parentName" 
              class="form-control" 
              placeholder="e.g., Sarah Ahmed Hassan" 
              value="{{ $user->name ?? '' }}"
              required
            >
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="parentPhone">
                <span class="icon"><i class="fas fa-phone"></i></span>
                Phone <span class="required">*</span>
              </label>
              <input 
                type="tel" 
                id="parentPhone" 
                class="form-control" 
                placeholder="+20 100 123 4567" 
                pattern="[0-9+\s\-\(\)]+"
                value="{{ $parentProfile?->phone ?? '' }}"
                required
              >
            </div>

            <div class="form-group">
              <label for="parentEmail">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                Email <span class="required">*</span>
              </label>
              <input 
                type="email" 
                id="parentEmail" 
                class="form-control" 
                placeholder="your.email@example.com" 
                value="{{ $user->email ?? '' }}"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label for="parentLocation">
              <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
              Your Location <span class="required">*</span>
            </label>
            <div class="location-input-group">
              <input 
                type="text" 
                id="parentLocation" 
                class="form-control" 
                placeholder="Enter your address or area..." 
                value="{{ $parentProfile?->address ?? '' }}"
                required
              >
              <button type="button" class="location-btn" onclick="getParentLocation()">
                <i class="fas fa-location-dot"></i> Detect
              </button>
            </div>
            <div class="helper-text">Your home location for pickup arrangements</div>
          </div>

          <!-- Children Information -->
          <div class="form-section-title">
            <i class="fas fa-child"></i> Children Information
          </div>

          <div class="form-group">
            <label>
              <span class="icon"><i class="fas fa-users"></i></span>
              Select Children <span class="required">*</span>
            </label>
            <div id="childrenContainer" style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 12px;">
              @forelse($children as $child)
              @php
                $busLabel = $child->bus?->bus_number ? 'Bus #' . $child->bus->bus_number : 'No bus assigned';
              @endphp
              <div class="child-item">
                <input
                  type="checkbox"
                  id="child{{ $child->id }}"
                  name="children"
                  value="{{ $child->full_name }}"
                  data-child-id="{{ $child->id }}"
                  data-grade="{{ $child->grade ?? '' }}"
                  data-school="{{ $child->school_name ?? '' }}"
                  data-bus="{{ $busLabel }}"
                >
                <label for="child{{ $child->id }}">{{ $child->full_name }} ({{ $child->grade ?? '—' }}) — {{ $busLabel }}</label>
              </div>
              @empty
              <div style="text-align:center;padding:16px;color:#94a3b8;font-size:14px;">
                <i class="fas fa-child" style="font-size:20px;margin-bottom:8px;display:block;"></i>
                No children registered yet. Add your children from the dashboard first.
              </div>
              @endforelse
            </div>
            <div class="helper-text">Select one or more children for this request</div>
          </div>

          <!-- School Information -->
          <div class="form-section-title">
            <i class="fas fa-school"></i> School Details
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="school">
                <span class="icon"><i class="fas fa-graduation-cap"></i></span>
                School Name <span class="required">*</span>
              </label>
              <select id="school" class="form-control" @if($schools->isNotEmpty()) required @endif onchange="updateGrades()">
                <option value="">Select School</option>
                @foreach($schools as $schoolName)
                <option value="{{ $schoolName }}" @selected($loop->first && $schools->count() === 1)>{{ $schoolName }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="grade">
                <span class="icon"><i class="fas fa-book"></i></span>
                Grade <span class="required">*</span>
              </label>
              <select id="grade" class="form-control" @if($children->isNotEmpty()) required @endif>
                <option value="">Select Grade</option>
                @foreach($children->pluck('grade')->filter()->unique() as $gradeOption)
                <option value="{{ $gradeOption }}">{{ $gradeOption }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Request Type Section -->
          <div class="form-section-title">
            <i class="fas fa-question-circle"></i> Request Type
          </div>

          <div class="form-group">
            <label>
              <span class="icon"><i class="fas fa-list-ul"></i></span>
              Request Category <span class="required">*</span>
            </label>
            <select id="requestType" class="form-control" required onchange="updateRequestFields()">
              <option value="">Select Request Type</option>
              <option value="pickup_change">📍 Change Pickup Location</option>
              <option value="dropoff_change">📍 Change Drop-off Location</option>
              <option value="absence">🚫 Absence Request</option>
              <option value="temporary_stop">⏸️ Temporary Bus Stop</option>
              <option value="fees">💳 Fees/Payment Issue</option>
              <option value="special_needs">♿ Special Requirements</option>
              <option value="complaint">😞 Complaint/Concern</option>
              <option value="emergency">🆘 Emergency Contact Update</option>
              <option value="other">📋 Other</option>
            </select>
          </div>

          <!-- Conditional Fields -->
          <div id="pickupChangeFields" style="display: none;">
            <div class="form-group">
              <label for="newPickupLocation">
                <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                New Pickup Location
              </label>
              <input 
                type="text" 
                id="newPickupLocation" 
                class="form-control" 
                placeholder="e.g., Near Central Park Gate"
              >
              <div class="helper-text">Provide clear address details</div>
            </div>
          </div>

          <div id="absenceFields" style="display: none;">
            <div class="form-row">
              <div class="form-group">
                <label for="absenceStartDate">
                  <span class="icon"><i class="fas fa-calendar-start"></i></span>
                  Start Date
                </label>
                <input type="date" id="absenceStartDate" class="form-control">
              </div>
              <div class="form-group">
                <label for="absenceEndDate">
                  <span class="icon"><i class="fas fa-calendar-end"></i></span>
                  End Date
                </label>
                <input type="date" id="absenceEndDate" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label for="absenceReason">
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                Reason for Absence
              </label>
              <select id="absenceReason" class="form-control">
                <option value="">Select Reason</option>
                <option value="vacation">Vacation</option>
                <option value="illness">Illness</option>
                <option value="medical">Medical Appointment</option>
                <option value="family">Family Matter</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div id="specialNeedsFields" style="display: none;">
            <div class="form-group">
              <label for="specialNeeds">
                <span class="icon"><i class="fas fa-accessibility"></i></span>
                Special Requirements
              </label>
              <select id="specialNeeds" class="form-control">
                <option value="">Select Type</option>
                <option value="wheelchair">Wheelchair Access</option>
                <option value="mobility">Mobility Assistance</option>
                <option value="medical">Medical Care Required</option>
                <option value="behavioral">Behavioral Support</option>
                <option value="other_special">Other</option>
              </select>
            </div>
          </div>

          <!-- Request Details Section -->
          <div class="form-section-title">
            <i class="fas fa-details"></i> Request Details
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
            <div class="helper-text">Medical certificates, proof of residence, etc. (Max 5MB)</div>
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
        console.warn('Failed to refresh parent requests', error);
      }
    }

    setInterval(loadMyRequests, 10000);

    const parentForm = document.getElementById('parentRequestForm');
    const themeToggleBtn = document.getElementById('themeToggle');
    const THEME_STORAGE_KEY = 'safestep-theme';
    const alertContainer = document.getElementById('alertContainer');
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const requestTypeSelect = document.getElementById('requestType');

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
      document.getElementById('pickupChangeFields').style.display = 
        (requestType === 'pickup_change' || requestType === 'dropoff_change') ? 'block' : 'none';
      document.getElementById('absenceFields').style.display = 
        (requestType === 'absence' || requestType === 'temporary_stop') ? 'block' : 'none';
      document.getElementById('specialNeedsFields').style.display = 
        requestType === 'special_needs' ? 'block' : 'none';
    }

    function updateGrades() {
      const school = document.getElementById('school')?.value;
      const gradeSelect = document.getElementById('grade');
      const children = window.__REQUEST_CONTEXT?.children || [];
      if (!gradeSelect) return;

      const grades = [...new Set(
        children
          .filter(child => !school || child.school_name === school)
          .map(child => child.grade)
          .filter(Boolean)
      )];

      const current = gradeSelect.value;
      gradeSelect.innerHTML = '<option value="">Select Grade</option>' +
        grades.map(grade => `<option value="${escapeHtml(grade)}">${escapeHtml(grade)}</option>`).join('');

      if (grades.includes(current)) {
        gradeSelect.value = current;
      } else if (grades.length === 1) {
        gradeSelect.value = grades[0];
      }
    }

    function resetParentFormDefaults() {
      const ctx = window.__REQUEST_CONTEXT || {};
      document.getElementById('parentName').value = ctx.parentName || '';
      document.getElementById('parentEmail').value = ctx.parentEmail || '';
      document.getElementById('parentPhone').value = ctx.parentPhone || '';
      document.getElementById('parentLocation').value = ctx.parentLocation || '';
      document.getElementById('priorityMedium').checked = true;
      updateGrades();
    }

    requestTypeSelect.addEventListener('change', updateRequestFields);

    function getParentLocation() {
      const locationInput = document.getElementById('parentLocation');
      
      if (!navigator.geolocation) {
        showAlert('⚠ Geolocation not supported by your browser', 'error');
        return;
      }

      const btn = event.target.closest('.location-btn');
      btn.disabled = true;
      btn.innerHTML = '<div class="spinner"></div> Detecting...';

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          const lat = latitude.toFixed(6);
          const lng = longitude.toFixed(6);
          locationInput.value = `${lat}, ${lng}`;
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect';
          showAlert('✓ Location detected successfully', 'success');
        },
        (error) => {
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-location-dot"></i> Detect';
          showAlert('⚠ Unable to detect location. Please enter manually.', 'error');
        }
      );
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

    parentForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Validation
      const childInputs = document.querySelectorAll('input[name="children"]');
      const selectedChildren = document.querySelectorAll('input[name="children"]:checked');
      if (childInputs.length > 0 && selectedChildren.length === 0) {
        showAlert('Please select at least one child', 'error');
        return;
      }

      if (!document.getElementById('acknowledgment').checked) {
        showAlert('Please confirm that the information is accurate', 'error');
        return;
      }

      const submitBtn = parentForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<div class="spinner"></div><span>Submitting...</span>';

      const childrenSelected = Array.from(selectedChildren).map(c => ({
        id: c.dataset.childId,
        name: c.value,
        grade: c.dataset.grade,
        school: c.dataset.school,
        bus: c.dataset.bus,
      }));
      const children = childrenSelected.map(c => c.name).join(', ');
      const requestType = document.getElementById('requestType').value;
      const subject = document.getElementById('subject').value.trim();
      const description = document.getElementById('description').value.trim();
      const priority = document.querySelector('input[name="priority"]:checked').value;

      // Build metadata with full request context
      const metadata = {
        children: childrenSelected,
        school: document.getElementById('school').value,
        grade: document.getElementById('grade').value,
        parentName: document.getElementById('parentName').value.trim(),
        parentEmail: document.getElementById('parentEmail').value.trim(),
        parentPhone: document.getElementById('parentPhone').value.trim(),
        parentLocation: document.getElementById('parentLocation').value.trim(),
        newPickupLocation: document.getElementById('newPickupLocation')?.value || null,
        absenceStartDate: document.getElementById('absenceStartDate')?.value || null,
        absenceEndDate: document.getElementById('absenceEndDate')?.value || null,
        absenceReason: document.getElementById('absenceReason')?.value || null,
        specialNeeds: document.getElementById('specialNeeds')?.value || null,
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
            notes: `Subject: ${subject}\nPriority: ${priority}\nChildren: ${children}`,
            metadata: metadata
          })
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.status === 'error') {
          throw new Error(data.message || 'Failed to submit request');
        }

        showAlert('✓ Request saved successfully!', 'success');
        parentForm.reset();
        resetParentFormDefaults();
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
        console.warn('Error sending parent request', err);
        showAlert('⚠ ' + err.message, 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });

    // Form validation feedback on blur
    parentForm.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('blur', () => {
        if (input.hasAttribute('required') && !input.value.trim()) {
          input.style.borderColor = '#ef4444';
        } else {
          input.style.borderColor = '#e5e7eb';
        }
      });
    });

    // Set minimum date to today for absence start date
    document.getElementById('absenceStartDate')?.addEventListener('click', (e) => {
      const today = new Date().toISOString().split('T')[0];
      e.target.min = today;
    });

    console.log('👨‍👩‍👧‍👦 Parent Request Form initialized');
    updateGrades();
    loadMyRequests();
  </script>
  <script src="{{ asset('js/ajax-forms.js') }}"></script>
</body>
</html>
