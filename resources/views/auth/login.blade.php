<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.35);
            --bg-dark: #0f172a;
            --bg-card: rgba(255, 255, 255, 0.05);
            --surface: rgba(255, 255, 255, 0.08);
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
            --danger: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-dark);
            overflow: hidden;
            position: relative;
        }

        /* ── Animated Background ── */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 80%, rgba(244, 114, 182, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(56, 189, 248, 0.08) 0%, transparent 60%);
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 12s ease-in-out infinite;
            z-index: 0;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(99, 102, 241, 0.2);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: rgba(244, 114, 182, 0.15);
            bottom: -80px;
            right: -80px;
            animation-delay: -4s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(56, 189, 248, 0.12);
            top: 50%;
            left: 60%;
            animation-delay: -8s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -20px) scale(1.05);
            }

            66% {
                transform: translate(-20px, 15px) scale(0.95);
            }
        }

        /* ── Login Card ── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 0 20px;
        }

        .login-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(40px) saturate(150%);
            -webkit-backdrop-filter: blur(40px) saturate(150%);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.05) inset,
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 80px -20px var(--primary-glow);
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes cardEntrance {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Logo & Header ── */
        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), #a78bfa);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px var(--primary-glow);
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {

            0%,
            100% {
                box-shadow: 0 8px 24px var(--primary-glow);
            }

            50% {
                box-shadow: 0 8px 40px rgba(99, 102, 241, 0.5);
            }
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.025em;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* ── Form Fields ── */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper>i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            transition: color 0.3s;
            z-index: 2;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input.has-toggle {
            padding-right: 48px;
        }

        .form-input::placeholder {
            color: rgba(148, 163, 184, 0.5);
        }

        .form-input:focus {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.06);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .form-input:focus+i,
        .form-input:focus~i {
            color: var(--primary);
        }

        .input-wrapper:focus-within>i {
            color: var(--primary);
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 4px;
            transition: color 0.3s;
            z-index: 2;
            line-height: 1;
        }

        .password-toggle:hover {
            color: var(--text);
        }

        /* ── Remember & Forgot ── */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--border);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            background: var(--surface);
        }

        .checkmark i {
            font-size: 10px;
            color: #fff;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s;
        }

        .remember-me input:checked+.checkmark {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-me input:checked+.checkmark i {
            opacity: 1;
            transform: scale(1);
        }

        .remember-me span {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .forgot-link {
            font-size: 0.85rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #818cf8;
        }

        /* ── Submit Button ── */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px var(--primary-glow);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px var(--primary-glow);
        }

        .btn-login i {
            margin-left: 8px;
            transition: transform 0.3s;
        }

        .btn-login:hover i {
            transform: translateX(4px);
        }

        /* ── Error Messages ── */
        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        .error-alert i {
            color: var(--danger);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .error-alert ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .error-alert li {
            color: #fca5a5;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-6px);
            }

            75% {
                transform: translateX(6px);
            }
        }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .login-footer p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .login-footer .version {
            font-size: 0.72rem;
            color: rgba(148, 163, 184, 0.4);
            margin-top: 8px;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .login-card {
                padding: 36px 24px;
                border-radius: 20px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- Animated Background -->
    <div class="bg-gradient"></div>
    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Header -->
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to your POS dashboard</p>
            </div>

            <!-- Error Messages (populated by JS) -->
            <div class="error-alert" id="errorAlert" style="display: none;">
                <i class="fas fa-exclamation-circle"></i>
                <ul id="errorList"></ul>
            </div>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-input" id="email" name="email" placeholder="you@example.com"
                            value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-input has-toggle" id="password" name="password"
                            placeholder="••••••••" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"><i class="fas fa-check"></i></span>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    Sign In <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p><i class="fas fa-shield-halved" style="margin-right: 4px; font-size: 0.75rem;"></i> Secured with
                    256-bit encryption</p>
                <p class="version">POS System v2.0 &middot; Laravel 12</p>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Subtle parallax on the orbs
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 2;
            const y = (e.clientY / window.innerHeight - 0.5) * 2;
            document.querySelectorAll('.orb').forEach((orb, i) => {
                const speed = (i + 1) * 8;
                orb.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
            });
        });

        // AJAX Login
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('submitBtn');
        const errorAlert = document.getElementById('errorAlert');
        const errorList = document.getElementById('errorList');
        const btnOriginalHTML = btn.innerHTML;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Reset errors & show loading
            errorAlert.style.display = 'none';
            errorList.innerHTML = '';
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

            // Remove previous error styling
            document.querySelectorAll('.form-input').forEach(el => el.style.borderColor = '');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        remember: form.querySelector('input[name="remember"]')?.checked ? true : false
                    })
                });

                if (response.ok || response.redirected) {
                    // Success — show checkmark then redirect
                    btn.innerHTML = '<i class="fas fa-check"></i> Success!';
                    btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    setTimeout(() => {
                        window.location.href = '{{ route("home") }}';
                    }, 600);
                    return;
                }

                const data = await response.json();

                // Show validation errors
                if (data.errors) {
                    const allErrors = [];
                    for (const field in data.errors) {
                        data.errors[field].forEach(msg => allErrors.push(msg));
                        // Highlight the field
                        const input = document.getElementById(field);
                        if (input) input.style.borderColor = '#ef4444';
                    }
                    showErrors(allErrors);
                } else if (data.message) {
                    showErrors([data.message]);
                } else {
                    showErrors(['Invalid credentials. Please try again.']);
                }

            } catch (err) {
                showErrors(['A network error occurred. Please try again.']);
            }

            // Reset button
            btn.disabled = false;
            btn.innerHTML = btnOriginalHTML;
            btn.style.background = '';
        });

        function showErrors(errors) {
            errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            errorAlert.style.display = 'flex';
            errorAlert.style.animation = 'none';
            errorAlert.offsetHeight; // trigger reflow
            errorAlert.style.animation = 'shake 0.4s ease';
        }
    </script>
</body>

</html>