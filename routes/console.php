<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Default: sekali sehari (cukup untuk rule "3 hari"). Set AUTO_CANCEL_SCHEDULE=every_minute
// di .env kalau perlu testing cepat tanpa nunggu tengah malam.
$autoCancelSchedule = Schedule::command('wastes:auto-cancel-organic');

if (env('AUTO_CANCEL_SCHEDULE') === 'every_minute') {
    $autoCancelSchedule->everyMinute();
} else {
    $autoCancelSchedule->daily();
}
