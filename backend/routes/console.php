<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('lol:sincronizar-estaticos')
    ->hourly();

Schedule::command('lol:seed-ranked-players --pages=2')
    ->everyThirtyMinutes();

Schedule::command('lol:fetch-player-matches --jogadores=100 --partidas=20')
    ->everyTenMinutes();

Schedule::command('lol:agregar-estatisticas-campeoes')
    ->hourlyAt(5);
