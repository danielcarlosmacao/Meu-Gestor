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
    }
</style>

<link rel="stylesheet" href="{{ asset('css/pagelogin.css') }}">
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
