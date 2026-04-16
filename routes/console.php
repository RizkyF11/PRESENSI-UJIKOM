<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('qr:cleanup')->daily();

Schedule::command('absensi:generate-alpha')
    ->dailyAt('12:20'); // production bisa dailyAt('23:59')