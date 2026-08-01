<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name') }}
        @else
            {{ config('app.name') }}
        @endif
    </title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Fonte --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Flatpickr --}}
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    {{-- CSS geral --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    {{-- CSS do cabeçalho --}}
    <link href="{{ asset('css/themer/' . ($appOptions['themer'] ?? 'header.css')) }}" rel="stylesheet">

    {{-- Cores configuráveis --}}
    <style>
        :root {
            --color-primary:
                {{ $appOptions['color-primary'] ?? '#24b153' }};

            --color-secondary:
                {{ $appOptions['color-secondary'] ?? '#6fbe89' }};

            --color-text:
                {{ $appOptions['color-text'] ?? '#0a6428' }};

            --color-hover:
                {{ $appOptions['color-hover'] ?? '#186d34' }};
        }
    </style>

    {{-- CSS específico das páginas --}}
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- ================================================================
        CABEÇALHO SUPERIOR
    ================================================================= --}}

    <header class="app-header sticky-top">

        <nav class="navbar navbar-expand-xl app-navbar">

            <div class="container-fluid px-3 px-xl-4">

                {{-- LOGO --}}
                <a class="navbar-brand app-brand" href="{{ route('welcome') }}">

                    @if (!empty($appOptions['logo']) && file_exists(public_path($appOptions['logo'])))
                        <img src="{{ asset($appOptions['logo']) }}" alt="Logo {{ config('app.name') }}"
                            class="app-logo">
                    @else
                        <span class="app-brand-icon">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </span>

                        <span class="app-brand-name">
                            {{ config('app.name') }}
                        </span>
                    @endif

                </a>

                {{-- BOTÃO MOBILE --}}
                <button class="navbar-toggler app-navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
                    aria-label="Abrir menu">

                    <i class="bi bi-list"></i>
                </button>

                {{-- MENU --}}
                <div class="collapse navbar-collapse" id="mainNavbar">

                    <ul class="navbar-nav align-items-xl-center me-auto">

                        {{-- ====================================================
                            REDE
                        ===================================================== --}}

                        @can('towers.view')

                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('tower.*', 'pon.*', 'fiberbox.*', 'maintenance.*', 'battery.*', 'equipment.*', 'plate.*')
                                        ? 'active'
                                        : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="networkMenu" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-broadcast-pin"></i>

                                    <span>Rede</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu" aria-labelledby="networkMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Gestão da rede
                                            </div>

                                            <div class="app-mega-description">
                                                Torres, rede óptica e equipamentos
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-broadcast"></i>
                                        </span>

                                    </div>

                                    <div class="row g-4">

                                        <div class="col-12 col-md-6">

                                            <div class="app-menu-group-title">
                                                Infraestrutura
                                            </div>

                                            <a class="app-menu-link
                                                    {{ request()->routeIs('tower.*') ? 'active' : '' }}"
                                                href="{{ route('tower.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-broadcast"></i>
                                                </span>

                                                <span>
                                                    <strong>Torres</strong>
                                                    <small>Gerenciar POPs e torres</small>
                                                </span>
                                            </a>

                                            @can('ftth.view')
                                                <a class="app-menu-link
                                                        {{ request()->routeIs('pon.*', 'fiberbox.*') ? 'active' : '' }}"
                                                    href="{{ route('pon.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-diagram-3"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Rede óptica</strong>
                                                        <small>PONs, CTOs, fibras e cabos</small>
                                                    </span>
                                                </a>
                                            @endcan

                                            @can('towers.maintenance')
                                                <a class="app-menu-link
                                                        {{ request()->routeIs('maintenance.*') ? 'active' : '' }}"
                                                    href="{{ route('maintenance.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-tools"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Manutenção</strong>
                                                        <small>Manutenções das torres</small>
                                                    </span>
                                                </a>
                                            @endcan

                                            @can('towers.manage')
                                                <a class="app-menu-link
                                                        {{ request()->routeIs('tower.gallery.*') ? 'active' : '' }}"
                                                    href="{{ route('tower.gallery.show') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-images"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Galeria</strong>
                                                        <small>Fotos e registros</small>
                                                    </span>
                                                </a>
                                            @endcan

                                        </div>

                                        <div class="col-12 col-md-6">

                                            <div class="app-menu-group-title">
                                                Equipamentos
                                            </div>

                                            <a class="app-menu-link
                                                    {{ request()->routeIs('battery.*') ? 'active' : '' }}"
                                                href="{{ route('battery.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-battery-charging"></i>
                                                </span>

                                                <span>
                                                    <strong>Baterias</strong>
                                                    <small>Controle das baterias</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link
                                                    {{ request()->routeIs('equipment.*') ? 'active' : '' }}"
                                                href="{{ route('equipment.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-router"></i>
                                                </span>

                                                <span>
                                                    <strong>Equipamentos</strong>
                                                    <small>Ativos da rede</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link
                                                    {{ request()->routeIs('plate.*') ? 'active' : '' }}"
                                                href="{{ route('plate.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-sun"></i>
                                                </span>

                                                <span>
                                                    <strong>Placas solares</strong>
                                                    <small>Energia e produção solar</small>
                                                </span>
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </li>

                        @endcan

                        {{-- ====================================================
                            FROTA
                        ===================================================== --}}

                        @can('fleets.view')
                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('fleet.*', 'vehicle-maintenance.*') ? 'active' : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="fleetMenu" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-truck"></i>

                                    <span>Frota</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu" aria-labelledby="fleetMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Gestão da frota
                                            </div>

                                            <div class="app-mega-description">
                                                Veículos, oficinas e manutenções
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-truck"></i>
                                        </span>

                                    </div>

                                    <div class="row g-4">

                                        <div class="col-12 col-md-7">

                                            <div class="app-menu-group-title">
                                                Cadastros
                                            </div>

                                            <a class="app-menu-link" href="{{ route('fleet.vehicles.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-car-front"></i>
                                                </span>

                                                <span>
                                                    <strong>Veículos</strong>
                                                    <small>Cadastro da frota</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link"
                                                href="{{ route('fleet.vehicle_maintenances.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-wrench-adjustable"></i>
                                                </span>

                                                <span>
                                                    <strong>Manutenções</strong>
                                                    <small>Histórico e serviços</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('fleet.vehicle_services.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-list-check"></i>
                                                </span>

                                                <span>
                                                    <strong>Tipos de serviços</strong>
                                                    <small>Serviços disponíveis</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('fleet.vehicle_workshop.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-building-gear"></i>
                                                </span>

                                                <span>
                                                    <strong>Oficinas</strong>
                                                    <small>Prestadores cadastrados</small>
                                                </span>
                                            </a>

                                        </div>

                                        <div class="col-12 col-md-5">

                                            <div class="app-menu-group-title">
                                                Relatórios
                                            </div>

                                            <a class="app-menu-link"
                                                href="{{ route('vehicle-maintenance.report.form') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-file-earmark-bar-graph"></i>
                                                </span>

                                                <span>
                                                    <strong>Manutenções</strong>
                                                    <small>Relatório da frota</small>
                                                </span>
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </li>
                        @endcan

                        {{-- ====================================================
                            SERVIÇOS
                        ===================================================== --}}

                        @can('service.view')
                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('service.*') ? 'active' : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="servicesMenu" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-tools"></i>

                                    <span>Serviços</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu" aria-labelledby="servicesMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Gestão de serviços
                                            </div>

                                            <div class="app-mega-description">
                                                Clientes, visitas e equipamentos
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-tools"></i>
                                        </span>

                                    </div>

                                    <div class="row g-3">

                                        <div class="col-12 col-md-6">

                                            <a class="app-menu-link" href="{{ route('service.clients.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-people"></i>
                                                </span>

                                                <span>
                                                    <strong>Clientes</strong>
                                                    <small>Clientes dos serviços</small>
                                                </span>
                                            </a>

                                        </div>

                                        <div class="col-12 col-md-6">

                                            <a class="app-menu-link"
                                                href="{{ route('service.equipment_maintenances.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-router"></i>
                                                </span>

                                                <span>
                                                    <strong>Equipamentos</strong>
                                                    <small>Manutenção de equipamentos</small>
                                                </span>
                                            </a>

                                        </div>

                                        <div class="col-12 col-md-6">

                                            <a class="app-menu-link" href="{{ route('service.maintenances.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-geo-alt"></i>
                                                </span>

                                                <span>
                                                    <strong>Visitas técnicas</strong>
                                                    <small>Controle de atendimentos</small>
                                                </span>
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </li>
                        @endcan

                        {{-- ====================================================
                            RH
                        ===================================================== --}}

                        @can('vacations.view')

                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('vacation_manager.*') ? 'active' : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="humanResourcesMenu"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-people"></i>

                                    <span>RH</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu" aria-labelledby="humanResourcesMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Recursos humanos
                                            </div>

                                            <div class="app-mega-description">
                                                Equipe, certificados e férias
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-people"></i>
                                        </span>

                                    </div>

                                    <div class="row g-4">

                                        <div class="col-12 col-md-6">

                                            <div class="app-menu-group-title">
                                                Equipe
                                            </div>

                                            @can('collaborators.view')
                                                <a class="app-menu-link"
                                                    href="{{ route('vacation_manager.collaborators.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-person-vcard"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Colaboradores</strong>
                                                        <small>Cadastro da equipe</small>
                                                    </span>
                                                </a>
                                            @endcan

                                            @can('collaborators.courses.view')
                                                <a class="app-menu-link"
                                                    href="{{ route('vacation_manager.collaborator.courses.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-patch-check"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Certificados</strong>
                                                        <small>Cursos e qualificações</small>
                                                    </span>
                                                </a>
                                            @endcan

                                        </div>

                                        <div class="col-12 col-md-6">

                                            <div class="app-menu-group-title">
                                                Férias
                                            </div>

                                            @can('vacations.edit')
                                                <a class="app-menu-link"
                                                    href="{{ route('vacation_manager.vacations.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-airplane"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Registro de férias</strong>
                                                        <small>Períodos e solicitações</small>
                                                    </span>
                                                </a>
                                            @endcan

                                            @can('vacation_manager.calendar')
                                                <a class="app-menu-link" href="{{ route('vacation_manager.calendar') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-calendar3"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Calendário</strong>
                                                        <small>Visualizar períodos</small>
                                                    </span>
                                                </a>
                                            @endcan

                                        </div>

                                    </div>

                                </div>

                            </li>

                        @endcan

                        {{-- ====================================================
                            EXTRAS
                        ===================================================== --}}

                        @can('extra.view')

                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('stock.*', 'admin.recipients.*', 'admin.notification.*', 'api.*') ? 'active' : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="extrasMenu" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-grid"></i>

                                    <span>Extras</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu app-mega-menu-wide" aria-labelledby="extrasMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Recursos adicionais
                                            </div>

                                            <div class="app-mega-description">
                                                Estoque, notificações e integrações
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-grid"></i>
                                        </span>

                                    </div>

                                    <div class="row g-4">

                                        @can('stock.view')
                                            <div class="col-12 col-md-4">

                                                <div class="app-menu-group-title">
                                                    Estoque
                                                </div>

                                                <a class="app-menu-link" href="{{ route('stock.items.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-box-seam"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Inventário</strong>
                                                        <small>Itens e quantidades</small>
                                                    </span>
                                                </a>

                                                <a class="app-menu-link" href="{{ route('stock.movements.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-arrow-left-right"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Movimentações</strong>
                                                        <small>Entradas e saídas</small>
                                                    </span>
                                                </a>

                                                <a class="app-menu-link" href="{{ route('stock.items.showProduction') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-bar-chart"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Estoque x produção</strong>
                                                        <small>Comparativo operacional</small>
                                                    </span>
                                                </a>

                                            </div>
                                        @endcan

                                        <div class="col-12 col-md-4">

                                            <div class="app-menu-group-title">
                                                Notificações
                                            </div>

                                            @can('recipients.view')
                                                <a class="app-menu-link" href="{{ route('admin.recipients.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-bell"></i>
                                                    </span>

                                                    <span>
                                                        <strong>Sistema</strong>
                                                        <small>Destinatários e alertas</small>
                                                    </span>
                                                </a>
                                            @endcan

                                            @can('notification.view')
                                                <a class="app-menu-link" href="{{ route('admin.notification.index') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </span>

                                                    <span>
                                                        <strong>WhatsApp</strong>
                                                        <small>Lembretes automáticos</small>
                                                    </span>
                                                </a>
                                            @endcan

                                        </div>

                                        <div class="col-12 col-md-4">

                                            <div class="app-menu-group-title">
                                                Integrações
                                            </div>
                                            {{--
                                            @can('api.nfe')
                                                <a class="app-menu-link" href="{{ route('api.mk.nfe') }}">

                                                    <span class="app-menu-link-icon">
                                                        <i class="bi bi-receipt"></i>
                                                    </span>

                                                    <span>
                                                        <strong>NFE MK-Auth</strong>
                                                        <small>Integração fiscal</small>
                                                    </span>
                                                </a>
                                            @endcan
--}}
                                            @if (config('services.wireguard.url') && config('services.wireguard.password'))
                                                @can('administrator.vpn')
                                                    <a class="app-menu-link" href="{{ route('api.vpn.index') }}">

                                                        <span class="app-menu-link-icon">
                                                            <i class="bi bi-shield-lock"></i>
                                                        </span>

                                                        <span>
                                                            <strong>VPN WF</strong>
                                                            <small>Acesso WireGuard</small>
                                                        </span>
                                                    </a>
                                                @endcan
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </li>

                        @endcan

                        {{-- ====================================================
                            ADMINISTRADOR
                        ===================================================== --}}

                        @can('administrator.user')
                            <li
                                class="nav-item dropdown app-menu-item
                                    {{ request()->routeIs('options.*', 'admin.*', 'activitylogs.*') ? 'active' : '' }}">

                                <a class="nav-link dropdown-toggle" href="#" id="administratorMenu" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="bi bi-gear"></i>

                                    <span>Administrador</span>
                                </a>

                                <div class="dropdown-menu app-mega-menu app-mega-menu-wide"
                                    aria-labelledby="administratorMenu">

                                    <div class="app-mega-header">

                                        <div>
                                            <div class="app-mega-title">
                                                Administração
                                            </div>

                                            <div class="app-mega-description">
                                                Sistema, usuários e segurança
                                            </div>
                                        </div>

                                        <span class="app-mega-icon">
                                            <i class="bi bi-gear"></i>
                                        </span>

                                    </div>

                                    <div class="row g-4">

                                        <div class="col-12 col-md-7">

                                            <div class="app-menu-group-title">
                                                Sistema
                                            </div>

                                            <a class="app-menu-link" href="{{ route('options.colors.edit') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-palette"></i>
                                                </span>

                                                <span>
                                                    <strong>Estilos e logo</strong>
                                                    <small>Aparência do sistema</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('options.resource.edit') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-toggles"></i>
                                                </span>

                                                <span>
                                                    <strong>Recursos do sistema</strong>
                                                    <small>Ativar e configurar módulos</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('admin.systempanel') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-speedometer2"></i>
                                                </span>

                                                <span>
                                                    <strong>Painel do sistema</strong>
                                                    <small>Informações operacionais</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('options.systemresource.edit') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-sliders"></i>
                                                </span>

                                                <span>
                                                    <strong>Recursos administrativos</strong>
                                                    <small>Configurações avançadas</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('activitylogs.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-clock-history"></i>
                                                </span>

                                                <span>
                                                    <strong>Logs</strong>
                                                    <small>Histórico de atividades</small>
                                                </span>
                                            </a>

                                        </div>

                                        <div class="col-12 col-md-5">

                                            <div class="app-menu-group-title">
                                                Acessos
                                            </div>

                                            <a class="app-menu-link" href="{{ route('admin.usuarios.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-people"></i>
                                                </span>

                                                <span>
                                                    <strong>Usuários</strong>
                                                    <small>Contas do sistema</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('admin.roles.index') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-person-badge"></i>
                                                </span>

                                                <span>
                                                    <strong>Perfis de usuário</strong>
                                                    <small>Grupos e permissões</small>
                                                </span>
                                            </a>

                                            <a class="app-menu-link" href="{{ route('admin.users.sessions') }}">

                                                <span class="app-menu-link-icon">
                                                    <i class="bi bi-display"></i>
                                                </span>

                                                <span>
                                                    <strong>Sessões ativas</strong>
                                                    <small>Acessos conectados</small>
                                                </span>
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </li>
                        @endcan

                    </ul>

                    {{-- ========================================================
                        USUÁRIO
                    ========================================================= --}}

                    @auth

                        <div class="dropdown app-user-menu">

                            <button class="btn app-user-button dropdown-toggle" type="button" id="userMenu"
                                data-bs-toggle="dropdown" aria-expanded="false">

                                <span class="app-user-avatar">
                                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                </span>

                                <span class="app-user-information">

                                    <strong>
                                        {{ Auth::user()->name }}
                                    </strong>

                                    <small>
                                        Minha conta
                                    </small>

                                </span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-end app-user-dropdown" aria-labelledby="userMenu">

                                <li class="app-user-dropdown-header">

                                    <span class="app-user-avatar app-user-avatar-large">
                                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                    </span>

                                    <div>

                                        <strong>
                                            {{ Auth::user()->name }}
                                        </strong>

                                        @if (Auth::user()->email)
                                            <small>
                                                {{ Auth::user()->email }}
                                            </small>
                                        @endif

                                    </div>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">

                                        <i class="bi bi-person"></i>
                                        Editar perfil
                                    </a>

                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <form method="POST" action="{{ route('logout') }}">

                                        @csrf

                                        <button type="submit" class="dropdown-item app-logout-button">

                                            <i class="bi bi-box-arrow-right"></i>
                                            Sair
                                        </button>

                                    </form>

                                </li>

                            </ul>

                        </div>

                    @endauth

                </div>

            </div>

        </nav>

    </header>

    {{-- CONTEÚDO --}}
    <main class="flex-grow-1 app-main">
        @yield('content')
    </main>

    {{-- MODAL DE CONFIRMAÇÃO --}}
    <x-confirm-modal />

    {{-- RODAPÉ --}}
    @include('layouts.footer')

    {{-- ================================================================
        SCRIPTS
    ================================================================= --}}

    {{-- jQuery, caso alguma tela ainda utilize --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Scripts do sistema --}}
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ asset('js/confirm-action.js') }}"></script>

    <script src="{{ asset('js/confirm-modal.js') }}"></script>

    <script src="{{ asset('js/header.js') }}"></script>

    {{-- Scripts específicos das páginas --}}
    @stack('scripts')

    {{-- Toast --}}
    @include('layouts.toast')

</body>

</html>
