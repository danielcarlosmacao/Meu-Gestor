<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>


    <!--bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="/css/style.css" type='text/css'>

    <style>
        :root {
            --color-primary: {{ $appOptions['color-primary'] ?? '#24b153' }};
            --color-secondary: {{ $appOptions['color-secondary'] ?? '#6fbe89' }};
            --color-text: {{ $appOptions['color-text'] ?? '#0a6428' }};
            --color-hover: {{ $appOptions['color-hover'] ?? '#186d34' }};
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bgc-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                @if (!empty($appOptions['logo']))
                    <img src="{{ asset($appOptions['logo']) }}" alt="Logo do Sistema" style="height: 50px;">
                @else
                    <strong>{{ config('app.name') }}</strong>
                @endif &nbsp;&nbsp;&nbsp;
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @can('towers.view')
                        <!-- gestao de torre -->
                        <li class="nav-item dropdown position-static" id="menutower">
                            <a class="nav-link dropdown-toggle" href="#">GESTAO DE TORRES</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-1">
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Gestão das torres</h6>
                                        <a href="{{ route('tower.index') }}">Torres</a>
                                        <a href="{{ route('battery.index') }}">Baterias</a>
                                        <a href="{{ route('equipment.index') }}">Equipamentos</a>
                                        <a href="{{ route('plate.index') }}">Placas solar</a>
                                    </div>
                                    <div class="col-md-5">
                                        <h6>Extra</h6>
                                        @can('towers.maintenance')<a href="{{ route('maintenance.index') }}"><i class="fa fa-cogs"></i> Serviços</a> @endcan
                                    </div>

                                </div>
                            </div>
                        </li>
                    @endcan
                    @can('fleets.view')
                        <!-- gestao de frota -->
                        <li class="nav-item dropdown position-static" id="menuFrota">
                            <a class="nav-link dropdown-toggle" href="#">GESTAO DE FROTA</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Gestão da frota</h6>
                                        <a href="{{ route('fleet.vehicles.index') }}">Veiculos</a>
                                        <a href="{{ route('fleet.vehicle_maintenances.index') }}">Manutencao</a>
                                        <a href="{{ route('fleet.vehicle_services.index') }}">Tipos de servicos</a>
                                        <a href="{{ route('fleet.vehicle_workshop.index') }}">Oficinas</a>
                                        <!--<a href="#">Abastecimentos</a>-->
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                    @can('service.view')
                        <!-- gestao de serviço -->
                        <li class="nav-item dropdown position-static" id="menuServico">
                            <a class="nav-link dropdown-toggle" href="#">GESTAO DE SERVIÇOS</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Gestão dos seviços</h6>
                                        <a href="{{ route('service.clients.index') }}">Clientes</a>
                                        <a href="{{ route('service.equipment_maintenances.index') }}">Manutencao de
                                            equipamentos</a>
                                        <a href="{{ route('service.maintenances.index') }}">Visitas tecnicas</a>
                                        <!--<a href="#">Abastecimentos</a>-->
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                    <!-- Ferias  -->
                    @can('vacations.view')
                        <li class="nav-item dropdown position-static" id="menuFerias">
                            <a class="nav-link dropdown-toggle" href="#">GESTAO DE FERIAS</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Gestão das ferias</h6>
                                        @can('collaborators.view')
                                            <a href="{{ route('vacation_manager.collaborators.index') }}">Colaboradores </a>
                                        @endcan
                                        @can('vacations.edit')
                                            <a href="{{ route('vacation_manager.vacations.index') }}">Registro de ferias</a>
                                        @endcan
                                        @can('vacation_manager.calendar')
                                            <a href="{{ route('vacation_manager.calendar') }}">Calendario</a>
                                        @endcan
                                    </div>
                                    <div class="col-md-6">
                                        <h6></h6>

                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                    <!-- administrador -->
                    @can('administrator.user')
                        <li class="nav-item dropdown position-static" id="menuAdmin">
                            <a class="nav-link dropdown-toggle" href="#">ADMINISTRADOR</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Gestão de recursos</h6>
                                        <a href="{{ route('options.colors.edit') }}">Estilos e logo do sistema</a>
                                        <a href="{{ route('options.resource.edit') }}">Recursos do sistema</a>
                                        <a href="{{ route('admin.systempanel') }}">Painel de informativo do sistema</a>
                                        <a href="{{ route('options.systemresource.edit') }}">Recursos do administrador</a>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Acessos</h6>
                                        <a href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                                        <a href="{{ route('admin.users.sessions') }}">Sessões ativas</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endcan
                    <!-- Verifica se o usuário está logado -->
                    @auth
                        <li class="nav-item dropdown position-static" id="menuUser">
                            <a class="nav-link dropdown-toggle" href="#" style="color:rgb(0, 0, 0);"> <i
                                    class="bi bi-person-fill me-1"></i>{{ Auth::user()->name }}</a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Editar Perfil</a>

                                        <form id="logout-form" method="POST" action="{{ route('logout') }}"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                        <a href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Sair
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endauth


                </ul>
            </div>
        </div>
    </nav>



    <main class="flex-grow-1">
        @yield('content')
    </main>

    @stack('scripts')
    @extends('layouts.footer')

    <script type="text/javascript" src="/js/app.js"></script>

    <!--bootstrap-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <!-- Bootstrap JS (necessário para modal funcionar) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



    <!-- Hover script -->
   <script>
    const hoverMenus = ['menutower', 'menuFrota', 'menuServico', 'menuFerias', 'menuAdmin', 'menuUser'];

    hoverMenus.forEach(id => {
        const menu = document.getElementById(id);
        if (!menu) return; // <-- ignora se o menu não existir para o usuário

        let timeout;

        menu.addEventListener('mouseenter', () => {
            clearTimeout(timeout);
            menu.classList.add('show');
        });

        menu.addEventListener('mouseleave', () => {
            timeout = setTimeout(() => {
                menu.classList.remove('show');
            }, 200);
        });
    });
</script>
</body>

</html>
