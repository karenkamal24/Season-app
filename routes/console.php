<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Refresh upcoming events cache daily at 2:00 AM
Schedule::command('events:warm-cache')->dailyAt('02:00')->runInBackground();

