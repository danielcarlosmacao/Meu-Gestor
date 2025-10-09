<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Option;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        
        

        // Recupera todas as opções e cria array key => value
        /**/
        if (Schema::hasTable('options')) {
        $options = Cache::rememberForever('app.options', function () {
            return Option::pluck('value', 'reference')->toArray();
        });
        // Torna disponíveis em todas as views
        View::share('appOptions', $options);
    }


    

    }
}
