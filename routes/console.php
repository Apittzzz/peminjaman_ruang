<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MarkFinishedBookings;

Schedule::command(MarkFinishedBookings::class)->everyFiveMinutes();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:mark-finished', function () {
    $this->call(MarkFinishedBookings::class);
})->purpose('Mark finished bookings');
