<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        Login - {{ config('app.name') }}
    </title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --login-primary:
                {{ $appOptions['color-primary-login'] ?? '#24b153' }};

            --login-secondary:
                {{ $appOptions['color-secondary-login'] ?? '#6fbe89' }};

            --login-background: #f4f7f5;
            --login-text: #25312a;
            --login-muted: #6c757d;
            --login-border: #dee5e0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            color: var(--login-text);
            background:
                radial-gradient(circle at top left,
                    color-mix(in srgb,
                        var(--login-secondary) 30%,
                        transparent),
                    transparent 35%),
                radial-gradient(circle at bottom right,
                    color-mix(in srgb,
                        var(--login-primary) 28%,
                        transparent),
                    transparent 40%),
                var(--login-background);

            font-family:
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 24px 12px;
            position: relative;
        }

        .login-page::before,
        .login-page::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(2px);
            opacity: 0.15;
        }

        .login-page::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -80px;
            background: var(--login-primary);
        }

        .login-page::after {
            width: 380px;
            height: 380px;
            right: -140px;
            bottom: -160px;
            background: var(--login-secondary);
        }

        .login-wrapper {
            width: 100%;
            max-width: 1050px;
            margin: auto;
            position: relative;
            z-index: 1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 25px 70px rgba(18, 55, 32, 0.17),
                0 4px 15px rgba(0, 0, 0, 0.05);

            backdrop-filter: blur(15px);
        }

        .login-banner {
            min-height: 620px;
            padding: 55px;
            color: #fff;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;

            background:
                linear-gradient(150deg,
                    var(--login-primary),
                    var(--login-secondary));
        }

        .login-banner::before {
            content: "";
            position: absolute;
            width: 330px;
            height: 330px;
            border-radius: 50%;
            top: -130px;
            right: -100px;
            background: rgba(255, 255, 255, 0.12);
        }

        .login-banner::after {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            left: -90px;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.1);
        }

        .banner-content,
        .banner-footer {
            position: relative;
            z-index: 1;
        }

        .banner-icon {
            width: 66px;
            height: 66px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            margin-bottom: 28px;
            background: rgba(255, 255, 255, 0.17);
            border: 1px solid rgba(255, 255, 255, 0.22);
            font-size: 30px;
            backdrop-filter: blur(8px);
        }

        .banner-title {
            max-width: 430px;
            margin-bottom: 18px;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 750;
            line-height: 1.12;
            letter-spacing: -0.04em;
        }

        .banner-description {
            max-width: 440px;
            margin-bottom: 0;
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.88);
        }

        .banner-security {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.92);
        }

        .login-form-area {
            min-height: 620px;
            display: flex;
            align-items: center;
            padding: 55px 60px;
        }

        .login-form-content {
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        .brand-area {
            margin-bottom: 34px;
        }

        .brand-logo {
            max-width: 210px;
            max-height: 72px;
            object-fit: contain;
        }

        .brand-fallback {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.35rem;
            font-weight: 750;
            color: var(--login-primary);
        }

        .brand-fallback-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background:
                color-mix(in srgb,
                    var(--login-primary) 13%,
                    white);

            color: var(--login-primary);
        }

        .login-title {
            margin-bottom: 8px;
            font-size: 1.9rem;
            font-weight: 750;
            letter-spacing: -0.03em;
        }

        .login-subtitle {
            margin-bottom: 30px;
            color: var(--login-muted);
            line-height: 1.6;
        }

        .form-label {
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 650;
            color: #34423a;
        }

        .input-group-login {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            z-index: 5;
            transform: translateY(-50%);
            color: #89958e;
            pointer-events: none;
        }

        .form-control-login {
            height: 53px;
            padding-left: 46px;
            padding-right: 44px;
            border-radius: 13px;
            border: 1px solid var(--login-border);
            background-color: #fbfcfb;
            font-size: 0.96rem;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        .form-control-login:hover {
            border-color:
                color-mix(in srgb,
                    var(--login-primary) 40%,
                    var(--login-border));
        }

        .form-control-login:focus {
            border-color: var(--login-primary);
            background-color: #fff;
            box-shadow:
                0 0 0 4px color-mix(in srgb,
                    var(--login-primary) 14%,
                    transparent);
        }

        .form-control-login.is-invalid {
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            z-index: 6;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            border-radius: 9px;
            transform: translateY(-50%);
            background: transparent;
            color: #78827d;
            transition:
                color 0.2s ease,
                background-color 0.2s ease;
        }

        .password-toggle:hover {
            background:
                color-mix(in srgb,
                    var(--login-primary) 10%,
                    transparent);

            color: var(--login-primary);
        }

        .form-check-input {
            width: 1.08rem;
            height: 1.08rem;
            margin-top: 0.18rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--login-primary);
            border-color: var(--login-primary);
        }

        .form-check-input:focus {
            border-color: var(--login-primary);
            box-shadow:
                0 0 0 0.2rem color-mix(in srgb,
                    var(--login-primary) 16%,
                    transparent);
        }

        .form-check-label {
            color: #58645e;
            cursor: pointer;
        }

        .forgot-password {
            color: var(--login-primary);
            font-size: 0.9rem;
            font-weight: 650;
            text-decoration: none;
        }

        .forgot-password:hover {
            color:
                color-mix(in srgb,
                    var(--login-primary) 78%,
                    black);

            text-decoration: underline;
        }

        .btn-login {
            min-height: 53px;
            border: 0;
            border-radius: 13px;
            background:
                linear-gradient(135deg,
                    var(--login-primary),
                    var(--login-secondary));

            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            box-shadow:
                0 12px 25px color-mix(in srgb,
                    var(--login-primary) 25%,
                    transparent);

            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease,
                filter 0.18s ease;
        }

        .btn-login:hover {
            color: #fff;
            transform: translateY(-1px);
            filter: brightness(0.97);
            box-shadow:
                0 16px 32px color-mix(in srgb,
                    var(--login-primary) 32%,
                    transparent);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:focus {
            color: #fff;
            box-shadow:
                0 0 0 4px color-mix(in srgb,
                    var(--login-primary) 18%,
                    transparent);
        }

        .login-alert {
            border: 0;
            border-radius: 13px;
            font-size: 0.92rem;
        }

        .login-footer {
            margin-top: 34px;
            padding-top: 24px;
            border-top: 1px solid #edf0ee;
            color: #909994;
            text-align: center;
            font-size: 0.82rem;
        }

        .invalid-feedback {
            font-size: 0.82rem;
        }

        @media (max-width: 991.98px) {
            .login-wrapper {
                max-width: 570px;
            }

            .login-banner {
                display: none;
            }

            .login-form-area {
                min-height: auto;
                padding: 45px;
            }

            .login-container {
                border-radius: 20px;
            }
        }

        @media (max-width: 575.98px) {
            .login-page {
                align-items: flex-start;
                padding: 12px;
            }

            .login-form-area {
                padding: 32px 23px;
            }

            .login-container {
                border-radius: 18px;
            }

            .login-title {
                font-size: 1.65rem;
            }

            .brand-area {
                margin-bottom: 28px;
            }

            .brand-logo {
                max-width: 175px;
                max-height: 60px;
            }
        }
    </style>
</head>

<body>

    <main class="login-page">

        <div class="login-wrapper">

            <div class="login-container">

                <div class="row g-0">

                    {{-- LADO VISUAL --}}
                    <div class="col-lg-6">

                        <section class="login-banner">

                            <div class="banner-content">

                                <div class="banner-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>

                                <h1 class="banner-title">
                                    Bem-vindo ao
                                    {{ config('app.name') }}
                                </h1>

                                <p class="banner-description">
                                    Acesse sua conta para gerenciar as informações
                                    e recursos disponíveis no sistema.
                                </p>

                            </div>

                            <div class="banner-footer">

                                <div class="banner-security">

                                    <i class="bi bi-lock-fill"></i>

                                    <span>
                                        Ambiente seguro e acesso restrito
                                    </span>

                                </div>

                            </div>

                        </section>

                    </div>

                    {{-- FORMULÁRIO --}}
                    <div class="col-lg-6">

                        <section class="login-form-area">

                            <div class="login-form-content">

                                {{-- LOGO --}}
                                <div class="brand-area">

                                    @if (!empty($appOptions['logo']))
                                        <img src="{{ asset($appOptions['logo']) }}" alt="Logo {{ config('app.name') }}"
                                            class="brand-logo">
                                    @else
                                        <div class="brand-fallback">

                                            <span class="brand-fallback-icon">
                                                <i class="bi bi-grid-1x2-fill"></i>
                                            </span>

                                            <span>
                                                {{ config('app.name') }}
                                            </span>

                                        </div>
                                    @endif

                                </div>

                                <h2 class="login-title">
                                    Acesse sua conta
                                </h2>

                                <p class="login-subtitle">
                                    Informe seu usuário e sua senha para continuar.
                                </p>

                                {{-- MENSAGEM DE SESSÃO --}}
                                @if (session('status'))
                                    <div class="alert alert-info login-alert
                                            d-flex align-items-start gap-2"
                                        role="alert">

                                        <i class="bi bi-info-circle-fill mt-1"></i>

                                        <div>
                                            {{ session('status') }}
                                        </div>

                                    </div>
                                @endif

                                {{-- ERRO GERAL --}}
                                @if ($errors->has('login'))
                                    <div class="alert alert-danger login-alert
                                            d-flex align-items-start gap-2"
                                        role="alert">

                                        <i class="bi bi-exclamation-circle-fill mt-1"></i>

                                        <div>
                                            {{ $errors->first('login') }}
                                        </div>

                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login.store') }}" autocomplete="on">

                                    @csrf

                                    {{-- USUÁRIO --}}
                                    <div class="mb-3">

                                        <label for="email" class="form-label">

                                            E-mail ou usuário
                                        </label>

                                        <div class="input-group-login">

                                            <i class="bi bi-person input-icon"></i>

                                            <input type="text" name="email" id="email"
                                                class="form-control form-control-login
                                                    @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" placeholder="Digite seu e-mail ou usuário"
                                                autocomplete="username" required autofocus>

                                        </div>

                                        @error('email')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- SENHA --}}
                                    <div class="mb-3">

                                        <label for="password" class="form-label">

                                            Senha
                                        </label>

                                        <div class="input-group-login">

                                            <i class="bi bi-lock input-icon"></i>

                                            <input type="password" name="password" id="password"
                                                class="form-control form-control-login
                                                    @error('password') is-invalid @enderror"
                                                placeholder="Digite sua senha" autocomplete="current-password" required>

                                            <button type="button" class="password-toggle" id="togglePassword"
                                                aria-label="Mostrar senha" title="Mostrar senha">

                                                <i class="bi bi-eye" id="passwordIcon">
                                                </i>

                                            </button>

                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- LEMBRAR E RECUPERAR --}}
                                    <div
                                        class="d-flex justify-content-between
                                            align-items-center gap-3 mb-4">

                                        <div class="form-check">

                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" value="1" @checked(old('remember'))>

                                            <label class="form-check-label" for="remember">

                                                Lembrar-me
                                            </label>

                                        </div>

                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="forgot-password">

                                                Esqueceu a senha?
                                            </a>
                                        @endif

                                    </div>

                                    {{-- BOTÃO --}}
                                    <div class="d-grid">

                                        <button type="submit" class="btn btn-login" id="loginButton">

                                            <span
                                                class="spinner-border spinner-border-sm
                                                    me-2 d-none"
                                                id="loginSpinner" aria-hidden="true">
                                            </span>

                                            <i class="bi bi-box-arrow-in-right me-2" id="loginIcon">
                                            </i>

                                            <span id="loginButtonText">
                                                Entrar
                                            </span>

                                        </button>

                                    </div>

                                </form>

                                {{-- RODAPÉ --}}
                                <footer class="login-footer">

                                    <div>
                                        &copy; {{ date('Y') }}
                                        {{ config('app.name') }}
                                    </div>

                                    <div class="mt-1">
                                        Todos os direitos reservados.
                                    </div>

                                </footer>

                            </div>

                        </section>

                    </div>

                </div>

            </div>

        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | MOSTRAR E OCULTAR SENHA
            |--------------------------------------------------------------------------
            */

            const passwordInput =
                document.getElementById('password');

            const togglePassword =
                document.getElementById('togglePassword');

            const passwordIcon =
                document.getElementById('passwordIcon');

            if (
                passwordInput &&
                togglePassword &&
                passwordIcon
            ) {

                togglePassword.addEventListener('click', function() {

                    const isPassword =
                        passwordInput.type === 'password';

                    passwordInput.type =
                        isPassword ? 'text' : 'password';

                    passwordIcon.classList.toggle(
                        'bi-eye',
                        !isPassword
                    );

                    passwordIcon.classList.toggle(
                        'bi-eye-slash',
                        isPassword
                    );

                    togglePassword.setAttribute(
                        'aria-label',
                        isPassword ?
                        'Ocultar senha' :
                        'Mostrar senha'
                    );

                    togglePassword.setAttribute(
                        'title',
                        isPassword ?
                        'Ocultar senha' :
                        'Mostrar senha'
                    );
                });
            }

            /*
            |--------------------------------------------------------------------------
            | EVITAR ENVIO DUPLICADO
            |--------------------------------------------------------------------------
            */

            const loginForm =
                document.querySelector('form');

            const loginButton =
                document.getElementById('loginButton');

            const loginSpinner =
                document.getElementById('loginSpinner');

            const loginIcon =
                document.getElementById('loginIcon');

            const loginButtonText =
                document.getElementById('loginButtonText');

            if (loginForm && loginButton) {

                loginForm.addEventListener('submit', function() {

                    if (!loginForm.checkValidity()) {
                        return;
                    }

                    loginButton.disabled = true;

                    if (loginSpinner) {
                        loginSpinner.classList.remove('d-none');
                    }

                    if (loginIcon) {
                        loginIcon.classList.add('d-none');
                    }

                    if (loginButtonText) {
                        loginButtonText.textContent = 'Entrando...';
                    }
                });
            }

        });
    </script>

</body>

</html>
