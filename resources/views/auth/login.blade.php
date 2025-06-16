<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Area restrita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primary-login: {{ $appOptions['color-primary-login'] ?? '#24b153' }};
            --color-secondary-login: {{ $appOptions['color-secondary-login'] ?? '#6fbe89' }};
        }

        body {
            background: linear-gradient(135deg, var(--color-primary-login), var(--color-secondary-login));
            min-height: 100vh;
        }

        .login-card {
            background-color: #fff;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .brand-logo {
            height: 50px;
        }
    </style>

</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="login-card">
                <div class="text-center mb-4">
                    @if (!empty($appOptions['logo']))
                        <img src="{{ asset($appOptions['logo']) }}" alt="Logo do Sistema" style="height: 50px;">
                    @else
                        <strong>{{ config('app.name') }}</strong>
                    @endif
                </div>

                @if (session('status'))
                    <div class="alert alert-info">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail ou Usuario</label>
                        <input type="text" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Lembrar-me</label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Esqueceu sua
                                senha?</a>
                        </div>
                    @endif
                </form>

                <hr class="my-4">

                <div class="text-center text-muted small">
                    &copy; {{ date('Y') }} Todos os direitos reservados.
                </div>

            </div>
        </div>
    </div>
</body>

</html>
