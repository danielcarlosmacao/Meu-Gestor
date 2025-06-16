<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        
        <link rel="stylesheet" href="/css/style.css" type='text/css'>
        <!--bootstrap-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        


        <title>@yield('title')</title>

    </head>
    <body class="d-flex flex-column min-vh-100">
        <div class="content" >
            <ul class="exo-menu"  style="z-index:999">
                <li><a class="active" href="{{route('welcome')}}"> Home</a></li>
                <li class="drop-down"><a href="#">Gestao de torres  <img src="/icon/tower.png" alt="1px" width="25" height="25" style="margin-right: 5px;"></a> 
                    <ul class="drop-down-ul animated ">
                    <li><a href="{{route('tower.index')}}">Torres</a></li>
                    <li><a href="{{route('equipment.index')}}">Equipamentos</a></li>
                    <li><a href="{{route('battery.index')}}">Bateria</a></li>
                    <li><a href="{{route('plate.index')}}">Placa solar</a></li>
                    </ul>
                
                </li>

                <li><a href="{{route('maintenance.index')}}"><i class="fa fa-cogs"></i> Serviços</a></li>	
                <li><a href="#"><i class="fa fa-briefcase"></i>Controle de frota</a></li>
                </li>                
                <li class="drop-down"><a href="#"><i class="fa fa-cogs"></i>Teste</a>
                    
                    <ul class="drop-down-ul animated ">
                    <li class="flyout-right"><a href="#">Cadastro</a><!--Flyout Right-->
                        <ul class="animated fadeIn">
                            <li><a href="#">Torres</a></li>
                            <li><a href="#">Equipamentos</a></li>
                            <li><a href="#">Bateria</a></li>
                            <li><a href="#">Placa sola</a></li>
                        </ul>
                    </li>
                    <li class="flyout-right"><a href="#">aaa</a><!--Flyout Right-->
                        <ul class="animated fadeIn">
                            <li><a href="#">Listar</a></li>
                            <li><a href="#">outros</a></li>
                            <li><a href="#">outros</a></li>
                        </ul>
                    </li>
                    <li><a href="/admin/lancamento">Lancamento</a></li>
                    </ul>
                
                </li>
            </ul>
        </div>
         
         
    
    
        
  

        <main class="flex-grow-1">
            @yield('content')
        </main>
    @stack('scripts')
    @extends('layouts.footer')  

    <!--bootstrap-->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- Bootstrap JS (necessário para modal funcionar) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
<script type="text/javascript" src="/js/app.js"></script>
</html>