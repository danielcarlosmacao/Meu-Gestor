<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('manutencao:enviar-whatsapp')
    ->dailyAt('07:30'); // ou everyMinute() para testes

Schedule::command('mensagens:enviar')->everyMinute();

// Executa todos os dias às 3h da manhã
Schedule::command('logs:clear 90')->dailyAt('03:00');