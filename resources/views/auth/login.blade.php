<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SafeStep</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,Arial;background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1d4ed8 100%);margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center}
        
        .wrap{display:flex;align-items:center;justify-content:center;padding:24px;width:100%}
        
        .card{
            width:100%;max-width:440px;
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(20px);
            border-radius:24px;
            box-shadow:0 25px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            padding:40px 36px;
            animation:cardIn 0.6s cubic-bezier(.16,1,.3,1);
        }
        @keyframes cardIn{from{opacity:0;transform:translateY(30px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}
        
        .brand{display:flex;align-items:center;gap:12px;margin-bottom:8px}
        .brand .icon-wrap{
            width:48px;height:48px;
            background:linear-gradient(135deg,#0ea5a4,#2563eb);
            border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 8px 20px rgba(14,165,164,0.3);
        }
        .brand .icon-wrap i{color:#fff;font-size:22px}
        .brand h1{font-size:22px;font-weight:800;color:#0f172a;letter-spacing:-0.3px}
        
        .muted{color:#64748b;font-size:13px;margin:4px 0 24px;line-height:1.5}
        
        .role-badges{display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap}
        .role-badge{
            display:inline-flex;align-items:center;gap:5px;
            padding:6px 14px;
            border-radius:20px;
            font-size:11px;font-weight:700;
            letter-spacing:0.3px;
            text-transform:uppercase;
        }
        .role-badge.admin{background:linear-gradient(135deg,rgba(220,38,38,0.12),rgba(220,38,38,0.06));color:#991b1b;border:1px solid rgba(220,38,38,0.2)}
        .role-badge.driver{background:linear-gradient(135deg,rgba(14,165,164,0.12),rgba(14,165,164,0.06));color:#065f46;border:1px solid rgba(14,165,164,0.2)}
        .role-badge.parent{background:linear-gradient(135deg,rgba(37,99,235,0.12),rgba(37,99,235,0.06));color:#1e3a8a;border:1px solid rgba(37,99,235,0.2)}
        .role-badge i{font-size:12px}
        
        .field{margin-bottom:16px}
        .field label{display:block;font-size:12px;font-weight:700;color:#334155;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px}
        .input-wrap{position:relative}
        .input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;transition:color 0.2s}
        .toggle-password{
            position:absolute;right:10px;top:50%;transform:translateY(-50%);
            border:none;background:transparent;color:#64748b;cursor:pointer;
            width:32px;height:32px;border-radius:8px;
        }
        .input-wrap input{
            width:100%;padding:14px 14px 14px 44px;
            border:2px solid #e2e8f0;border-radius:14px;
            font-size:14px;outline:none;
            background:#f8fafc;
            transition:all 0.25s ease;
        }
        .input-wrap input:focus{border-color:#0ea5a4;background:#fff;box-shadow:0 0 0 4px rgba(14,165,164,0.12)}
        .input-wrap input:focus ~ i,
        .input-wrap:focus-within i{color:#0ea5a4}
        
        .row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:4px 0 20px}
        .row label{margin:0;font-weight:500;color:#475569;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px}
        .row input[type="checkbox"]{accent-color:#0ea5a4;width:16px;height:16px}
        
        .btn{
            width:100%;padding:14px 16px;border:none;border-radius:14px;
            background:linear-gradient(135deg,#0ea5a4,#2563eb);
            color:#fff;font-weight:700;font-size:15px;
            cursor:pointer;
            letter-spacing:0.3px;
            transition:all 0.3s ease;
            box-shadow:0 8px 24px rgba(14,165,164,0.25);
        }
        .btn:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(14,165,164,0.35)}
        .btn:active{transform:translateY(0);box-shadow:0 4px 12px rgba(14,165,164,0.2)}
        
        .success-msg{
            background:linear-gradient(135deg,rgba(5,150,105,0.12),rgba(5,150,105,0.06));
            color:#065f46;
            border:1px solid rgba(5,150,105,0.25);
            padding:14px 16px;border-radius:14px;
            font-size:13px;font-weight:600;
            margin-bottom:16px;
            display:flex;align-items:center;gap:8px;
            animation:fadeIn 0.4s ease;
        }
        .success-msg i{font-size:16px;color:#059669}
        
        .err{
            background:linear-gradient(135deg,rgba(220,38,38,0.1),rgba(220,38,38,0.04));
            color:#991b1b;
            border:1px solid rgba(220,38,38,0.2);
            padding:14px 16px;border-radius:14px;
            font-size:13px;font-weight:600;
            margin-bottom:16px;
            display:flex;align-items:center;gap:8px;
            animation:shake 0.4s ease;
        }
        .err i{font-size:16px;color:#dc2626}
        @keyframes shake{0%,100%{transform:translateX(0)}15%,45%,75%{transform:translateX(-4px)}30%,60%{transform:translateX(4px)}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
        
        .links{text-align:center;margin-top:20px;font-size:12px;color:#94a3b8}
        .links a{color:#0ea5a4;font-weight:600;text-decoration:none;transition:color 0.2s}
        .links a:hover{color:#2563eb;text-decoration:underline}
        
        .divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:#cbd5e1;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#e2e8f0}
    </style>
</head>
<body>
    <div class="wrap">
        <form id="loginForm" class="card ajax-form">
            @csrf
            <div class="brand">
                <div class="icon-wrap">
                    <i class="fas fa-bus"></i>
                </div>
                <h1>Login to SafeStep</h1>
            </div>
            <div id="loginDescription" class="muted">Access your dashboard</div>

            <div class="role-badges">
                <span id="badge-admin" class="role-badge admin" style="display:none"><i class="fas fa-shield-halved"></i> Admin</span>
                <span id="badge-driver" class="role-badge driver" style="display:none"><i class="fas fa-car"></i> Driver</span>
                <span id="badge-parent" class="role-badge parent" style="display:none"><i class="fas fa-user-group"></i> Parent</span>
            </div>

            <div id="errorMessage" class="err" style="display:none">
                <i class="fas fa-exclamation-circle"></i>
                <span class="msg-text"></span>
            </div>

            <div class="field">
                <label for="email"><i class="fas fa-envelope" style="margin-right:4px;font-size:11px;opacity:0.6"></i> Email</label>
                <div class="input-wrap">
                    <input id="email" name="email" type="email" required autocomplete="email" placeholder="you@example.com">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>

            <div class="field">
                <label for="password"><i class="fas fa-lock" style="margin-right:4px;font-size:11px;opacity:0.6"></i> Password</label>
                <div class="input-wrap">
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">
                    <i class="fas fa-lock"></i>
                    <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="row">
                <label><input type="checkbox" name="remember" value="1"> Remember me</label>
            </div>

            <button class="btn" type="submit">
                <i class="fas fa-arrow-right-to-bracket" style="margin-right:6px"></i> Login
            </button>

            <div class="divider">or</div>

            <div class="links">
                New here? <a href="{{ url('/apply') }}">Apply to Join SafeStep</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="{{ asset('js/ajax-forms.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Check URL for role hint
            const urlParams = new URLSearchParams(window.location.search);
            const roleHint = urlParams.get('role');
            if (roleHint) {
                $(`#badge-${roleHint}`).show();
                $('#loginDescription').text(`Sign in as ${roleHint.charAt(0).toUpperCase() + roleHint.slice(1)} to access your dashboard.`);
            } else {
                $('.role-badge').show();
            }

            // Role → dashboard route mapping
            const dashboardRoutes = {
                admin:  '/admin/applications',
                driver: '/dashboard/driver',
                parent: '/dashboard/parent'
            };

            // Always clear stale auth state when the login page loads.
            // This prevents infinite redirect loops caused by expired sessions
            // but persisted localStorage tokens.
            function clearAuthState() {
                localStorage.removeItem('token');
                localStorage.removeItem('safestep_token');
                localStorage.removeItem('safestep_user');
                localStorage.removeItem('safestep_role');
            }
            clearAuthState();

            $('#togglePassword').on('click', function() {
                const input = $('#password');
                const icon = $(this).find('i');
                const isPassword = input.attr('type') === 'password';
                input.attr('type', isPassword ? 'text' : 'password');
                icon.attr('class', isPassword ? 'fas fa-eye-slash' : 'fas fa-eye');
            });

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                const $btn  = $(this).find('button');
                const $err  = $('#errorMessage');
                const email = $('#email').val();
                const pass  = $('#password').val();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Authenticating...');
                $err.hide();

                // Step 1: Obtain API token via Sanctum
                $.ajax({
                    url: '/api/auth/login',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'Accept': 'application/json' },
                    data: JSON.stringify({ email: email, password: pass }),
                    success: function(response) {
                        if (!response.success) {
                            showError('Unexpected response from server.');
                            return;
                        }

                        const token = response.data.token;
                        const user  = response.data.user;
                        const role  = user.roles[0];

                        // Store API token for future fetch calls
                        localStorage.setItem('token', token);
                        localStorage.setItem('safestep_token', token);
                        localStorage.setItem('safestep_user', JSON.stringify(user));
                        localStorage.setItem('safestep_role', role);

                        // Step 2: Also create a web session so dashboard
                        // auth middleware works (POST /login with CSRF)
                        $.ajax({
                            url: '/login',
                            method: 'POST',
                            data: {
                                email: email,
                                password: pass,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(sessionResponse) {
                                // Use server-provided redirect if available,
                                // otherwise fall back to the dashboardRoutes map.
                                const dest = (sessionResponse && sessionResponse.redirect)
                                    ? sessionResponse.redirect
                                    : (dashboardRoutes[role] || '/');
                                window.location.href = dest;
                            },
                            error: function() {
                                // Session creation failed — token is stored; attempt redirect.
                                window.location.href = dashboardRoutes[role] || '/';
                            }
                        });
                    },
                    error: function(xhr) {
                        showError(xhr);
                    }
                });

                function showError(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-arrow-right-to-bracket" style="margin-right:6px"></i> Login');
                    let msg = 'Invalid email or password';
                    if (typeof xhr === 'string') { msg = xhr; }
                    else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = 'Invalid email or password';
                    }
                    if (xhr && (xhr.status === 401 || xhr.status === 422)) {
                        msg = 'Invalid email or password';
                        clearAuthState();
                    }
                    $err.find('.msg-text').text(msg);
                    $err.fadeIn();
                }
            });
        });
    </script>
</body>
</html>
