<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-token" content="{{ config('services.applications.token') }}">
    <title>{{ $pageTitle ?? 'Apply - SafeStep' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --danger:    #ef4444;
            --warning:   #f59e0b;
            --text:      #f1f5f9;
            --muted:     #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
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
        }
        .navbar-links { display: flex; gap: 24px; list-style: none; }
        .navbar-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color .2s;
        }
        .navbar-links a:hover { color: var(--text); }

        /* ── Hero ── */
        .hero {
            text-align: center;
            padding: 56px 24px 32px;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 20px;
            border: 1px solid;
        }
        .role-pill.parent  { background: rgba(59,130,246,.12);  color: #60a5fa; border-color: rgba(59,130,246,.3); }
        .role-pill.driver  { background: rgba(34,197,94,.12);   color: #4ade80; border-color: rgba(34,197,94,.3); }
        .role-pill.admin   { background: rgba(168,85,247,.12);  color: #c084fc; border-color: rgba(168,85,247,.3); }

        .hero h1 { font-size: 36px; font-weight: 800; margin-bottom: 12px; }
        .hero p   { font-size: 16px; color: var(--muted); max-width: 520px; margin: 0 auto; }

        /* ── Form card ── */
        .form-wrap {
            max-width: 760px;
            margin: 0 auto 64px;
            padding: 0 20px;
            width: 100%;
        }
        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px;
        }
        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--accent-h);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }
        label .req { color: var(--danger); margin-left: 3px; }
        input, textarea, select {
            width: 100%;
            padding: 11px 14px;
            background: rgba(15, 23, 42, .6);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        input.error, textarea.error, select.error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239,68,68,.12);
        }
        select option { background: var(--surface); }
        textarea { resize: vertical; min-height: 110px; }
        .field-error {
            font-size: 12px;
            color: var(--danger);
            display: none;
            margin-top: 2px;
        }
        .field-error.visible { display: block; }

        /* Divider */
        .divider { margin: 24px 0; border: none; border-top: 1px solid var(--border); }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: opacity .2s, transform .15s;
            margin-top: 8px;
        }
        .btn-submit:hover:not(:disabled) { opacity: .9; transform: translateY(-1px); }
        .btn-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
            display: none;
        }
        .alert.visible { display: flex; }
        .alert-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); color: #4ade80; }
        .alert-error   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.3);  color: #f87171; }
        .alert i { margin-top: 1px; flex-shrink: 0; }

        /* Success state */
        .success-screen {
            text-align: center;
            padding: 48px 24px;
            display: none;
        }
        .success-screen.visible { display: block; }
        .success-icon {
            width: 80px; height: 80px;
            background: rgba(34,197,94,.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--success);
            margin: 0 auto 24px;
        }
        .success-screen h2 { font-size: 26px; font-weight: 700; margin-bottom: 10px; }
        .success-screen p  { color: var(--muted); margin-bottom: 28px; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: var(--accent);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: opacity .2s;
        }
        .btn-back:hover { opacity: .85; }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-card  { padding: 24px 20px; }
            .navbar { padding: 14px 20px; }
            .navbar-links { display: none; }
            .hero h1 { font-size: 26px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ url('/') }}" class="navbar-brand"><i class="fas fa-shield-alt"></i> SafeStep</a>
    <ul class="navbar-links">
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/apply') }}">Apply</a></li>
        <li><a href="{{ url('/login') }}">Login</a></li>
    </ul>
</nav>

<div class="hero">
    <div class="role-pill {{ $activeRole ?? 'parent' }}">
        <i class="fas fa-{{ ($activeRole ?? 'parent') === 'driver' ? 'id-card' : (($activeRole ?? 'parent') === 'admin' ? 'shield-alt' : 'users') }}"></i>
        {{ ucfirst($activeRole ?? 'parent') }} Application
    </div>
    <h1>{{ $formTitle ?? 'Apply Now' }}</h1>
    <p>{{ $formSubtitle ?? 'Fill in the form below to submit your application.' }}</p>
</div>

<div class="form-wrap">
    <div class="form-card">

        {{-- Success Screen --}}
        <div class="success-screen" id="successScreen">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Application Submitted!</h2>
            <p>Thank you for applying. Our team will review your application and get back to you soon.</p>
            <a href="{{ url('/') }}" class="btn-back"><i class="fas fa-home"></i> Back to Home</a>
        </div>

        {{-- Form --}}
        <div id="formSection">
            <div class="alert alert-success" id="alertSuccess">
                <i class="fas fa-check-circle"></i>
                <span id="alertSuccessMsg">Application submitted successfully!</span>
            </div>
            <div class="alert alert-error" id="alertError">
                <i class="fas fa-exclamation-circle"></i>
                <span id="alertErrorMsg">Something went wrong. Please try again.</span>
            </div>

            <form id="applicationForm" novalidate>
                <input type="hidden" name="role" value="{{ $activeRole ?? 'parent' }}">

                {{-- Personal Info --}}
                <div class="section-label"><i class="fas fa-user"></i> Personal Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="field_name">Full Name <span class="req">*</span></label>
                        <input type="text" id="field_name" name="name" required placeholder="e.g. Ahmed Mohamed">
                        <span class="field-error" id="err_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="field_email">Email Address <span class="req">*</span></label>
                        <input type="email" id="field_email" name="email" required placeholder="you@example.com">
                        <span class="field-error" id="err_email"></span>
                    </div>
                    <div class="form-group">
                        <label for="field_phone">Phone Number <span class="req">*</span></label>
                        <input type="tel" id="field_phone" name="phone" required placeholder="01XXXXXXXXX">
                        <span class="field-error" id="err_phone"></span>
                    </div>
                    <div class="form-group">
                        <label for="field_address">Address <span class="req">*</span></label>
                        <input type="text" id="field_address" name="address" required placeholder="City, Governorate">
                        <span class="field-error" id="err_address"></span>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full">
                        <label for="field_experience">Experience / Background <span class="req">*</span></label>
                        <textarea id="field_experience" name="experience" required placeholder="Briefly describe your background and experience..."></textarea>
                        <span class="field-error" id="err_experience"></span>
                    </div>
                </div>

                {{-- Role-specific fields --}}
                @if(!empty($extraFields))
                <hr class="divider">
                <div class="section-label"><i class="fas fa-tag"></i>
                    @if(($activeRole ?? '') === 'parent')   Student & School Details
                    @elseif(($activeRole ?? '') === 'driver') Vehicle & Owner Details
                    @else                                    Additional Information
                    @endif
                </div>
                <div class="form-grid">
                    @foreach($extraFields as $field)
                    <div class="form-group {{ !empty($field['full']) ? 'full' : '' }}">
                        <label for="field_{{ $field['name'] }}">
                            {{ $field['label'] }}
                            @if(!empty($field['required'])) <span class="req">*</span> @endif
                        </label>

                        @if(($field['type'] ?? 'text') === 'select')
                            <select id="field_{{ $field['name'] }}" name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}>
                                <option value="">-- Select --</option>
                                @foreach($field['options'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        @elseif(($field['type'] ?? 'text') === 'number')
                            <input type="number" id="field_{{ $field['name'] }}" name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                placeholder="{{ $field['placeholder'] ?? '' }}">
                        @else
                            <input type="text" id="field_{{ $field['name'] }}" name="{{ $field['name'] }}"
                                {{ !empty($field['required']) ? 'required' : '' }}
                                placeholder="{{ $field['placeholder'] ?? '' }}">
                        @endif

                        <span class="field-error" id="err_{{ $field['name'] }}"></span>
                    </div>
                    @endforeach
                </div>
                @endif

                <hr class="divider">
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="field_notes">Additional Notes</label>
                        <textarea id="field_notes" name="notes" placeholder="Any additional information you'd like to share (optional)..." style="min-height:80px"></textarea>
                        <span class="field-error" id="err_notes"></span>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    {{ $submitLabel ?? 'Submit Application' }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const API_TOKEN = document.querySelector('meta[name="api-token"]').content;
    const form      = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitBtn');

    // ── Clear field errors
    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => {
            el.textContent = '';
            el.classList.remove('visible');
        });
        document.querySelectorAll('input, textarea, select').forEach(el => {
            el.classList.remove('error');
        });
        hideAlert('alertSuccess');
        hideAlert('alertError');
    }

    // ── Show / hide global alerts
    function showAlert(id, message) {
        const el = document.getElementById(id);
        const msgEl = document.getElementById(id + 'Msg');
        if (msgEl) msgEl.textContent = message;
        el.classList.add('visible');
    }
    function hideAlert(id) {
        document.getElementById(id)?.classList.remove('visible');
    }

    // ── Set field error
    function setFieldError(fieldName, message) {
        const errEl  = document.getElementById('err_' + fieldName);
        const inpEl  = document.getElementById('field_' + fieldName) ||
                       form.querySelector('[name="' + fieldName + '"]');
        if (errEl) { errEl.textContent = message; errEl.classList.add('visible'); }
        if (inpEl) inpEl.classList.add('error');
    }

    // ── Set loading state
    function setLoading(loading) {
        submitBtn.disabled = loading;
        submitBtn.innerHTML = loading
            ? '<i class="fas fa-spinner fa-spin"></i> Submitting...'
            : '<i class="fas fa-paper-plane"></i> {{ $submitLabel ?? "Submit Application" }}';
    }

    // ── Collect form data as JSON
    function collectData() {
        const data = {};
        new FormData(form).forEach((value, key) => {
            data[key] = value;
        });
        return data;
    }

    // ── Submit handler
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const payload = collectData();

        try {
            const response = await fetch('/api/applications', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                // Show success screen
                document.getElementById('formSection').style.display = 'none';
                document.getElementById('successScreen').classList.add('visible');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // Validation errors (422)
            if (response.status === 422 && result.errors) {
                let firstFocused = false;
                for (const [field, messages] of Object.entries(result.errors)) {
                    const msg = Array.isArray(messages) ? messages[0] : messages;
                    setFieldError(field, msg);
                    if (!firstFocused) {
                        const el = document.getElementById('field_' + field) ||
                                   form.querySelector('[name="' + field + '"]');
                        if (el) { el.focus(); firstFocused = true; }
                    }
                }
                showAlert('alertError', result.message || 'Please fix the errors above.');
            } else if (response.status === 401) {
                showAlert('alertError', 'Authorization error. Please refresh and try again.');
            } else {
                showAlert('alertError', result.message || 'Unexpected error. Please try again.');
            }

        } catch (err) {
            showAlert('alertError', 'Network error. Please check your connection and try again.');
            console.error('Submission error:', err);
        } finally {
            setLoading(false);
        }
    });

    // ── Clear field error on user input
    form.querySelectorAll('input, textarea, select').forEach(el => {
        el.addEventListener('input', function () {
            this.classList.remove('error');
            const name = this.name;
            const errEl = document.getElementById('err_' + name);
            if (errEl) { errEl.textContent = ''; errEl.classList.remove('visible'); }
        });
    });
})();
</script>
</body>
</html>
