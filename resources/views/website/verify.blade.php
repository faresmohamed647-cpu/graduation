<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SafeStep</title>
    <link rel="stylesheet" href="{{ asset('css/pearents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public-theme.css') }}">
    <style>
        .alert-success {
            background: linear-gradient(135deg, rgba(5,150,105,0.12), rgba(5,150,105,0.06));
            color: #065f46;
            border: 1px solid rgba(5,150,105,0.3);
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 14px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.4s ease;
        }
        .alert-error {
            background: linear-gradient(135deg, rgba(220,38,38,0.12), rgba(220,38,38,0.06));
            color: #991b1b;
            border: 1px solid rgba(220,38,38,0.3);
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 14px;
            width: 100%;
            animation: fadeIn 0.4s ease;
        }
        .alert-error ul { list-style: none; padding: 0; margin: 0; }

        /* Override container height for simpler login forms */
        .container { min-height: 480px; }

        .form-container form {
            justify-content: center;
            gap: 4px;
        }
        .form-container form h1 {
            margin-bottom: 6px;
            font-size: 22px;
        }
        .form-container form p.subtitle {
            color: var(--text-light);
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }
        .form-container form .input-group {
            margin-bottom: 6px;
        }
        .form-container form button[type="submit"] {
            margin-top: 12px;
        }
        .register-link {
            margin-top: 12px;
            font-size: 12px;
            color: var(--text-light);
            text-align: center;
        }
        .register-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .register-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="container" id="container">

    <!-- ===== Parent Login Form ===== -->
    <div class="form-container join-student-container">
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <h1>🎓 Parent Login</h1>
            <p class="subtitle">Sign in with your registered email & password</p>

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any() && old('_form') !== 'driver-login')
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="hidden" name="_form" value="parent-login">

            <label>Email</label>
            <div class="input-group">
                <i class='bx bxs-envelope' style="font-size:16px; position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--primary-color);"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autocomplete="email" style="padding-left:40px;">
            </div>

            <label>Password</label>
            <div class="input-group">
                <i class='bx bxs-lock-alt' style="font-size:16px; position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--primary-color);"></i>
                <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password" style="padding-left:40px;">
            </div>

            <button type="submit">Login</button>
            <p class="register-link">Don't have an account? <a href="{{ url('/apply/parent') }}">Register here</a></p>
        </form>
    </div>

    <!-- ===== Car Owner / Driver Login Form ===== -->
    <div class="form-container join-us">
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <h1>🚗 Driver Login</h1>
            <p class="subtitle">Sign in with your registered email & password</p>

            @if($errors->any() && old('_form') === 'driver-login')
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="hidden" name="_form" value="driver-login">

            <label>Email</label>
            <div class="input-group">
                <i class='bx bxs-envelope' style="font-size:16px; position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--primary-color);"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autocomplete="email" style="padding-left:40px;">
            </div>

            <label>Password</label>
            <div class="input-group">
                <i class='bx bxs-lock-alt' style="font-size:16px; position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--primary-color);"></i>
                <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password" style="padding-left:40px;">
            </div>

            <button type="submit">Login</button>
            <p class="register-link">Don't have an account? <a href="{{ url('/apply/driver') }}">Register here</a></p>
        </form>
    </div>

    <!-- ===== Toggle Panels ===== -->
    <div class="toggle-container">
        <div class="toggle">

            <div class="toggle-panel toggle-left">
                <h1>Parent Login</h1>
                <p>Sign in to track your children on the bus</p>
                <button type="button" id="login">Parent</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Driver Login</h1>
                <p>Sign in to manage your trips and routes</p>
                <button type="button" id="register">Driver</button>
            </div>

        </div>
    </div>

</div>

<script>
const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");

registerBtn.addEventListener("click", () => {
    container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
    container.classList.remove("active");
});

window.addEventListener("load", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get("type");
    
    if (type === "car-owner" || type === "driver") {
        container.classList.add("active");
    } else if (type === "student" || type === "parent") {
        container.classList.remove("active");
    } else {
        const hash = window.location.hash;
        if (hash === "#carowner" || hash === "#driver") {
            container.classList.add("active");
        } else {
            container.classList.remove("active");
        }
    }
});
</script>
<script src="{{ asset('js/public-theme.js') }}"></script>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

</body>
</html>
