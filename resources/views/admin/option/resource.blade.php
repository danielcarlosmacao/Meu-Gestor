@extends('layouts.header')

@section('title', 'Configurações do Sistema')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-module.css') }}">
@endpush
@section('content')
<div class="admin-module-scope">

    <div class="container py-4">

        {{-- Cabeçalho --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-sliders me-2"></i>
                    Configurações do Sistema
                </h2>

                <p class="text-muted mb-0">
                    Configure os parâmetros gerais, integrações e atualizações do sistema.
                </p>
            </div>

        </div>

        {{-- Mensagens --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar">
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>

                {{ session('info') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar">
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">

                <div class="fw-semibold mb-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Verifique os campos informados.
                </div>

                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        <form action="{{ route('options.resource.update') }}" method="POST" class="needs-validation" novalidate>

            @csrf

            {{-- =====================================================
                CONFIGURAÇÕES GERAIS
            ====================================================== --}}

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3">

                    <div class="d-flex align-items-center gap-3">

                        <span class="fs-4 text-primary">
                            <i class="bi bi-gear-fill"></i>
                        </span>

                        <div>
                            <h5 class="mb-0">
                                Configurações Gerais
                            </h5>

                            <small class="text-muted">
                                Parâmetros utilizados nos cálculos e listagens.
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-4">

                            <label for="hours_Generation" class="form-label">
                                Horas para geração
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input type="number" id="hours_Generation" name="hours_Generation"
                                    class="form-control @error('hours_Generation') is-invalid @enderror"
                                    value="{{ old('hours_Generation', $options['hours_Generation'] ?? 5) }}" min="1"
                                    max="24" required>

                                <span class="input-group-text">
                                    horas
                                </span>

                                @error('hours_Generation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                Tempo utilizado para a geração automática.
                            </div>

                        </div>

                        <div class="col-md-4">

                            <label for="hours_autonomy" class="form-label">
                                Horas de autonomia
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input type="number" id="hours_autonomy" name="hours_autonomy"
                                    class="form-control @error('hours_autonomy') is-invalid @enderror"
                                    value="{{ old('hours_autonomy', $options['hours_autonomy'] ?? 48) }}" min="1"
                                    max="8760" required>

                                <span class="input-group-text">
                                    horas
                                </span>

                                @error('hours_autonomy')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                Tempo considerado para a autonomia dos equipamentos.
                            </div>

                        </div>

                        <div class="col-md-4">

                            <label for="pagination" class="form-label">
                                Registros por página
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input type="number" id="pagination" name="pagination"
                                    class="form-control @error('pagination') is-invalid @enderror"
                                    value="{{ old('pagination', $options['pagination'] ?? 20) }}" min="1"
                                    max="100" required>

                                <span class="input-group-text">
                                    registros
                                </span>

                                @error('pagination')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                Quantidade padrão exibida nas tabelas do sistema.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- =====================================================
                INTEGRAÇÃO WHATSAPP
            ====================================================== --}}

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3">

                    <div class="d-flex align-items-center gap-3">

                        <span class="fs-4 text-success">
                            <i class="bi bi-whatsapp"></i>
                        </span>

                        <div>
                            <h5 class="mb-0">
                                Integração WhatsApp
                            </h5>

                            <small class="text-muted">
                                Dados utilizados para comunicação com a API.
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-3">

                            <label for="whatsapp_method" class="form-label">
                                Método de envio
                                <span class="text-danger">*</span>
                            </label>

                            <select id="whatsapp_method" name="whatsapp_method"
                                class="form-select @error('whatsapp_method') is-invalid @enderror" required>

                                <option value="GET" @selected(old('whatsapp_method', $options['whatsapp_method'] ?? 'GET') === 'GET')>

                                    GET

                                </option>

                                <option value="POST" @selected(old('whatsapp_method', $options['whatsapp_method'] ?? 'GET') === 'POST')>

                                    POST

                                </option>

                            </select>

                            @error('whatsapp_method')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-9">

                            <label for="whatsapp_ip" class="form-label">
                                Endereço da API
                            </label>

                            <input type="text" id="whatsapp_ip" name="whatsapp_ip"
                                class="form-control @error('whatsapp_ip') is-invalid @enderror"
                                value="{{ old('whatsapp_ip', $options['whatsapp_ip'] ?? '') }}"
                                placeholder="http://192.168.1.10:8080" maxlength="255">

                            @error('whatsapp_ip')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Informe o IP, domínio e porta da API.
                            </div>

                        </div>

                        <div class="col-md-6">

                            <label for="whatsapp_user" class="form-label">
                                Usuário da API
                            </label>

                            <input type="text" id="whatsapp_user" name="whatsapp_user"
                                class="form-control @error('whatsapp_user') is-invalid @enderror"
                                value="{{ old('whatsapp_user', $options['whatsapp_user'] ?? '') }}"
                                placeholder="Informe o usuário" maxlength="255" autocomplete="username">

                            @error('whatsapp_user')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label for="whatsapp_token" class="form-label">
                                Token da API
                            </label>

                            <div class="input-group">

                                <input type="password" id="whatsapp_token" name="whatsapp_token"
                                    class="form-control @error('whatsapp_token') is-invalid @enderror" value=""
                                    placeholder="{{ $tokenConfigured ?? false ? 'Token configurado — deixe vazio para manter' : 'Informe o token da API' }}"
                                    maxlength="1000" autocomplete="new-password">

                                <button type="button" class="btn btn-outline-secondary" id="toggleWhatsappToken"
                                    aria-label="Mostrar ou ocultar token">

                                    <i class="bi bi-eye" id="toggleWhatsappTokenIcon"></i>

                                </button>

                                @error('whatsapp_token')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="form-text">
                                @if ($tokenConfigured ?? false)
                                    Já existe um token configurado. Preencha somente para substituí-lo.
                                @else
                                    Nenhum token está configurado.
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            </div>

          

            {{-- =====================================================
                AÇÕES
            ====================================================== --}}

            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-3">

                <button type="button" class="btn btn-warning" onclick="mostrarPopupConfirmacao()">

                    <i class="bi bi-tools me-1"></i>
                    Reparar Torres

                </button>

                <button type="submit" class="btn dcm-btn-primary">

                    <i class="bi bi-check-lg me-1"></i>
                    Salvar Configurações

                </button>

            </div>

        </form>

    </div>


</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleWhatsappToken');
            const tokenInput = document.getElementById('whatsapp_token');
            const toggleIcon = document.getElementById('toggleWhatsappTokenIcon');

            if (!toggleButton || !tokenInput || !toggleIcon) {
                return;
            }

            toggleButton.addEventListener('click', function() {
                const showingToken = tokenInput.type === 'text';

                tokenInput.type = showingToken ? 'password' : 'text';

                toggleIcon.classList.toggle('bi-eye', showingToken);
                toggleIcon.classList.toggle('bi-eye-slash', !showingToken);
            });
        });
    </script>
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-module.js') }}"></script>
@endpush
